{{-- resources/views/guru/grades/components/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Komponen Penilaian — ' . $class->name)

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
    [x-cloak] { display: none !important; }
    .ring-progress { transition: stroke-dashoffset 1.1s cubic-bezier(.4,0,.2,1); }
    .donut-seg { transition: stroke-dashoffset 1s cubic-bezier(.4,0,.2,1), stroke-dasharray 1s cubic-bezier(.4,0,.2,1); }
</style>

@php
    // Palet warna bergilir (rumpun indigo/violet/sky/blue sesuai panduan)
    $palette = [
        ['bg' => '#4F46E5', 'soft' => 'bg-indigo-50', 'text' => 'text-[#4F46E5]', 'ring' => '#4F46E5', 'shadow' => 'shadow-indigo-200/60'],
        ['bg' => '#7C3AED', 'soft' => 'bg-violet-50', 'text' => 'text-violet-600', 'ring' => '#7C3AED', 'shadow' => 'shadow-violet-200/60'],
        ['bg' => '#0EA5E9', 'soft' => 'bg-sky-50',    'text' => 'text-sky-600',    'ring' => '#0EA5E9', 'shadow' => 'shadow-sky-200/60'],
        ['bg' => '#3B82F6', 'soft' => 'bg-blue-50',   'text' => 'text-blue-600',   'ring' => '#3B82F6', 'shadow' => 'shadow-blue-200/60'],
    ];
    $featured = $components->isEmpty() ? null : $components->sortByDesc('weight')->first();
    $circumference = 2 * M_PI * 42; // r=42 dipakai di donut hero
    $offset = 0;
@endphp

<div class="max-w-2xl mx-auto px-4 py-6 md:py-8 space-y-5 md:space-y-6"
     x-data="{ confirmDelete: false, deleteForm: null, deleteName: '' }">

    <div>
        <a href="{{ route('guru.classes.show', $class) }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke kelas
        </a>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mt-2">Komponen Penilaian</h1>
        <p class="text-sm text-gray-500">{{ $class->name }}</p>
    </div>

    @if (session('success'))
        <div class="rise-in bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rise-in bg-red-50 border border-red-200 text-[#EF4444] text-sm rounded-xl p-3.5">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ============ HERO — DONUT KOMPOSISI BOBOT ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex flex-col sm:flex-row items-center gap-6 sm:gap-8">
            {{-- Donut chart komposisi --}}
            <div class="shrink-0 relative w-32 h-32 sm:w-36 sm:h-36"
                 x-data="{ drawn: false }" x-init="setTimeout(() => drawn = true, 200)">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="12"/>
                    @foreach ($components as $i => $component)
                        @php
                            $frac = $totalWeight > 0 ? $component->weight / max($totalWeight, 100) : 0;
                            $len = $frac * $circumference;
                            $color = $palette[$i % 4]['ring'];
                        @endphp
                        <circle cx="50" cy="50" r="42" fill="none" stroke="{{ $color }}" stroke-width="12"
                                stroke-linecap="round"
                                class="donut-seg"
                                :stroke-dasharray="drawn ? '{{ $len }} {{ $circumference - $len }}' : '0 {{ $circumference }}'"
                                stroke-dashoffset="{{ -$offset }}"
                                style="transition-delay: {{ $i * 120 }}ms"/>
                        @php $offset += $len; @endphp
                    @endforeach
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl sm:text-3xl font-bold tabular-nums">{{ $totalWeight }}%</span>
                    <span class="text-[10px] sm:text-xs text-indigo-200">total bobot</span>
                </div>
            </div>

            <div class="flex-1 w-full">
                <p class="text-xs sm:text-sm text-indigo-200 uppercase tracking-wide mb-2">Komposisi Penilaian</p>

                @if ($components->isEmpty())
                    <p class="text-sm text-indigo-100">Belum ada komponen untuk divisualisasikan.</p>
                @else
                    <div class="flex flex-wrap gap-x-4 gap-y-1.5 mb-3">
                        @foreach ($components as $i => $component)
                            <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-indigo-50">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $palette[$i % 4]['ring'] }}"></span>
                                {{ $component->name }} · {{ $component->weight }}%
                            </span>
                        @endforeach
                    </div>
                @endif

                @if ($totalWeight !== 100)
                    <p class="text-xs sm:text-sm text-amber-200 flex items-start gap-1.5">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Idealnya 100% supaya nilai akhir akurat.
                    </p>
                @else
                    <p class="text-xs sm:text-sm text-indigo-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Bobot sudah pas dan seimbang.
                    </p>
                @endif
            </div>
        </div>
    </div>

    @if ($components->isNotEmpty())
    {{-- ============ FEATURED — komponen bobot terbesar ============ --}}
    <div class="rise-in relative overflow-hidden rounded-2xl md:rounded-3xl p-5 sm:p-6 text-white"
         style="background:linear-gradient(135deg,{{ $palette[$components->search($featured) % 4]['ring'] }},{{ $palette[$components->search($featured) % 4]['ring'] }}cc)">
        <div class="absolute -bottom-6 -right-6 w-32 h-32 rounded-full bg-white opacity-10"></div>
        <div class="relative flex items-center gap-4">
            <div class="relative shrink-0">
                <div class="absolute inset-0 rounded-2xl bg-white/30" style="animation: ping 2.8s cubic-bezier(0,0,0.2,1) infinite"></div>
                <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[11px] sm:text-xs text-white/80 uppercase tracking-wide">Bobot Terbesar</p>
                <p class="font-bold text-base sm:text-lg truncate">{{ $featured->name }}</p>
                <span class="inline-flex mt-1 bg-white border rounded-lg px-2.5 py-1 text-xs font-semibold text-gray-800">
                    {{ $featured->weight }}% dari nilai akhir
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- ============ DAFTAR KOMPONEN ============ --}}
    <div class="space-y-3">
        @forelse ($components as $i => $component)
            @php $c = $palette[$i % 4]; @endphp
            <div class="rise-in group bg-white border border-gray-100 rounded-2xl p-4 sm:p-5 flex items-center justify-between gap-3 sm:gap-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl {{ $c['shadow'] }}"
                 style="animation-delay: {{ $i * 70 }}ms"
                 x-data="{ editing: false }">

                <div x-show="!editing" class="flex items-center gap-3 flex-1 min-w-0">
                    {{-- Mini ring bobot per komponen --}}
                    <div class="relative w-11 h-11 sm:w-12 sm:h-12 shrink-0"
                         x-data="{ pct: 0 }" x-init="setTimeout(() => pct = {{ $component->weight }}, 200 + {{ $i }}*70)">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 44 44">
                            <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $c['bg'] }}1a" stroke-width="5"/>
                            <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $c['bg'] }}" stroke-width="5" stroke-linecap="round"
                                    class="ring-progress" stroke-dasharray="113"
                                    :stroke-dashoffset="113 - (113 * Math.min(pct,100) / 100)"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-[10px] sm:text-xs font-bold {{ $c['text'] }}">{{ $component->weight }}%</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $component->name }}</p>
                        <p class="text-xs text-gray-400">komponen #{{ $i + 1 }}</p>
                    </div>
                </div>

                <form x-show="editing" x-cloak method="POST" action="{{ route('guru.classes.grade-components.update', [$class, $component]) }}"
                      class="flex items-center gap-2 flex-1">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $component->name }}"
                           class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
                    <input type="number" name="weight" value="{{ $component->weight }}" min="1" max="100"
                           class="w-16 border border-gray-200 rounded-xl px-2 py-2 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
                    <span class="text-sm text-gray-400 shrink-0">%</span>
                    <button type="submit" class="text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium shrink-0">Simpan</button>
                </form>

                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" @click="editing = !editing"
                            class="p-2 rounded-lg text-gray-400 hover:text-[#4F46E5] hover:bg-indigo-50 hover:scale-110 active:scale-95 transition-all duration-150">
                        <svg x-show="!editing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <svg x-show="editing" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <button type="button"
                            @click="confirmDelete = true; deleteName = '{{ $component->name }}'; deleteForm = 'delete-form-{{ $component->id }}'"
                            class="p-2 rounded-lg text-gray-400 hover:text-[#EF4444] hover:bg-red-50 hover:scale-110 active:scale-95 transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    <form id="delete-form-{{ $component->id }}" method="POST" action="{{ route('guru.classes.grade-components.destroy', [$class, $component]) }}" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        @empty
            <div class="rise-in text-center py-16 bg-white border border-dashed border-indigo-200 rounded-2xl">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p class="font-medium text-gray-600">Belum ada komponen penilaian</p>
                <p class="text-sm text-gray-400 mt-1">Tambahkan di bawah, misal "Tugas Harian" 20%.</p>
            </div>
        @endforelse
    </div>

    {{-- ============ FORM TAMBAH ============ --}}
    <div class="rise-in bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-7">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-900 text-sm md:text-base">Tambah Komponen</h2>
        </div>
        <form method="POST" action="{{ route('guru.classes.grade-components.store', $class) }}"
              x-data="{ saving: false }" @submit="saving = true"
              class="flex flex-col sm:flex-row sm:items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Komponen</label>
                <input type="text" name="name" placeholder="Contoh: Ujian Tengah Semester" required
                       class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
            </div>
            <div class="w-full sm:w-28">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Bobot (%)</label>
                <input type="number" name="weight" min="1" max="100" required
                       class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
            </div>
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-md shadow-indigo-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 shrink-0 disabled:opacity-70">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="saving ? 'Menambah...' : 'Tambah'"></span>
            </button>
        </form>
    </div>

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
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hapus Komponen?</h3>
            <p class="text-sm text-gray-500 mb-5">Komponen <span class="font-medium text-gray-700" x-text="deleteName"></span> beserta nilai terkait akan dihapus permanen.</p>
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