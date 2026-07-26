{{-- resources/views/guru/assignments/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Tugas — ' . $class->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-10">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <a href="{{ route('guru.classes.show', $class) }}"
                   class="w-9 h-9 md:w-10 md:h-10 shrink-0 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center hover:bg-white/25 transition">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg md:text-xl font-bold">Tugas</h1>
                    <p class="text-indigo-100 text-xs md:text-sm">{{ $class->name }}</p>
                </div>
            </div>

            <a href="{{ route('guru.classes.assignments.create', $class) }}"
               class="inline-flex items-center gap-1.5 bg-white text-[#4338CA] px-4 py-2.5 rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold text-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Tugas
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
        @forelse ($assignments as $assignment)
            @php
                $isPublished = $assignment->status === 'published';
            @endphp
            <div class="group relative flex flex-col sm:flex-row sm:items-start gap-3 md:gap-4 bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-4 md:p-5 shadow-sm shadow-indigo-100/40 hover:shadow-lg hover:shadow-indigo-100/60 transition-all">

                <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                    {{-- Ikon tugas --}}
                    <div class="w-10 h-10 md:w-11 md:h-11 shrink-0 rounded-xl flex items-center justify-center"
                         style="background-color: {{ $isPublished ? '#ECFDF5' : '#FFFBEB' }};">
                        <svg class="w-5 h-5" style="color: {{ $isPublished ? '#059669' : '#92400E' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1.5">
                            <h3 class="font-semibold text-gray-900 text-sm md:text-base">{{ $assignment->title }}</h3>
                            <span class="inline-flex items-center gap-1 text-[11px] md:text-xs px-2.5 py-1 rounded-full font-medium"
                                  style="background-color: {{ $isPublished ? '#ECFDF5' : '#FFFBEB' }}; color: {{ $isPublished ? '#059669' : '#92400E' }};">
                                @if ($isPublished)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.293-6.293a1 1 0 011.414 0l2.586 2.586a1 1 0 010 1.414L13 15l-4 1 1-4z"/></svg>
                                @endif
                                {{ $isPublished ? 'Dipublish' : 'Draft' }}
                            </span>
                            @if ($assignment->target_type === 'specific')
                                <span class="inline-flex items-center gap-1 text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-indigo-50 text-[#4338CA] font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
                                    {{ count($assignment->target_student_ids ?? []) }} siswa dipilih
                                </span>
                            @endif
                        </div>
                        <p class="text-xs md:text-sm text-gray-500 flex items-center gap-1.5 flex-wrap">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $assignment->due_date->translatedFormat('D, d M Y H:i') }}
                            <span class="text-gray-300">&middot;</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.9 6.26L21.6 9l-4.8 4.4L18.2 21 12 17.4 5.8 21l1.4-7.6L2.4 9l6.7-.74L12 2z"/></svg>
                            Nilai maks {{ $assignment->max_score }}
                            <span class="text-gray-300">&middot;</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            {{ $assignment->submissions_count }} pengumpulan
                        </p>
                    </div>
                </div>

                {{-- Aksi: ikon + label jelas, bukan ikon polos --}}
                <div class="flex items-center gap-2 shrink-0 pl-13 sm:pl-0 -mt-1 sm:mt-0">
                    <a href="{{ route('guru.classes.assignments.grade.show', [$class, $assignment]) }}"
                       class="inline-flex items-center gap-1.5 text-xs md:text-sm font-medium text-[#4338CA] bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm8 0v-4a2 2 0 012-2h0a2 2 0 012 2v4a2 2 0 01-2 2h0a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="hidden sm:inline">Nilai</span>
                    </a>
                    <a href="{{ route('guru.classes.assignments.edit', [$class, $assignment]) }}"
                       class="inline-flex items-center gap-1.5 text-xs md:text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.293-6.293a1 1 0 011.414 0l2.586 2.586a1 1 0 010 1.414L13 15l-4 1 1-4z"/>
                        </svg>
                        <span class="hidden sm:inline">Edit</span>
                    </a>
                    <form method="POST" action="{{ route('guru.classes.assignments.destroy', [$class, $assignment]) }}"
                          onsubmit="return confirm('Hapus tugas ini?')">
                        @csrf @method('DELETE')
                        <button class="inline-flex items-center gap-1.5 text-xs md:text-sm font-medium text-[#DC2626] bg-[#FEF2F2] hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-14 md:py-16 bg-white border border-dashed border-indigo-100 rounded-2xl md:rounded-3xl">
                <div class="w-14 h-14 mx-auto rounded-full bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-500 text-sm md:text-base">Belum ada tugas</p>
                <p class="text-xs md:text-sm mt-1 text-gray-400">Klik "Buat Tugas" untuk membuat tugas pertama.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection