<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SchoolClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'rombel_id',
        'name',
        'subject',
        'code',
        'description',
        'cover_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Generate kode unik format: MTK-2F3G ──────────
    public static function generateCode(string $subject): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $subject), 0, 3));
        if (empty($prefix)) $prefix = 'KLS';

        do {
            $code = $prefix . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    // ── Relasi ────────────────────────────────────────
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            ClassEnrollment::class,
            'school_class_id',
            'id',
            'id',
            'student_id'
        );
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    // ── Helper ────────────────────────────────────────
    public function getStudentCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function materials()
    {
        return $this->hasManyThrough(
            \App\Models\Material::class,
            \App\Models\Meeting::class,
            'school_class_id', // FK di meetings
            'meeting_id',      // FK di materials
        );
    }
    public function gradeComponents()
    {
        return $this->hasMany(GradeComponent::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
