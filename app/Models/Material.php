<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'meeting_id',
        'title',
        'description',
        'order',
        'estimated_minutes',
        'status',
        'sequential_unlock',
        'unlock_method',
    ];

    protected function casts(): array
    {
        return [
            'sequential_unlock' => 'boolean',
            'order'             => 'integer',
            'estimated_minutes' => 'integer',
        ];
    }

    // ── Relasi ────────────────────────────────────────

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function contentBlocks()
    {
        return $this->hasMany(ContentBlock::class)->orderBy('order');
    }

    public function progresses()
    {
        return $this->hasMany(MaterialProgress::class);
    }

    // ── Helper ────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Cek apakah materi ini terkunci untuk siswa tertentu.
     * Sequential unlock hanya berlaku dalam satu pertemuan yang sama.
     */
    public function isLockedFor(Student $student): bool
    {
        if (! $this->sequential_unlock) {
            return false;
        }

        $previous = $this->meeting->materials()
            ->where('order', '<', $this->order)
            ->where('status', 'published')
            ->orderByDesc('order')
            ->first();

        if (! $previous) {
            return false;
        }

        $progress = MaterialProgress::where('material_id', $previous->id)
            ->where('student_id', $student->id)
            ->first();

        return ! ($progress && $progress->isCompleted());
    }

    /**
     * Progres siswa tertentu (0-100).
     */
    public function progressFor(Student $student): int
    {
        $progress = $this->progresses()->where('student_id', $student->id)->first();
        return $progress?->progress_percent ?? 0;
    }

    public function discussions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function recalculateProgressFor(Student $student): void
    {
        $totalBlocks = $this->contentBlocks()->count();
        if ($totalBlocks === 0) {
            return;
        }

        $completedBlocks = MaterialProgress::where('material_id', $this->id)
            ->where('student_id', $student->id)
            ->whereNotNull('content_block_id')
            ->where('status', 'completed')
            ->count();

        $percentage = (int) round(($completedBlocks / $totalBlocks) * 100);
        $isComplete = $completedBlocks >= $totalBlocks;

        MaterialProgress::updateOrCreate(
            [
                'material_id'      => $this->id,
                'student_id'       => $student->id,
                'content_block_id' => null,
            ],
            [
                'progress_percent' => $percentage,
                'status'           => $isComplete ? 'completed' : 'in_progress',
                'completed_at'     => $isComplete ? now() : null,
            ]
        );
    }
}
