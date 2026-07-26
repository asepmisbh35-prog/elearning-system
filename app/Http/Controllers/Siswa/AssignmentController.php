<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitAssignmentRequest;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    /**
     * Daftar tugas untuk siswa di satu kelas (yang published & visible saja).
     */
    public function index(SchoolClass $class): View
    {
        $student = $this->authorizeStudent($class);

        $assignments = $class->assignments()
            ->where('status', 'published')
            ->orderBy('due_date')
            ->get()
            ->filter(fn(Assignment $a) => $a->isVisibleFor($student))
            ->map(function (Assignment $a) use ($student) {
                $a->my_submission = $a->submissionFor($student);
                return $a;
            });

        return view('siswa.assignments.index', compact('class', 'assignments'));
    }

    /**
     * Detail tugas + form pengumpulan.
     */
    public function show(SchoolClass $class, Assignment $assignment): View
    {
        $student = $this->authorizeStudent($class);
        $this->authorizeVisible($assignment, $student);

        $submission = $assignment->submissionFor($student);

        return view('siswa.assignments.show', compact('class', 'assignment', 'submission'));
    }

    /**
     * Simpan / update pengumpulan tugas (satu form untuk create & edit).
     */
    public function submit(SubmitAssignmentRequest $request, SchoolClass $class, Assignment $assignment): RedirectResponse
    {
        $student = $this->authorizeStudent($class);
        $this->authorizeVisible($assignment, $student);

        $existing = $assignment->submissionFor($student);

        // Kalau sudah pernah submit tepat waktu/terlambat dan sekarang lewat deadline dan
        // BUKAN sedang dalam masa revisi yang diminta guru, tetap izinkan (spec: submit setelah
        // deadline tetap boleh, hanya ditandai terlambat).
        $timingStatus = now()->greaterThan($assignment->due_date) ? 'late' : 'on_time';

        $attachments = $this->storeAttachments($request, $existing);

        $data = [
            'text_answer'   => $request->text_answer,
            'attachments'   => $attachments,
            'timing_status' => $timingStatus,
            'submitted_at'  => now(),
        ];

        // Kalau ini pengumpulan ulang setelah revisi diminta, reset status revisi
        if ($existing && $existing->revision_status === 'requested') {
            $data['revision_status'] = 'revised';
        }

        $assignment->submissions()->updateOrCreate(
            ['student_id' => $student->id],
            $data
        );

        return redirect()
            ->route('siswa.classes.assignments.show', [$class, $assignment])
            ->with('success', $timingStatus === 'late'
                ? 'Tugas berhasil dikumpulkan (Terlambat).'
                : 'Tugas berhasil dikumpulkan.');
    }

    // ── Private helper ────────────────────────────────

    private function storeAttachments(SubmitAssignmentRequest $request, $existing): ?array
    {
        if (! $request->hasFile('files')) {
            // Tidak upload file baru → pertahankan file lama kalau ada
            return $existing->attachments ?? null;
        }

        // Upload baru menggantikan semua lampiran lama
        foreach ($existing->attachments ?? [] as $old) {
            Storage::disk('public')->delete($old['path']);
        }

        return collect($request->file('files'))->map(function ($file) {
            $path = $file->store('submissions', 'public');
            return [
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ];
        })->toArray();
    }

    private function authorizeStudent(SchoolClass $class)
    {
        $student = Auth::user()->student;

        abort_unless(
            $student && $class->students()->where('students.id', $student->id)->exists(),
            403
        );

        return $student;
    }

    private function authorizeVisible(Assignment $assignment, $student): void
    {
        abort_unless(
            $assignment->status === 'published' && $assignment->isVisibleFor($student),
            404
        );
    }
}
