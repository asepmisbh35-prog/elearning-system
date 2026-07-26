<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'content',
        'image_path',
        'target_type',
        'school_class_id',
        'target_role',
        'target_user_ids',
        'publish_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'target_user_ids' => 'array',
            'publish_at'      => 'datetime',
            'expires_at'      => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    // ── Helper ────────────────────────────────────────

    public function isPublished(): bool
    {
        $now = now();
        if ($this->publish_at && $now->lessThan($this->publish_at)) return false;
        if ($this->expires_at && $now->greaterThan($this->expires_at)) return false;
        return true;
    }

    public function isVisibleFor(User $user): bool
    {
        return match ($this->target_type) {
            'all'      => true,
            'role'     => $this->target_role === $user->role,
            'class'    => $this->checkClassAccess($user),
            'specific' => in_array($user->id, $this->target_user_ids ?? []),
            default    => false,
        };
    }

    private function checkClassAccess(User $user): bool
    {
        if (! $this->school_class_id) {
            return false;
        }

        if ($user->role === 'guru') {
            return SchoolClass::where('id', $this->school_class_id)
                ->whereHas('teacher', fn($q) => $q->where('user_id', $user->id))
                ->exists();
        }

        if ($user->role === 'siswa' && $user->student) {
            return ClassEnrollment::where('school_class_id', $this->school_class_id)
                ->where('student_id', $user->student->id)
                ->exists();
        }

        return false;
    }

    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }

    /**
     * Query scope: pengumuman yang published DAN visible untuk user tertentu.
     */
    public static function visibleFor(User $user)
    {
        $now = now();

        return static::where(function ($q) use ($now) {
            $q->whereNull('publish_at')->orWhere('publish_at', '<=', $now);
        })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->get()
            ->filter(fn($a) => $a->isVisibleFor($user));
    }
}
