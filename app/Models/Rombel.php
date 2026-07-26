<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rombel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'grade',
        'academic_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getFullNameAttribute(): string
    {
        return "Kelas {$this->name} ({$this->academic_year})";
    }
}
