<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_class_id',
        'grade_component_id',
        'title',
        'instructions',
        'duration_minutes',
        'access_start_at',
        'access_end_at',
        'max_attempts',
        'score_method',
        'display_mode',
        'shuffle_questions',
        'shuffle_options',
        'show_score',
        'show_review',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'access_start_at'    => 'datetime',
            'access_end_at'      => 'datetime',
            'shuffle_questions'  => 'boolean',
            'shuffle_options'    => 'boolean',
            'show_review'        => 'boolean',
            'score_released_at'  => 'datetime',
        ];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function gradeComponent()
    {
        return $this->belongsTo(GradeComponent::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
            ->withPivot(['order', 'points_override'])
            ->withTimestamps()
            ->orderBy('quiz_questions.order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }



    // ── Helper ────────────────────────────────────────

    public function isScoreReleased(): bool
    {
        return $this->show_score === 'immediately' || ! is_null($this->score_released_at);
    }

    public function needsManualRelease(): bool
    {
        return $this->show_score === 'held';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isOpen(): bool
    {
        $now = now();
        return $this->isPublished()
            && $now->greaterThanOrEqualTo($this->access_start_at)
            && $now->lessThanOrEqualTo($this->access_end_at);
    }

    public function timeStatus(): string
    {
        $now = now();
        if ($now->lessThan($this->access_start_at)) return 'upcoming';
        if ($now->greaterThan($this->access_end_at)) return 'finished';
        return 'ongoing';
    }

    public function attemptsFor(Student $student)
    {
        return $this->attempts()->where('student_id', $student->id)->orderByDesc('attempt_number');
    }

    public function remainingAttemptsFor(Student $student): ?int
    {
        if (is_null($this->max_attempts)) {
            return null; // unlimited
        }

        $used = $this->attemptsFor($student)->count();
        return max(0, $this->max_attempts - $used);
    }

    public function canAttempt(Student $student): bool
    {
        return is_null($this->max_attempts) || $this->remainingAttemptsFor($student) > 0;
    }

    /**
     * Hitung nilai final sesuai score_method (best/last/average).
     */
    public function finalScoreFor(Student $student): ?int
    {
        $scores = $this->attemptsFor($student)
            ->where('status', '!=', 'in_progress')
            ->pluck('score')
            ->filter(fn($s) => ! is_null($s));

        if ($scores->isEmpty()) {
            return null;
        }

        return match ($this->score_method) {
            'best'    => (int) $scores->max(),
            'last'    => (int) $scores->first(), // sudah orderByDesc attempt_number
            'average' => (int) round($scores->avg()),
            default   => (int) $scores->max(),
        };
    }
}
