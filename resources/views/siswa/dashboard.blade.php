@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

<div class="max-w-5xl mx-auto px-4 pb-24 lg:pb-10"
     x-data="siswaDashboard({
        progressUrl: @js(route('siswa.dashboard.progress')),
        heartbeatUrl: @js(route('siswa.dashboard.heartbeat')),
        classesUrl: @js(route('siswa.classes.index')),
     })"
     x-init="init()">

    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-6 sm:p-8 text-white shadow-lg shadow-indigo-200/50">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08] animate-[pulse_6s_ease-in-out_infinite]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <p class="text-xs text-indigo-200 font-medium tracking-wide">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-1 flex items-center gap-2">
                Halo, {{ explode(' ', auth()->user()->name)[0] }}
                <span class="inline-block animate-[wave_1.8s_ease-in-out_infinite]">👋</span>
            </h1>
            <p class="mt-2 text-indigo-100 max-w-lg text-sm">
                <template x-if="!loading">
                    <span>Progres semester kamu <span class="font-bold text-white" x-text="overall + '%'"></span>. Terus lanjutkan!</span>
                </template>
                <template x-if="loading">
                    <span class="inline-block h-4 w-40 bg-white/20 rounded-full animate-pulse"></span>
                </template>
            </p>
            <div class="mt-5 flex gap-3 flex-wrap">
                <a href="{{ route('siswa.classes.index') }}"
                   class="inline-flex items-center gap-2 bg-white text-[#4338CA] text-sm font-bold px-4 py-2.5 rounded-xl hover:scale-105 hover:shadow-lg transition-all duration-200">
                    <i class="ti ti-player-play-filled text-base" aria-hidden="true"></i> Lanjutkan belajar
                </a>
                <a href="{{ route('siswa.classes.index') }}"
                   class="inline-flex items-center gap-2 border border-white/40 text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-white/15 hover:border-white/60 transition-all duration-200">
                    <i class="ti ti-books text-base" aria-hidden="true"></i> Semua kelas
                </a>
            </div>
        </div>
    </div>

    {{-- Indikator live --}}
    <div class="flex items-center gap-2 text-xs text-gray-400 px-1 mt-4">
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#10B981] opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#10B981]"></span>
        </span>
        <span>Update otomatis &middot; terakhir <span x-text="lastUpdated || '...'" class="font-medium text-gray-500"></span></span>
    </div>

    {{-- RINGKASAN --}}
    <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="group relative rounded-2xl p-4 border border-gray-100 bg-gradient-to-br from-indigo-50 to-white overflow-hidden hover:shadow-md hover:shadow-indigo-100/60 hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute -bottom-6 -right-6 w-20 h-20 rounded-full bg-indigo-100/50 blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative w-10 h-10 rounded-xl flex items-center justify-center text-white bg-gradient-to-br from-[#4F46E5] to-[#818CF8] shadow-md shadow-indigo-300/50 group-hover:rotate-6 transition-transform duration-300">
                <i class="ti ti-flame text-lg" aria-hidden="true"></i>
            </div>
            <p class="relative text-xs text-gray-400 mt-3 font-medium">Rangkaian harian</p>
            <p class="relative text-2xl font-extrabold text-gray-800"><span x-text="streak"></span> <span class="text-sm font-normal text-gray-400">hari</span></p>
        </div>

        <div class="group relative rounded-2xl p-4 border border-gray-100 bg-gradient-to-br from-violet-50 to-white overflow-hidden hover:shadow-md hover:shadow-violet-100/60 hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute -bottom-6 -right-6 w-20 h-20 rounded-full bg-violet-100/50 blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative w-10 h-10 rounded-xl flex items-center justify-center text-white bg-gradient-to-br from-[#7C3AED] to-[#C4B5FD] shadow-md shadow-violet-300/50 group-hover:rotate-6 transition-transform duration-300">
                <i class="ti ti-medal text-lg" aria-hidden="true"></i>
            </div>
            <p class="relative text-xs text-gray-400 mt-3 font-medium">Stempel</p>
            <p class="relative text-2xl font-extrabold text-gray-800"><span x-text="badges.earned.length"></span> <span class="text-sm font-normal text-gray-400">terkumpul</span></p>
        </div>

        <div class="group relative rounded-2xl p-4 border border-gray-100 bg-gradient-to-br from-sky-50 to-white overflow-hidden hover:shadow-md hover:shadow-sky-100/60 hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute -bottom-6 -right-6 w-20 h-20 rounded-full bg-sky-100/50 blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative w-10 h-10 rounded-xl flex items-center justify-center text-white bg-gradient-to-br from-[#0284C7] to-[#7DD3FC] shadow-md shadow-sky-300/50 group-hover:rotate-6 transition-transform duration-300">
                <i class="ti ti-compass text-lg" aria-hidden="true"></i>
            </div>
            <p class="relative text-xs text-gray-400 mt-3 font-medium">Mapel diikuti</p>
            <p class="relative text-2xl font-extrabold text-gray-800">{{ $totalSubjects }}</p>
        </div>

        <div class="group relative rounded-2xl p-4 border border-gray-100 bg-gradient-to-br from-blue-50 to-white overflow-hidden hover:shadow-md hover:shadow-blue-100/60 hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute -bottom-6 -right-6 w-20 h-20 rounded-full bg-blue-100/50 blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative w-10 h-10 rounded-xl flex items-center justify-center text-white bg-gradient-to-br from-[#2563EB] to-[#93C5FD] shadow-md shadow-blue-300/50 group-hover:rotate-6 transition-transform duration-300">
                <i class="ti ti-trending-up text-lg" aria-hidden="true"></i>
            </div>
            <p class="relative text-xs text-gray-400 mt-3 font-medium">Progres semester</p>
            <p class="relative text-2xl font-extrabold text-gray-800" x-text="overall + '%'"></p>
        </div>
    </div>

    {{-- KEMAJUAN BELAJAR --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-[1fr_1.4fr] gap-4 md:gap-5">

        {{-- Progres Akumulasi (donut) --}}
        <div class="relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm shadow-indigo-100/40 overflow-hidden hover:shadow-lg hover:shadow-indigo-100/60 transition-shadow duration-300">
            <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 opacity-50 blur-2xl"></div>

            <p class="relative text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                <span class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#4F46E5] to-[#818CF8] flex items-center justify-center shadow-sm shadow-indigo-300/50">
                    <i class="ti ti-chart-donut-3 text-[13px] text-white" aria-hidden="true"></i>
                </span>
                Progres Akumulasi
            </p>

            <div class="relative w-40 h-40 md:w-48 md:h-48 mx-auto">
                <canvas id="overallChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-3xl md:text-4xl font-extrabold bg-gradient-to-br from-[#4F46E5] to-[#818CF8] bg-clip-text text-transparent" x-text="overall + '%'"></span>
                    <span class="text-xs text-gray-400 mt-0.5 font-medium">rata-rata nilai</span>
                </div>
            </div>
            <p class="relative text-xs text-gray-400 text-center mt-4">Dihitung dari nilai seluruh mata pelajaran yang kamu ikuti</p>
        </div>

        {{-- Progres per Mapel (bar) --}}
        <div class="relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm shadow-indigo-100/40 overflow-hidden hover:shadow-lg hover:shadow-sky-100/60 transition-shadow duration-300">
            <div class="absolute -bottom-14 -left-14 w-40 h-40 rounded-full bg-gradient-to-br from-sky-100 to-blue-100 opacity-50 blur-2xl"></div>

            <p class="relative text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                <span class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#2563EB] to-[#93C5FD] flex items-center justify-center shadow-sm shadow-blue-300/50">
                    <i class="ti ti-chart-bar text-[13px] text-white" aria-hidden="true"></i>
                </span>
                Progres per Mata Pelajaran
            </p>

            <template x-if="!loading && subjects.length === 0">
                <div class="relative text-center py-10">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-indigo-50 to-violet-50 flex items-center justify-center mb-2">
                        <i class="ti ti-chart-bar text-xl text-indigo-300" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm text-gray-400">Belum ada kelas yang diikuti.</p>
                </div>
            </template>

            <div class="relative h-52 md:h-60">
                <canvas id="subjectChart"></canvas>
            </div>
        </div>
    </div>

    {{-- RUTE PENJELAJAHAN --}}
    <div class="mt-8">
        <div class="flex justify-between items-baseline">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-route text-[#4F46E5]" aria-hidden="true"></i>
                Rute penjelajahan
            </h2>
            <span class="text-xs text-gray-400 font-medium" x-text="subjects.length + ' mata pelajaran'"></span>
        </div>

        <template x-if="!loading && subjects.length === 0">
            <div class="mt-4 rounded-2xl border border-gray-100 p-8 text-center">
                <div class="w-14 h-14 mx-auto rounded-full bg-gradient-to-br from-indigo-50 to-violet-50 flex items-center justify-center mb-3">
                    <i class="ti ti-map-off text-2xl text-indigo-300" aria-hidden="true"></i>
                </div>
                <p class="text-sm text-gray-400">Belum ada kelas yang diikuti.</p>
            </div>
        </template>

        <div class="mt-4 rounded-2xl border border-gray-100 p-5 overflow-x-auto bg-gradient-to-b from-indigo-50/30 to-white" x-show="subjects.length > 0">
            <div class="flex items-center min-w-[560px] relative">
                <div class="absolute left-6 right-6 top-6 h-1 rounded-full bg-gradient-to-r from-[#4F46E5] via-[#7C3AED] to-[#2563EB] opacity-30"></div>
                <div class="flex justify-between w-full relative">
                    <template x-for="(s, i) in subjects" :key="s.subject">
                        <a :href="classesUrl" class="flex flex-col items-center gap-2 z-10 group"
                           x-transition:enter="transition ease-out duration-500"
                           x-transition:enter-start="opacity-0 scale-50"
                           x-transition:enter-end="opacity-100 scale-100"
                           :style="`transition-delay: ${i * 80}ms`">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white group-hover:scale-125 group-hover:shadow-lg transition-all duration-300 shadow-sm"
                                 :class="s.has_data && s.progress < 40 ? 'ring-4 ring-red-100 animate-pulse' : ''"
                                 :style="`background:${s.color.grad}`">
                                <i class="ti text-xl" :class="s.icon" aria-hidden="true"></i>
                            </div>
                            <p class="text-xs font-semibold text-center text-gray-700" x-text="s.subject"></p>
                            <p class="text-[11px] font-bold" :style="`color:${s.color.solid}`" x-text="(s.has_data ? s.progress : '-') + '%'"></p>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL PER MAPEL --}}
    <div class="mt-8 grid sm:grid-cols-2 gap-4" x-show="subjects.length > 0">
        <template x-for="(s, i) in subjects" :key="'detail-' + s.subject">
            <a :href="classesUrl"
               class="group relative rounded-2xl border border-gray-100 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden bg-white"
               x-transition:enter="transition ease-out duration-500"
               x-transition:enter-start="opacity-0 translate-y-3"
               x-transition:enter-end="opacity-100 translate-y-0"
               :style="`transition-delay: ${i * 70}ms`">
                <div class="absolute -top-8 -right-8 w-24 h-24 rounded-full opacity-0 group-hover:opacity-40 blur-xl transition-opacity duration-300" :style="`background:${s.color.grad}`"></div>
                <div class="relative flex justify-between items-start">
                    <div class="flex items-center gap-2.5">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform duration-300" :style="`background:${s.color.grad}`">
                            <i class="ti text-lg" :class="s.icon" aria-hidden="true"></i>
                        </span>
                        <h3 class="font-bold text-gray-800" x-text="s.subject"></h3>
                    </div>
                    <span class="font-extrabold text-lg" :style="`color:${s.color.solid}`" x-text="(s.has_data ? s.progress : '-') + '%'"></span>
                </div>
                <div class="relative mt-4 h-2.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden" :style="`background:${s.color.grad}; width:${s.has_data ? s.progress : 0}%`">
                        <div class="absolute inset-0 bg-white/30 animate-[shimmer_2s_linear_infinite]" style="background-size: 200% 100%;"></div>
                    </div>
                </div>
                <p class="relative mt-3 text-xs text-gray-400 flex items-center gap-1 group-hover:text-gray-600 transition-colors">
                    Buka halaman kelas <i class="ti ti-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-200" aria-hidden="true"></i>
                </p>
            </a>
        </template>
    </div>

    {{-- TENGGAT TERDEKAT --}}
    <div class="mt-8">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-clock-exclamation text-[#4F46E5]" aria-hidden="true"></i>
            Tenggat terdekat
        </h2>
        <template x-if="!loading && deadlines.length === 0">
            <div class="mt-3 flex items-center gap-2 text-sm text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3">
                <i class="ti ti-circle-check text-base" aria-hidden="true"></i>
                Tidak ada tenggat dalam 7 hari ke depan. Aman!
            </div>
        </template>
        <div class="mt-4 grid sm:grid-cols-2 gap-4">
            <template x-for="(d, i) in deadlines" :key="d.title">
                <div class="rounded-2xl p-5 relative overflow-hidden hover:shadow-lg transition-shadow duration-300"
                     :class="d.urgent ? 'text-white' : 'border border-gray-100 bg-white'"
                     :style="d.urgent ? 'background:linear-gradient(135deg,#DC2626,#F87171)' : ''"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     :style="`transition-delay: ${i * 80}ms`">
                    <i class="ti ti-alarm absolute -right-2 -top-2 text-7xl opacity-10" :class="d.urgent ? 'text-white' : 'text-gray-300'" aria-hidden="true"></i>
                    <span class="relative inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-semibold"
                          :class="d.urgent ? 'bg-white/20 text-white' : 'text-white'"
                          :style="!d.urgent ? `background:${d.color.grad}` : ''">
                        <i class="ti ti-book-2 text-sm" aria-hidden="true"></i>
                        <span x-text="d.subject"></span>
                    </span>
                    <h3 class="relative font-bold mt-3" :class="d.urgent ? 'text-white' : 'text-gray-800'" x-text="d.title"></h3>
                    <p class="relative text-sm mt-1 flex items-center gap-1" :class="d.urgent ? 'text-white/85' : 'text-gray-400'">
                        <i class="ti ti-clock text-xs" aria-hidden="true"></i>
                        <span x-text="'Tenggat ' + d.due"></span>
                    </p>
                </div>
            </template>
        </div>
    </div>

    {{-- STEMPEL --}}
    <div class="mt-8">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-medal text-[#4F46E5]" aria-hidden="true"></i>
            Stempel terkumpul
        </h2>
        <div class="mt-4 rounded-2xl border border-gray-100 p-5 flex flex-wrap gap-4 bg-gradient-to-b from-violet-50/30 to-white">
            <template x-for="(b, i) in badges.earned" :key="b.label">
                <div class="flex flex-col items-center gap-2 w-16 group"
                     :style="`transform: rotate(${[-8,5,-4,7][i % 4]}deg)`"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-50"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-white shadow-md group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"
                         :style="`background:linear-gradient(135deg, ${colorMap[b.color_key].from}, ${colorMap[b.color_key].to})`">
                        <i class="ti text-xl" :class="b.icon" aria-hidden="true"></i>
                    </div>
                    <p class="text-[11px] text-gray-500 text-center font-medium" x-text="b.label"></p>
                </div>
            </template>
            <div class="flex flex-col items-center gap-2 w-16" x-show="badges.locked_count > 0">
                <div class="w-14 h-14 rounded-full border-2 border-dashed border-gray-200 flex items-center justify-center text-xs text-gray-400 font-bold">
                    <span x-text="'+' + badges.locked_count"></span>
                </div>
                <p class="text-[11px] text-gray-400">Belum dibuka</p>
            </div>
            <template x-if="badges.earned.length === 0 && badges.locked_count === 0">
                <p class="text-sm text-gray-400">Belum ada stempel.</p>
            </template>
        </div>
    </div>

    {{-- AKTIVITAS TERBARU --}}
    <div class="mt-8">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <i class="ti ti-activity text-[#4F46E5]" aria-hidden="true"></i>
            Aktivitas terbaru
        </h2>
        <template x-if="!loading && recentActivity.length === 0">
            <p class="mt-3 text-sm text-gray-400">Belum ada aktivitas tercatat.</p>
        </template>
        <div class="mt-4 rounded-2xl border border-gray-100 divide-y divide-gray-50 overflow-hidden" x-show="recentActivity.length > 0">
            <template x-for="(a, i) in recentActivity" :key="a.description + a.meta">
                <div class="flex gap-3 p-4 hover:bg-indigo-50/40 transition-colors duration-200"
                     x-transition:enter="transition ease-out duration-400"
                     x-transition:enter-start="opacity-0 -translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     :style="`transition-delay: ${i * 60}ms`">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm"
                          :class="a.type === 'grade' ? 'bg-gradient-to-br from-[#7C3AED] to-[#C4B5FD]' : (a.type === 'quiz_completed' ? 'bg-gradient-to-br from-[#0284C7] to-[#7DD3FC]' : 'bg-gradient-to-br from-[#4F46E5] to-[#818CF8]')">
                        <i class="ti text-base"
                           :class="a.type === 'grade' ? 'ti-writing' : (a.type === 'quiz_completed' ? 'ti-clipboard-check' : (a.type === 'assignment_submitted' ? 'ti-file-check' : 'ti-circle-check'))"
                           aria-hidden="true"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800" x-text="a.description"></p>
                        <p class="text-xs text-gray-400" x-text="a.meta"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- MENIT BELAJAR --}}
    <div class="mt-8">
        <div class="flex justify-between items-baseline">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-clock-play text-[#4F46E5]" aria-hidden="true"></i>
                Menit belajar (halaman ini)
            </h2>
            <span class="flex items-center gap-1.5 text-xs text-gray-400">
                <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] animate-pulse"></span>
                <span>Live</span>
            </span>
        </div>
        <div class="mt-4 rounded-2xl border border-gray-100 p-5 bg-gradient-to-br from-indigo-50/20 to-white">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-gray-800" x-text="study.total"></span>
                <span class="text-sm text-gray-400">menit dalam 30 menit terakhir</span>
            </div>
            <div style="height:160px" class="mt-4">
                <canvas id="studyChart"></canvas>
            </div>
            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                <i class="ti ti-info-circle text-xs" aria-hidden="true"></i>
                Terhitung selama kamu membuka halaman dashboard ini.
            </p>
        </div>
    </div>

    {{-- BOTTOM NAV MOBILE --}}
    <nav class="lg:hidden fixed bottom-2 left-2 right-2 bg-white border border-gray-100 rounded-[20px] h-14 flex justify-around items-center z-40 shadow-lg shadow-indigo-100/50">
        @foreach([
            ['route' => 'siswa.dashboard', 'icon' => 'ti-home-2', 'label' => 'Beranda'],
            ['route' => 'siswa.classes.index', 'icon' => 'ti-map-2', 'label' => 'Kelas'],
            ['route' => 'siswa.calendar.index', 'icon' => 'ti-calendar-event', 'label' => 'Agenda'],
            ['route' => 'profile.show', 'icon' => 'ti-user-circle', 'label' => 'Profil'],
        ] as $menu)
            @continue(!Route::has($menu['route']))
            @php $isActiveMenu = request()->routeIs($menu['route']); @endphp
            <a href="{{ route($menu['route']) }}"
               class="flex flex-col items-center justify-center gap-0.5 transition-all duration-200 {{ $isActiveMenu ? 'text-white bg-gradient-to-br from-[#4F46E5] to-[#818CF8] px-3.5 py-1.5 rounded-2xl shadow-md shadow-indigo-300/50 -translate-y-1' : 'text-gray-400 px-2' }}">
                <i class="ti {{ $menu['icon'] }} text-[18px]" aria-hidden="true"></i>
                <span class="text-[9px] font-medium">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>

<style>
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    20% { transform: rotate(14deg); }
    40% { transform: rotate(-8deg); }
    60% { transform: rotate(14deg); }
}
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
function siswaDashboard(config) {
    return {
        progressUrl: config.progressUrl,
        heartbeatUrl: config.heartbeatUrl,
        classesUrl: config.classesUrl,
        loading: true,
        overall: 0,
        subjects: [],
        streak: 0,
        badges: { earned: [], locked_count: 0 },
        deadlines: [],
        recentActivity: [],
        study: { total: 0, points: [] },
        lastUpdated: '',
        studyChartInstance: null,
        overallChartInstance: null,
        subjectChartInstance: null,
        colorMap: {
            indigo: { from: '#4F46E5', to: '#818CF8' },
            violet: { from: '#7C3AED', to: '#C4B5FD' },
            sky:    { from: '#0284C7', to: '#7DD3FC' },
            blue:   { from: '#2563EB', to: '#93C5FD' },
        },

        init() {
            this.fetchProgress(true);
            setInterval(() => this.fetchProgress(false), 15000);

            this.sendHeartbeat();
            setInterval(() => {
                if (document.visibilityState === 'visible') this.sendHeartbeat();
            }, 60000);
        },

        async fetchProgress(isFirstLoad) {
            try {
                const res = await fetch(this.progressUrl, { headers: { Accept: 'application/json' } });
                const data = await res.json();
                this.overall = data.overall;
                this.subjects = data.subjects;
                this.streak = data.streak;
                this.badges = data.badges;
                this.deadlines = data.deadlines;
                this.recentActivity = data.recent_activity;
                this.study = data.study;
                this.lastUpdated = data.updated_at;

                if (isFirstLoad) {
                    this.loading = false;
                    this.$nextTick(() => {
                        this.renderStudyChart();
                        this.renderOverallChart();
                        this.renderSubjectChart();
                    });
                } else {
                    this.updateStudyChart();
                    this.updateOverallChart();
                    this.updateSubjectChart();
                }
            } catch (e) {
                console.error('Gagal memuat progres:', e);
                this.loading = false;
            }
        },

        async sendHeartbeat() {
            try {
                await fetch(this.heartbeatUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        Accept: 'application/json',
                    },
                });
            } catch (e) { /* diamkan */ }
        },

        renderStudyChart() {
            const ctx = document.getElementById('studyChart');
            if (!ctx) return;
            this.studyChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.study.points,
                    datasets: [{
                        label: 'Menit aktif',
                        data: this.study.points.map(() => 1),
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79,70,229,0.1)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800 },
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                    },
                },
            });
            setTimeout(() => this.studyChartInstance?.resize(), 50);
        },

        updateStudyChart() {
            if (!this.studyChartInstance) return;
            this.studyChartInstance.data.labels = this.study.points;
            this.studyChartInstance.data.datasets[0].data = this.study.points.map(() => 1);
            this.studyChartInstance.update();
        },

        renderOverallChart() {
            const ctx = document.getElementById('overallChart');
            if (!ctx) return;
            this.overallChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Progres', 'Sisa'],
                    datasets: [{
                        data: [this.overall, 100 - this.overall],
                        backgroundColor: ['#4F46E5', '#EEF2FF'],
                        borderWidth: 0,
                        cutout: '78%',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    animation: { animateRotate: true, duration: 1000, easing: 'easeOutQuart' },
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                },
            });
            setTimeout(() => this.overallChartInstance?.resize(), 50);
        },

        updateOverallChart() {
            if (!this.overallChartInstance) return;
            this.overallChartInstance.data.datasets[0].data = [this.overall, 100 - this.overall];
            this.overallChartInstance.update();
        },

        renderSubjectChart() {
            const ctx = document.getElementById('subjectChart');
            if (!ctx) return;
            this.subjectChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.subjects.map(s => s.subject),
                    datasets: [{
                        data: this.subjects.map(s => s.progress),
                        backgroundColor: this.subjects.map(s => s.color.solid),
                        hoverBackgroundColor: this.subjects.map(s => s.color.solid),
                        borderRadius: 10,
                        maxBarThickness: 40,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    scales: {
                        y: { beginAtZero: true, max: 100, grid: { color: '#F3F4F6' }, ticks: { callback: v => v + '%' } },
                        x: { grid: { display: false } },
                    },
                    plugins: { legend: { display: false } },
                },
            });
            setTimeout(() => this.subjectChartInstance?.resize(), 50);
        },

        updateSubjectChart() {
            if (!this.subjectChartInstance) return;
            this.subjectChartInstance.data.labels = this.subjects.map(s => s.subject);
            this.subjectChartInstance.data.datasets[0].data = this.subjects.map(s => s.progress);
            this.subjectChartInstance.data.datasets[0].backgroundColor = this.subjects.map(s => s.color.solid);
            this.subjectChartInstance.update();
        },
    };
}
</script>
@endpush