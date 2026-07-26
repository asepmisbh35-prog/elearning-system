{{-- resources/views/guru/announcements/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pengumuman')

@section('content')
<div class="max-w-3xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-10">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 md:w-11 md:h-11 shrink-0 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold">Pengumuman</h1>
                    <p class="text-indigo-100 text-xs md:text-sm">Kelola informasi untuk siswa dan kelas kamu</p>
                </div>
            </div>

            <a href="{{ route('guru.announcements.create') }}"
               class="inline-flex items-center gap-1.5 bg-white text-[#4338CA] px-4 py-2.5 rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold text-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pengumuman
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 bg-[#ECFDF5] border border-[#A7F3D0] text-[#059669] text-sm rounded-xl md:rounded-2xl px-4 py-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($announcements as $a)
            @php
                $targetLabel = match($a->target_type) {
                    'all' => 'Semua Pengguna',
                    'class' => 'Kelas: ' . ($a->schoolClass->name ?? '-'),
                    'role' => 'Role: ' . ucfirst($a->target_role),
                    'specific' => count($a->target_user_ids ?? []) . ' pengguna spesifik',
                };
            @endphp
            <div class="group relative flex items-start gap-3 md:gap-4 bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-4 md:p-5 shadow-sm shadow-indigo-100/40 hover:shadow-lg hover:shadow-indigo-100/60 transition-all">
                {{-- Ikon pengumuman --}}
                <div class="w-10 h-10 md:w-11 md:h-11 shrink-0 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                        <h3 class="font-semibold text-gray-900 text-sm md:text-base">{{ $a->title }}</h3>
                        <span class="text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-indigo-50 text-[#4338CA] font-medium">
                            {{ $targetLabel }}
                        </span>
                        @if (! $a->isPublished())
                            <span class="inline-flex items-center gap-1 text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-[#FFFBEB] text-[#92400E] font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Belum Tayang / Kedaluwarsa
                            </span>
                        @endif
                    </div>
                    <p class="text-xs md:text-sm text-gray-600 line-clamp-2">{{ Str::limit(strip_tags($a->content), 150) }}</p>
                    <p class="text-[11px] md:text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $a->created_at->translatedFormat('d M Y H:i') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('guru.announcements.destroy', $a) }}" onsubmit="return confirm('Hapus pengumuman ini?')" class="shrink-0">
                    @csrf @method('DELETE')
                    <button class="w-8 h-8 md:w-9 md:h-9 rounded-full flex items-center justify-center text-gray-300 hover:text-[#EF4444] hover:bg-[#FEF2F2] transition" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        @empty
            <div class="text-center py-14 md:py-16 bg-white border border-dashed border-indigo-100 rounded-2xl md:rounded-3xl">
                <div class="w-14 h-14 mx-auto rounded-full bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-500 text-sm md:text-base">Belum ada pengumuman</p>
                <p class="text-xs md:text-sm mt-1 text-gray-400">Buat pengumuman pertamamu untuk siswa.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection