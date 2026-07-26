<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AnnouncementPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'announcement',
            'title'   => $this->announcement->title,
            'summary' => str(strip_tags($this->announcement->content))->limit(100)->toString(),
            'url'     => route($notifiable->role . '.announcements.index'),
            'icon'    => 'megaphone',
        ];
    }
}
