{{-- resources/views/siswa/messages/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pesan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8 pb-24 md:pb-8">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-5 md:mb-6">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <h1 class="text-xl md:text-2xl font-bold">Pesan</h1>
            <p class="text-indigo-100 text-sm md:text-base mt-1">Terhubung dengan guru dan pantau siaran kelasmu</p>

            {{-- Search bar mengambang di dalam hero --}}
            <form method="GET" class="relative mt-4 md:mt-5">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama guru atau isi pesan..."
                       class="w-full bg-white text-gray-700 placeholder-gray-400 rounded-xl md:rounded-2xl pl-10 pr-4 py-2.5 md:py-3 text-sm shadow-lg shadow-indigo-900/20 border-0 focus:ring-2 focus:ring-white/70 outline-none">
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 text-[#059669] px-4 py-3 rounded-xl md:rounded-2xl mb-5 md:mb-6 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- DAFTAR PERCAKAPAN --}}
    <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl shadow-sm shadow-indigo-100/50 divide-y divide-gray-50 mb-5 md:mb-6 overflow-hidden">
        @forelse ($conversations as $c)
            @php
                $other = $c->participants->firstWhere('id', '!=', auth()->id());
                $initial = $other->name ? strtoupper(substr($other->name, 0, 1)) : '?';
                $unread = $c->unreadCountFor(auth()->id());
                $palette = ['from-[#4F46E5] to-[#818CF8]', 'from-violet-500 to-violet-300', 'from-sky-500 to-sky-300', 'from-blue-500 to-blue-300'];
                $color = $palette[$loop->index % count($palette)];
            @endphp
            <a href="{{ route('siswa.messages.show', $c) }}"
               class="flex items-center gap-3 px-4 md:px-5 py-3.5 md:py-4 hover:bg-indigo-50/40 active:bg-indigo-50/60 transition group">
                <div class="w-11 h-11 md:w-12 md:h-12 shrink-0 rounded-full bg-gradient-to-br {{ $color }} flex items-center justify-center text-white font-semibold text-sm md:text-base shadow-sm">
                    {{ $initial }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-800 text-sm md:text-base truncate">{{ $other->name ?? '-' }}</p>
                    <p class="text-xs md:text-sm text-gray-500 truncate">{{ $c->lastMessage->content ?? '(lampiran)' }}</p>
                </div>
                @if ($unread > 0)
                    <span class="text-[10px] md:text-xs bg-[#EF4444] text-white font-semibold rounded-full min-w-[20px] h-5 px-1.5 flex items-center justify-center shrink-0">
                        {{ $unread }}
                    </span>
                @else
                    <svg class="w-4 h-4 text-gray-300 shrink-0 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                @endif
            </a>
        @empty
            <div class="px-5 py-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-full bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-400">Belum ada percakapan.</p>
            </div>
        @endforelse
    </div>

    {{-- MULAI PERCAKAPAN BARU --}}
    <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl shadow-sm shadow-indigo-100/50 p-4 md:p-5 mb-5 md:mb-6">
        <h2 class="font-semibold text-gray-800 text-sm md:text-base mb-3 flex items-center gap-2">
            <span class="w-1.5 h-4 rounded-full bg-[#4F46E5]"></span>
            Mulai Percakapan Baru
        </h2>
        <div class="flex flex-wrap gap-2">
            @forelse ($teacherContacts as $t)
                <form method="POST" action="{{ route('siswa.messages.start', $t['teacher_user']->id) }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 text-xs md:text-sm bg-indigo-50 hover:bg-indigo-100 text-[#4338CA] font-medium px-3 py-2 rounded-full transition">
                        <span class="w-5 h-5 rounded-full bg-gradient-to-br from-[#4F46E5] to-[#818CF8] text-white text-[10px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($t['teacher_user']->name ?? '?', 0, 1)) }}
                        </span>
                        {{ $t['teacher_user']->name ?? '-' }}
                        <span class="text-indigo-300">·</span>
                        <span class="text-indigo-400">{{ $t['class_name'] }}</span>
                    </button>
                </form>
            @empty
                <p class="text-sm text-gray-400">Belum ada guru yang bisa dihubungi.</p>
            @endforelse
        </div>
    </div>

    {{-- SIARAN DARI GURU --}}
    <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl shadow-sm shadow-indigo-100/50 p-4 md:p-5">
        <h2 class="font-semibold text-gray-800 text-sm md:text-base mb-3 flex items-center gap-2">
            <span class="w-1.5 h-4 rounded-full bg-[#4F46E5]"></span>
            Siaran dari Guru
        </h2>
        <div class="space-y-3">
            @forelse ($broadcasts as $b)
                @php $msg = $b->messages->first(); @endphp
                <div class="rounded-xl md:rounded-2xl border border-indigo-50 bg-indigo-50/30 p-3.5 md:p-4 border-l-4 border-l-[#4F46E5]">
                    <p class="text-xs font-medium text-[#4338CA]">{{ $b->title }}
                        <span class="text-gray-400 font-normal">— {{ $b->schoolClass->name ?? '' }}</span>
                    </p>
                    <p class="text-sm text-gray-700 mt-1.5">{{ $msg->content ?? '-' }}</p>
                    <form method="POST" action="{{ route('siswa.messages.broadcast.reply', $b) }}" class="mt-3 flex gap-2">
                        @csrf
                        <input type="text" name="content" placeholder="Balas secara privat ke guru..."
                               class="flex-1 border border-gray-200 bg-white rounded-lg md:rounded-xl px-3 py-1.5 text-xs md:text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
                        <button type="submit"
                                class="text-xs md:text-sm bg-[#4F46E5] hover:bg-[#4338CA] text-white font-medium px-3.5 py-1.5 rounded-lg md:rounded-xl transition">
                            Balas
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada siaran.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection