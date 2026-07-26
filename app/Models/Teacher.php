<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'nip', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahkan ini:
    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
}
