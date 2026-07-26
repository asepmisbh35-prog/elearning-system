<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySession extends Model
{
    protected $fillable = ['student_id', 'occurred_at'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
