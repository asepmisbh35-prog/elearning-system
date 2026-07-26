<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAssignmentCreated extends Notification
{
    use Queueable;

    public function __construct(public Assignment $assignment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'         => 'Tugas baru',
            'message'       => "\"{$this->assignment->title}\" — batas waktu {$this->assignment->due_date->translatedFormat('d M Y H:i')}.",
            'assignment_id' => $this->assignment->id,
        ];
    }
}
