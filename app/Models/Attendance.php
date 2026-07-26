<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'checked_in_at',
        'keterangan',
        'document_path',
        'approval_status',
        'approved_by',
        'approved_at',
        'status_log',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'approved_at' => 'datetime',
        'status_log' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function logStatusChange(string $from, string $to, int $changedBy): void
    {
        $log = $this->status_log ?? [];
        $log[] = [
            'from' => $from,
            'to' => $to,
            'changed_by' => $changedBy,
            'changed_at' => now()->toDateTimeString(),
        ];
        $this->status_log = $log;
    }

    public static function percentageFor(int $studentId, ?int $schoolClassId = null): float
    {
        $query = self::where('student_id', $studentId);

        if ($schoolClassId) {
            $query->whereHas('session.meeting', fn($q) => $q->where('school_class_id', $schoolClassId));
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            return 0;
        }

        $hadir = (clone $query)->where('status', 'hadir')->count();

        return round(($hadir / $total) * 100, 1);
    }
}
