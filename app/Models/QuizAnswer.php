<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'answer_data',
        'is_correct',
        'points_earned',
        'needs_manual_grading',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'answer_data'           => 'array',
            'is_correct'            => 'boolean',
            'needs_manual_grading'  => 'boolean',
            'graded_at'             => 'datetime',
        ];
    }

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
