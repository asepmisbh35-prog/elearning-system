{{-- resources/views/siswa/quizzes/result.blade.php --}}
@extends('layouts.app')
@section('title', 'Hasil — ' . $quiz->title)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8 pb-24 md:pb-8 space-y-5 md:space-y-6">

    <a href="{{ route('siswa.classes.quizzes.index', $schoolClass) }}"
       class="inline-flex items-center gap-1.5 text-sm text-[#4F46E5] hover:text-[#4338CA] transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke daftar kuis
    </a>

    {{-- HERO SKOR --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-6 md:p-8 text-white text-center">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <div class="w-11 h-11 md:w-12 md:h-12 mx-auto rounded-xl bg-white/15 backdrop-blur flex items-center justify-center mb-3">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <h1 class="text-lg md:text-xl font-bold">{{ $quiz->title }}</h1>
            <p class="text-indigo-100 text-xs md:text-sm mt-1.5">
                {{ $attempt->status === 'auto_submitted' ? 'Disubmit otomatis' : 'Selesai' }}
                &middot; Percobaan ke-{{ $attempt->attempt_number }}
            </p>

            @if ($canSeeScore)
                <div class="mt-6">
                    <div class="text-5xl md:text-6xl font-extrabold">{{ $attempt->score }}</div>
                    <p class="text-indigo-100 text-xs md:text-sm mt-1.5">Nilai percobaan ini</p>
                </div>

                @if (! is_null($finalScore) && $finalScore !== $attempt->score)
                    <p class="inline-flex items-center gap-1.5 text-xs bg-white/10 rounded-full px-3 py-1.5 mt-4">
                        Nilai final kuis (sesuai mode nilai): <strong>{{ $finalScore }}</strong>
                    </p>
                @endif
            @else
                <div class="mt-6 flex flex-col items-center gap-2">
                    <svg class="w-9 h-9 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <p class="text-indigo-100 text-sm">Nilai akan diumumkan oleh guru.</p>
                </div>
            @endif

            @if ($attempt->hasPendingManualGrading())
                <div class="inline-flex items-center gap-1.5 text-xs bg-[#FFFBEB]/95 text-[#92400E] font-medium rounded-full px-3 py-1.5 mt-4">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ada soal isian singkat yang menunggu koreksi manual guru.
                </div>
            @endif
        </div>
    </div>

    @if ($quiz->show_review)
        <div class="space-y-3">
            <h2 class="font-semibold text-gray-800 text-sm md:text-base flex items-center gap-2">
                <span class="w-1.5 h-4 rounded-full bg-[#4F46E5]"></span>
                Pembahasan
            </h2>
            @foreach ($attempt->answers as $answer)
                @php
                    $question = $answer->question;
                    if ($answer->is_correct === true) {
                        $stateBg = '#ECFDF5'; $stateText = '#059669'; $label = 'Benar';
                    } elseif ($answer->is_correct === false) {
                        $stateBg = '#FEF2F2'; $stateText = '#DC2626'; $label = 'Salah';
                    } else {
                        $stateBg = '#F3F4F6'; $stateText = '#4B5563'; $label = 'Menunggu koreksi';
                    }
                @endphp
                <div class="bg-white border border-gray-100 rounded-2xl p-4 md:p-5 shadow-sm shadow-indigo-100/40">
                    <p class="text-gray-800 text-sm md:text-base mb-2.5">{{ $question->question_text }}</p>
                    <span class="inline-flex items-center gap-1 text-[11px] md:text-xs px-2.5 py-1 rounded-full font-medium"
                          style="background-color: {{ $stateBg }}; color: {{ $stateText }};">
                        @if ($answer->is_correct === true)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif ($answer->is_correct === false)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                        {{ $label }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection