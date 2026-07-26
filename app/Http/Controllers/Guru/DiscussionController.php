<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request, Material $material): RedirectResponse
    {
        $request->validate([
            'content'    => 'required|string|max:2000',
            'parent_id'  => 'nullable|exists:discussions,id',
        ]);

        $schoolClass = $material->meeting->schoolClass;
        $this->authorizeTeacher($schoolClass);

        abort_if(! $schoolClass->is_active, 403, 'Forum diskusi terkunci karena kelas sudah diarsipkan.');

        Discussion::create([
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
            'parent_id'   => $request->parent_id,
            'content'     => $request->content,
        ]);

        return back()->with('success', 'Balasan terkirim.');
    }

    public function destroy(Discussion $discussion): RedirectResponse
    {
        $schoolClass = $discussion->material->meeting->schoolClass;
        $this->authorizeTeacher($schoolClass);

        abort_if(! $schoolClass->is_active, 403, 'Forum diskusi terkunci karena kelas sudah diarsipkan.');

        $discussion->delete();

        return back()->with('success', 'Komentar dihapus.');
    }

    private function authorizeTeacher($schoolClass): void
    {
        abort_unless($schoolClass->teacher->user_id === Auth::id(), 403);
    }
}
