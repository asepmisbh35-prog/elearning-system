<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\SchoolClass;
use App\Models\User;
use App\Notifications\AnnouncementPublished;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::orderByDesc('created_at')->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        $classes = SchoolClass::all();
        $users = User::all();

        return view('admin.announcements.create', compact('classes', 'users'));
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('announcements', 'public')
            : null;

        $announcement = Announcement::create([
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

        $targetUsers = match ($announcement->target_type) {
            'all'      => User::all(),
            'role'     => User::where('role', $announcement->target_role)->get(),
            'class'    => User::whereHas(
                'student',
                fn($q) =>
                $q->whereHas('enrollments', fn($q2) => $q2->where('school_class_id', $announcement->school_class_id))
            )->get(),
            'specific' => User::whereIn('id', $announcement->target_user_ids ?? [])->get(),
            default    => collect(),
        };

        Notification::send($targetUsers, new AnnouncementPublished($announcement));

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
