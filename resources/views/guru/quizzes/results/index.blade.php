{{-- resources/views/guru/quizzes/results/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Hasil — ' . $quiz->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 md:py-8" x-data="{ releasing: false }">

    {{-- Hero Header + Ringkasan Angka --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.classes.quizzes.index', $class) }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke daftar kuis
            </a>
            <h1 class="text-xl md:text-2xl font-bold mt-2">{{ $quiz->title }}</h1>
            <p class="text-indigo-100 text-sm md:text-base">{{ $class->name }} — Hasil & Koreksi</p>

            {{-- Chip stat di dalam hero --}}
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2.5">
                <div class="bg-white/10 border border-white/15 rounded-2xl px-3 py-2.5 text-center">
                    <p class="text-lg md:text-xl font-bold leading-none">{{ $stats['done'] }}/{{ $stats['total'] }}</p>
                    <p class="text-[10px] md:text-xs text-indigo-100 mt-1">Sudah Kerjakan</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-2xl px-3 py-2.5 text-center">
                    <p class="text-lg md:text-xl font-bold leading-none">{{ $stats['average'] ?? '-' }}</p>
                    <p class="text-[10px] md:text-xs text-indigo-100 mt-1">Rata-rata</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-2xl px-3 py-2.5 text-center">
                    <p class="text-lg md:text-xl font-bold leading-none text-emerald-300">{{ $stats['highest'] ?? '-' }}</p>
                    <p class="text-[10px] md:text-xs text-indigo-100 mt-1">Tertinggi</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-2xl px-3 py-2.5 text-center">
                    <p class="text-lg md:text-xl font-bold leading-none text-red-300">{{ $stats['lowest'] ?? '-' }}</p>
                    <p class="text-[10px] md:text-xs text-indigo-100 mt-1">Terendah</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm rise-in flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-[#10B981]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Featured: Rilis Nilai --}}
    @if($quiz->needsManualRelease())
        <div class="mb-6 rise-in" style="animation-delay: 60ms">
            @if($quiz->score_released_at)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-3 inline-flex items-center gap-2 text-sm text-emerald-700 font-medium">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Nilai sudah dirilis pada {{ $quiz->score_released_at->translatedFormat('d M Y, H:i') }}
                </div>
            @else
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#4F46E5] to-[#3730A3] p-5 text-white flex items-center justify-between gap-4 flex-wrap">
                    <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/5"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="relative w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center shrink-0">
                            <span class="absolute inset-0 rounded-2xl bg-white/20" style="animation: soft-pulse 2.8s ease-in-out infinite"></span>
                            <svg class="w-5 h-5 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-sm md:text-base">Nilai Belum Dirilis</p>
                            <p class="text-xs md:text-sm text-indigo-200">Siswa belum bisa melihat skor akhir mereka</p>
                        </div>
                    </div>
                    <form action="{{ route('guru.classes.quizzes.results.release', [$class, $quiz]) }}" method="POST"
                          @submit="releasing = true" class="relative shrink-0">
                        @csrf
                        <button type="button" x-data @click="if(confirm('Rilis nilai kuis ini ke semua siswa yang sudah selesai mengerjakan?')) { releasing = true; $el.closest('form').submit(); }"
                                :disabled="releasing"
                                class="inline-flex items-center gap-2 bg-white text-[#4338CA] px-4 py-2.5 rounded-xl font-semibold text-sm hover:-translate-y-0.5 active:scale-95 transition-all disabled:opacity-70 disabled:pointer-events-none">
                            <svg x-show="!releasing" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <svg x-show="releasing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="releasing ? 'Merilis...' : 'Rilis Nilai ke Siswa'"></span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endif

    {{-- Daftar Siswa --}}
    <div class="space-y-3">
        @foreach ($rows as $i => $row)
            @php
                $student = $row['student'];
                $palette = ['from-[#4F46E5] to-[#818CF8]', 'from-[#7C3AED] to-[#a78bfa]', 'from-[#0EA5E9] to-[#7dd3fc]', 'from-[#3B82F6] to-[#93c5fd]'];
            @endphp
            <div class="bg-white border border-gray-200 rounded-2xl p-4 md:p-5 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-100 transition-all duration-200 rise-in" style="animation-delay: {{ min($i,12) * 40 }}ms">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-br {{ $palette[$i % 4] }} flex items-center justify-center text-sm font-semibold text-white shrink-0">
                            {{ strtoupper(substr($student->user->name ?? $student->nama_lengkap ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $student->user->name ?? $student->nama_lengkap ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $row['attempts']->count() }} percobaan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @if ($row['needs_manual'])
                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-amber-50 text-[#F59E0B] font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                                </svg>
                                Perlu koreksi
                            </span>
                        @endif
                        @if (! is_null($row['final_score']))
                            <span class="text-sm font-bold text-[#4F46E5] bg-indigo-50 px-2.5 py-1 rounded-lg">{{ $row['final_score'] }}</span>
                        @else
                            <span class="text-xs text-gray-400">Belum kerjakan</span>
                        @endif
                    </div>
                </div>

                @if ($row['attempts']->isNotEmpty())
                    <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap gap-2">
                        @foreach ($row['attempts'] as $attempt)
                            <a href="{{ route('guru.classes.quizzes.results.attempt', [$class, $quiz, $attempt]) }}"
                               class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg border border-gray-200 hover:border-[#4F46E5] hover:bg-indigo-50/40 hover:text-[#4338CA] transition-colors duration-150">
                                Percobaan {{ $attempt->attempt_number }}: {{ $attempt->score ?? '-' }}
                                @if ($attempt->hasPendingManualGrading())
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes soft-pulse {
    0%, 100% { opacity: .35; transform: scale(1); }
    50% { opacity: 0; transform: scale(1.35); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection