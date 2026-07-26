<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentDeadlineReminder extends Notification
{
    use Queueable;

    public function __construct(public Assignment $assignment, public string $window)
    {
        // $window: 'H-1' atau 'H-3jam'
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $label = $this->window === 'H-1' ? 'besok' : '3 jam lagi';

        return [
            'title'         => 'Pengingat batas waktu tugas',
            'message'       => "\"{$this->assignment->title}\" batas waktunya {$label} ({$this->assignment->due_date->translatedFormat('d M Y H:i')}).",
            'assignment_id' => $this->assignment->id,
        ];
    }
}
