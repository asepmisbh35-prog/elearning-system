<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_class_id',
        'grade_component_id',
        'title',
        'instructions',
        'due_date',
        'max_score',
        'status',
        'target_type',
        'target_student_ids',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'due_date'            => 'datetime',
            'target_student_ids'  => 'array',
            'attachments'         => 'array',
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

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // ── Helper ────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isPastDue(): bool
    {
        return now()->greaterThan($this->due_date);
    }

    /**
     * Cek apakah tugas ini terlihat untuk siswa tertentu.
     * Kalau target_type = specific, siswa yang tidak dipilih tidak melihat sama sekali.
     */
    public function isVisibleFor(Student $student): bool
    {
        if ($this->target_type === 'all') {
            return true;
        }

        return in_array($student->id, $this->target_student_ids ?? []);
    }

    public function submissionFor(Student $student): ?AssignmentSubmission
    {
        return $this->submissions()->where('student_id', $student->id)->first();
    }
}
