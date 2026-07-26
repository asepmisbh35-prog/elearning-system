<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Notifications\QuizResultReleased;

class QuizController extends Controller
{
    /**
     * Daftar kuis dalam satu kelas.
     */
    public function index(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $quizzes = $class->quizzes()
            ->withCount(['attempts', 'questions'])
            ->orderByDesc('access_start_at')
            ->get();

        return view('guru.quizzes.index', compact('class', 'quizzes'));
    }

    /**
     * Form buat kuis baru — pilih soal dari bank soal milik guru.
     */
    public function create(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $teacher = Auth::user()->teacher;
        $questions = Question::where('teacher_id', $teacher->id)->orderByDesc('created_at')->get();
        $gradeComponents = $class->gradeComponents()->get();

        return view('guru.quizzes.create', compact('class', 'questions', 'gradeComponents'));
    }

    /**
     * Simpan kuis baru + attach soal terpilih.
     */
    public function store(StoreQuizRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $quiz = $class->quizzes()->create($request->validated());

        $this->syncQuestions($quiz, $request->question_ids);

        return redirect()
            ->route('guru.classes.quizzes.index', $class)
            ->with('success', 'Kuis berhasil dibuat.');
    }

    /**
     * Form edit kuis.
     */
    public function edit(SchoolClass $class, Quiz $quiz): View
    {
        $this->authorizeTeacher($class);

        $teacher = Auth::user()->teacher;
        $questions = Question::where('teacher_id', $teacher->id)->orderByDesc('created_at')->get();
        $gradeComponents = $class->gradeComponents()->get();
        $selectedIds = $quiz->questions()->pluck('questions.id')->toArray();
        $hasAttempts = $quiz->attempts()->exists();

        return view('guru.quizzes.edit', compact('class', 'quiz', 'questions', 'gradeComponents', 'selectedIds', 'hasAttempts'));
    }

    /**
     * Update kuis.
     * Kalau sudah ada siswa yang attempt, soal & durasi tidak boleh diubah — hanya jendela waktu akses.
     */
    public function update(StoreQuizRequest $request, SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $hasAttempts = $quiz->attempts()->exists();

        if ($hasAttempts) {
            $quiz->update([
                'access_start_at' => $request->access_start_at,
                'access_end_at'   => $request->access_end_at,
            ]);

            return redirect()
                ->route('guru.classes.quizzes.index', $class)
                ->with('success', 'Jendela waktu akses kuis berhasil diperbarui.');
        }

        $quiz->update($request->validated());
        $this->syncQuestions($quiz, $request->question_ids);

        return redirect()
            ->route('guru.classes.quizzes.index', $class)
            ->with('success', 'Kuis berhasil diperbarui.');
    }

    /**
     * Hapus kuis.
     */
    public function destroy(SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $quiz->delete();

        return redirect()
            ->route('guru.classes.quizzes.index', $class)
            ->with('success', 'Kuis berhasil dihapus.');
    }


    /**
     * Rilis nilai kuis yang ditahan (show_score = held) ke semua siswa.
     */
    public function releaseScores(SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->authorizeTeacher($class);

        abort_unless($quiz->needsManualRelease(), 400, 'Kuis ini tidak menahan nilai.');

        if ($quiz->score_released_at) {
            return back()->with('success', 'Nilai kuis ini sudah dirilis sebelumnya.');
        }

        $quiz->update(['score_released_at' => now()]);

        $studentsFinished = $class->students()
            ->with('user')
            ->whereHas('quizAttempts', function ($q) use ($quiz) {
                $q->where('quiz_id', $quiz->id)->whereIn('status', ['submitted', 'auto_submitted']);
            })
            ->get();

        foreach ($studentsFinished as $student) {
            $student->user->notify(new QuizResultReleased($quiz));
        }

        return back()->with('success', 'Nilai kuis berhasil dirilis ke ' . $studentsFinished->count() . ' siswa.');
    }

    // ── Private helper ────────────────────────────────

    private function syncQuestions(Quiz $quiz, array $questionIds): void
    {
        $syncData = [];
        foreach (array_values($questionIds) as $order => $questionId) {
            $syncData[$questionId] = ['order' => $order];
        }

        $quiz->questions()->sync($syncData);
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
