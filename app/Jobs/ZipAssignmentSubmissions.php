<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Models\User;
use App\Notifications\ZipReadyToDownload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ZipAssignmentSubmissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Assignment $assignment, public int $requestedByUserId) {}

    public function handle(): void
    {
        $assignment = $this->assignment->load('submissions.student.user');

        $zipRelativePath = "exports/assignment-{$assignment->id}-submissions.zip";
        $zipFullPath = Storage::disk('public')->path($zipRelativePath);

        Storage::disk('public')->makeDirectory('exports');

        $zip = new ZipArchive();
        $zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($assignment->submissions as $submission) {
            $studentName = $submission->student->user->name ?? "siswa-{$submission->student_id}";
            $folderName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $studentName);

            foreach ($submission->attachments ?? [] as $file) {
                $localPath = Storage::disk('public')->path($file['path']);
                if (file_exists($localPath)) {
                    $zip->addFile($localPath, "{$folderName}/{$file['original_name']}");
                }
            }

            if ($submission->text_answer) {
                $zip->addFromString("{$folderName}/jawaban_teks.txt", $submission->text_answer);
            }
        }

        $zip->close();

        cache()->put("assignment_zip_ready_{$assignment->id}", $zipRelativePath, now()->addHours(6));

        $teacher = User::find($this->requestedByUserId);
        $teacher?->notify(new ZipReadyToDownload($assignment));
    }
}
