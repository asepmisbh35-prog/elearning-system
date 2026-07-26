<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\Discussion;
use App\Models\Material;
use App\Notifications\NewDiscussionQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request, Material $material): RedirectResponse
    {
        $request->validate([
            'content'   => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:discussions,id',
        ]);

        $schoolClass = $material->meeting->schoolClass;
        $this->authorizeEnrollment($schoolClass);

        abort_if(! $schoolClass->is_active, 403, 'Forum diskusi terkunci karena kelas sudah diarsipkan.');

        $discussion = Discussion::create([
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
            'parent_id'   => $request->parent_id,
            'content'     => $request->content,
        ]);

        // Notifikasi ke guru hanya untuk pertanyaan baru (bukan balasan siswa lain)
        if ($request->parent_id === null) {
            $schoolClass->teacher->user->notify(new NewDiscussionQuestion($discussion));
        }

        return back()->with('success', 'Pertanyaan terkirim.');
    }

    public function destroy(Discussion $discussion): RedirectResponse
    {
        $schoolClass = $discussion->material->meeting->schoolClass;
        $this->authorizeEnrollment($schoolClass);

        abort_if(! $schoolClass->is_active, 403, 'Forum diskusi terkunci karena kelas sudah diarsipkan.');
        abort_unless($discussion->canBeDeletedBy(Auth::user()), 403);

        $discussion->delete();

        return back()->with('success', 'Komentar dihapus.');
    }

    private function authorizeEnrollment($schoolClass): void
    {
        $student = Auth::user()->student;
        abort_unless(
            ClassEnrollment::where('school_class_id', $schoolClass->id)->where('student_id', $student->id)->exists(),
            403
        );
    }
}
