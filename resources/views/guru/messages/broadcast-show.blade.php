{{-- resources/views/guru/messages/broadcast-show.blade.php --}}
@extends('layouts.app')
@section('title', 'Status Siaran')

@section('content')
@php
    $total = $readStatus->count();
    $readCount = $readStatus->where('read', true)->count();
    $pct = $total > 0 ? round(($readCount / $total) * 100) : 0;
    $circumference = 2 * pi() * 40; // r=40
    $offset = $circumference - ($pct / 100) * $circumference;
@endphp
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.messages.index') }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
            <h1 class="text-xl md:text-2xl font-bold mt-2">{{ $conversation->title }}</h1>
            <p class="text-indigo-100 text-sm md:text-base">{{ $conversation->schoolClass->name ?? '' }}</p>
        </div>
    </div>

    {{-- Isi Pesan --}}
    <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-4 md:p-5 mb-6 rise-in" style="animation-delay: 60ms">
        <p class="text-xs font-semibold text-[#4338CA] bg-indigo-50/60 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md mb-2">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
            Pesan Siaran
        </p>
        <p class="text-sm text-gray-700">{{ $firstMessage->content ?? '-' }}</p>
    </div>

    {{-- Featured: Progres Baca (radial ring) --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] to-[#3730A3] p-5 md:p-6 text-white mb-6 rise-in" style="animation-delay: 100ms">
        <div class="absolute -bottom-8 -left-8 w-40 h-40 rounded-full bg-white/5"></div>
        <div class="relative flex items-center gap-5">
            <div class="relative w-24 h-24 md:w-28 md:h-28 shrink-0">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 96 96">
                    <circle cx="48" cy="48" r="40" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="8" />
                    <circle cx="48" cy="48" r="40" fill="none" stroke="white" stroke-width="8"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circumference }}"
                            style="stroke-dashoffset: {{ $circumference }}; animation: draw-ring 1.1s cubic-bezier(.16,1,.3,1) forwards .1s;"
                            data-final-offset="{{ $offset }}" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl md:text-2xl font-bold" x-data="{ n: 0 }" x-init="let target={{ $pct }}; let step=Math.max(1,Math.round(target/30)); let iv=setInterval(()=>{ n=Math.min(target, n+step); if(n>=target) clearInterval(iv); },20)" x-text="n + '%'"></span>
                </div>
            </div>
            <div>
                <p class="font-semibold text-base md:text-lg">Progres Baca</p>
                <p class="text-indigo-100 text-sm mt-0.5">{{ $readCount }} dari {{ $total }} siswa sudah membaca</p>
                <div class="mt-3 flex gap-2">
                    <span class="bg-white/10 border border-white/15 rounded-lg px-2.5 py-1 text-xs font-semibold">{{ $readCount }} Dibaca</span>
                    <span class="bg-white/10 border border-white/15 rounded-lg px-2.5 py-1 text-xs font-semibold">{{ $total - $readCount }} Belum</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Status Baca --}}
    <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl divide-y divide-gray-100 overflow-hidden rise-in" style="animation-delay: 140ms">
        <div class="px-4 md:px-5 py-3 bg-indigo-50/60">
            <h2 class="font-semibold text-[#4338CA] text-sm">Status Baca Siswa</h2>
        </div>
        @forelse ($readStatus as $i => $r)
            <div class="px-4 md:px-5 py-3 flex items-center gap-3 hover:bg-indigo-50/40 transition-colors duration-150 rise-in" style="animation-delay: {{ min($i, 15) * 30 }}ms">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#4F46E5] to-[#818CF8] text-white flex items-center justify-center text-xs font-semibold shrink-0">
                    {{ strtoupper(substr($r['user']->name ?? '-', 0, 1)) }}
                </div>
                <span class="text-sm text-gray-700 flex-1 truncate">{{ $r['user']->name ?? '-' }}</span>
                @if ($r['read'])
                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5M2.25 12.75l6 6" />
                        </svg>
                        Dibaca
                    </span>
                @else
                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Belum dibaca</span>
                @endif
            </div>
        @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">Belum ada peserta.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes draw-ring {
    to { stroke-dashoffset: var(--final-offset); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('circle[data-final-offset]').forEach(el => {
        el.style.setProperty('--final-offset', el.dataset.finalOffset);
    });
});
</script>
@endsection