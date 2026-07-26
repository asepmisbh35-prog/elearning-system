<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\ClassEnrollment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Daftar percakapan guru (thread direct + broadcast yang dia buat).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $conversations = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->with(['participants', 'lastMessage', 'schoolClass'])
            ->get()
            ->sortByDesc(fn($c) => $c->lastMessage->created_at ?? $c->created_at)
            ->when($request->search, fn($col) => $col->filter(function ($c) use ($request, $user) {
                $other = $c->participants->firstWhere('id', '!=', $user->id);
                return str_contains(strtolower($other->name ?? ''), strtolower($request->search))
                    || $c->messages->contains(fn($m) => str_contains(strtolower($m->content ?? ''), strtolower($request->search)));
            }));

        // Daftar siswa dari kelas yang diampu, untuk mulai percakapan baru
        $classes = SchoolClass::whereHas('teacher', fn($q) => $q->where('user_id', $user->id))
            ->with(['enrollments.student.user'])
            ->get();

        return view('guru.messages.index', compact('conversations', 'classes'));
    }

    /**
     * Buka/mulai thread direct dengan 1 siswa.
     */
    public function startDirect(int $studentUserId): RedirectResponse
    {
        $conversation = Conversation::findOrCreateDirect(Auth::id(), $studentUserId);

        return redirect()->route('guru.messages.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $user = Auth::user();
        abort_unless($conversation->participants->contains('id', $user->id), 403);

        $conversation->load('messages.sender', 'participants');

        // Tandai semua pesan dari lawan bicara sebagai dibaca
        $conversation->messages->where('sender_id', '!=', $user->id)->each(function ($m) use ($user) {
            MessageRead::firstOrCreate(
                ['message_id' => $m->id, 'user_id' => $user->id],
                ['read_at' => now()]
            );
        });

        return view('guru.messages.show', compact('conversation'));
    }

    public function send(SendMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($conversation->participants->contains('id', $user->id), 403);

        $attachments = null;
        if ($request->hasFile('files')) {
            $attachments = collect($request->file('files'))->map(function ($file) {
                $path = $file->store('messages', 'public');
                return ['path' => $path, 'original_name' => $file->getClientOriginalName(), 'size' => $file->getSize()];
            })->toArray();
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->content,
            'attachments'     => $attachments,
        ]);

        return back();
    }

    /**
     * Kirim pesan siaran ke semua siswa 1 kelas.
     */
    public function broadcast(SendMessageRequest $request, SchoolClass $class): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($class->teacher->user_id === $user->id, 403);

        $conversation = Conversation::create([
            'type'             => 'broadcast',
            'created_by'       => $user->id,
            'school_class_id'  => $class->id,
            'title'            => $request->title ?? 'Siaran — ' . now()->translatedFormat('d M Y H:i'),
        ]);

        $studentUserIds = ClassEnrollment::where('school_class_id', $class->id)
            ->with('student.user')
            ->get()
            ->pluck('student.user.id')
            ->filter();

        $conversation->participants()->attach($studentUserIds->push($user->id)->unique());

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->content,
        ]);

        return redirect()->route('guru.messages.broadcast.show', $conversation)
            ->with('success', 'Pesan siaran berhasil dikirim ke ' . $studentUserIds->count() . ' siswa.');
    }

    /**
     * Halaman guru lihat status baca broadcast per siswa.
     */
    public function broadcastShow(Conversation $conversation): View
    {
        abort_unless($conversation->type === 'broadcast' && $conversation->created_by === Auth::id(), 403);

        $conversation->load('participants', 'messages');
        $firstMessage = $conversation->messages->first();

        $readStatus = $conversation->participants
            ->where('id', '!=', Auth::id())
            ->map(fn($p) => [
                'user' => $p,
                'read' => $firstMessage?->isReadBy($p->id) ?? false,
            ]);

        return view('guru.messages.broadcast-show', compact('conversation', 'firstMessage', 'readStatus'));
    }
}
