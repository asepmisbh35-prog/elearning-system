<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Http\Requests\StoreQuestionsBulkRequest;

class QuestionController extends Controller
{
    /**
     * Daftar bank soal milik guru (per mapel, per kategori).
     */
    public function index(): View
    {
        $teacher = Auth::user()->teacher;

        $subject  = request('subject');
        $category = request('category');

        $questions = Question::where('teacher_id', $teacher->id)
            ->when($subject, fn($q) => $q->where('subject', $subject))
            ->when($category, fn($q) => $q->where('category', $category))
            ->orderByDesc('created_at')
            ->get();

        // Kelompokkan: soal dengan batch_name jadi 1 grup, soal tanpa batch_name (single-add) tampil sendiri-sendiri
        $grouped = $questions->groupBy(fn($q) => $q->batch_name ?? 'single-' . $q->id);

        $subjects   = Question::where('teacher_id', $teacher->id)->distinct()->pluck('subject');
        $categories = Question::where('teacher_id', $teacher->id)->whereNotNull('category')->distinct()->pluck('category');

        return view('guru.quizzes.questions.index', compact('grouped', 'subjects', 'categories', 'subject', 'category'));
    }

    /**
     * Form buat soal baru.
     */
    public function create(): View
    {
        return view('guru.quizzes.questions.create');
    }

    /**
     * Simpan soal baru + opsi jawaban.
     */
    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('questions', 'public')
            : null;

        $answerKeywords = null;
        if ($request->type === 'short_answer' && $request->filled('answer_keywords')) {
            $answerKeywords = collect(explode(',', $request->answer_keywords))
                ->map(fn($k) => trim(strtolower($k)))
                ->filter()
                ->values()
                ->toArray();
        }

        $meta = match ($request->type) {
            'fill_blank' => [
                'blanks' => collect($request->blank_keywords)->map(function ($group) {
                    return collect(explode(',', $group))->map(fn($k) => trim(strtolower($k)))->filter()->values()->toArray();
                })->toArray(),
            ],
            'timer_challenge' => [
                'time_limit_seconds' => (int) $request->time_limit_seconds,
                'bonus_max_percent'  => (int) ($request->bonus_max_percent ?? 50),
            ],
            'drag_drop' => [
                'pairs' => collect($request->pairs_left)->map(function ($left, $i) use ($request) {
                    return ['left' => $left, 'right' => $request->pairs_right[$i] ?? ''];
                })->filter(fn($p) => trim($p['left']) !== '' && trim($p['right']) !== '')->values()->toArray(),
            ],
            'word_search' => [
                'words' => collect(explode(',', $request->word_search_words))
                    ->map(fn($w) => strtoupper(trim($w)))
                    ->filter()
                    ->values()
                    ->toArray(),
            ],
            'crossword' => [
                'entries' => collect($request->crossword_words)->map(function ($w, $i) use ($request) {
                    return ['word' => strtoupper(trim($w)), 'clue' => trim($request->crossword_clues[$i] ?? '')];
                })->filter(fn($e) => $e['word'] !== '')->values()->toArray(),
            ],
            default => null,
        };

        $question = Question::create([
            'teacher_id'      => $teacher->id,
            'subject'         => $request->subject,
            'category'        => $request->category,
            'type'            => $request->type,
            'question_text'   => $request->question_text,
            'image_path'      => $imagePath,
            'points'          => $request->points,
            'answer_keywords' => $answerKeywords,
            'meta'            => $meta,
        ]);

        $this->storeOptions($question, $request);

        return redirect()
            ->route('guru.quizzes.questions.index')
            ->with('success', 'Soal berhasil ditambahkan ke bank soal.');
    }

    /**
     * Form edit soal.
     */
    public function edit(Question $question): View
    {
        $this->authorizeOwner($question);

        $question->load('options');

        return view('guru.quizzes.questions.edit', compact('question'));
    }

    /**
     * Update soal.
     */
    public function update(StoreQuestionRequest $request, Question $question): RedirectResponse
    {
        $this->authorizeOwner($question);

        $imagePath = $question->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $answerKeywords = $question->answer_keywords;
        if ($request->type === 'short_answer') {
            $answerKeywords = $request->filled('answer_keywords')
                ? collect(explode(',', $request->answer_keywords))->map(fn($k) => trim(strtolower($k)))->filter()->values()->toArray()
                : null;
        }

        $meta = match ($request->type) {
            'fill_blank' => [
                'blanks' => collect($request->blank_keywords)->map(function ($group) {
                    return collect(explode(',', $group))->map(fn($k) => trim(strtolower($k)))->filter()->values()->toArray();
                })->toArray(),
            ],
            'timer_challenge' => [
                'time_limit_seconds' => (int) $request->time_limit_seconds,
                'bonus_max_percent'  => (int) ($request->bonus_max_percent ?? 50),
            ],
            'drag_drop' => [
                'pairs' => collect($request->pairs_left)->map(function ($left, $i) use ($request) {
                    return ['left' => $left, 'right' => $request->pairs_right[$i] ?? ''];
                })->filter(fn($p) => trim($p['left']) !== '' && trim($p['right']) !== '')->values()->toArray(),
            ],
            'word_search' => [
                'words' => collect(explode(',', $request->word_search_words))
                    ->map(fn($w) => strtoupper(trim($w)))
                    ->filter()
                    ->values()
                    ->toArray(),
            ],
            'crossword' => [
                'entries' => collect($request->crossword_words)->map(function ($w, $i) use ($request) {
                    return ['word' => strtoupper(trim($w)), 'clue' => trim($request->crossword_clues[$i] ?? '')];
                })->filter(fn($e) => $e['word'] !== '')->values()->toArray(),
            ],
            default => null,
        };
 
        $question->update([
            'subject'         => $request->subject,
            'category'        => $request->category,
            'type'            => $request->type,
            'question_text'   => $request->question_text,
            'image_path'      => $imagePath,
            'points'          => $request->points,
            'answer_keywords' => $answerKeywords,
            'meta'            => $meta,
        ]);

        $question->options()->delete();
        $this->storeOptions($question, $request);

        return redirect()
            ->route('guru.quizzes.questions.index')
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Hapus soal.
     */
    public function destroy(Question $question): RedirectResponse
    {
        $this->authorizeOwner($question);

        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return redirect()
            ->route('guru.quizzes.questions.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    // ── Private helper ────────────────────────────────

    private function storeOptions(Question $question, StoreQuestionRequest $request): void
    {
        if (in_array($request->type, ['multiple_choice', 'timer_challenge'])) {
            foreach ($request->options as $i => $optionText) {
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct'  => (int) $request->correct_option === $i,
                    'order'       => $i,
                ]);
            }
            
        } elseif (in_array($request->type, ['true_false', 'true_false_swipe'])) {
            $question->options()->create(['option_text' => 'Benar', 'is_correct' => $request->true_false_answer === 'true', 'order' => 0]);
            $question->options()->create(['option_text' => 'Salah', 'is_correct' => $request->true_false_answer === 'false', 'order' => 1]);
        } elseif ($request->type === 'sorting') {
            foreach ($request->sorting_items as $i => $text) {
                $question->options()->create([
                    'option_text' => $text,
                    'is_correct'  => true, // tidak relevan untuk sorting, urutan yang menentukan benar/salah
                    'order'       => $i,
                ]);
            }
        }
        // short_answer tidak butuh options, pakai answer_keywords
        
    }


// ... di dalam class QuestionController, tambahkan:

    /**
     * Form buat banyak soal sekaligus (variatif tipe).
     */
    public function createBulk(): View
    {
        return view('guru.quizzes.questions.create-bulk');
    }

    /**
     * Simpan banyak soal sekaligus dalam satu batch.
     */
    public function storeBulk(StoreQuestionsBulkRequest $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        $saved = 0;

        foreach ($request->questions as $q) {
            $answerKeywords = null;
            if ($q['type'] === 'short_answer' && !empty($q['answer_keywords'])) {
                $answerKeywords = collect(explode(',', $q['answer_keywords']))
                    ->map(fn($k) => trim(strtolower($k)))
                    ->filter()
                    ->values()
                    ->toArray();
            }

            $meta = match ($q['type']) {
                'fill_blank' => [
                    'blanks' => collect($q['blank_keywords'] ?? [])->map(function ($group) {
                        return collect(explode(',', $group))->map(fn($k) => trim(strtolower($k)))->filter()->values()->toArray();
                    })->toArray(),
                ],
                'timer_challenge' => [
                    'time_limit_seconds' => (int) ($q['time_limit_seconds'] ?? 15),
                    'bonus_max_percent'  => (int) ($q['bonus_max_percent'] ?? 50),
                ],
                'drag_drop' => [
                    'pairs' => collect($q['pairs_left'] ?? [])->map(function ($left, $i) use ($q) {
                        return ['left' => $left, 'right' => $q['pairs_right'][$i] ?? ''];
                    })->filter(fn($p) => trim($p['left']) !== '' && trim($p['right']) !== '')->values()->toArray(),
                ],
                'word_search' => [
                    'words' => collect(explode(',', $q['word_search_words'] ?? ''))
                        ->map(fn($w) => strtoupper(trim($w)))
                        ->filter()
                        ->values()
                        ->toArray(),
                ],
                'crossword' => [
                    'entries' => collect($q['crossword_words'] ?? [])->map(function ($w, $i) use ($q) {
                        return ['word' => strtoupper(trim($w)), 'clue' => trim($q['crossword_clues'][$i] ?? '')];
                    })->filter(fn($e) => $e['word'] !== '')->values()->toArray(),
                ],
                default => null,
            };

            $question = Question::create([
                'teacher_id'      => $teacher->id,
                'subject'         => $request->subject,
                'category'        => $request->category,
                'batch_name'      => $request->batch_name,
                'type'            => $q['type'],
                'question_text'   => $q['question_text'],
                'points'          => $q['points'],
                'answer_keywords' => $answerKeywords,
                'meta'            => $meta,
            ]);

            if (in_array($q['type'], ['multiple_choice', 'timer_challenge'])) {
                foreach ($q['options'] as $i => $optionText) {
                    if (trim($optionText) === '') continue;
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct'  => (int) $q['correct_option'] === (int) $i,
                        'order'       => $i,
                    ]);
                }
            } elseif (in_array($q['type'], ['true_false', 'true_false_swipe'])) {
                $question->options()->create(['option_text' => 'Benar', 'is_correct' => $q['true_false_answer'] === 'true', 'order' => 0]);
                $question->options()->create(['option_text' => 'Salah', 'is_correct' => $q['true_false_answer'] === 'false', 'order' => 1]);
            } elseif ($q['type'] === 'sorting') {
                foreach ($q['sorting_items'] as $i => $text) {
                    if (trim($text) === '') continue;
                    $question->options()->create([
                        'option_text' => $text,
                        'is_correct'  => true, // tidak relevan untuk sorting, urutan yang menentukan benar/salah
                        'order'       => $i,
                    ]);
                }
            }

            $saved++;
        }

        return redirect()
            ->route('guru.quizzes.questions.index')
            ->with('success', "{$saved} soal berhasil ditambahkan ke bank soal.");
    }

    private function authorizeOwner(Question $question): void
    {
        $teacher = Auth::user()->teacher;
        abort_unless($question->teacher_id === $teacher->id || Auth::user()->isAdmin(), 403);
    }

}
