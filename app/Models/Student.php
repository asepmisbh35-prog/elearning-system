<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nisn',
        'rombel_id',      // ← ganti dari 'rombel' string
        'has_registered',
    ];

    protected function casts(): array
    {
        return [
            'has_registered' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);  // ← relasi yang hilang
    }
    
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function studySessions()
    {
        return $this->hasMany(StudySession::class);
    }

    public function studentBadges()
    {
        return $this->hasMany(StudentBadge::class);
    }
}
