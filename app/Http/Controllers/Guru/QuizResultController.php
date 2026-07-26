<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Notifications\QuizResultReleased;

class QuizResultController extends Controller
{
    /**
     * Rekap nilai semua siswa untuk satu kuis.
     */
    public function show(SchoolClass $class, Quiz $quiz): View
    {
        $this->authorizeTeacher($class);

        $students = $class->students()->with('user')->get();
        $attempts = $quiz->attempts()->with('student.user')->orderByDesc('attempt_number')->get()->groupBy('student_id');

        $rows = $students->map(function ($student) use ($attempts, $quiz) {
            $studentAttempts = $attempts->get($student->id, collect());
            return [
                'student'      => $student,
                'attempts'     => $studentAttempts,
                'final_score'  => $quiz->finalScoreFor($student),
                'needs_manual' => $studentAttempts->contains(fn ($a) => $a->hasPendingManualGrading()),
            ];
        });

        // Statistik ringkas
        $finishedScores = $rows->pluck('final_score')->filter(fn ($s) => ! is_null($s));
        $stats = [
            'average' => $finishedScores->isNotEmpty() ? round($finishedScores->avg(), 1) : null,
            'highest' => $finishedScores->isNotEmpty() ? $finishedScores->max() : null,
            'lowest'  => $finishedScores->isNotEmpty() ? $finishedScores->min() : null,
            'done'    => $finishedScores->count(),
            'total'   => $students->count(),
        ];

        return view('guru.quizzes.results.index', compact('class', 'quiz', 'rows', 'stats'));
    }

    /**
     * Detail jawaban 1 attempt siswa — untuk koreksi manual isian singkat.
     */
    public function attempt(SchoolClass $class, Quiz $quiz, \App\Models\QuizAttempt $attempt): View
    {
        $this->authorizeTeacher($class);

        $attempt->load('answers.question.options', 'student.user');

        return view('guru.quizzes.results.attempt', compact('class', 'quiz', 'attempt'));
    }

    /**
     * Koreksi manual 1 jawaban isian singkat.
     */
    public function gradeAnswer(Request $request, SchoolClass $class, Quiz $quiz, \App\Models\QuizAttempt $attempt, QuizAnswer $answer): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $request->validate([
            'is_correct' => ['required', 'boolean'],
        ]);

        $question = $answer->question;
        $points = $quiz->questions()->find($question->id)?->pivot->points_override ?? $question->points;

        $answer->update([
            'is_correct'           => $request->boolean('is_correct'),
            'points_earned'        => $request->boolean('is_correct') ? $points : 0,
            'needs_manual_grading' => false,
            'graded_at'            => now(),
        ]);

        // Hitung ulang skor total attempt setelah koreksi manual
        $this->recalculateAttemptScore($attempt);

        return back()->with('success', 'Jawaban berhasil dikoreksi.');
    }



    /**
     * Rilis nilai kuis yang ditahan (show_score = held) ke semua siswa yang sudah selesai mengerjakan.
     */
    public function releaseScores(SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->authorizeTeacher($class);

        abort_unless($quiz->needsManualRelease(), 400, 'Kuis ini tidak menahan nilai.');

        if ($quiz->score_released_at) {
            return back()->with('success', 'Nilai kuis ini sudah dirilis sebelumnya.');
        }

        $quiz->update(['score_released_at' => now()]);

        $finishedAttempts = $quiz->attempts()
            ->with('student.user')
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->get();

        foreach ($finishedAttempts as $attempt) {
            $attempt->student->user->notify(new QuizResultReleased($attempt));
        }

        return back()->with('success', 'Nilai kuis berhasil dirilis ke ' . $finishedAttempts->count() . ' siswa.');
    }



    // ── Private helper ────────────────────────────────

    private function recalculateAttemptScore(\App\Models\QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz;
        $answers = $attempt->answers;

        $totalPossible = $quiz->questions->sum(fn ($q) => $q->pivot->points_override ?? $q->points);
        $totalEarned = $answers->where('needs_manual_grading', false)->sum('points_earned');

        $scorePercent = $totalPossible > 0 ? (int) round(($totalEarned / $totalPossible) * 100) : 0;

        $attempt->update(['score' => $scorePercent]);
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}