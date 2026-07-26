<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnnouncementReadController extends Controller
{
    /**
     * Halaman pengumuman untuk pengguna (guru/siswa) — semua yang visible untuknya.
     */
    public function index(): View
    {
        $user = Auth::user();
        $announcements = Announcement::visibleFor($user)->sortByDesc('created_at');

        return view('announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement): View
    {
        $user = Auth::user();
        abort_unless($announcement->isVisibleFor($user), 403);

        AnnouncementRead::firstOrCreate(
            ['announcement_id' => $announcement->id, 'user_id' => $user->id],
            ['read_at' => now()]
        );

        return view('announcements.show', compact('announcement'));
    }
}
