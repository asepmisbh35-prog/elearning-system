{{-- resources/views/guru/messages/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Percakapan')

@section('content')
@php $other = $conversation->participants->firstWhere('id', '!=', auth()->id()); @endphp
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8" x-data="{ sending: false }">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4 rise-in">
        <a href="{{ route('guru.messages.index') }}"
           class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:-translate-y-0.5 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4F46E5] to-[#818CF8] text-white flex items-center justify-center font-semibold text-sm shrink-0">
            {{ strtoupper(substr($other->name ?? '-', 0, 1)) }}
        </div>
        <div class="min-w-0">
            <h1 class="text-base md:text-lg font-bold text-gray-900 truncate">{{ $other->name ?? '-' }}</h1>
            <p class="text-xs text-gray-400">Siswa</p>
        </div>
    </div>

    {{-- Bubble Chat --}}
    <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-4 md:p-5 mb-4 space-y-3 max-h-[28rem] overflow-y-auto rise-in"
         style="animation-delay: 60ms" x-data x-init="$el.scrollTop = $el.scrollHeight">
        @forelse ($conversation->messages as $i => $message)
            @php $isMine = $message->sender_id === auth()->id(); @endphp
            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} rise-in" style="animation-delay: {{ min($i, 10) * 30 }}ms">
                <div class="max-w-[75%] {{ $isMine ? 'bg-gradient-to-br from-[#4F46E5] to-[#4338CA] text-white' : 'bg-indigo-50 text-gray-800' }} rounded-2xl px-4 py-2.5">
                    @if ($message->content)
                        <p class="text-sm">{{ $message->content }}</p>
                    @endif
                    @foreach ($message->attachments ?? [] as $file)
                        <a href="{{ Storage::url($file['path']) }}" target="_blank"
                           class="flex items-center gap-1.5 text-xs underline mt-1.5 {{ $isMine ? 'text-indigo-100' : 'text-[#4F46E5]' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                            </svg>
                            {{ $file['original_name'] }}
                        </a>
                    @endforeach
                    <p class="text-[10px] mt-1 flex items-center gap-1 {{ $isMine ? 'text-indigo-100' : 'text-gray-400' }}">
                        {{ $message->created_at->format('H:i') }}
                        @if ($isMine && $message->isReadBy($other->id ?? 0))
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5M2.25 12.75l6 6" />
                            </svg>
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-400">Belum ada pesan. Mulai percakapan di bawah.</p>
            </div>
        @endforelse
    </div>

    {{-- Form Kirim Pesan --}}
    <form method="POST" action="{{ route('guru.messages.send', $conversation) }}" enctype="multipart/form-data"
          @submit="sending = true"
          class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-3 flex items-center gap-2 rise-in" style="animation-delay: 100ms">
        @csrf

        <label class="w-9 h-9 rounded-full bg-indigo-50 hover:bg-indigo-100 flex items-center justify-center text-[#4F46E5] cursor-pointer transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
            </svg>
            <input type="file" name="files[]" multiple class="hidden">
        </label>

        <input type="text" name="content" placeholder="Tulis pesan..."
               class="flex-1 border-0 bg-transparent focus:outline-none focus:ring-0 text-sm px-1 py-2">

        <button type="submit" :disabled="sending"
                class="w-10 h-10 rounded-full bg-[#4F46E5] hover:bg-[#4338CA] text-white flex items-center justify-center transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-70 disabled:pointer-events-none shrink-0">
            <svg x-show="!sending" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
            <svg x-show="sending" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </button>
    </form>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection