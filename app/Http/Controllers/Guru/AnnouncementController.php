<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::where('created_by', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        $classes = Auth::user()->teacher->schoolClasses ?? collect();

        return view('guru.announcements.index', compact('announcements', 'classes'));
    }

    public function create(): View
    {
        $classes = Auth::user()->teacher->schoolClasses ?? collect();

        return view('guru.announcements.create', compact('classes'));
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('announcements', 'public')
            : null;

        Announcement::create([
            'created_by'       => Auth::id(),
            'title'            => $request->title,
            'content'          => $request->content,
            'image_path'       => $imagePath,
            'target_type'      => $request->target_type,
            'school_class_id'  => $request->target_type === 'class' ? $request->school_class_id : null,
            'target_role'      => $request->target_type === 'role' ? $request->target_role : null,
            'target_user_ids'  => $request->target_type === 'specific' ? $request->target_user_ids : null,
            'publish_at'       => $request->publish_at,
            'expires_at'       => $request->expires_at,
        ]);

        return redirect()
            ->route('guru.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        abort_unless($announcement->created_by === Auth::id(), 403);

        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
