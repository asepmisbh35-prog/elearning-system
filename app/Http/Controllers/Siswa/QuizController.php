<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Notifications\StudentCheatingDetected;
use App\Notifications\QuizResultReleased;

class QuizController extends Controller
{
    /**
     * Daftar kuis untuk siswa di satu kelas.
     */
    public function index(SchoolClass $schoolClass): View
    {
        $student = Auth::user()->student;
        $this->authorizeEnrollment($schoolClass, $student);

        $quizzes = $schoolClass->quizzes()
            ->where('status', 'published')
            ->orderBy('access_start_at')
            ->get()
            ->map(function ($quiz) use ($student) {
                $quiz->my_final_score = $quiz->finalScoreFor($student);
                $quiz->my_attempts_used = $quiz->attemptsFor($student)->count();
                return $quiz;
            });

        return view('siswa.quizzes.index', compact('schoolClass', 'quizzes'));
    }

    /**
     * Mulai percobaan baru — atau lanjutkan percobaan yang masih berjalan.
     */
    public function start(SchoolClass $schoolClass, Quiz $quiz): RedirectResponse
    {
        $student = Auth::user()->student;
        $this->authorizeEnrollment($schoolClass, $student);

        abort_unless($quiz->isOpen(), 403, 'Kuis ini belum dibuka atau sudah ditutup.');

        // Lanjutkan percobaan yang masih in_progress kalau ada
        $ongoing = $quiz->attemptsFor($student)->where('status', 'in_progress')->first();
        if ($ongoing) {
            if ($ongoing->isExpired()) {
                $this->finalizeAttempt($ongoing, 'auto_submitted');
            } else {
                return redirect()->route('siswa.classes.quizzes.attempt', [$schoolClass, $quiz, $ongoing]);
            }
        }

        abort_unless($quiz->canAttempt($student), 403, 'Kamu sudah menghabiskan jumlah percobaan maksimal.');

        $attemptNumber = $quiz->attemptsFor($student)->count() + 1;

        $attempt = $quiz->attempts()->create([
            'student_id'     => $student->id,
            'attempt_number' => $attemptNumber,
            'started_at'     => now(),
            'status'         => 'in_progress',
        ]);

        return redirect()->route('siswa.classes.quizzes.attempt', [$schoolClass, $quiz, $attempt]);
    }

    /**
     * Halaman pengerjaan kuis (timer, semua/satu-per-satu soal).
     */
    public function attempt(SchoolClass $schoolClass, Quiz $quiz, QuizAttempt $attempt): View|RedirectResponse
    {
        $student = Auth::user()->student;
        $this->authorizeEnrollment($schoolClass, $student);

        abort_unless($attempt->student_id === $student->id, 403);

        if ($attempt->isFinished()) {
            return redirect()->route('siswa.classes.quizzes.result', [$schoolClass, $quiz, $attempt]);
        }

        if ($attempt->isExpired()) {
            $this->finalizeAttempt($attempt, 'auto_submitted');
            return redirect()->route('siswa.classes.quizzes.result', [$schoolClass, $quiz, $attempt]);
        }

        $questions = $quiz->questions()->with('options')->get();

        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        if ($quiz->shuffle_options) {
            $questions->each(function ($q) {
                $q->setRelation('options', $q->options->shuffle());
            });
            $questions->where('type', 'sorting')->each(function ($q) {
                $q->setRelation('options', $q->options->shuffle());
            });
        }

        $existingAnswers = $attempt->answers()->pluck('answer_data', 'question_id');
        $deadline = $attempt->deadlineAt();

        return view('siswa.quizzes.attempt', compact('schoolClass', 'quiz', 'attempt', 'questions', 'existingAnswers', 'deadline'));
    }

    /**
     * Simpan/update jawaban satu soal (AJAX, autosave).
     */
    public function saveAnswer(Request $request, SchoolClass $schoolClass, Quiz $quiz, QuizAttempt $attempt): JsonResponse
    {
        $student = Auth::user()->student;
        abort_unless($attempt->student_id === $student->id, 403);

        if ($attempt->isFinished() || $attempt->isExpired()) {
            return response()->json(['status' => 'closed'], 403);
        }

        $request->validate([
            'question_id'  => ['required', 'exists:questions,id'],
            'answer_data'  => ['required'],
        ]);

        QuizAnswer::updateOrCreate(
            ['quiz_attempt_id' => $attempt->id, 'question_id' => $request->question_id],
            ['answer_data' => $request->answer_data]
        );

        return response()->json(['status' => 'saved']);
    }

    /**
     * Catat pelanggaran anti-kecurangan (keluar tab/window).
     */
    public function reportTabSwitch(SchoolClass $schoolClass, Quiz $quiz, QuizAttempt $attempt): JsonResponse
    {
        $student = Auth::user()->student;
        abort_unless($attempt->student_id === $student->id, 403);

        if ($attempt->isFinished()) {
            return response()->json(['status' => 'closed']);
        }

        $attempt->increment('tab_switch_count');

        // Auto-submit setelah 3x pelanggaran
        if ($attempt->tab_switch_count >= 3) {
            $freshAttempt = $attempt->fresh();
            $this->finalizeAttempt($freshAttempt, 'auto_submitted');

            $quiz->schoolClass->teacher->user->notify(new StudentCheatingDetected($freshAttempt));

            return response()->json(['status' => 'auto_submitted', 'count' => $attempt->tab_switch_count]);
        }

        return response()->json(['status' => 'warned', 'count' => $attempt->tab_switch_count]);
    }

    /**
     * Submit final kuis (manual oleh siswa).
     */
    public function submit(SchoolClass $schoolClass, Quiz $quiz, QuizAttempt $attempt): RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($attempt->student_id === $student->id, 403);

        if (! $attempt->isFinished()) {
            $this->finalizeAttempt($attempt, 'submitted');
        }

        return redirect()->route('siswa.classes.quizzes.result', [$schoolClass, $quiz, $attempt]);
    }

    /**
     * Halaman hasil kuis.
     */
    public function result(SchoolClass $schoolClass, Quiz $quiz, QuizAttempt $attempt): View
    {
        $student = Auth::user()->student;
        abort_unless($attempt->student_id === $student->id, 403);

        $attempt->load('answers.question.options');

        $canSeeScore = $quiz->isScoreReleased();
        $finalScore = $quiz->finalScoreFor($student);

        return view('siswa.quizzes.result', compact('schoolClass', 'quiz', 'attempt', 'canSeeScore', 'finalScore'));
    }

    // ── Private helper ────────────────────────────────

    /**
     * Hitung skor otomatis & tutup attempt.
     */
    private function finalizeAttempt(QuizAttempt $attempt, string $status): void
    {
        $quiz = $attempt->quiz;
        $answers = $attempt->answers()->with('question.options')->get();
        $questionsById = $quiz->questions()->get()->keyBy('id');

        $totalEarned = 0;
        $totalPossible = 0;

        foreach ($questionsById as $question) {
            $totalPossible += $question->pivot->points_override ?? $question->points;

            $answer = $answers->firstWhere('question_id', $question->id);
            if (! $answer) continue;

            $points = $question->pivot->points_override ?? $question->points;
            $result = $this->gradeAnswer($question, $answer->answer_data, $points);

            $answer->update([
                'is_correct'           => $result['is_correct'],
                'points_earned'        => $result['points_earned'],
                'needs_manual_grading' => $result['needs_manual_grading'],
            ]);

            if (! $result['needs_manual_grading']) {
                $totalEarned += $result['points_earned'];
            }
        }

        $scorePercent = $totalPossible > 0 ? (int) round(($totalEarned / $totalPossible) * 100) : 0;

        $attempt->update([
            'submitted_at' => now(),
            'status'       => $status,
            'score'        => $scorePercent,
        ]);

       

if ($attempt->quiz->show_score === 'immediately') {
    $attempt->student->user->notify(new QuizResultReleased($attempt));
}
    }

    /**
     * Koreksi otomatis per tipe soal.
     */
    private function gradeAnswer($question, $answerData, int $points): array
    {
        switch ($question->type) {
            case 'multiple_choice':
            case 'true_false':
            case 'true_false_swipe':
                $correctOption = $question->options->firstWhere('is_correct', true);
                $selectedId = $answerData['option_id'] ?? null;
                $isCorrect = $correctOption && (string) $selectedId === (string) $correctOption->id;

                return [
                    'is_correct'           => $isCorrect,
                    'points_earned'        => $isCorrect ? $points : 0,
                    'needs_manual_grading' => false,
                ];

            case 'short_answer':
                $keywords = $question->answer_keywords ?? [];
                $answerText = strtolower(trim($answerData['text'] ?? ''));

                if (empty($keywords)) {
                    return [
                        'is_correct'           => null,
                        'points_earned'        => 0,
                        'needs_manual_grading' => true,
                    ];
                }

                $isCorrect = collect($keywords)->contains(fn($kw) => str_contains($answerText, $kw));

                return [
                    'is_correct'           => $isCorrect,
                    'points_earned'        => $isCorrect ? $points : 0,
                    'needs_manual_grading' => false,
                ];

            case 'sorting':
                $correctOrder = $question->options->sortBy('order')->pluck('id')->map(fn($id) => (string) $id)->values()->toArray();
                $studentOrder = collect($answerData['order'] ?? [])->map(fn($id) => (string) $id)->values()->toArray();
                $isCorrect = $correctOrder === $studentOrder;

                return [
                    'is_correct'           => $isCorrect,
                    'points_earned'        => $isCorrect ? $points : 0,
                    'needs_manual_grading' => false,
                ];

            case 'fill_blank':
                $blanksKeywords = $question->meta['blanks'] ?? [];
                $studentBlanks  = $answerData['blanks'] ?? [];

                // Kalau ada blank yang keywordnya kosong, seluruh soal butuh koreksi manual
                if (empty($blanksKeywords) || collect($blanksKeywords)->contains(fn($kw) => empty($kw))) {
                    return ['is_correct' => null, 'points_earned' => 0, 'needs_manual_grading' => true];
                }

                $correctCount = 0;
                foreach ($blanksKeywords as $i => $keywords) {
                    $studentText = strtolower(trim($studentBlanks[$i] ?? ''));
                    if (collect($keywords)->contains(fn($kw) => str_contains($studentText, $kw))) {
                        $correctCount++;
                    }
                }

                $totalBlanks = count($blanksKeywords);
                $earnedPoints = $totalBlanks > 0 ? (int) round($points * $correctCount / $totalBlanks) : 0;

                return [
                    'is_correct'           => $correctCount === $totalBlanks,
                    'points_earned'        => $earnedPoints,
                    'needs_manual_grading' => false,
                ];

            case 'timer_challenge':
                $selected = $answerData['option_id'] ?? null;
                $correctOption = $question->options->firstWhere('is_correct', true);
                $isCorrect = $correctOption && (string) $selected === (string) $correctOption->id;

                $timeLimit = $question->meta['time_limit_seconds'] ?? 15;
                $timeTaken = min($answerData['time_taken_seconds'] ?? $timeLimit, $timeLimit);
                $bonusMaxPercent = $question->meta['bonus_max_percent'] ?? 50;

                $earnedPoints = $isCorrect ? $points : 0;
                if ($isCorrect && $timeLimit > 0) {
                    $speedRatio = max(0, ($timeLimit - $timeTaken) / $timeLimit);
                    $bonus = $points * ($bonusMaxPercent / 100) * $speedRatio;
                    $earnedPoints += $bonus;
                }

                return [
                    'is_correct'           => $isCorrect,
                    'points_earned'        => round($earnedPoints, 2),
                    'needs_manual_grading' => false,
                ];

            case 'drag_drop':
                $pairs = $question->meta['pairs'] ?? [];
                $studentMatches = $answerData['matches'] ?? []; // ['0' => '2', '1' => '0', ...]

                if (empty($pairs)) {
                    return ['is_correct' => null, 'points_earned' => 0, 'needs_manual_grading' => true];
                }

                $correctCount = 0;
                foreach (array_keys($pairs) as $leftIndex) {
                    if ((string) ($studentMatches[$leftIndex] ?? null) === (string) $leftIndex) {
                        $correctCount++;
                    }
                }

                $total = count($pairs);
                $earnedPoints = $total > 0 ? (int) round($points * $correctCount / $total) : 0;

                return [
                    'is_correct'           => $correctCount === $total,
                    'points_earned'        => $earnedPoints,
                    'needs_manual_grading' => false,
                ];

            case 'word_search':
                $words = collect($question->meta['words'] ?? [])->map(fn($w) => strtoupper(trim($w)))->filter()->values();
                $foundWords = collect($answerData['found_words'] ?? [])->map(fn($w) => strtoupper(trim($w)))->filter();

                if ($words->isEmpty()) {
                    return ['is_correct' => null, 'points_earned' => 0, 'needs_manual_grading' => true];
                }

                // Hanya hitung kata yang memang valid ada di daftar (jaga-jaga data manipulasi dari client)
                $validFoundCount = $foundWords->intersect($words)->unique()->count();
                $totalWords = $words->count();

                $earnedPoints = $totalWords > 0 ? (int) round($points * $validFoundCount / $totalWords) : 0;

                return [
                    'is_correct'           => $validFoundCount === $totalWords,
                    'points_earned'        => $earnedPoints,
                    'needs_manual_grading' => false,
                ];
            case 'crossword':
                $entries = collect($question->meta['entries'] ?? []);
                $studentEntries = $answerData['entries'] ?? [];

                if ($entries->isEmpty()) {
                    return ['is_correct' => null, 'points_earned' => 0, 'needs_manual_grading' => true];
                }

                $correctCount = 0;
                foreach ($entries as $i => $entry) {
                    $expected = strtoupper(trim($entry['word'] ?? ''));
                    $actual = strtoupper(trim($studentEntries[$i] ?? ''));
                    if ($expected !== '' && $expected === $actual) {
                        $correctCount++;
                    }
                }

                $total = $entries->count();
                $earnedPoints = $total > 0 ? (int) round($points * $correctCount / $total) : 0;

                return [
                    'is_correct'           => $correctCount === $total,
                    'points_earned'        => $earnedPoints,
                    'needs_manual_grading' => false,
                ];

            default:
                return [
                    'is_correct'           => null,
                    'points_earned'        => 0,
                    'needs_manual_grading' => true,
                ];
        } 
    }

    private function authorizeEnrollment(SchoolClass $schoolClass, $student): void
    {
        abort_unless($student, 403);

        $enrolled = $schoolClass->enrollments()->where('student_id', $student->id)->exists();
        abort_unless($enrolled, 403, 'Kamu belum bergabung di kelas ini.');
    }
}
