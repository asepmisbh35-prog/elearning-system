<?php

namespace App\Notifications;

use App\Models\QuizAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentCheatingDetected extends Notification
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
            'title'   => 'Indikasi kecurangan terdeteksi',
            'message' => "{$this->attempt->student->user->name} keluar dari halaman kuis \"{$this->attempt->quiz->title}\" 3x, otomatis disubmit.",
            'quiz_id' => $this->attempt->quiz_id,
            'url'     => route('guru.classes.quizzes.results.attempt', [
                $this->attempt->quiz->school_class_id,
                $this->attempt->quiz_id,
                $this->attempt->id,
            ]),
        ];
    }
}
