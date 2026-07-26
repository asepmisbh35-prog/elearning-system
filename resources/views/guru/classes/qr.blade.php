@extends('layouts.app')
@section('title', 'QR Code — ' . $class->name)

@section('content')
<div class="max-w-md mx-auto space-y-5 md:space-y-6"
     x-data="{
        copied: false,
        confirmReset: false,
        copyCode() {
            navigator.clipboard.writeText('{{ $class->code }}');
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        }
     }">

    {{-- ============ HEADER ============ --}}
    <div class="flex items-center gap-3" x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)">
        <a href="{{ route('guru.classes.show', $class) }}"
           class="group flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div x-show="show" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">QR Code Kelas</h2>
            <p class="text-xs md:text-sm text-gray-500">{{ $class->name }}</p>
        </div>
    </div>

    {{-- ============ HERO CARD (mini, di atas QR) ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-6 text-white shadow-lg shadow-indigo-200/60"
         x-show="true" x-transition:enter="transition ease-out duration-500 delay-100" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0">

        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white opacity-[0.08] animate-[pulse_6s_ease-in-out_infinite]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>

        <div class="relative flex items-center gap-3">
            <div class="flex-shrink-0 w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-4h2m-2 0v-2m6-4h2m-6 0h-2m6 0v-2m-6 6v2m-4-6H4m4 0v2m0-2V4m6 12h.01M17 4h2v2h-2V4zM4 4h2v2H4V4zm0 12h2v2H4v-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs md:text-sm text-indigo-100">Bagikan ke siswa</p>
                <p class="text-sm md:text-base font-semibold">Scan atau salin kode di bawah</p>
            </div>
        </div>
    </div>

    {{-- ============ QR CARD ============ --}}
    <div class="bg-white rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm p-6 md:p-8 text-center space-y-5"
         x-show="true" x-transition:enter="transition ease-out duration-500 delay-200" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- QR Code SVG dari server --}}
        <div class="flex justify-center">
            <div class="p-4 border-2 border-indigo-100 rounded-2xl bg-white transition-transform duration-300 hover:scale-[1.03] hover:shadow-lg hover:shadow-indigo-100">
                {!! $qr !!}
            </div>
        </div>

        {{-- Kode teks besar, klik buat copy --}}
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1.5">
                Kode Kelas
            </p>
            <button type="button" @click="copyCode()"
                    class="group inline-flex items-center gap-2.5 px-2 py-1 rounded-xl hover:bg-indigo-50 transition-colors duration-200">
                <span class="text-2xl md:text-3xl font-mono font-bold text-[#4F46E5] tracking-widest">
                    {{ $class->code }}
                </span>
                <span class="relative w-5 h-5 flex-shrink-0">
                    <svg x-show="!copied" class="w-5 h-5 text-gray-300 group-hover:text-[#4F46E5] transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg x-show="copied" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                         class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
            </button>
            <p class="text-[11px] mt-1 h-4 transition-opacity duration-200" :class="copied ? 'opacity-100 text-[#10B981]' : 'opacity-0'">
                Kode disalin!
            </p>
        </div>

        <p class="text-sm text-gray-500">
            Tampilkan QR Code ke siswa, atau bagikan kode di atas.
        </p>

        {{-- Tombol --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center pt-2">
            <button onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA]
                           text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-md shadow-indigo-200
                           hover:shadow-lg hover:shadow-indigo-300 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a1 1 0 00-1-1H8a1 1 0 00-1 1v4h10z"/>
                </svg>
                Print QR Code
            </button>

            {{-- Reset kode dengan konfirmasi animasi (bukan native confirm) --}}
            <button type="button" @click="confirmReset = true"
                    class="inline-flex items-center justify-center gap-2 border border-gray-200 text-gray-600
                           hover:bg-gray-50 hover:border-indigo-200 text-sm font-medium px-4 py-2.5 rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset Kode
            </button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3"
                 x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)"
                 x-show="show" x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- ============ MODAL KONFIRMASI RESET (custom, animatif) ============ --}}
    <div x-show="confirmReset" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmReset = false"></div>

        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-show="confirmReset"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
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
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-all duration-200">
                        Ya, Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
  nav, aside, header, footer { display: none !important; }
}
[x-cloak] { display: none !important; }
</style>
@endsection