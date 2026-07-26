{{-- resources/views/guru/grades/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Nilai — ' . $class->name)

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rise-in { animation: rise-in .45s ease both; }
    [x-cloak] { display: none !important; }
    .ring-progress { transition: stroke-dashoffset 1s cubic-bezier(.4,0,.2,1); }
</style>

@php
    $total = count($rows);
    $lulus = collect($rows)->filter(fn($r) => !is_null($r['final']) && $r['final'] >= $kkm)->count();
    $belum = collect($rows)->filter(fn($r) => !is_null($r['final']) && $r['final'] < $kkm)->count();
    $rata = $total ? round(collect($rows)->pluck('final')->filter()->avg(), 1) : 0;
@endphp

<div class="max-w-5xl mx-auto px-4 py-6 md:py-8 space-y-5 md:space-y-6"
     x-data="{
        modalOpen: false, modalStudent: null, modalStudentName: '', modalComponent: null, modalComponentName: '', modalScore: '', modalNote: '',
        confirmRecalc: false, loadingExcel: false, loadingPdf: false,
        filter: 'semua', expanded: null,
     }">

    {{-- ============ HEADER ============ --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <a href="{{ route('guru.classes.show', $class) }}"
               class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke kelas
            </a>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 mt-2">Input Nilai</h1>
            <p class="text-sm text-gray-500">{{ $class->name }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('guru.classes.grades.export.excel', $class) }}" @click="loadingExcel = true"
               class="inline-flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-xl text-white transition-all duration-200 shadow-md shadow-emerald-200 hover:shadow-lg hover:-translate-y-0.5"
               style="background:linear-gradient(135deg,#10B981,#059669)">
                <svg x-show="!loadingExcel" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                <svg x-show="loadingExcel" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Excel
            </a>
            <a href="{{ route('guru.classes.grades.export.pdf', $class) }}" @click="loadingPdf = true"
               class="inline-flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-xl text-white transition-all duration-200 shadow-md shadow-red-200 hover:shadow-lg hover:-translate-y-0.5"
               style="background:linear-gradient(135deg,#EF4444,#DC2626)">
                <svg x-show="!loadingPdf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <svg x-show="loadingPdf" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                PDF
            </a>
            <button type="button" @click="confirmRecalc = true"
                    class="inline-flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-indigo-200 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Hitung Ulang
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="rise-in bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    @if ($components->isEmpty())
        <div class="rise-in text-center py-16 bg-white border border-dashed border-indigo-200 rounded-2xl">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="font-medium text-gray-600">Belum ada komponen penilaian</p>
            <p class="text-sm mt-1">
                <a href="{{ route('guru.classes.grade-components.index', $class) }}" class="text-[#4F46E5] hover:text-[#4338CA] font-medium hover:underline">Tambahkan komponen penilaian dulu</a>
            </p>
        </div>
    @else

    {{-- ============ HERO RINGKASAN KELAS — bento, angka animasi ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-7 text-white">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center gap-6">
            {{-- Radial gauge rata-rata kelas --}}
            <div class="flex items-center gap-4"
                 x-data="{ pct: 0, val: 0 }"
                 x-init="
                    let target = {{ $rata }}, start = null;
                    const step = ts => { if(!start) start = ts; let p = Math.min((ts-start)/900, 1);
                        val = (p*target).toFixed(1); pct = (val/100)*100;
                        if (p < 1) requestAnimationFrame(step); else { val = target.toFixed(1); pct = (target/100)*100; } };
                    requestAnimationFrame(step)">
                <svg class="w-20 h-20 sm:w-24 sm:h-24 -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="10"/>
                    <circle cx="50" cy="50" r="42" fill="none" stroke="white" stroke-width="10" stroke-linecap="round"
                            class="ring-progress" stroke-dasharray="264"
                            :stroke-dashoffset="264 - (264 * pct / 100)"/>
                </svg>
                <div>
                    <p class="text-[11px] text-indigo-200 uppercase tracking-wide">Rata-rata Kelas</p>
                    <p class="text-2xl sm:text-3xl font-bold tabular-nums" x-text="val"></p>
                    <p class="text-xs text-indigo-200">KKM: {{ $kkm }}</p>
                </div>
            </div>

            <div class="hidden sm:block w-px h-16 bg-white/15"></div>

            {{-- Chip lulus/belum --}}
            <div class="flex items-center gap-3 flex-wrap"
                 x-data="{ l: 0, b: 0 }"
                 x-init="
                    let tl={{ $lulus }}, tb={{ $belum }}, start=null;
                    const step=ts=>{ if(!start)start=ts; let p=Math.min((ts-start)/700,1); l=Math.floor(p*tl); b=Math.floor(p*tb);
                        if(p<1) requestAnimationFrame(step); else { l=tl; b=tb; } };
                    requestAnimationFrame(step)">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2.5 border border-white/10">
                    <p class="text-xl font-bold tabular-nums" x-text="l"></p>
                    <p class="text-[11px] text-indigo-200">Lulus KKM</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2.5 border border-white/10">
                    <p class="text-xl font-bold tabular-nums" x-text="b"></p>
                    <p class="text-[11px] text-indigo-200">Belum Lulus</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2.5 border border-white/10">
                    <p class="text-xl font-bold tabular-nums">{{ $total }}</p>
                    <p class="text-[11px] text-indigo-200">Total Siswa</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ FILTER CHIP ============ --}}
    <div class="flex items-center gap-2 flex-wrap">
        <button @click="filter = 'semua'" :class="filter === 'semua' ? 'bg-[#4F46E5] text-white border-[#4F46E5]' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-200'"
                class="text-xs sm:text-sm font-medium px-3.5 py-1.5 rounded-full border transition-all duration-200">Semua ({{ $total }})</button>
        <button @click="filter = 'lulus'" :class="filter === 'lulus' ? 'bg-[#10B981] text-white border-[#10B981]' : 'bg-white text-gray-600 border-gray-200 hover:border-emerald-200'"
                class="text-xs sm:text-sm font-medium px-3.5 py-1.5 rounded-full border transition-all duration-200">Lulus ({{ $lulus }})</button>
        <button @click="filter = 'belum'" :class="filter === 'belum' ? 'bg-[#EF4444] text-white border-[#EF4444]' : 'bg-white text-gray-600 border-gray-200 hover:border-red-200'"
                class="text-xs sm:text-sm font-medium px-3.5 py-1.5 rounded-full border transition-all duration-200">Belum Lulus ({{ $belum }})</button>
    </div>

    {{-- ============ KARTU NILAI PER SISWA ============ --}}
    <div class="space-y-3">
        @foreach ($rows as $i => $row)
            @php
                $studentName = $row['student']->user->name ?? $row['student']->nama_lengkap ?? '-';
                $final = $row['final'];
                $isLulus = ! is_null($final) && $final >= $kkm;
                $statusKey = is_null($final) ? 'semua' : ($isLulus ? 'lulus' : 'belum');
                $pctRing = min(100, max(0, (float) ($final ?? 0)));
            @endphp
            <div x-show="filter === 'semua' || filter === '{{ $statusKey }}'"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="rise-in bg-white border border-gray-100 rounded-2xl overflow-hidden" style="animation-delay: {{ $i * 60 }}ms">

                {{-- Baris ringkas — klik untuk expand --}}
                <button type="button" @click="expanded = expanded === {{ $row['student']->id }} ? null : {{ $row['student']->id }}"
                        class="w-full flex items-center gap-4 p-4 sm:p-5 hover:bg-indigo-50/40 transition-colors duration-200 text-left">

                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-semibold text-white shrink-0"
                         style="background:linear-gradient(135deg,#4F46E5,#7C3AED)">
                        {{ mb_substr($studentName, 0, 1) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $studentName }}</p>
                        <p class="text-xs text-gray-400">{{ $components->count() }} komponen dinilai</p>
                    </div>

                    @if (! is_null($final))
                        <span class="hidden sm:inline-flex text-xs px-2.5 py-1 rounded-full font-medium {{ $isLulus ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-[#EF4444]' }}">
                            {{ \App\Models\KkmSetting::predikat($final) }}
                        </span>
                    @endif

                    {{-- Radial ring nilai akhir --}}
                    <div class="relative shrink-0 w-12 h-12 sm:w-14 sm:h-14"
                         x-data="{ pct: 0 }"
                         x-init="setTimeout(() => pct = {{ $pctRing }}, 150 + {{ $i }}*60)">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 64 64">
                            <circle cx="32" cy="32" r="28" fill="none" stroke="#EEF2FF" stroke-width="6"/>
                            <circle cx="32" cy="32" r="28" fill="none" stroke="{{ $isLulus ? '#10B981' : ($final ? '#EF4444' : '#C7D2FE') }}" stroke-width="6" stroke-linecap="round"
                                    class="ring-progress" stroke-dasharray="176"
                                    :stroke-dashoffset="176 - (176 * pct / 100)"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs sm:text-sm font-bold text-gray-800 tabular-nums">
                            {{ $final ?? '-' }}
                        </span>
                    </div>

                    <svg class="w-4 h-4 text-gray-300 shrink-0 transition-transform duration-300" :class="expanded === {{ $row['student']->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Detail komponen — expand --}}
                <div x-show="expanded === {{ $row['student']->id }}" x-cloak
                     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-4 sm:px-5 pb-5 pt-1 border-t border-gray-50 space-y-3">
                    @foreach ($components as $component)
                        @php
                            $grade = $row['scores'][$component->id] ?? null;
                            $score = $grade->score ?? null;
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-28 sm:w-36 shrink-0">
                                <p class="text-xs sm:text-sm font-medium text-gray-700 truncate">{{ $component->name }}</p>
                                <p class="text-[10px] text-gray-400">bobot {{ $component->weight }}%</p>
                            </div>
                            <div class="flex-1 h-2.5 rounded-full bg-indigo-50 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700"
                                     style="width: {{ $score ?? 0 }}%; background: {{ $grade?->is_manual ? '#7C3AED' : '#4F46E5' }}"></div>
                            </div>
                            <button type="button"
                                    @click="modalOpen = true; modalStudent = {{ $row['student']->id }}; modalStudentName = '{{ $studentName }}'; modalComponent = {{ $component->id }}; modalComponentName = '{{ $component->name }}'; modalScore = '{{ $score ?? '' }}'; modalNote = '{{ $grade->note ?? '' }}'"
                                    class="shrink-0 w-14 text-right text-sm font-semibold hover:underline transition-colors duration-150 {{ $grade?->is_manual ? 'text-violet-600' : 'text-gray-700' }}">
                                {{ $score ?? '-' }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <p class="text-xs text-gray-400">
        Klik baris siswa untuk lihat rincian per komponen. Klik angka nilai untuk input/edit manual (misal praktik, presentasi) — bar warna ungu menandakan input manual dan tidak tertimpa rekap otomatis.
    </p>
    @endif

    {{-- ============ MODAL INPUT NILAI ============ --}}
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         @click.self="modalOpen = false"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 w-full max-w-sm shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-0.5" x-text="modalStudentName"></h3>
            <p class="text-sm text-gray-500 mb-4" x-text="modalComponentName"></p>

            <form method="POST" action="{{ route('guru.classes.grades.manual.store', $class) }}"
                  x-data="{ saving: false }" @submit="saving = true">
                @csrf
                <input type="hidden" name="student_id" :value="modalStudent">
                <input type="hidden" name="grade_component_id" :value="modalComponent">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nilai (0-100)</label>
                    <input type="number" name="score" x-model="modalScore" min="0" max="100" step="0.01" required
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                    <textarea name="note" x-model="modalNote" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm resize-none outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" :disabled="saving"
                            class="flex-1 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-4 py-2.5 rounded-xl text-sm font-medium shadow-md shadow-indigo-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 disabled:opacity-70 flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                    <button type="button" @click="modalOpen = false"
                            class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============ MODAL KONFIRMASI HITUNG ULANG ============ --}}
    <div x-show="confirmRecalc" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmRecalc = false"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hitung Ulang Rekap Nilai?</h3>
            <p class="text-sm text-gray-500 mb-5">Nilai akhir akan dihitung ulang dari semua komponen. Nilai manual (bertanda ungu) tidak akan berubah.</p>
            <div class="flex gap-3">
                <button type="button" @click="confirmRecalc = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all duration-200">Batal</button>
                <form method="POST" action="{{ route('guru.classes.grades.recalculate', $class) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white rounded-xl text-sm font-medium hover:shadow-lg transition-all duration-200">Ya, Hitung Ulang</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection