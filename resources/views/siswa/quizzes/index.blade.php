{{-- resources/views/siswa/quizzes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Kuis — ' . $schoolClass->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-24 md:pb-10">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('siswa.classes.show', $schoolClass) }}"
               class="inline-flex items-center gap-1.5 text-xs md:text-sm text-indigo-100 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Kelas
            </a>

            <div class="flex items-center gap-3 mt-3">
                <div class="w-10 h-10 md:w-11 md:h-11 shrink-0 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold">Kuis</h1>
                    <p class="text-indigo-100 text-sm">{{ $schoolClass->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        @forelse ($quizzes as $quiz)
            @php
                $status = $quiz->timeStatus();
                $usedUp = ! is_null($quiz->max_attempts) && $quiz->my_attempts_used >= $quiz->max_attempts;

                // Warna & label status: [bg-hex, text-hex, accent-gradient]
                if ($status === 'upcoming') {
                    $stateBg = '#EEF2FF'; $stateText = '#4338CA'; $accent = 'from-[#4F46E5] to-[#818CF8]';
                } elseif ($status === 'finished') {
                    $stateBg = '#F3F4F6'; $stateText = '#4B5563'; $accent = 'from-gray-300 to-gray-400';
                } elseif ($usedUp) {
                    $stateBg = '#FEF2F2'; $stateText = '#DC2626'; $accent = 'from-[#EF4444] to-[#F87171]';
                } else {
                    $stateBg = '#ECFDF5'; $stateText = '#059669'; $accent = 'from-[#10B981] to-[#34D399]';
                }
            @endphp
            <div class="group relative flex items-stretch bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-indigo-100/60 transition-all">

                {{-- Accent bar --}}
                <div class="w-1.5 bg-gradient-to-b {{ $accent }} shrink-0"></div>

                <div class="flex items-start gap-3 md:gap-4 p-4 md:p-5 flex-1">
                    {{-- Ikon kuis --}}
                    <div class="w-11 h-11 md:w-12 md:h-12 shrink-0 rounded-xl flex items-center justify-center"
                         style="background-color: {{ $stateBg }};">
                        <svg class="w-5 h-5 md:w-6 md:h-6" style="color: {{ $stateText }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-semibold text-gray-900 text-sm md:text-base truncate">{{ $quiz->title }}</h3>
                            @if (! is_null($quiz->my_final_score))
                                <span class="shrink-0 inline-flex items-center gap-1 text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-[#ECFDF5] text-[#059669] font-semibold whitespace-nowrap">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.9 6.26L21.6 9l-4.8 4.4L18.2 21 12 17.4 5.8 21l1.4-7.6L2.4 9l6.7-.74L12 2z"/></svg>
                                    {{ $quiz->my_final_score }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 flex-wrap text-xs md:text-sm text-gray-500 mt-1.5">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $quiz->duration_minutes }} menit
                            </span>
                            <span class="text-gray-300">&middot;</span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $quiz->access_start_at->translatedFormat('d M Y H:i') }} — {{ $quiz->access_end_at->translatedFormat('d M Y H:i') }}
                            </span>
                            <span class="text-gray-300">&middot;</span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                @if ($quiz->max_attempts)
                                    {{ $quiz->my_attempts_used }}/{{ $quiz->max_attempts }} percobaan
                                @else
                                    {{ $quiz->my_attempts_used }} percobaan
                                @endif
                            </span>
                        </div>

                        <div class="mt-3.5">
                            @if ($status === 'upcoming')
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: {{ $stateBg }}; color: {{ $stateText }};">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Belum dibuka
                                </span>
                            @elseif ($status === 'finished')
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: {{ $stateBg }}; color: {{ $stateText }};">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Sudah ditutup
                                </span>
                            @elseif ($usedUp)
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: {{ $stateBg }}; color: {{ $stateText }};">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Percobaan habis
                                </span>
                            @else
                                <form method="POST" action="{{ route('siswa.classes.quizzes.start', [$schoolClass, $quiz]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-4 py-2 rounded-lg md:rounded-xl hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 transition-all text-xs md:text-sm font-semibold">
                                        @if ($quiz->my_attempts_used > 0)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Kerjakan Lagi
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Mulai Kerjakan
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-14 md:py-16 bg-white border border-dashed border-indigo-100 rounded-2xl md:rounded-3xl">
                <div class="w-14 h-14 mx-auto rounded-full bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-500 text-sm md:text-base">Belum ada kuis</p>
                <p class="text-xs md:text-sm mt-1 text-gray-400">Kuis akan muncul setelah guru mempublikasikannya.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection