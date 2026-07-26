<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'grade_component_id',
        'student_id',
        'score',
        'is_manual',
        'note',
        'input_by',
    ];

    protected function casts(): array
    {
        return [
            'score'     => 'decimal:2',
            'is_manual' => 'boolean',
        ];
    }

    public function gradeComponent()
    {
        return $this->belongsTo(GradeComponent::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
