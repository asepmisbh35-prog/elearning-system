<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'subject',
        'category',
        'batch_name',
        'type',
        'question_text',
        'image_path',
        'points',
        'answer_keywords',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'answer_keywords' => 'array',
            'meta'            => 'array',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_questions')
            ->withPivot(['order', 'points_override'])
            ->withTimestamps();
    }

    // ── Helper ────────────────────────────────────────

    public function isAutoGraded(): bool
    {
        if ($this->type === 'short_answer') {
            return ! empty($this->answer_keywords);
        }

        if ($this->type === 'fill_blank') {
            $blanks = $this->meta['blanks'] ?? [];
            return count($blanks) > 0 && collect($blanks)->every(fn($kw) => ! empty($kw));
        }

        return true;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'multiple_choice'  => 'Pilihan Ganda',
            'true_false'       => 'Benar/Salah',
            'short_answer'     => 'Isian Singkat',
            'drag_drop'        => 'Drag & Drop',
            'word_search'      => 'Word Search',
            'crossword'        => 'Teka-teki Silang',
            'fill_blank'       => 'Fill in the Blank',
            'sorting'          => 'Sorting/Urutan',
            'timer_challenge'  => 'Timer Challenge',
            'true_false_swipe' => 'True/False Swipe',
            default            => ucfirst($this->type),
        };
    }
}
