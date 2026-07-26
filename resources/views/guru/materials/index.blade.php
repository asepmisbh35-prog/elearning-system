{{-- resources/views/guru/materials/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Materi — ' . $meeting->topic)

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
    [x-cloak] { display: none !important; }
    @keyframes grow-line { from { transform: scaleY(0); } to { transform: scaleY(1); } }
    .grow-line { transform-origin: top; animation: grow-line .6s cubic-bezier(.16,1,.3,1) both; }
</style>

@php
    $palette = [
        ['ring' => '#4F46E5', 'soft' => 'bg-indigo-50', 'text' => 'text-[#4F46E5]', 'shadow' => 'shadow-indigo-200/60'],
        ['ring' => '#7C3AED', 'soft' => 'bg-violet-50', 'text' => 'text-violet-600', 'shadow' => 'shadow-violet-200/60'],
        ['ring' => '#0EA5E9', 'soft' => 'bg-sky-50',    'text' => 'text-sky-600',    'shadow' => 'shadow-sky-200/60'],
        ['ring' => '#3B82F6', 'soft' => 'bg-blue-50',   'text' => 'text-blue-600',   'shadow' => 'shadow-blue-200/60'],
    ];
    $published = $materials->where('status', 'published')->count();
    $totalMinutes = $materials->sum('estimated_minutes');
    $totalReaders = $materials->sum('progresses_count');
@endphp

<div class="max-w-4xl mx-auto px-4 py-6 md:py-8 space-y-5 md:space-y-6"
     x-data="{ confirmDelete: false, deleteForm: null, deleteName: '' }">

    {{-- ============ BREADCRUMB ============ --}}
    <nav class="text-xs sm:text-sm text-gray-500 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('guru.classes.index') }}" class="hover:text-[#4F46E5] transition-colors duration-150">Kelas</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('guru.classes.show', $meeting->schoolClass) }}" class="hover:text-[#4F46E5] transition-colors duration-150">{{ $meeting->schoolClass->name }}</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('guru.classes.meetings.index', $meeting->schoolClass) }}" class="hover:text-[#4F46E5] transition-colors duration-150">Pertemuan</a>
        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 font-medium truncate">Pertemuan {{ $meeting->order }}: {{ $meeting->topic }}</span>
    </nav>

    {{-- ============ HERO ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <p class="text-xs sm:text-sm text-indigo-200 uppercase tracking-wide mb-1">Pertemuan {{ $meeting->order }}</p>
                <h1 class="text-xl md:text-2xl font-bold">{{ $meeting->topic }}</h1>
                <p class="text-sm text-indigo-200 mt-1">Daftar Materi</p>
            </div>
            <a href="{{ route('guru.meetings.materials.create', $meeting) }}"
               class="inline-flex items-center justify-center gap-2 bg-white text-[#4F46E5] px-4 py-2.5 rounded-xl text-sm font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Materi
            </a>
        </div>

        @if ($materials->isNotEmpty())
            <div class="relative flex flex-wrap gap-3 mt-6"
                 x-data="{ p: 0, m: 0, r: 0 }"
                 x-init="
                    let tp={{ $published }}, tm={{ $totalMinutes }}, tr={{ $totalReaders }}, start=null;
                    const step=ts=>{ if(!start)start=ts; let pr=Math.min((ts-start)/700,1);
                        p=Math.floor(pr*tp); m=Math.floor(pr*tm); r=Math.floor(pr*tr);
                        if(pr<1) requestAnimationFrame(step); else { p=tp; m=tm; r=tr; } };
                    requestAnimationFrame(step)">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2.5 border border-white/10">
                    <p class="text-xl font-bold tabular-nums" x-text="p"></p>
                    <p class="text-[11px] text-indigo-200">Dipublish</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2.5 border border-white/10">
                    <p class="text-xl font-bold tabular-nums" x-text="m"></p>
                    <p class="text-[11px] text-indigo-200">Total Menit</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2.5 border border-white/10">
                    <p class="text-xl font-bold tabular-nums" x-text="r"></p>
                    <p class="text-[11px] text-indigo-200">Siswa Mulai Baca</p>
                </div>
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="rise-in bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- ============ TIMELINE MATERI ============ --}}
    @forelse($materials as $i => $material)
        @php $c = $palette[$i % 4]; @endphp
        <div class="rise-in flex gap-3 sm:gap-4" style="animation-delay: {{ $i * 70 }}ms">

            {{-- Kolom step: badge order + garis penghubung --}}
            <div class="flex flex-col items-center shrink-0 w-9 sm:w-10">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-sm text-white shrink-0 shadow-md"
                     style="background:linear-gradient(135deg,{{ $c['ring'] }},{{ $c['ring'] }}cc)">
                    {{ $material->order }}
                </div>
                @if (! $loop->last)
                    <div class="grow-line w-0.5 flex-1 mt-1 mb-1 rounded-full" style="background:linear-gradient(to bottom,{{ $c['ring'] }}55,{{ $palette[($i+1) % 4]['ring'] }}22); animation-delay: {{ $i * 70 + 150 }}ms; min-height:1.5rem"></div>
                @endif
            </div>

            {{-- Card materi --}}
            <div class="flex-1 min-w-0 bg-white border border-gray-100 rounded-2xl p-4 sm:p-5 mb-1 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl {{ $c['shadow'] }}">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl {{ $c['soft'] }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $material->title }}</h3>

                            @if($material->status === 'published')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Dipublish
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium" style="background:#FEF3C7;color:#B45309">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background:#F59E0B"></span>
                                    Draft
                                </span>
                            @endif

                            @if($material->sequential_unlock)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Sequential
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 sm:gap-4 mt-1.5 text-xs sm:text-sm text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $material->estimated_minutes }} menit
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-8a4 4 0 110 8 4 4 0 010-8zm6 3a4 4 0 10-8 0"/></svg>
                                {{ $material->progresses_count }} siswa mulai baca
                            </span>
                        </div>

                        @if($material->description)
                            <p class="text-xs sm:text-sm text-gray-500 mt-1.5 line-clamp-1">{{ $material->description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="flex items-center justify-end gap-1 mt-3 pt-3 border-t border-gray-50">
                    <a href="{{ route('guru.meetings.materials.show', [$meeting, $material]) }}"
                       class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 px-2.5 py-1.5 rounded-lg transition-colors duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat
                    </a>
                    <a href="{{ route('guru.meetings.materials.edit', [$meeting, $material]) }}"
                       class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-[#4F46E5] hover:text-[#4338CA] hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg transition-colors duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <button type="button"
                            @click="confirmDelete = true; deleteName = '{{ $material->title }}'; deleteForm = 'delete-form-{{ $material->id }}'"
                            class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-[#EF4444] hover:text-red-600 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                    <form id="delete-form-{{ $material->id }}" method="POST" action="{{ route('guru.meetings.materials.destroy', [$meeting, $material]) }}" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="rise-in text-center py-16 bg-white border border-dashed border-indigo-200 rounded-2xl">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="font-medium text-gray-600">Belum ada materi</p>
            <p class="text-sm text-gray-400 mt-1">Tambahkan materi pertama untuk pertemuan ini.</p>
        </div>
    @endforelse

    {{-- ============ MODAL KONFIRMASI HAPUS ============ --}}
    <div x-show="confirmDelete" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmDelete = false"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hapus Materi?</h3>
            <p class="text-sm text-gray-500 mb-5">Materi <span class="font-medium text-gray-700" x-text="deleteName"></span> beserta progres baca siswa terkait akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button type="button" @click="confirmDelete = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all duration-200">Batal</button>
                <button type="button"
                        @click="document.getElementById(deleteForm).submit()"
                        class="flex-1 px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-all duration-200">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection