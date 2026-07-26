<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_class_id',
        'order',
        'topic',
        'description',
        'scheduled_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'order'        => 'integer',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    // ── Helper ────────────────────────────────────────
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    // app/Models/Meeting.php — tambahkan
    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
