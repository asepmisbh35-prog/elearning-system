<?php

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuizStartingSoon extends Notification
{
    use Queueable;

    public function __construct(public Quiz $quiz) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Kuis akan segera dibuka',
            'message' => "\"{$this->quiz->title}\" akan dibuka 1 jam lagi ({$this->quiz->access_start_at->translatedFormat('d M Y H:i')}).",
            'quiz_id' => $this->quiz->id,
        ];
    }
}
