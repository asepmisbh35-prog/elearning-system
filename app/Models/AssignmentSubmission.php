<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'text_answer',
        'attachments',
        'timing_status',
        'submitted_at',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'revision_count',
        'revision_status',
        'revision_deadline',
    ];

    protected function casts(): array
    {
        return [
            'attachments'        => 'array',
            'submitted_at'       => 'datetime',
            'graded_at'          => 'datetime',
            'revision_deadline'  => 'datetime',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function revisions()
    {
        return $this->hasMany(SubmissionRevision::class);
    }

    // ── Helper ────────────────────────────────────────

    public function isSubmitted(): bool
    {
        return ! is_null($this->submitted_at);
    }

    public function isGraded(): bool
    {
        return ! is_null($this->score);
    }

    public function canRevise(): bool
    {
        return $this->revision_status === 'requested'
            && $this->revision_count < 3
            && $this->revision_deadline
            && now()->lessThan($this->revision_deadline);
    }
}
