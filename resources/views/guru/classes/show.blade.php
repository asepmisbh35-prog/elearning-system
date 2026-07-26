@extends('layouts.app')
@section('title', $class->name)

@php
    $aksenMenu = [
        ['wash' => 'bg-indigo-50', 'border' => 'border-indigo-100', 'solid' => '#4F46E5'],
        ['wash' => 'bg-violet-50', 'border' => 'border-violet-100', 'solid' => '#7C3AED'],
        ['wash' => 'bg-sky-50',    'border' => 'border-sky-100',    'solid' => '#0284C7'],
        ['wash' => 'bg-blue-50',   'border' => 'border-blue-100',   'solid' => '#2563EB'],
        ['wash' => 'bg-indigo-50', 'border' => 'border-indigo-100', 'solid' => '#6366F1'],
    ];

    $menuItems = [
        ['route' => route('guru.classes.assignments.index', $class),       'label' => 'Tugas',             'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route' => route('guru.classes.quizzes.index', $class),          'label' => 'Kuis',               'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => route('guru.classes.grade-components.index', $class), 'label' => 'Komponen Penilaian', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['route' => route('guru.classes.kkm.index', $class),              'label' => 'KKM Kelas',          'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => route('guru.classes.grades.index', $class),           'label' => 'Input Nilai',        'icon' => 'M9 17v-2a4 4 0 014-4h4m0 0l-3-3m3 3l-3 3M5 5h14M5 5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2M5 5l7 7 7-7'],
    ];
@endphp

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes gentle-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }
    .rise-in { animation: rise-in .45s ease both; }
    .icon-float { animation: gentle-float 3s ease-in-out infinite; }
    @media (prefers-reduced-motion: reduce) {
        .rise-in { animation: none; }
        .icon-float { animation: none; }
    }
    [x-cloak] { display: none !important; }
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="space-y-5 md:space-y-6"
     x-data="{
        confirmReset: false,
        removeTarget: null,
        removeName: '',
        askRemove(el, name) { this.removeTarget = el; this.removeName = name; },
     }">

    {{-- Hero header --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-7 text-white">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>

        <div class="relative flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('guru.classes.index') }}" class="text-indigo-200 hover:text-white transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="icon-float w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white/15 flex items-center justify-center shrink-0 shadow-lg shadow-black/10">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold truncate">{{ $class->name }}</h2>
                    <p class="text-xs sm:text-sm text-indigo-100 truncate">{{ $class->rombel?->name }} &middot; {{ $class->subject }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('guru.classes.qr', $class) }}"
                   class="inline-flex items-center gap-1.5 bg-white/15 hover:bg-white/25 text-white text-xs sm:text-sm font-medium px-3 py-2 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4h2m-2 0v-2m6-4h2m-6 0h-2m6 0v-2m-6 6v2m-4-6H4m4 0v2m0-2V4m6 12h.01M17 4h2v2h-2V4zM4 4h2v2H4V4zm0 12h2v2H4v-2z"/>
                    </svg>
                    <span class="hidden sm:inline">QR Code</span>
                </a>
                <a href="{{ route('guru.classes.edit', $class) }}"
                   class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-white text-[#4338CA] rounded-lg text-xs sm:text-sm font-semibold hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span class="hidden sm:inline">Edit Kelas</span>
                </a>
            </div>
        </div>

        <div class="relative flex items-center gap-3 flex-wrap mt-5" x-data="{ copied: false }">
            <span class="text-xs text-indigo-200">Kode Kelas</span>
            <span class="relative font-mono font-bold text-lg sm:text-xl tracking-widest bg-white/15 px-3 py-1 rounded-lg">
                <span class="absolute inset-0 rounded-lg bg-white/20 animate-pulse"></span>
                <span class="relative">{{ $class->code }}</span>
            </span>
            <button type="button"
                    @click="navigator.clipboard.writeText('{{ $class->code }}'); copied = true; setTimeout(() => copied = false, 1500)"
                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium px-3 py-1.5 rounded-lg bg-white/15 hover:bg-white/25 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
            </button>

            <button type="button" @click="confirmReset = true"
                    class="text-xs sm:text-sm font-medium px-3 py-1.5 rounded-lg text-indigo-100 hover:bg-white/15 transition">
                Reset Kode
            </button>
        </div>
        @if($class->description)
            <p class="relative text-xs sm:text-sm text-indigo-100 mt-2 max-w-lg">{{ $class->description }}</p>
        @endif
    </div>

    {{-- ============ BENTO: Pertemuan (featured, urutan #1) + Statistik nempel di kartu yang sama ============ --}}
    <a href="{{ route('guru.classes.meetings.index', $class) }}"
       class="rise-in group relative overflow-hidden flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-6 rounded-2xl md:rounded-3xl p-5 sm:p-6 bg-gradient-to-br from-white to-sky-50 border border-sky-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400"
       style="animation-delay: 0ms">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-sky-100 opacity-60 transition-transform duration-500 group-hover:scale-125 pointer-events-none"></div>

        {{-- Ikon jam animasi denyut --}}
        <div class="relative shrink-0 w-16 h-16 sm:w-[70px] sm:h-[70px] rounded-2xl flex items-center justify-center shadow-lg shadow-sky-200"
             style="background:linear-gradient(135deg,#0284C7,#0369A1)">
            <span class="absolute inset-0 rounded-2xl bg-sky-400 opacity-40 animate-ping" style="animation-duration:2.5s"></span>
            <svg class="relative w-8 h-8 sm:w-9 sm:h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>

        <div class="relative flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-base sm:text-lg font-bold text-gray-900">Pertemuan</span>
                <span class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">
                    Kelola jadwal
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Lihat, tambah, atau atur pertemuan kelas ini.</p>

            {{-- Chip statistik ditempel di sini, bukan kotak terpisah --}}
            <div class="flex items-center gap-2 flex-wrap mt-3"
                 x-data="{ m: 0, s: 0 }"
                 x-init="
                    let tm={{ $class->meetings()->count() }}, ts={{ $class->enrollments->count() }}, start=null;
                    const step=ts_=>{ if(!start)start=ts_; let p=Math.min((ts_-start)/700,1);
                        m=Math.floor(p*tm); s=Math.floor(p*ts);
                        if(p<1) requestAnimationFrame(step); else { m=tm; s=ts; } };
                    requestAnimationFrame(step)">
                <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold px-2.5 py-1 rounded-lg bg-white border border-sky-100 text-sky-700">
                    <span class="tabular-nums" x-text="m"></span> pertemuan
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold px-2.5 py-1 rounded-lg bg-white border border-indigo-100 text-[#4338CA]">
                    <span class="tabular-nums" x-text="s"></span> siswa
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold px-2.5 py-1 rounded-lg {{ $class->is_active ? 'bg-emerald-50 border border-emerald-100 text-emerald-600' : 'bg-gray-50 border border-gray-200 text-gray-400' }}">
                    <span class="relative flex w-1.5 h-1.5">
                        @if ($class->is_active)
                            <span class="absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70 animate-ping"></span>
                        @endif
                        <span class="relative inline-flex w-1.5 h-1.5 rounded-full {{ $class->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    </span>
                    {{ $class->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <svg class="relative w-5 h-5 text-sky-400 shrink-0 self-end sm:self-center transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    {{-- ============ Carousel menu lainnya — scroll-snap, bukan grid kaku ============ --}}
    <div class="-mx-4 sm:mx-0 px-4 sm:px-0">
        <div class="flex sm:grid sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 overflow-x-auto sm:overflow-visible snap-x snap-mandatory pb-1 sm:pb-0 scrollbar-none">
            @foreach ($menuItems as $i => $menu)
                @php $aksen = $aksenMenu[$i % count($aksenMenu)]; @endphp
                <a href="{{ $menu['route'] }}"
                   class="rise-in group relative overflow-hidden shrink-0 w-[42vw] xs:w-[38vw] sm:w-auto snap-start flex flex-col gap-3 {{ $aksen['wash'] }} border {{ $aksen['border'] }} rounded-2xl p-4 sm:p-5 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-indigo-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                   style="animation-delay: {{ 80 + $i * 70 }}ms">
                    <div class="absolute -top-6 -right-6 w-20 h-20 rounded-full opacity-[0.08] transition-transform duration-500 group-hover:scale-125 pointer-events-none"
                         style="background:{{ $aksen['solid'] }}"></div>

                    <div class="relative w-11 h-11 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shadow-md transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3"
                         style="background:{{ $aksen['solid'] }}">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="relative flex items-center justify-between gap-1">
                        <span class="text-xs sm:text-sm font-semibold text-gray-800">{{ $menu['label'] }}</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-300 group-hover:translate-x-1" style="color:{{ $aksen['solid'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
        {{-- indikator scroll, cuma muncul di mobile --}}
        <div class="flex sm:hidden justify-center gap-1 mt-2">
            @foreach ($menuItems as $i => $menu)
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-200"></span>
            @endforeach
        </div>
    </div>

    {{-- Daftar Siswa --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Daftar Siswa</h3>
            <span class="text-xs sm:text-sm text-gray-400">{{ $class->enrollments->count() }} siswa</span>
        </div>

        @if($class->enrollments->isEmpty())
        <div class="p-8 text-center text-gray-400 text-xs sm:text-sm">
            Belum ada siswa yang bergabung. Bagikan kode
            <span class="font-mono font-bold text-[#4338CA]">{{ $class->code }}</span>
            ke siswa.
        </div>
        @else

        {{-- Mobile: kartu bertumpuk --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($class->enrollments as $i => $enroll)
            <div class="p-4 flex items-center gap-3" x-data="{ removing: false }">
                <img src="{{ $enroll->student->user->avatar_url }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-50 shrink-0">
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900 text-sm truncate">{{ $enroll->student->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $enroll->student->nisn }} &middot; {{ $enroll->joined_at->format('d M Y') }}</p>
                </div>
                <button type="button"
                        @click="askRemove($root.querySelector('#remove-form-{{ $enroll->id }}'), '{{ $enroll->student->user->name }}')"
                        class="text-xs font-medium px-2.5 py-1 rounded-lg transition-colors duration-200" style="color:#EF4444; background:#FEE2E2">
                    Keluarkan
                </button>
                <form id="remove-form-{{ $enroll->id }}" method="POST" action="{{ route('guru.classes.remove-student', [$class, $enroll]) }}" class="hidden">
                    @csrf @method('DELETE')
                </form>
            </div>
            @endforeach
        </div>

        {{-- Desktop/tablet: tabel --}}
        <table class="w-full text-sm hidden md:table">
            <thead class="bg-indigo-50/60">
                <tr>
                    <th class="text-left px-5 py-3 font-medium text-[#4338CA] w-8 text-xs">#</th>
                    <th class="text-left px-5 py-3 font-medium text-[#4338CA] text-xs">Nama</th>
                    <th class="text-left px-5 py-3 font-medium text-[#4338CA] text-xs">NISN</th>
                    <th class="text-left px-5 py-3 font-medium text-[#4338CA] text-xs">Bergabung</th>
                    <th class="text-right px-5 py-3 font-medium text-[#4338CA] text-xs">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($class->enrollments as $i => $enroll)
                <tr class="hover:bg-indigo-50/40 transition">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $enroll->student->user->avatar_url }}" class="w-7 h-7 rounded-full object-cover ring-2 ring-indigo-50">
                            <span class="font-medium text-gray-900">{{ $enroll->student->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ $enroll->student->nisn }}</td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $enroll->joined_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <button type="button"
                                @click="askRemove($root.querySelector('#remove-form-desktop-{{ $enroll->id }}'), '{{ $enroll->student->user->name }}')"
                                class="text-xs font-medium hover:underline" style="color:#EF4444">
                            Keluarkan
                        </button>
                        <form id="remove-form-desktop-{{ $enroll->id }}" method="POST" action="{{ route('guru.classes.remove-student', [$class, $enroll]) }}" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- ============ MODAL: RESET KODE ============ --}}
    <div x-show="confirmReset" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmReset = false"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Reset Kode Kelas?</h3>
            <p class="text-sm text-gray-500 mb-5">
                Kode lama <strong class="font-mono">{{ $class->code }}</strong> tidak bisa dipakai lagi setelah direset. Siswa perlu kode baru untuk bergabung.
            </p>
            <div class="flex gap-3">
                <button type="button" @click="confirmReset = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all duration-200">
                    Batal
                </button>
                <form method="POST" action="{{ route('guru.classes.reset-code', $class) }}" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-all duration-200">
                        Ya, Reset
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL: KELUARKAN SISWA ============ --}}
    <div x-show="removeTarget !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="removeTarget = null"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 105.636 5.636a9 9 0 0012.728 12.728zM9 9l6 6m0-6l-6 6"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Keluarkan Siswa?</h3>
            <p class="text-sm text-gray-500 mb-5">
                <strong x-text="removeName"></strong> akan dikeluarkan dari kelas ini dan perlu bergabung ulang pakai kode kelas.
            </p>
            <div class="flex gap-3">
                <button type="button" @click="removeTarget = null"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all duration-200">
                    Batal
                </button>
                <button type="button" @click="removeTarget.submit()"
                        class="flex-1 px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-all duration-200">
                    Ya, Keluarkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection