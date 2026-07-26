<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KkmSetting extends Model
{
    protected $fillable = [
        'school_class_id',
        'value',
        'set_by',
        'effective_from',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'datetime',
        ];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function setter()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    // ── Helper statis ────────────────────────────────

    public static function activeFor(SchoolClass $class, $atTime = null): int
    {
        $atTime = $atTime ?? now();

        $classOverride = static::where('school_class_id', $class->id)
            ->where('effective_from', '<=', $atTime)
            ->orderByDesc('effective_from')
            ->first();

        if ($classOverride) {
            return $classOverride->value;
        }

        $schoolDefault = static::whereNull('school_class_id')
            ->where('effective_from', '<=', $atTime)
            ->orderByDesc('effective_from')
            ->first();

        return $schoolDefault?->value ?? 70;
    }

    public static function predikat(int $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default      => 'E',
        };
    }

    public static function predikatLabel(int $score): string
    {
        return match (true) {
            $score >= 90 => 'Sangat Baik',
            $score >= 80 => 'Baik',
            $score >= 70 => 'Cukup',
            $score >= 60 => 'Perlu Bimbingan',
            default      => 'Belum Tuntas',
        };
    }
}