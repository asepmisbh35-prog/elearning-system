<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'student_id',
        'attempt_number',
        'started_at',
        'submitted_at',
        'score',
        'status',
        'tab_switch_count',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    // ── Helper ────────────────────────────────────────

    public function isFinished(): bool
    {
        return in_array($this->status, ['submitted', 'auto_submitted']);
    }

    public function deadlineAt()
    {
        return $this->started_at->copy()->addMinutes($this->quiz->duration_minutes);
    }

    public function isExpired(): bool
    {
        return ! $this->isFinished() && now()->greaterThan($this->deadlineAt());
    }

    public function hasPendingManualGrading(): bool
    {
        return $this->answers()->where('needs_manual_grading', true)->whereNull('graded_at')->exists();
    }
}
