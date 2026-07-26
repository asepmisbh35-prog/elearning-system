{{-- resources/views/siswa/assignments/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Tugas — ' . $class->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-24 md:pb-10">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('siswa.classes.show', $class) }}"
               class="inline-flex items-center gap-1.5 text-xs md:text-sm text-indigo-100 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Kelas
            </a>

            <div class="flex items-center gap-3 mt-3">
                <div class="w-10 h-10 md:w-11 md:h-11 shrink-0 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold">Tugas</h1>
                    <p class="text-indigo-100 text-sm">{{ $class->name }}</p>
                </div>
            </div>
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

    {{-- Ringkasan cepat + progress --}}
    @php
        $total = $assignments->count();
        $done = $assignments->filter(fn($a) => $a->my_submission && $a->my_submission->isSubmitted())->count();
        $pending = $total - $done;
        $progressPct = $total > 0 ? round(($done / $total) * 100) : 0;
    @endphp
    @if ($total > 0)
        <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-4 md:p-5 shadow-sm shadow-indigo-100/40">
            <div class="grid grid-cols-3 gap-2.5 md:gap-3 mb-4">
                <div class="text-center">
                    <p class="text-xl md:text-2xl font-extrabold text-gray-800">{{ $total }}</p>
                    <p class="text-[11px] md:text-xs text-gray-400 mt-0.5">Total Tugas</p>
                </div>
                <div class="text-center border-x border-gray-100">
                    <p class="text-xl md:text-2xl font-extrabold text-[#10B981]">{{ $done }}</p>
                    <p class="text-[11px] md:text-xs text-gray-400 mt-0.5">Selesai</p>
                </div>
                <div class="text-center">
                    <p class="text-xl md:text-2xl font-extrabold text-[#F59E0B]">{{ $pending }}</p>
                    <p class="text-[11px] md:text-xs text-gray-400 mt-0.5">Belum</p>
                </div>
            </div>

            {{-- Progress bar keseluruhan --}}
            <div class="flex items-center justify-between text-[11px] md:text-xs text-gray-400 mb-1.5">
                <span>Progres pengumpulan</span>
                <span class="font-semibold text-[#10B981]">{{ $progressPct }}%</span>
            </div>
            <div class="w-full h-2 md:h-2.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-[#10B981] to-[#34D399] transition-all duration-500"
                     style="width: {{ $progressPct }}%"></div>
            </div>
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($assignments as $assignment)
            @php
                $submission = $assignment->my_submission;
                $isPastDue = $assignment->isPastDue();
                $hoursLeft = $isPastDue ? null : now()->diffInHours($assignment->due_date, false);

                // Setiap status: [label, icon-key, bg-hex, text-hex, accent-gradient]
                if ($submission && $submission->revision_status === 'requested') {
                    $badge = ['Perlu revisi', 'refresh', '#FFFBEB', '#B45309'];
                    $accent = 'from-[#F59E0B] to-[#FBBF24]';
                } elseif ($submission && $submission->isGraded()) {
                    $badge = ['Sudah dinilai', 'target', '#EEF2FF', '#4338CA'];
                    $accent = 'from-[#4F46E5] to-[#818CF8]';
                } elseif ($submission && $submission->isSubmitted()) {
                    $badge = $submission->timing_status === 'late'
                        ? ['Terlambat', 'clock', '#FEF2F2', '#DC2626']
                        : ['Tepat waktu', 'check', '#ECFDF5', '#059669'];
                    $accent = $submission->timing_status === 'late' ? 'from-[#EF4444] to-[#F87171]' : 'from-[#10B981] to-[#34D399]';
                } elseif ($isPastDue) {
                    $badge = ['Lewat waktu', 'warning', '#FEF2F2', '#DC2626'];
                    $accent = 'from-[#EF4444] to-[#F87171]';
                } else {
                    $badge = ['Belum dikumpulkan', 'clock', '#F3F4F6', '#4B5563'];
                    $accent = 'from-gray-300 to-gray-400';
                }

                // Chip tenggat waktu di kanan card
                if ($isPastDue) {
                    $dueChipBg = '#FEF2F2'; $dueChipText = '#DC2626'; $dueChipLabel = 'Lewat';
                } elseif ($hoursLeft <= 3) {
                    $dueChipBg = '#FEF2F2'; $dueChipText = '#DC2626'; $dueChipLabel = $hoursLeft . ' jam lagi';
                } elseif ($hoursLeft <= 24) {
                    $dueChipBg = '#FFFBEB'; $dueChipText = '#B45309'; $dueChipLabel = $assignment->due_date->diffForHumans(now(), true) . ' lagi';
                } else {
                    $dueChipBg = '#EEF2FF'; $dueChipText = '#4338CA'; $dueChipLabel = $assignment->due_date->diffForHumans(now(), true) . ' lagi';
                }
            @endphp
            <a href="{{ route('siswa.classes.assignments.show', [$class, $assignment]) }}"
               class="group relative flex items-stretch bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-indigo-100/60 hover:-translate-y-0.5 transition-all">

                {{-- Accent bar --}}
                <div class="w-1.5 bg-gradient-to-b {{ $accent }} shrink-0"></div>

                <div class="flex items-center gap-3 md:gap-4 p-4 md:p-5 flex-1">
                    {{-- Ikon dokumen --}}
                    <div class="w-11 h-11 md:w-12 md:h-12 shrink-0 rounded-xl flex items-center justify-center"
                         style="background-color: {{ $badge[2] }};">
                        <svg class="w-5 h-5 md:w-6 md:h-6" style="color: {{ $badge[3] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-semibold text-gray-900 text-sm md:text-base truncate">{{ $assignment->title }}</h3>
                            <span class="shrink-0 text-[10px] md:text-[11px] font-semibold px-2 py-1 rounded-full whitespace-nowrap"
                                  style="background-color: {{ $dueChipBg }}; color: {{ $dueChipText }};">
                                {{ $dueChipLabel }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap mt-2">
                            <span class="inline-flex items-center gap-1 text-[11px] md:text-xs px-2.5 py-1 rounded-full font-medium"
                                  style="background-color: {{ $badge[2] }}; color: {{ $badge[3] }};">
                                @switch($badge[1])
                                    @case('refresh')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        @break
                                    @case('target')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                                        @break
                                    @case('check')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @break
                                    @case('warning')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                        @break
                                    @default
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endswitch
                                {{ $badge[0] }}
                            </span>
                            @if ($submission && $submission->isGraded())
                                <span class="inline-flex items-center gap-1 text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.9 6.26L21.6 9l-4.8 4.4L18.2 21 12 17.4 5.8 21l1.4-7.6L2.4 9l6.7-.74L12 2z"/></svg>
                                    {{ $submission->score }}/{{ $assignment->max_score }}
                                </span>
                            @endif
                        </div>

                        <p class="text-xs md:text-sm mt-2.5 text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @if ($isPastDue)
                                Batas waktu telah lewat · {{ $assignment->due_date->translatedFormat('d M Y, H:i') }}
                            @else
                                {{ $assignment->due_date->translatedFormat('d M Y, H:i') }}
                            @endif
                        </p>
                    </div>

                    <svg class="w-5 h-5 text-gray-300 group-hover:text-[#4F46E5] shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        @empty
            <div class="text-center py-14 md:py-16 bg-white border border-dashed border-indigo-100 rounded-2xl md:rounded-3xl">
                <div class="w-14 h-14 mx-auto rounded-full bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 7l1.5 12.5A2 2 0 006.49 21h11.02a2 2 0 001.99-1.5L21 7M3 7l2-4h14l2 4M9 11h6"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-500 text-sm md:text-base">Belum ada tugas</p>
                <p class="text-xs md:text-sm mt-1 text-gray-400">Tugas dari guru akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection