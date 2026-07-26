<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['student_id', 'type', 'subject', 'description', 'occurred_at'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Helper untuk dipanggil dari controller lain, mis:
     * ActivityLog::record($student->id, 'assignment_submitted', $assignment->schoolClass->subject, 'Tugas '.$assignment->title.' dikumpulkan');
     */
    public static function record(int $studentId, string $type, ?string $subject, string $description): self
    {
        return static::create([
            'student_id'  => $studentId,
            'type'        => $type,
            'subject'     => $subject,
            'description' => $description,
            'occurred_at' => now(),
        ]);
    }
}
