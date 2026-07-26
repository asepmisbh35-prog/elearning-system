<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = ['key', 'label', 'icon', 'color_key'];

    public function studentBadges()
    {
        return $this->hasMany(StudentBadge::class);
    }
}
