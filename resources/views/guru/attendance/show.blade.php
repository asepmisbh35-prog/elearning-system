{{-- resources/views/guru/attendance/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Absensi — ' . $meeting->topic)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6 md:py-8">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] text-white p-5 sm:p-6 mb-6">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white opacity-[0.08] blur-xl"></div>
        <div class="absolute -bottom-10 -left-10 w-36 h-36 rounded-full bg-white opacity-[0.06] blur-xl"></div>
        <div class="relative">
            <a href="{{ route('guru.classes.meetings.index', $class) }}"
               class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-indigo-100 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke pertemuan
            </a>
            <h1 class="text-xl sm:text-2xl font-bold mt-2">Absensi</h1>
            <p class="text-indigo-100 text-xs sm:text-sm mt-0.5">{{ $class->name }} — Pertemuan {{ $meeting->order }}: {{ $meeting->topic }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ═══ Sesi aktif ═══ --}}
    @if ($currentSession)
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 mb-6">
            <div class="flex items-start justify-between gap-3 mb-4 flex-wrap">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Kode Absensi</p>
                    <div class="flex items-center gap-2"
                         x-data="{ copied: false }">
                        <span class="inline-block bg-indigo-50 text-[#4338CA] text-2xl sm:text-3xl font-bold font-mono tracking-widest px-3 sm:px-4 py-1.5 rounded-xl">
                            {{ $currentSession->code }}
                        </span>
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $currentSession->code }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                class="text-xs sm:text-sm font-medium px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:border-indigo-200 hover:text-[#4F46E5] hover:bg-indigo-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                                x-text="copied ? '✓ Tersalin' : 'Salin'">
                        </button>
                    </div>
                </div>
                <span class="flex items-center gap-1.5 text-xs px-3 py-1 rounded-full font-medium bg-emerald-50 text-emerald-600 shrink-0">
                    <span class="relative flex w-1.5 h-1.5">
                        <span class="absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-60 animate-ping"></span>
                        <span class="relative inline-flex w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    </span>
                    Sesi Aktif
                </span>
            </div>

            <p class="text-xs sm:text-sm text-gray-500 mb-1">
                Check-in: {{ $currentSession->check_in_start->format('H:i') }} — {{ $currentSession->check_in_end->format('H:i') }}
            </p>

            {{-- Countdown waktu check-in --}}
            <div class="mb-5"
                 x-data="{
                    target: {{ $currentSession->check_in_end->timestamp }} * 1000,
                    now: Date.now(),
                    init() { setInterval(() => this.now = Date.now(), 1000) },
                    get remainingMs() { return this.target - this.now },
                    get expired() { return this.remainingMs <= 0 },
                    get label() {
                        if (this.expired) return 'Waktu check-in sudah habis';
                        const s = Math.floor(this.remainingMs / 1000);
                        const m = Math.floor(s / 60), sec = s % 60;
                        return m + ':' + String(sec).padStart(2, '0') + ' tersisa';
                    }
                 }">
                <span class="text-xs sm:text-sm font-semibold"
                      :class="expired ? 'text-red-600' : (remainingMs < 5*60*1000 ? 'text-amber-600' : 'text-emerald-600')"
                      x-text="label"></span>
            </div>

            <div class="flex items-center gap-3 mb-6 flex-wrap">
                <form method="POST" action="{{ route('guru.attendance-sessions.extend', $currentSession) }}" class="flex items-center gap-2">
                    @csrf @method('PATCH')
                    <input type="time" name="check_in_end" value="{{ $currentSession->check_in_end->format('H:i') }}"
                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <button type="submit" class="text-sm bg-gray-800 text-white px-4 py-1.5 rounded-lg hover:bg-gray-900 hover:-translate-y-0.5 active:scale-95 transition-all font-medium">
                        Perpanjang
                    </button>
                </form>
                <form method="POST" action="{{ route('guru.attendance-sessions.close', $currentSession) }}" onsubmit="return confirm('Tutup sesi absensi sekarang?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-sm bg-[#EF4444] text-white px-4 py-1.5 rounded-lg hover:bg-red-600 hover:-translate-y-0.5 active:scale-95 transition-all font-medium">
                        Tutup Sesi
                    </button>
                </form>
            </div>

            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-indigo-50/50 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Daftar Kehadiran</span>
                    <span class="text-xs text-gray-400">{{ $attendances->count() }} siswa</span>
                </div>
                @foreach ($attendances as $attendance)
                    @include('guru.attendance._attendance-row', ['attendance' => $attendance, 'closed' => false])
                @endforeach
            </div>
        </div>
    @else
        {{-- Tidak ada sesi aktif — tombol buka sesi baru --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 mb-6">
            <h2 class="font-semibold text-gray-800 mb-4">Buka Sesi Absensi Baru</h2>
            <form method="POST" action="{{ route('guru.classes.meetings.attendance.store', [$class, $meeting]) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Mulai</label>
                        <input type="time" name="check_in_start" value="{{ now()->format('H:i') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Selesai</label>
                        <input type="time" name="check_in_end" value="{{ now()->addMinutes(15)->format('H:i') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <p class="text-xs text-gray-400">Semua siswa di kelas ini otomatis tercatat "Alfa" sampai mereka check-in.</p>
                <button type="submit" class="bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-6 py-2 rounded-lg hover:shadow-lg hover:shadow-indigo-200/60 hover:-translate-y-0.5 active:scale-95 transition-all font-medium">
                    Buka Sesi
                </button>
            </form>
        </div>
    @endif

    {{-- ═══ Riwayat sesi yang sudah ditutup ═══ --}}
    @if ($pastSessions->isNotEmpty())
        <div class="space-y-3">
            <h2 class="font-semibold text-gray-700 text-sm">Riwayat Sesi Sebelumnya</h2>
            @foreach ($pastSessions as $past)
                @php
                    $countHadir = $past->attendances->where('status', 'hadir')->count();
                    $countBermasalah = $past->attendances->whereIn('status', ['izin', 'sakit', 'alfa'])->count();
                @endphp
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                            class="w-full px-4 sm:px-5 py-3 flex items-center justify-between gap-3 text-left hover:bg-indigo-50/40 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-inset">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-sm font-medium text-gray-700">{{ $past->code }}</span>
                                <span class="text-xs text-gray-400">
                                    {{ $past->created_at->translatedFormat('d M Y, H:i') }} — ditutup {{ $past->closed_at?->format('H:i') }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                <span class="text-emerald-600 font-medium">{{ $countHadir }} hadir</span>
                                @if ($countBermasalah)
                                    · <span class="text-amber-600 font-medium">{{ $countBermasalah }} lainnya</span>
                                @endif
                            </p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-t border-gray-100">
                        @foreach ($past->attendances as $attendance)
                            @include('guru.attendance._attendance-row', ['attendance' => $attendance, 'closed' => true])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection