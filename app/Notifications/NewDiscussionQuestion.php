<?php

namespace App\Notifications;

use App\Models\Discussion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDiscussionQuestion extends Notification
{
    use Queueable;

    public function __construct(public Discussion $discussion) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Pertanyaan baru di forum diskusi',
            'message' => $this->discussion->user->name . ' bertanya di materi "' . $this->discussion->material->title . '"',
            'url'     => route('guru.meetings.materials.show', [
                $this->discussion->material->meeting_id,
                $this->discussion->material_id,
            ]),
        ];
    }
}
