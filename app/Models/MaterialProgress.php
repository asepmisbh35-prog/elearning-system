<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialProgress extends Model
{
    protected $table = 'material_progress';

    protected $fillable = [
        'student_id',
        'material_id',
        'content_block_id',
        'status',
        'progress_percent',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at'       => 'datetime',
            'completed_at'     => 'datetime',
            'progress_percent' => 'integer',
        ];
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function contentBlock()
    {
        return $this->belongsTo(ContentBlock::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
