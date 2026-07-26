<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['type', 'created_by', 'school_class_id', 'title'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $userId))
            ->count();
    }

    /**
     * Cari/buat conversation direct antara 2 user.
     */
    public static function findOrCreateDirect(int $userIdA, int $userIdB): self
    {
        $existing = static::where('type', 'direct')
            ->whereHas('participants', fn($q) => $q->where('user_id', $userIdA))
            ->whereHas('participants', fn($q) => $q->where('user_id', $userIdB))
            ->first();

        if ($existing) {
            return $existing;
        }

        $conversation = static::create(['type' => 'direct', 'created_by' => $userIdA]);
        $conversation->participants()->attach([$userIdA, $userIdB]);

        return $conversation;
    }
}
