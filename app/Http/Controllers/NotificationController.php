<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Halaman riwayat notifikasi, bisa di-scroll (load more).
     */
    public function index(): View
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai 1 notifikasi sebagai dibaca, lalu redirect (kalau ada link tujuan).
     */
    public function markRead(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($url = $notification->data['url'] ?? null) {
            return redirect($url);
        }

        return back();
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     */
    public function markAllRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Endpoint AJAX untuk badge navbar — jumlah belum dibaca (notifikasi + pengumuman).
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();

        $notifCount = $user->unreadNotifications()->count();

        $announcementCount = \App\Models\Announcement::visibleFor($user)
            ->filter(fn($a) => ! $a->isReadBy($user))
            ->count();

        return response()->json([
            'total'         => $notifCount + $announcementCount,
            'notifications' => $notifCount,
            'announcements' => $announcementCount,
        ]);
    }

    /**
     * List notifikasi + pengumuman terbaru untuk dropdown popup.
     */
    public function recent(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        $notifItems = $user->notifications()->latest()->take(5)->get()->map(function ($n) {
            return [
                'id'         => $n->id,
                'title'      => $n->data['title'] ?? 'Notifikasi',
                'message'    => $n->data['message'] ?? '',
                'time'       => $n->created_at->diffForHumans(),
                'read'       => ! is_null($n->read_at),
                'read_url'   => route('notifications.read', $n->id),
                'target_url' => $n->data['url'] ?? null, // ikut link dari payload notifikasi kalau ada
            ];
        });

        $announcementItems = \App\Models\Announcement::visibleFor($user)
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function ($a) use ($user) {
                return [
                    'id'         => 'ann-' . $a->id,
                    'title'      => '📢 ' . $a->title,
                    'message'    => \Illuminate\Support\Str::limit(strip_tags($a->content), 60),
                    'time'       => $a->created_at->diffForHumans(),
                    'read'       => $a->isReadBy($user),
                    'read_url'   => null,
                    'target_url' => route('announcements.show', $a),
                ];
            })
            ->values();

        $items = $notifItems->concat($announcementItems)->values();

        return response()->json(['items' => $items]);
    }
}
