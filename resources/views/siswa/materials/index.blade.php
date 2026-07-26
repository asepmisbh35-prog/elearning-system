{{-- resources/views/siswa/materials/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Materi — ' . $schoolClass->name)

@php
    // Warna status — tetap di rumpun Indigo/hijau Success/netral, sesuai token tema
    $warnaStatus = [
        'selesai' => ['bg' => '#D1FAE5', 'ring' => '#10B981', 'text' => '#047857'],
        'aktif'   => ['bg' => '#E0E7FF', 'ring' => '#4F46E5', 'text' => '#3730A3'],
        'kunci'   => ['bg' => '#F1F5F9', 'ring' => '#94A3B8', 'text' => '#64748B'],
    ];

    // Aksen per pertemuan — sekarang jadi "zona warna" (background wash + border kiri + lingkaran solid)
    // Tetap di rumpun biru-ungu (indigo/violet/sky/blue), bukan warna hangat
    $aksenPertemuan = [
        ['bg' => '#E0E7FF', 'ring' => '#4F46E5', 'text' => '#3730A3', 'line' => '#A5B4FC'], // indigo
        ['bg' => '#EDE9FE', 'ring' => '#7C3AED', 'text' => '#5B21B6', 'line' => '#C4B5FD'], // violet
        ['bg' => '#E0F2FE', 'ring' => '#0284C7', 'text' => '#075985', 'line' => '#7DD3FC'], // sky
        ['bg' => '#DBEAFE', 'ring' => '#2563EB', 'text' => '#1E40AF', 'line' => '#93C5FD'], // blue
        ['bg' => '#EEF2FF', 'ring' => '#6366F1', 'text' => '#4338CA', 'line' => '#818CF8'], // indigo muda
    ];

    $revealIndex = 0;
@endphp

@section('content')
<style>
    .reveal-item {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity .5s ease, transform .5s ease;
    }
    .reveal-item.reveal-in {
        opacity: 1;
        transform: translateY(0);
    }
    @media (prefers-reduced-motion: reduce) {
        .reveal-item { opacity: 1; transform: none; transition: none; }
        .path-node-pulse, .animate-bounce { animation: none !important; }
    }

    @keyframes gentle-float {
        0%, 100% { transform: translateY(0) rotate(-4deg); }
        50% { transform: translateY(-4px) rotate(-2deg); }
    }
    .compass-float { animation: gentle-float 3.5s ease-in-out infinite; }
    @media (prefers-reduced-motion: reduce) {
        .compass-float { animation: none; }
    }
</style>

<div class="max-w-3xl mx-auto px-4 sm:px-6 pt-6 sm:pt-8 pb-20">
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-indigo-300 via-violet-800 to-sky-400 border border-indigo-100/70 px-4 sm:px-6 py-5 sm:py-6 mb-6 sm:mb-8">

        {{-- dekorasi lingkaran blur, senada hero card lain --}}
        <div class="absolute -top-8 -right-6 w-32 h-32 rounded-full bg-white opacity-50 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white opacity-40 blur-2xl pointer-events-none"></div>

        {{-- doodle jalur samar di pojok, gema dari peta di bawahnya --}}
        <svg class="hidden sm:block absolute right-6 top-1/2 -translate-y-1/2 w-24 h-16 opacity-[0.12] pointer-events-none" viewBox="0 0 100 60" fill="none">
            <path d="M2 50 C 25 10, 45 55, 70 15 S 95 5, 98 8" stroke="#4F46E5" stroke-width="3" stroke-dasharray="5,7" stroke-linecap="round"/>
            <circle cx="98" cy="8" r="4" fill="#4F46E5"/>
        </svg>

        <a href="{{ route('siswa.classes.show', $schoolClass) }}"
           class="group relative inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-400 hover:text-[#4F46E5] transition-colors font-medium">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke kelas
        </a>

        <div class="relative flex items-start gap-3 mt-3">
            <span class="compass-float hidden sm:flex w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-gradient-to-br from-[#4F46E5] to-[#7C3AED] items-center justify-center text-xl md:text-2xl shrink-0 shadow-lg shadow-indigo-200">
                🧭
            </span>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Peta materi</h1>
                <p class="text-gray-500 text-xs sm:text-sm lg:text-base mt-0.5">{{ $schoolClass->name }} — {{ $schoolClass->subject }}</p>
            </div>
        </div>

        <p class="relative inline-flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-[#4338CA] bg-white/80 backdrop-blur px-3 py-1.5 rounded-full mt-3 shadow-sm">
            🌈 Tiap pertemuan punya warna jalurnya sendiri — ikuti terus sampai selesai!
        </p>

        {{-- Legenda status — ikonnya sengaja dibuat identik dengan node asli di peta --}}
        <div class="relative flex gap-2 flex-wrap mt-4">
            <span class="flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold pl-1.5 pr-3 py-1 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all"
                  style="background:{{ $warnaStatus['selesai']['bg'] }}; color:{{ $warnaStatus['selesai']['text'] }}">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-white text-[9px]" style="background:{{ $warnaStatus['selesai']['ring'] }}">✓</span>
                Selesai
            </span>
            <span class="flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold pl-1.5 pr-3 py-1 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all"
                  style="background:{{ $warnaStatus['aktif']['bg'] }}; color:{{ $warnaStatus['aktif']['text'] }}">
                <span class="relative w-4 h-4 rounded-full flex items-center justify-center" style="background:{{ $warnaStatus['aktif']['ring'] }}">
                    <span class="absolute inset-0 rounded-full animate-ping opacity-60" style="background:{{ $warnaStatus['aktif']['ring'] }}"></span>
                </span>
                Sedang di sini
            </span>
            <span class="flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold pl-1.5 pr-3 py-1 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all"
                  style="background:{{ $warnaStatus['kunci']['bg'] }}; color:{{ $warnaStatus['kunci']['text'] }}">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-white text-[8px]" style="background:{{ $warnaStatus['kunci']['ring'] }}">🔒</span>
                Terkunci
            </span>
        </div>
    </div>

    @if ($meetings->isEmpty())
        <div class="text-center py-14 sm:py-16 bg-indigo-50/50 rounded-2xl border border-indigo-100">
            <p class="text-2xl mb-2">🗺️</p>
            <p class="text-sm sm:text-base font-medium text-gray-700">Belum ada materi tersedia</p>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Materi akan muncul setelah guru mempublikasikannya.</p>
        </div>
    @else
        {{-- Legenda status --}}
        <div class="flex gap-2 flex-wrap mb-5 sm:mb-6">
            <span class="flex items-center gap-1.5 text-[11px] sm:text-xs font-medium px-2.5 py-1 rounded-full"
                  style="background:{{ $warnaStatus['selesai']['bg'] }}; color:{{ $warnaStatus['selesai']['text'] }}">
                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $warnaStatus['selesai']['ring'] }}"></span>Selesai
            </span>
            <span class="flex items-center gap-1.5 text-[11px] sm:text-xs font-medium px-2.5 py-1 rounded-full"
                  style="background:{{ $warnaStatus['aktif']['bg'] }}; color:{{ $warnaStatus['aktif']['text'] }}">
                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $warnaStatus['aktif']['ring'] }}"></span>Sedang di sini
            </span>
            <span class="flex items-center gap-1.5 text-[11px] sm:text-xs font-medium px-2.5 py-1 rounded-full"
                  style="background:{{ $warnaStatus['kunci']['bg'] }}; color:{{ $warnaStatus['kunci']['text'] }}">
                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $warnaStatus['kunci']['ring'] }}"></span>Terkunci
            </span>
        </div>

        <div class="path-map relative rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 overflow-hidden"
             style="background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 45%, #F0F9FF 100%)">
            {{-- dekorasi lingkaran blur, konsisten dengan hero card lain --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white opacity-40 blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 -left-10 w-40 h-40 rounded-full bg-white opacity-40 blur-2xl pointer-events-none"></div>

            <svg class="absolute inset-0 w-full h-full pointer-events-none" style="z-index:0" data-path-svg></svg>

            <div class="relative flex flex-col gap-4 sm:gap-5" style="z-index:1">
                @php $materialCounter = 0; @endphp
                @foreach ($meetings as $meeting)
                    @php $aksen = $aksenPertemuan[$loop->index % count($aksenPertemuan)]; @endphp

                    {{-- Zona per pertemuan — background wash + garis kiri pakai warna aksen, ini yang bikin tiap chapter kelihatan beda --}}
                    <div class="rounded-2xl border-l-4 p-3 sm:p-4 md:p-5"
                         style="background:{{ $aksen['bg'] }}66; border-left-color:{{ $aksen['ring'] }}">
                        <div class="flex flex-col gap-4 sm:gap-5">

                            {{-- Header pertemuan --}}
                            <div class="reveal-item flex items-center gap-3 sm:gap-4" style="transition-delay: {{ min($revealIndex++ * 60, 360) }}ms">
                                <div class="path-node-circle w-9 h-9 sm:w-10 sm:h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center text-xs sm:text-sm md:text-base font-bold shrink-0 shadow-md ring-4 ring-white"
                                     style="background:{{ $aksen['ring'] }}; color:#fff"
                                     data-line-color="{{ $aksen['ring'] }}77">
                                    {{ $meeting->order }}
                                </div>
                                <div class="min-w-0">
                                    <h2 class="font-semibold text-gray-900 text-sm sm:text-base lg:text-lg truncate">{{ $meeting->topic }}</h2>
                                    @if ($meeting->scheduled_at)
                                        <p class="text-[11px] sm:text-xs lg:text-sm text-gray-400">{{ $meeting->scheduled_at->translatedFormat('l, d F Y') }}</p>
                                    @endif
                                </div>
                            </div>

                            @forelse ($meeting->materials as $material)
                                @php
                                    $progress   = $material->progresses->first();
                                    $percentage = $progress?->progress_percent ?? 0;
                                    $completed  = $progress?->status === 'completed';
                                    $isLocked   = $material->isLockedFor($student);
                                    $isActive   = !$isLocked && !$completed;
                                    $statusKey  = $completed ? 'selesai' : ($isActive ? 'aktif' : 'kunci');
                                    $w          = $warnaStatus[$statusKey];
                                    $statusLabel = $completed ? 'Selesai' : ($isActive ? 'Sedang di sini' : 'Terkunci');

                                    // Geser horizontal mengikuti gelombang sinus supaya kurva mengalir natural.
                                    // Amplitudo dikecilkan di mobile (via CSS var) supaya kartu tidak meluber di layar sempit.
                                    $waveDesktop = round((sin($materialCounter * 0.9) + 1) / 2 * 16); // 0-16% di layar >= sm (dikecilkan krn ada padding zona)
                                    $waveMobile  = round($waveDesktop / 2);
                                    $materialCounter++;
                                @endphp

                                <div class="reveal-item flex items-center gap-3 sm:gap-4 ml-[var(--wm)] sm:ml-[var(--wd)]"
                                     style="--wm: {{ $waveMobile }}%; --wd: {{ $waveDesktop }}%; transition-delay: {{ min($revealIndex++ * 60, 360) }}ms">
                                    {{-- Node --}}
                                    <div class="relative shrink-0">
                                        @if ($isActive)
                                            <span class="path-node-pulse absolute inset-0 rounded-full animate-ping opacity-50" style="background:{{ $w['ring'] }}"></span>
                                            <span class="absolute -top-2.5 -right-2 z-20 text-[9px] font-bold text-white px-1.5 py-0.5 rounded-full whitespace-nowrap animate-bounce shadow-sm"
                                                  style="background:{{ $w['ring'] }}">
                                                Kamu di sini
                                            </span>
                                        @endif
                                        <div class="relative z-10 path-node-circle w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center border-2 transition-transform duration-200 {{ $isLocked ? '' : 'hover:scale-110' }}"
                                             style="background:{{ $w['bg'] }}; border-color:{{ $w['ring'] }}; box-shadow: 0 0 0 4px {{ $aksen['ring'] }}22"
                                             data-line-color="{{ $aksen['line'] }}">
                                            @if ($completed)
                                                <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" style="color:{{ $w['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @elseif ($isActive)
                                                <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full" style="background:{{ $w['ring'] }}"></span>
                                            @else
                                                <i class="ti ti-lock text-base sm:text-lg" style="color:{{ $w['text'] }}"></i>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Card --}}
                                    <div class="flex-1 min-w-0 max-w-[200px] sm:max-w-[260px] md:max-w-xs">
                                        @if ($isLocked)
                                            <div class="bg-white/70 border border-gray-200 rounded-xl px-3.5 py-2.5 sm:px-5 sm:py-3.5">
                                                <p class="text-[10px] sm:text-xs font-medium uppercase tracking-wide text-gray-400">Materi · {{ $statusLabel }}</p>
                                                <p class="text-xs sm:text-sm lg:text-base font-medium text-gray-400 mt-0.5 truncate">{{ $material->title }}</p>
                                                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Selesaikan materi sebelumnya untuk membuka ini</p>
                                            </div>
                                        @else
                                            <a href="{{ route('siswa.classes.materials.show', [$schoolClass, $meeting, $material]) }}"
                                               class="group block bg-white/90 rounded-xl border-2 px-3.5 py-2.5 sm:px-5 sm:py-3.5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                                               style="border-color:{{ $aksen['ring'] }}40">
                                                <div class="flex items-center justify-between gap-2">
                                                    <p class="text-[10px] sm:text-xs font-medium uppercase tracking-wide" style="color:{{ $w['text'] }}">Materi · {{ $statusLabel }}</p>
                                                    <span class="text-[10px] sm:text-xs text-gray-400 shrink-0">{{ $material->estimated_minutes }} mnt</span>
                                                </div>
                                                <p class="text-xs sm:text-sm lg:text-base font-medium text-gray-900 mt-0.5 truncate">{{ $material->title }}</p>
                                                @if (!$completed && $percentage > 0)
                                                    <div class="w-full bg-gray-100 rounded-full h-1 overflow-hidden mt-2">
                                                        <div class="h-1 rounded-full transition-[width] duration-700 ease-out"
                                                             style="width: 0%; background:{{ $w['ring'] }}"
                                                             x-data x-init="setTimeout(() => $el.style.width = '{{ $percentage }}%', 300)"></div>
                                                    </div>
                                                @endif
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="reveal-item text-xs sm:text-sm text-gray-400 italic ml-14 sm:ml-16">Belum ada materi untuk pertemuan ini.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function drawPathLines(firstRun) {
    const map = document.querySelector('.path-map');
    if (!map) return;
    const svg = map.querySelector('svg[data-path-svg]');
    const nodes = Array.from(map.querySelectorAll('.path-node-circle'));
    if (!svg || nodes.length < 2) return;

    const mapRect = map.getBoundingClientRect();
    svg.innerHTML = '';
    svg.setAttribute('width', mapRect.width);
    svg.setAttribute('height', mapRect.height);
    svg.setAttribute('viewBox', `0 0 ${mapRect.width} ${mapRect.height}`);

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    for (let i = 0; i < nodes.length - 1; i++) {
        const a = nodes[i].getBoundingClientRect();
        const b = nodes[i + 1].getBoundingClientRect();

        const x1 = a.left + a.width / 2 - mapRect.left;
        const y1 = a.top + a.height / 2 - mapRect.top;
        const x2 = b.left + b.width / 2 - mapRect.left;
        const y2 = b.top + b.height / 2 - mapRect.top;

        const color = nodes[i].dataset.lineColor || '#d1d5db';

        const midY = (y1 + y2) / 2;
        const d = `M ${x1} ${y1} C ${x1} ${midY}, ${x2} ${midY}, ${x2} ${y2}`;

        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', d);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', color);
        path.setAttribute('stroke-width', '2');
        path.setAttribute('stroke-dasharray', '5,6');
        path.setAttribute('stroke-linecap', 'round');

        if (firstRun && !reduceMotion) {
            path.style.opacity = '0';
            path.style.transition = 'opacity .45s ease';
            path.style.transitionDelay = `${Math.min(i * 70, 400)}ms`;
            svg.appendChild(path);
            requestAnimationFrame(() => requestAnimationFrame(() => { path.style.opacity = '1'; }));
        } else {
            svg.appendChild(path);
        }
    }
}

let redrawTimer;
function scheduleRedraw() {
    clearTimeout(redrawTimer);
    redrawTimer = setTimeout(() => drawPathLines(false), 80);
}

function setupRevealOnScroll() {
    const items = document.querySelectorAll('.reveal-item');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach(el => el.classList.add('reveal-in'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    items.forEach(el => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
    drawPathLines(true);
    setupRevealOnScroll();
    window.addEventListener('load', () => drawPathLines(false));
    window.addEventListener('resize', scheduleRedraw);

    if (window.ResizeObserver) {
        const map = document.querySelector('.path-map');
        if (map) new ResizeObserver(scheduleRedraw).observe(map);
    }
});
</script>
@endpush
@endsection