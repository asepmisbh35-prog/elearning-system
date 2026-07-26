{{-- resources/views/siswa/messages/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Percakapan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8 pb-24 md:pb-8">

    @php
        $other = $conversation->participants->firstWhere('id', '!=', auth()->id());
        $initial = $other->name ? strtoupper(substr($other->name, 0, 1)) : '?';
    @endphp

    {{-- HEADER PERCAKAPAN --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-4 md:p-6 text-white mb-4 md:mb-6">
        <div class="absolute -top-8 -right-8 w-32 h-32 md:w-44 md:h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/4 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>

        <div class="relative">
            <a href="{{ route('siswa.messages.index') }}"
               class="inline-flex items-center gap-1.5 text-xs md:text-sm text-indigo-100 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>

            <div class="flex items-center gap-3 mt-3">
                <div class="w-11 h-11 md:w-12 md:h-12 shrink-0 rounded-full bg-white/15 backdrop-blur flex items-center justify-center font-semibold text-base md:text-lg ring-2 ring-white/30">
                    {{ $initial }}
                </div>
                <h1 class="text-lg md:text-xl font-bold truncate">{{ $other->name ?? '-' }}</h1>
            </div>
        </div>
    </div>

    {{-- BUBBLE CHAT --}}
    <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl shadow-sm shadow-indigo-100/50 p-4 md:p-5 mb-4 space-y-3 max-h-[28rem] overflow-y-auto">
        @forelse ($conversation->messages as $message)
            @php $isMine = $message->sender_id === auth()->id(); @endphp
            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[78%] {{ $isMine
                        ? 'bg-gradient-to-br from-[#4F46E5] to-[#4338CA] text-white'
                        : 'bg-indigo-50 text-gray-800' }} rounded-2xl px-4 py-2.5 shadow-sm">
                    @if ($message->content)
                        <p class="text-sm leading-relaxed">{{ $message->content }}</p>
                    @endif
                    @foreach ($message->attachments ?? [] as $file)
                        <a href="{{ Storage::url($file['path']) }}" target="_blank"
                           class="flex items-center gap-1.5 text-xs mt-2 {{ $isMine ? 'text-indigo-100' : 'text-[#4338CA]' }} underline underline-offset-2">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            {{ $file['original_name'] }}
                        </a>
                    @endforeach
                    <p class="text-[10px] mt-1.5 flex items-center gap-1 {{ $isMine ? 'text-indigo-100' : 'text-gray-400' }}">
                        {{ $message->created_at->format('H:i') }}
                        @if ($isMine && $message->isReadBy($other->id ?? 0))
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7M1 13l4 4L9.5 12.5" />
                            </svg>
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-400">Belum ada pesan.</p>
            </div>
        @endforelse
    </div>

    {{-- FORM KIRIM PESAN --}}
    <form method="POST" action="{{ route('siswa.messages.send', $conversation) }}" enctype="multipart/form-data"
          class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl shadow-sm shadow-indigo-100/50 p-3 md:p-4 flex items-center gap-2">
        @csrf
        <label class="shrink-0 w-9 h-9 md:w-10 md:h-10 rounded-full bg-indigo-50 hover:bg-indigo-100 flex items-center justify-center cursor-pointer transition">
            <svg class="w-4 h-4 md:w-5 md:h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
            <input type="file" name="files[]" multiple class="hidden">
        </label>

        <input type="text" name="content" placeholder="Tulis pesan..."
               class="flex-1 border border-gray-200 rounded-full px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-200 outline-none">

        <button type="submit"
                class="shrink-0 w-9 h-9 md:w-10 md:h-10 rounded-full bg-[#4F46E5] hover:bg-[#4338CA] flex items-center justify-center text-white transition shadow-sm shadow-indigo-300">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9-7-9-7v14zM12 19V5" transform="rotate(90 12 12)" />
            </svg>
        </button>
    </form>
</div>
@endsection