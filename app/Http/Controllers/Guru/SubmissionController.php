<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeSubmissionRequest;
use App\Jobs\ZipAssignmentSubmissions;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Notifications\AssignmentGraded;

class SubmissionController extends Controller
{
    public function show(SchoolClass $class, Assignment $assignment): View
    {
        $this->authorizeTeacher($class);

        $students = $class->students()->with(['user'])->get();
        $submissions = $assignment->submissions()->get()->keyBy('student_id');

        $rows = $students->map(function ($student) use ($submissions) {
            return [
                'student'    => $student,
                'submission' => $submissions->get($student->id),
            ];
        });

        $zipReady = cache()->has("assignment_zip_ready_{$assignment->id}");

        return view('guru.assignments.grade', compact('class', 'assignment', 'rows', 'zipReady'));
    }

    public function grade(GradeSubmissionRequest $request, SchoolClass $class, Assignment $assignment, AssignmentSubmission $submission): RedirectResponse
    {
        $this->authorizeTeacher($class);

        if ($submission->isGraded()) {
            $submission->revisions()->create([
                'score'     => $submission->score,
                'feedback'  => $submission->feedback,
                'graded_by' => $submission->graded_by,
            ]);
        }

        $submission->update([
            'score'           => $request->score,
            'feedback'        => $request->feedback,
            'graded_by'       => Auth::id(),
            'graded_at'       => now(),
            'revision_status' => 'none',
        ]);

        return back()->with('success', 'Nilai berhasil disimpan.');
        
        $submission->student->user->notify(new AssignmentGraded($submission));
    }

    public function requestRevision(SchoolClass $class, Assignment $assignment, AssignmentSubmission $submission): RedirectResponse
    {
        $this->authorizeTeacher($class);

        if ($submission->revision_count >= 3) {
            return back()->withErrors(['revision' => 'Siswa ini sudah mencapai batas maksimal 3x revisi.']);
        }

        $submission->update([
            'revision_status'   => 'requested',
            'revision_deadline' => now()->addHours(72),
        ]);

        return back()->with('success', 'Permintaan revisi dikirim ke siswa. Batas waktu 3x24 jam.');
    }

    public function updateRevisionDeadline(Request $request, SchoolClass $class, Assignment $assignment, AssignmentSubmission $submission): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $request->validate([
            'revision_deadline' => ['required', 'date'],
        ]);

        abort_unless($submission->revision_status === 'requested', 400, 'Tidak ada revisi aktif untuk siswa ini.');

        $submission->update([
            'revision_deadline' => $request->revision_deadline,
        ]);

        return back()->with('success', 'Batas waktu revisi berhasil diperbarui.');
    }

    public function generateZip(SchoolClass $class, Assignment $assignment): RedirectResponse
    {
        $this->authorizeTeacher($class);

        cache()->forget("assignment_zip_ready_{$assignment->id}");
        ZipAssignmentSubmissions::dispatch($assignment, Auth::id());

        return back()->with('success', 'Sedang menyiapkan file ZIP di background. Kamu akan mendapat notifikasi saat selesai.');
    }

    public function downloadZip(SchoolClass $class, Assignment $assignment): Response|RedirectResponse
    {
        $this->authorizeTeacher($class);

        $path = cache()->get("assignment_zip_ready_{$assignment->id}");

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return back()->withErrors(['zip' => 'File ZIP belum siap atau sudah kedaluwarsa. Klik "Generate ZIP" lagi.']);
        }

        return response()->download(Storage::disk('public')->path($path), "tugas-{$assignment->id}-submissions.zip");
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
