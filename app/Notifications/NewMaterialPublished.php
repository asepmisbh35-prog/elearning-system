<?php

namespace App\Notifications;

use App\Models\Material;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMaterialPublished extends Notification
{
    use Queueable;

    public function __construct(public Material $material) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'       => 'Materi baru tersedia',
            'message'     => "\"{$this->material->title}\" sudah bisa dipelajari.",
            'material_id' => $this->material->id,
        ];
    }
}
