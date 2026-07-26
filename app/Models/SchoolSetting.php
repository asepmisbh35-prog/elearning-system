<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolSetting extends Model
{
    protected $fillable = ['school_name', 'logo_path'];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['school_name' => config('app.name', 'E-Learning')]
        );
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }
}