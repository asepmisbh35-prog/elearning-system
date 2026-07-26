<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Notifications\NewAssignmentCreated;
use App\Models\ClassEnrollment;
use Illuminate\Support\Facades\Notification;

class AssignmentController extends Controller
{
    /**
     * Daftar tugas dalam satu kelas.
     */
    public function index(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $assignments = $class->assignments()
            ->withCount('submissions')
            ->orderByDesc('due_date')
            ->get();

        return view('guru.assignments.index', compact('class', 'assignments'));
    }

    /**
     * Form buat tugas baru.
     */
    public function create(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $gradeComponents = $class->gradeComponents()->get();
        $students = $class->students()->get();

        return view('guru.assignments.create', compact('class', 'gradeComponents', 'students'));
    }

    /**
     * Simpan tugas baru.
     */
    public function store(StoreAssignmentRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $attachments = $this->storeAttachments($request);

        $assignment = $class->assignments()->create([
            ...$request->validated(),
            'target_student_ids' => $request->target_type === 'specific' ? $request->target_student_ids : null,
            'attachments'         => $attachments,
        ]);

        if ($assignment->status === 'published') {
            $targetStudentIds = $assignment->target_type === 'specific'
                ? $assignment->target_student_ids
                : ClassEnrollment::where('school_class_id', $class->id)->pluck('student_id');

            $studentUsers = \App\Models\Student::whereIn('id', $targetStudentIds)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            Notification::send($studentUsers, new NewAssignmentCreated($assignment));
        }

        return redirect()
            ->route('guru.classes.assignments.index', $class)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    /**
     * Form edit tugas.
     */
    public function edit(SchoolClass $class, Assignment $assignment): View
    {
        $this->authorizeTeacher($class);

        $gradeComponents = $class->gradeComponents()->get();
        $students = $class->students()->get();
        $hasSubmissions = $assignment->submissions()->exists();

        return view('guru.assignments.edit', compact('class', 'assignment', 'gradeComponents', 'students', 'hasSubmissions'));
    }

    /**
     * Update tugas.
     * Jika sudah ada yang submit, hanya due_date yang boleh diubah.
     */
    public function update(StoreAssignmentRequest $request, SchoolClass $class, Assignment $assignment): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $hasSubmissions = $assignment->submissions()->exists();

        if ($hasSubmissions) {
            // Hanya boleh ubah batas waktu setelah ada pengumpulan
            $assignment->update([
                'due_date' => $request->due_date,
            ]);

            return redirect()
                ->route('guru.classes.assignments.index', $class)
                ->with('success', 'Batas waktu tugas berhasil diperbarui.');
        }

        $newAttachments = $this->storeAttachments($request);
        $attachments = $newAttachments ?: $assignment->attachments;

        $assignment->update([
            ...$request->validated(),
            'target_student_ids' => $request->target_type === 'specific' ? $request->target_student_ids : null,
            'attachments'         => $attachments,
        ]);

        return redirect()
            ->route('guru.classes.assignments.index', $class)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Hapus tugas.
     */
    public function destroy(SchoolClass $class, Assignment $assignment): RedirectResponse
    {
        $this->authorizeTeacher($class);

        foreach ($assignment->attachments ?? [] as $file) {
            Storage::disk('public')->delete($file['path']);
        }

        $assignment->delete();

        return redirect()
            ->route('guru.classes.assignments.index', $class)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    // ── Private helper ────────────────────────────────

    private function storeAttachments(StoreAssignmentRequest $request): ?array
    {
        if (! $request->hasFile('files')) {
            return null;
        }

        return collect($request->file('files'))->map(function ($file) {
            $path = $file->store('assignments', 'public');
            return [
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ];
        })->toArray();
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
