<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use SoftDeletes;

    protected $fillable = ['material_id', 'user_id', 'parent_id', 'content'];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Discussion::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Discussion::class, 'parent_id')->oldest();
    }

    /**
     * Cek apakah user ini boleh menghapus komentar ini.
     * Guru pengampu kelas: selalu boleh.
     * Siswa: hanya komentar sendiri, dan kalau ini pertanyaan induk, hanya kalau belum ada balasan.
     */
    public function canBeDeletedBy(User $user): bool
    {
        $schoolClass = $this->material->meeting->schoolClass;

        if ($user->teacher && $schoolClass->teacher->user_id === $user->id) {
            return true;
        }

        if ($this->user_id !== $user->id) {
            return false;
        }

        if ($this->parent_id === null && $this->replies()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Forum terkunci (read-only) kalau kelasnya sudah non-aktif/diarsipkan.
     */
    public function isLocked(): bool
    {
        return ! $this->material->meeting->schoolClass->is_active;
    }
}
