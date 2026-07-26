<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\ClassEnrollment;
use App\Models\Quiz;
use App\Models\Student;
use App\Notifications\AssignmentDeadlineReminder;
use App\Notifications\QuizStartingSoon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDeadlineReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Kirim notifikasi H-1, H-3 jam untuk tugas, dan 1 jam sebelum kuis dibuka.';

    public function handle(): void
    {
        $this->sendAssignmentH1();
        $this->sendAssignmentH3Jam();
        $this->sendQuizStartingSoon();
    }

    private function sendAssignmentH1(): void
    {
        $assignments = Assignment::where('status', 'published')
            ->where('reminder_h1_sent', false)
            ->whereBetween('due_date', [now()->addHours(23), now()->addHours(25)])
            ->get();

        foreach ($assignments as $assignment) {
            $this->notifyAssignmentStudents($assignment, 'H-1');
            $assignment->update(['reminder_h1_sent' => true]);
        }

        $this->info("{$assignments->count()} pengingat H-1 tugas terkirim.");
    }

    private function sendAssignmentH3Jam(): void
    {
        $assignments = Assignment::where('status', 'published')
            ->where('reminder_h3jam_sent', false)
            ->whereBetween('due_date', [now()->addHours(2.5), now()->addHours(3.5)])
            ->get();

        foreach ($assignments as $assignment) {
            $this->notifyAssignmentStudents($assignment, 'H-3jam');
            $assignment->update(['reminder_h3jam_sent' => true]);
        }

        $this->info("{$assignments->count()} pengingat H-3 jam tugas terkirim.");
    }

    private function sendQuizStartingSoon(): void
    {
        $quizzes = Quiz::where('status', 'published')
            ->where('reminder_1jam_sent', false)
            ->whereBetween('access_start_at', [now()->addMinutes(55), now()->addMinutes(65)])
            ->get();

        foreach ($quizzes as $quiz) {
            $studentIds = ClassEnrollment::where('school_class_id', $quiz->school_class_id)->pluck('student_id');
            $studentUsers = Student::whereIn('id', $studentIds)->with('user')->get()->pluck('user')->filter();

            Notification::send($studentUsers, new QuizStartingSoon($quiz));
            $quiz->update(['reminder_1jam_sent' => true]);
        }

        $this->info("{$quizzes->count()} pengingat kuis akan dimulai terkirim.");
    }

    private function notifyAssignmentStudents(Assignment $assignment, string $window): void
    {
        $targetStudentIds = $assignment->target_type === 'specific'
            ? $assignment->target_student_ids
            : ClassEnrollment::where('school_class_id', $assignment->school_class_id)->pluck('student_id');

        $studentUsers = Student::whereIn('id', $targetStudentIds)->with('user')->get()->pluck('user')->filter();

        Notification::send($studentUsers, new AssignmentDeadlineReminder($assignment, $window));
    }
}
