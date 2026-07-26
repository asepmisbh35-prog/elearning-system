<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignmentGraded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AssignmentSubmission $submission) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $assignment = $this->submission->assignment;

        return [
            'type'    => 'assignment_graded',
            'title'   => 'Nilai tugas keluar: ' . $assignment->title,
            'summary' => 'Nilai kamu: ' . $this->submission->score . ' / ' . $assignment->max_score,
            'url'     => route('siswa.classes.assignments.show', [
                $assignment->school_class_id,
                $assignment->id,
            ]),
            'icon'    => 'check-circle',
        ];
    }



    public function toArray($notifiable): array
    {
        return [
            'title'         => 'Tugas sudah dinilai',
            'message'       => "\"{$this->submission->assignment->title}\" — nilai kamu: {$this->submission->score}/{$this->submission->assignment->max_score}.",
            'assignment_id' => $this->submission->assignment_id,
        ];
    }
}

