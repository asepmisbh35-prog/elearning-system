<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeComponent extends Model
{
    protected $fillable = ['school_class_id', 'name', 'weight'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
