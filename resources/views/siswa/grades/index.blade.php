{{-- resources/views/siswa/grades/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Rapor — ' . $schoolClass->name)

@php
    $ring = round(2 * M_PI * 52, 2);

    $scoreList = collect($components)->mapWithKeys(function ($c) use ($scores) {
        return [$c->name => $scores[$c->id]->score ?? null];
    })->filter(fn ($v) => ! is_null($v));

    $avgScore  = $scoreList->count() ? round($scoreList->avg(), 1) : null;
    $topName   = $scoreList->count() ? $scoreList->sortDesc()->keys()->first() : null;
    $topScore  = $scoreList->count() ? $scoreList->max() : null;
    $lowName   = $scoreList->count() ? $scoreList->sort()->keys()->first() : null;
    $lowScore  = $scoreList->count() ? $scoreList->min() : null;
@endphp

@section('content')
<style>
    @media print {
        aside, header, .no-print { display: none !important; }
        main { padding: 0 !important; }
        .print-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

<div class="max-w-3xl lg:max-w-5xl mx-auto px-4 py-6 md:py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between no-print mb-5 md:mb-6">
        <a href="{{ route('siswa.classes.show', $class ?? $schoolClass) }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-400 hover:text-gray-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke kelas
        </a>
        <button onclick="window.print()"
                class="text-xs sm:text-sm bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl hover:shadow-lg hover:shadow-indigo-200/60 hover:-translate-y-0.5 active:scale-95 transition-all font-semibold flex items-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 md:gap-6 items-start">

        {{-- ================= KARTU RAPOR (selalu tampil, ini yang dicetak) ================= --}}
        <div class="print-card bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 sm:p-6 md:p-8 shadow-sm">

            {{-- Hero mini header --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] text-white p-5 sm:p-6 mb-5 md:mb-6 text-center">
                <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white opacity-[0.08] blur-xl"></div>
                <div class="absolute -bottom-10 -left-10 w-36 h-36 rounded-full bg-white opacity-[0.06] blur-xl"></div>
                <div class="relative z-10">
                    <p class="text-xl sm:text-2xl mb-1">📊</p>
                    <h1 class="text-lg sm:text-xl font-bold">Rapor Nilai</h1>
                    <p class="text-xs sm:text-sm text-indigo-100 mt-0.5">{{ $schoolClass->name }} &middot; {{ $schoolClass->subject }}</p>
                    <span class="inline-block mt-2 text-[11px] sm:text-xs bg-white/15 backdrop-blur px-3 py-1 rounded-full font-medium">
                        KKM: {{ $kkm }}
                    </span>
                </div>
            </div>

            {{-- Ringkasan cepat — hanya di layar kecil/menengah, karena di desktop sudah ada panel samping --}}
            @if ($scoreList->count())
                <div class="lg:hidden no-print flex gap-2 mb-5 overflow-x-auto pb-1 -mx-1 px-1">
                    <div class="shrink-0 min-w-[92px] bg-indigo-50 rounded-xl px-3 py-2 text-center">
                        <p class="text-[10px] text-indigo-400 font-semibold uppercase tracking-wide">Rata-rata</p>
                        <p class="text-base font-bold text-indigo-700">{{ $avgScore }}</p>
                    </div>
                    <div class="shrink-0 min-w-[92px] bg-emerald-50 rounded-xl px-3 py-2 text-center">
                        <p class="text-[10px] text-emerald-500 font-semibold uppercase tracking-wide">Tertinggi</p>
                        <p class="text-base font-bold text-emerald-700">{{ $topScore }}</p>
                        <p class="text-[10px] text-emerald-500 truncate">{{ $topName }}</p>
                    </div>
                    <div class="shrink-0 min-w-[92px] bg-amber-50 rounded-xl px-3 py-2 text-center">
                        <p class="text-[10px] text-amber-500 font-semibold uppercase tracking-wide">Terendah</p>
                        <p class="text-base font-bold text-amber-700">{{ $lowScore }}</p>
                        <p class="text-[10px] text-amber-500 truncate">{{ $lowName }}</p>
                    </div>
                </div>
            @endif

            {{-- Komponen nilai --}}
            <div class="space-y-2 mb-5 md:mb-6">
                <h2 class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wide px-1 mb-2 flex items-center gap-1.5">
                    Rincian Komponen
                    <span class="normal-case font-normal text-gray-300">· garis abu = ambang KKM</span>
                </h2>
                @foreach ($components as $component)
                    @php
                        $grade = $scores[$component->id] ?? null;
                        $score = $grade?->score;
                        $barColor = $score === null ? 'bg-gray-200' : ($score >= $kkm ? 'bg-[#10B981]' : 'bg-[#EF4444]');
                        $name = strtolower($component->name);
                        $icon = match (true) {
                            str_contains($name, 'uts') => '📄',
                            str_contains($name, 'uas') => '🧾',
                            str_contains($name, 'kuis') => '🧠',
                            str_contains($name, 'tugas') => '📝',
                            str_contains($name, 'absen') || str_contains($name, 'hadir') => '✅',
                            str_contains($name, 'sikap') => '🌟',
                            str_contains($name, 'praktik') || str_contains($name, 'praktek') => '🔬',
                            str_contains($name, 'proyek') || str_contains($name, 'project') => '🚀',
                            default => '📌',
                        };
                    @endphp
                    <div class="border border-gray-100 rounded-xl px-3.5 sm:px-4 py-3 transition hover:border-indigo-100 hover:shadow-sm hover:bg-indigo-50/30">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-sm shrink-0">{{ $icon }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $component->name }}</p>
                                    <p class="text-xs text-gray-400">Bobot {{ $component->weight }}%</p>
                                </div>
                            </div>
                            <span class="text-base sm:text-lg font-bold {{ $score === null ? 'text-gray-300' : 'text-gray-800' }} shrink-0">
                                {{ $score ?? '-' }}
                            </span>
                        </div>
                        @if (! is_null($score))
                            <div class="relative w-full h-1.5 sm:h-2 bg-gray-100 rounded-full mt-2.5 overflow-hidden">
                                <div class="absolute inset-y-0 {{ $barColor }} rounded-full transition-[width] duration-700 ease-out motion-reduce:transition-none"
                                     style="width: 0%"
                                     x-data
                                     x-init="setTimeout(() => $el.style.width = '{{ min($score, 100) }}%', {{ 150 + $loop->index * 90 }})"></div>
                                <div class="absolute inset-y-0 w-px bg-gray-500/50" style="left: {{ min($kkm, 100) }}%" title="Ambang KKM: {{ $kkm }}"></div>
                            </div>
                        @endif
                        @if ($grade?->note)
                            <p class="text-xs text-gray-400 italic mt-2">💬 {{ $grade->note }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Nilai Akhir --}}
            @php $lulus = ! is_null($final) && $final >= $kkm; @endphp
            <div class="relative overflow-hidden rounded-2xl p-5 sm:p-6 text-center text-white
                        {{ $lulus ? 'bg-gradient-to-br from-[#10B981] to-emerald-600' : 'bg-gradient-to-br from-[#F59E0B] to-amber-600' }}"
                 x-data="{ display: 0 }"
                 x-init="
                    let target = {{ $final ?? 0 }};
                    if ({{ is_null($final) ? 'false' : 'true' }}) {
                        let start = null;
                        const step = (ts) => {
                            if (!start) start = ts;
                            let p = Math.min((ts - start) / 900, 1);
                            display = Math.floor(p * target);
                            if (p < 1) requestAnimationFrame(step); else display = target;
                        };
                        requestAnimationFrame(step);
                    }
                 ">
                <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -top-10 -left-10 w-28 h-28 bg-white/10 rounded-full blur-xl"></div>
                @if ($lulus)
                    <span class="absolute top-3 right-4 text-lg animate-bounce motion-reduce:animate-none">🎉</span>
                    <span class="absolute top-6 left-5 text-sm animate-bounce motion-reduce:animate-none" style="animation-delay:150ms">✨</span>
                @endif
                <div class="relative z-10">
                    <p class="text-xs sm:text-sm text-white/80 mb-1">Nilai Akhir</p>
                    <p class="text-4xl sm:text-5xl font-extrabold tabular-nums" x-text="{{ is_null($final) ? "'-'" : 'display' }}"></p>
                    @if (! is_null($final))
                        <span class="inline-block mt-3 text-[11px] sm:text-xs bg-white/20 backdrop-blur px-3 py-1.5 rounded-full font-semibold">
                            {{ $lulus ? '🎉' : '💪' }} Predikat {{ \App\Models\KkmSetting::predikat($final) }} — {{ \App\Models\KkmSetting::predikatLabel($final) }}
                            {{ $lulus ? '(Tuntas)' : '(Belum Tuntas)' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= PANEL SAMPING — hanya desktop, tidak ikut tercetak ================= --}}
        <aside class="hidden lg:flex lg:flex-col gap-4 no-print sticky top-6">

            {{-- Ring skor --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-center">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-4">Progres Nilai Akhir</p>
                <div class="relative w-32 h-32 mx-auto">
                    <svg viewBox="0 0 120 120" class="w-32 h-32 -rotate-90">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="#E5E7EB" stroke-width="10" />
                        <circle cx="60" cy="60" r="52" fill="none"
                                stroke="{{ is_null($final) ? '#CBD5E1' : ($lulus ? '#10B981' : '#F59E0B') }}"
                                stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="{{ $ring }}"
                                style="stroke-dashoffset: {{ $ring }}"
                                class="transition-[stroke-dashoffset] duration-1000 ease-out motion-reduce:transition-none"
                                x-data
                                x-init="setTimeout(() => $el.style.strokeDashoffset = {{ $ring * (1 - min($final ?? 0, 100) / 100) }}, 300)"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-gray-800">{{ $final ?? '-' }}</span>
                        <span class="text-[10px] text-gray-400 font-medium">dari 100</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-4 leading-relaxed">
                    @if (is_null($final))
                        Nilai akhir belum tersedia — beberapa komponen belum dinilai.
                    @elseif ($lulus)
                        Mantap! Nilai kamu sudah di atas KKM ({{ $kkm }}). Terus pertahankan ya 🎉
                    @else
                        Masih di bawah KKM ({{ $kkm }}). Yuk cek komponen yang paling lemah dan minta bantuan gurumu 💪
                    @endif
                </p>
            </div>

            {{-- Statistik komponen --}}
            @if ($scoreList->count())
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm space-y-3">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Sekilas Komponen</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Rata-rata</span>
                        <span class="text-sm font-bold text-indigo-600">{{ $avgScore }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 truncate max-w-[120px]">Tertinggi · {{ $topName }}</span>
                        <span class="text-sm font-bold text-emerald-600">{{ $topScore }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 truncate max-w-[120px]">Terendah · {{ $lowName }}</span>
                        <span class="text-sm font-bold text-amber-600">{{ $lowScore }}</span>
                    </div>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection