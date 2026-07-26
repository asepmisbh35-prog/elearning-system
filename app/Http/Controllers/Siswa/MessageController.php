<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\ClassEnrollment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $conversations = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->where('type', 'direct') // broadcast ditampilkan terpisah, hanya baca
            ->with(['participants', 'lastMessage', 'messages'])
            ->get()
            ->sortByDesc(fn($c) => $c->lastMessage->created_at ?? $c->created_at)
            ->when($request->search, fn($col) => $col->filter(function ($c) use ($request, $user) {
                $other = $c->participants->firstWhere('id', '!=', $user->id);
                return str_contains(strtolower($other->name ?? ''), strtolower($request->search))
                    || $c->messages->contains(fn($m) => str_contains(strtolower($m->content ?? ''), strtolower($request->search)));
            }));

        // Daftar guru dari kelas yang diikuti siswa — kontak untuk mulai chat baru
        $student = $user->student;
        $teacherContacts = ClassEnrollment::where('student_id', $student->id)
            ->with('schoolClass.teacher.user')
            ->get()
            ->map(fn($e) => [
                'teacher_user' => $e->schoolClass->teacher->user,
                'class_name'   => $e->schoolClass->name,
            ])
            ->unique(fn($t) => $t['teacher_user']->id);

        $broadcasts = Conversation::where('type', 'broadcast')
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->with('messages', 'schoolClass')
            ->get();

        return view('siswa.messages.index', compact('conversations', 'teacherContacts', 'broadcasts'));
    }

    public function startDirect(int $teacherUserId): RedirectResponse
    {
        $conversation = Conversation::findOrCreateDirect(Auth::id(), $teacherUserId);

        return redirect()->route('siswa.messages.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $user = Auth::user();
        abort_unless($conversation->participants->contains('id', $user->id), 403);

        $conversation->load('messages.sender', 'participants');

        $conversation->messages->where('sender_id', '!=', $user->id)->each(function ($m) use ($user) {
            MessageRead::firstOrCreate(
                ['message_id' => $m->id, 'user_id' => $user->id],
                ['read_at' => now()]
            );
        });

        return view('siswa.messages.show', compact('conversation'));
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
     * Balas broadcast — otomatis buat/pakai thread direct privat dgn guru pengirim.
     */
    public function replyBroadcast(SendMessageRequest $request, Conversation $broadcast): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($broadcast->type === 'broadcast', 400);
        abort_unless($broadcast->participants->contains('id', $user->id), 403);

        $directConversation = Conversation::findOrCreateDirect($user->id, $broadcast->created_by);

        Message::create([
            'conversation_id' => $directConversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->content,
        ]);

        return redirect()->route('siswa.messages.show', $directConversation)
            ->with('success', 'Balasan terkirim secara privat ke guru.');
    }
}
