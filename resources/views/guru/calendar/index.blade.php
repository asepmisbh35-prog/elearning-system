{{-- resources/views/guru/calendar/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Kalender')

@section('content')
<div class="max-w-5xl mx-auto px-4 md:px-6 py-6 md:py-8"
     x-data="{
        selectedDay: null,
        showModal: false,
        activeFilter: null,
        toggleFilter(type) { this.activeFilter = (this.activeFilter === type) ? null : type },
        matches(types) { return !this.activeFilter || types.includes(this.activeFilter) }
     }">

    {{-- ===== HERO ===== --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-8 text-white mb-5 md:mb-8">
        <div class="absolute -top-10 -right-10 w-44 h-44 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex items-start justify-between flex-wrap gap-4">
            <div>
                <p class="text-indigo-200 text-[11px] md:text-sm font-medium mb-1 tracking-wide uppercase">Jadwal Mengajar</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold flex items-center gap-2">
                    <span>🗓️</span> Kalender Mengajar
                </h1>
                <p class="text-indigo-100 text-xs sm:text-sm mt-1 max-w-sm">
                    Pantau tugas, kuis, dan pertemuan di semua kelas yang kamu ajar.
                </p>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <a href="{{ route('guru.calendar.index', ['month' => $start->copy()->subMonth()->month, 'year' => $start->copy()->subMonth()->year]) }}"
                   class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/25 active:scale-95 transition text-white text-sm">‹</a>
                <span class="font-semibold w-28 sm:w-36 md:w-40 text-center text-xs sm:text-sm md:text-base">
                    {{ $start->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('guru.calendar.index', ['month' => $start->copy()->addMonth()->month, 'year' => $start->copy()->addMonth()->year]) }}"
                   class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/25 active:scale-95 transition text-white text-sm">›</a>
            </div>
        </div>

        {{-- Filter interaktif per tipe --}}
        <div class="relative flex flex-wrap gap-2 mt-5 md:mt-6">
            <button type="button" @click="activeFilter = null"
                    :class="activeFilter === null ? 'bg-white text-[#4338CA]' : 'bg-white/15 text-white hover:bg-white/25'"
                    class="text-[11px] sm:text-xs md:text-sm font-semibold px-3 py-1.5 rounded-full transition">
                Semua
            </button>
            <button type="button" @click="toggleFilter('tugas')"
                    :class="activeFilter === 'tugas' ? 'bg-white text-amber-600 ring-2 ring-amber-300' : 'bg-white/15 text-white hover:bg-white/25'"
                    class="flex items-center gap-1.5 text-[11px] sm:text-xs md:text-sm font-medium px-3 py-1.5 rounded-full transition">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Tugas
            </button>
            <button type="button" @click="toggleFilter('kuis')"
                    :class="activeFilter === 'kuis' ? 'bg-white text-violet-600 ring-2 ring-violet-300' : 'bg-white/15 text-white hover:bg-white/25'"
                    class="flex items-center gap-1.5 text-[11px] sm:text-xs md:text-sm font-medium px-3 py-1.5 rounded-full transition">
                <span class="w-2 h-2 rounded-full bg-violet-400"></span> Kuis
            </button>
            <button type="button" @click="toggleFilter('pertemuan')"
                    :class="activeFilter === 'pertemuan' ? 'bg-white text-sky-600 ring-2 ring-sky-300' : 'bg-white/15 text-white hover:bg-white/25'"
                    class="flex items-center gap-1.5 text-[11px] sm:text-xs md:text-sm font-medium px-3 py-1.5 rounded-full transition">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span> Pertemuan
            </button>
        </div>
    </div>

    {{-- ===== KALENDER ===== --}}
    <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl overflow-hidden shadow-sm">
        <div class="grid grid-cols-7 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white text-[10px] sm:text-xs md:text-sm font-semibold">
            @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $d)
                <div class="py-2 md:py-2.5 text-center">{{ $d }}</div>
            @endforeach
        </div>

        @foreach ($weeks as $week)
            <div class="grid grid-cols-7 border-t border-gray-100">
                @foreach ($week as $day)
                    @php $dayEvents = $day['events']; @endphp
                    <button
                        type="button"
                        @if ($dayEvents->count()) @click="selectedDay = '{{ $day['date'] }}'; showModal = true" @endif
                        :class="!matches({{ Js::from($dayEvents->pluck('type')->unique()->values()) }}) && {{ $dayEvents->count() }} > 0 ? 'opacity-30 grayscale' : ''"
                        class="group relative h-16 sm:h-20 md:h-24 p-1 sm:p-1.5 md:p-2 border-r border-b border-gray-100 last:border-r-0 text-left align-top transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:z-10
                               {{ $day['inMonth'] ? 'bg-white hover:bg-indigo-50/70' : 'bg-gray-50 text-gray-300' }}
                               {{ $day['isToday'] ? 'ring-2 ring-inset ring-[#4F46E5]' : '' }}
                               {{ $dayEvents->count() === 0 ? 'cursor-default' : 'cursor-pointer hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-200/50 hover:z-10' }}">
                        <span class="inline-flex items-center justify-center text-[11px] sm:text-xs md:text-sm font-medium w-5 h-5 sm:w-6 sm:h-6 rounded-full
                            {{ $day['isToday'] ? 'bg-[#4F46E5] text-white font-bold' : '' }}">
                            {{ $day['day'] }}
                        </span>
                        <div class="flex flex-wrap gap-1 mt-1 sm:mt-1.5">
                            @foreach ($dayEvents->take(3) as $ev)
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full transition-transform group-hover:scale-125
                                    {{ $ev['type'] === 'tugas' ? 'bg-amber-400' : ($ev['type'] === 'kuis' ? 'bg-violet-500' : 'bg-sky-500') }}"></span>
                            @endforeach
                            @if ($dayEvents->count() > 3)
                                <span class="text-[9px] sm:text-[10px] text-gray-400 leading-none">+{{ $dayEvents->count() - 3 }}</span>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>

    @if (collect($weeks)->flatten(1)->sum(fn($d) => $d['events']->count()) === 0)
        <div class="flex flex-col items-center text-center mt-8 md:mt-10">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-indigo-50 flex items-center justify-center text-2xl md:text-3xl mb-3">
                📭
            </div>
            <p class="text-sm text-gray-400">Belum ada tugas, kuis, atau pertemuan terjadwal bulan ini.</p>
        </div>
    @endif

    {{-- Modal detail hari --}}
    <div x-show="showModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="showModal = false"
         @keydown.escape.window="showModal = false"
         class="fixed inset-0 bg-black/40 backdrop-blur-[2px] flex items-end sm:items-center justify-center p-0 sm:p-4 z-50">
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="bg-white rounded-t-3xl sm:rounded-2xl md:rounded-3xl max-w-md w-full p-5 md:p-6 max-h-[85vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900 text-sm md:text-base" x-text="selectedDay"></h2>
                <button @click="showModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 text-xl leading-none transition">
                    &times;
                </button>
            </div>

            @foreach ($weeks as $week)
                @foreach ($week as $day)
                    @if ($day['events']->count())
                        <template x-if="selectedDay === '{{ $day['date'] }}'">
                            <div class="space-y-3">
                                @foreach ($day['events'] as $ev)
                                    @php
                                        $palette = match ($ev['type']) {
                                            'tugas' => ['bg-amber-50', 'text-amber-600', '📝'],
                                            'kuis'  => ['bg-violet-50', 'text-violet-600', '🧠'],
                                            default => ['bg-sky-50', 'text-sky-600', '👨‍🏫'],
                                        };
                                    @endphp
                                    <a href="{{ $ev['url'] ?? '#' }}"
                                       class="block border border-gray-100 rounded-xl md:rounded-2xl p-3 transition-all
                                              {{ $ev['url'] ? 'hover:shadow-md hover:shadow-indigo-100 hover:-translate-y-0.5 hover:border-indigo-100' : 'pointer-events-none opacity-70' }}">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="w-7 h-7 rounded-lg {{ $palette[0] }} flex items-center justify-center text-sm">
                                                {{ $palette[2] }}
                                            </span>
                                            <span class="text-[11px] font-semibold uppercase tracking-wide {{ $palette[1] }}">
                                                {{ ucfirst($ev['type']) }} · {{ $ev['time'] }}
                                            </span>
                                        </div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $ev['title'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $ev['class'] }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </template>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
</div>
@endsection