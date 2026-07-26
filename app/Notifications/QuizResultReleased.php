<?php

namespace App\Notifications;

use App\Models\QuizAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuizResultReleased extends Notification
{
    use Queueable;

    public function __construct(public QuizAttempt $attempt) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Nilai kuis keluar',
            'message' => "\"{$this->attempt->quiz->title}\" — nilai kamu: {$this->attempt->score}.",
            'quiz_id' => $this->attempt->quiz_id,
            'url'     => route('siswa.classes.quizzes.result', [
                $this->attempt->quiz->school_class_id,
                $this->attempt->quiz_id,
                $this->attempt->id,
            ]),
        ];
    }
}
