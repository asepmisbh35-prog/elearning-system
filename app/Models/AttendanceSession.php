<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'code',
        'opened_by',
        'check_in_start',
        'check_in_end',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'check_in_start' => 'datetime',
        'check_in_end' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->where('status', 'open')->exists());

        return $code;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && now()->lessThanOrEqualTo($this->check_in_end);
    }
}
