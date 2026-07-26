@extends('layouts.app')
@section('title', $class->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-4 md:space-y-6 pb-10 px-1 sm:px-0">

    {{-- Header / Back --}}
    <div class="flex items-center gap-2.5 md:gap-3">
        <a href="{{ route('siswa.classes.index') }}"
           class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-[#4F46E5] hover:border-indigo-300 shadow-sm transition flex-shrink-0">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <p class="text-xs md:text-sm text-gray-400 font-medium">Kembali ke Kelas Saya</p>
    </div>

    {{-- Hero Card --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-6 md:p-8 text-white shadow-lg">
        {{-- decorative blobs: lingkaran solid semi-transparan, lebih kelihatan seperti di dashboard --}}
        <div class="absolute -top-10 -right-10 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 -left-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white/10"></div>
        <div class="absolute top-1/3 -left-8 w-24 h-24 md:w-32 md:h-32 rounded-full bg-white/10"></div>
        <div class="absolute -top-6 right-1/3 w-16 h-16 md:w-20 md:h-20 rounded-full bg-white/10"></div>

        <div class="relative z-10">
            <span class="inline-block bg-white/20 backdrop-blur text-[11px] md:text-xs font-semibold px-3 py-1 rounded-full mb-2.5 md:mb-3">
                {{ $class->rombel->name ?? '-' }} &middot; {{ $class->subject }}
            </span>
            <h1 class="text-xl md:text-3xl font-extrabold leading-tight">{{ $class->name }}</h1>
            <p class="text-xs md:text-sm text-white/80 mt-1">👩‍🏫 Guru: {{ $class->teacher->user->name ?? '-' }}</p>

            @if ($class->description)
                <p class="text-xs md:text-sm text-white/90 bg-white/10 backdrop-blur rounded-xl px-3 md:px-4 py-2 md:py-2.5 mt-3 md:mt-4 inline-block">
                    {{ $class->description }}
                </p>
            @endif

            {{-- Stats inline --}}
            <div class="flex gap-3 md:gap-4 mt-5 md:mt-6">
                <div class="flex-1 bg-white/15 backdrop-blur rounded-xl md:rounded-2xl px-3 md:px-4 py-2.5 md:py-3 text-center">
                    <p class="text-xl md:text-2xl font-extrabold">{{ $meetings->count() }}</p>
                    <p class="text-[11px] md:text-xs text-white/80 mt-0.5">Pertemuan</p>
                </div>
                <div class="flex-1 bg-white/15 backdrop-blur rounded-xl md:rounded-2xl px-3 md:px-4 py-2.5 md:py-3 text-center">
                    <p class="text-xl md:text-2xl font-extrabold">{{ $class->enrollments->count() }}</p>
                    <p class="text-[11px] md:text-xs text-white/80 mt-0.5">Teman Sekelas</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs md:text-sm rounded-xl px-3 md:px-4 py-2.5 md:py-3 flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    {{-- Menu Grid --}}
    <div>
        <h2 class="text-xs md:text-sm font-bold text-gray-500 uppercase tracking-wide mb-2.5 md:mb-3 px-1">Menu Kelas</h2>

        {{-- Mobile: grid biasa 2 kolom. Desktop (md+): layout bento asimetris, Materi jadi featured card 2x2 --}}
        <div class="grid grid-cols-2 md:grid-cols-4 md:grid-rows-3 gap-2.5 md:gap-3 md:auto-rows-[88px]">

            {{-- FEATURED: Materi — mobile: horizontal ringkas, desktop (md+): vertical besar 2x2 --}}
            <a href="{{ route('siswa.classes.materials.index', $class) }}"
               class="group relative overflow-hidden rounded-xl md:rounded-2xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] text-white
                      p-3.5 md:p-5 flex flex-row md:flex-col items-center md:items-stretch justify-start md:justify-between gap-3 md:gap-0
                      shadow-md hover:shadow-xl hover:shadow-indigo-300/50 hover:-translate-y-0.5 transition-all
                      col-span-2 md:col-span-2 md:row-span-2">
                <div class="absolute -bottom-8 -right-8 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white/10"></div>
                <div class="absolute top-1/2 -left-6 w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10"></div>

                {{-- Icon: selalu tampil --}}
                <div class="relative w-11 h-11 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-xl md:text-3xl shrink-0 group-hover:scale-105 transition-transform">📘</div>

                {{-- Mobile: teks di samping icon. Desktop: disembunyikan, digantikan blok bawah --}}
                <div class="relative flex-1 min-w-0 md:hidden">
                    <p class="font-bold text-sm">Materi</p>
                    <p class="text-[11px] text-white/75 truncate">Bahan belajar &amp; modul pertemuan</p>
                </div>
                <svg class="relative w-4 h-4 text-white/60 group-hover:text-white group-hover:translate-x-1 transition-all shrink-0 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>

                {{-- Desktop only: arrow di kanan atas icon, teks di bawah (gaya bento besar) --}}
                <svg class="hidden md:block relative w-5 h-5 text-white/60 group-hover:text-white group-hover:translate-x-1 transition-all absolute top-5 right-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
                <div class="hidden md:block relative">
                    <p class="font-bold text-lg">Materi</p>
                    <p class="text-sm text-white/75 mt-0.5">Bahan belajar &amp; modul pertemuan</p>
                </div>
            </a>

            <a href="{{ route('siswa.classes.assignments.index', $class) }}"
               class="group relative overflow-hidden rounded-xl md:rounded-2xl bg-white border border-gray-100 p-3.5 md:p-4 flex flex-col justify-center gap-1.5 md:gap-2
                      shadow-sm hover:shadow-lg hover:shadow-sky-200/60 hover:-translate-y-0.5 transition-all
                      md:col-start-3 md:row-start-1">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-sky-500 to-sky-300 scale-x-0 group-hover:scale-x-100 origin-left transition-transform"></div>
                <div class="flex items-center gap-2.5 md:gap-3">
                    <div class="relative w-9 h-9 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-gradient-to-br from-sky-500 to-sky-300 text-white flex items-center justify-center text-base md:text-lg shrink-0 shadow-md shadow-sky-300/60 group-hover:scale-105 transition-transform">📝</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-xs md:text-sm">Tugas</p>
                        <p class="text-[10px] md:text-xs text-gray-400 truncate">Kerjakan &amp; kumpul</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('siswa.classes.quizzes.index', $class) }}"
               class="group relative overflow-hidden rounded-xl md:rounded-2xl bg-white border border-gray-100 p-3.5 md:p-4 flex flex-col justify-center gap-1.5 md:gap-2
                      shadow-sm hover:shadow-lg hover:shadow-violet-200/60 hover:-translate-y-0.5 transition-all
                      md:col-start-4 md:row-start-1">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-violet-500 to-violet-300 scale-x-0 group-hover:scale-x-100 origin-left transition-transform"></div>
                <div class="flex items-center gap-2.5 md:gap-3">
                    <div class="relative w-9 h-9 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-gradient-to-br from-violet-500 to-violet-300 text-white flex items-center justify-center text-base md:text-lg shrink-0 shadow-md shadow-violet-300/60 group-hover:scale-105 transition-transform">🧠</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-xs md:text-sm">Kuis</p>
                        <p class="text-[10px] md:text-xs text-gray-400 truncate">Uji pemahaman</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('siswa.classes.grades.index', $class) }}"
               class="group relative overflow-hidden rounded-xl md:rounded-2xl bg-white border border-gray-100 p-3.5 md:p-4 flex flex-col justify-center gap-1.5 md:gap-2
                      shadow-sm hover:shadow-lg hover:shadow-blue-200/60 hover:-translate-y-0.5 transition-all
                      md:col-start-3 md:row-start-2">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-blue-400 scale-x-0 group-hover:scale-x-100 origin-left transition-transform"></div>
                <div class="flex items-center gap-2.5 md:gap-3">
                    <div class="relative w-9 h-9 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-gradient-to-br from-blue-600 to-blue-400 text-white flex items-center justify-center text-base md:text-lg shrink-0 shadow-md shadow-blue-300/60 group-hover:scale-105 transition-transform">📊</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-xs md:text-sm">Rapor</p>
                        <p class="text-[10px] md:text-xs text-gray-400 truncate">Nilai &amp; progres</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('siswa.classes.attendance.index', $class) }}"
               class="group relative overflow-hidden rounded-xl md:rounded-2xl bg-white border border-gray-100 p-3.5 md:p-4 flex flex-col justify-center gap-1.5 md:gap-2
                      shadow-sm hover:shadow-lg hover:shadow-indigo-200/60 hover:-translate-y-0.5 transition-all
                      md:col-start-4 md:row-start-2">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#4338CA] to-[#818CF8] scale-x-0 group-hover:scale-x-100 origin-left transition-transform"></div>
                <div class="flex items-center gap-2.5 md:gap-3">
                    <div class="relative w-9 h-9 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-gradient-to-br from-[#4338CA] to-[#818CF8] text-white flex items-center justify-center text-base md:text-lg shrink-0 shadow-md shadow-indigo-300/60 group-hover:scale-105 transition-transform">✅</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-xs md:text-sm">Absensi</p>
                        <p class="text-[10px] md:text-xs text-gray-400 truncate">Kehadiran kamu</p>
                    </div>
                </div>
            </a>

            {{-- Baris ke-3 di desktop: strip ringkasan progres, mengisi penuh 4 kolom biar grid tidak menggantung --}}
            <div class="hidden md:flex md:col-span-4 md:row-start-3 relative overflow-hidden rounded-2xl border border-indigo-100 bg-indigo-50/50 items-center justify-between px-5">
                <div class="flex items-center gap-2 text-xs text-indigo-400 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#4F46E5] animate-pulse"></span>
                    Semua menu kelas tersedia di atas — tinggal pilih & mulai belajar
                </div>
                <span class="text-[11px] text-indigo-300">5 menu aktif</span>
            </div>
        </div>
    </div>

    {{-- Pertemuan Section --}}
    <div class="bg-white rounded-xl md:rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-4 md:px-6 py-3.5 md:py-4 border-b border-gray-100 bg-gray-50/60">
            <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm md:text-base">🗓️ Daftar Pertemuan</h2>
            <span class="text-[11px] md:text-xs font-semibold text-gray-400">{{ $meetings->count() }} total</span>
        </div>

        @forelse ($meetings as $meeting)
            <div class="relative px-4 md:px-6 py-3.5 md:py-4 border-b border-gray-50 last:border-0 flex items-start gap-3 md:gap-4 hover:bg-indigo-50/40 transition">
                {{-- Garis konektor timeline --}}
                @unless($loop->last)
                    <div class="absolute left-[2.15rem] md:left-[2.65rem] top-11 md:top-12 bottom-0 w-px bg-gradient-to-b from-indigo-200 to-transparent"></div>
                @endunless

                <div class="relative w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-[#4F46E5] to-[#818CF8] text-white flex items-center justify-center font-bold text-xs md:text-sm shrink-0 shadow-md shadow-indigo-300/60 ring-4 ring-white">
                    {{ $meeting->order }}
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm md:text-base">{{ $meeting->topic }}</h3>
                    <div class="flex flex-wrap items-center gap-2 md:gap-3 mt-1.5 text-xs md:text-sm text-gray-500">
                        @if ($meeting->scheduled_at)
                            <span class="inline-flex items-center gap-1 bg-indigo-50 text-[#4338CA] font-medium px-2 py-0.5 rounded-full text-[11px] md:text-xs">
                                📅 {{ $meeting->scheduled_at->translatedFormat('D, d F Y') }}
                            </span>
                        @endif
                        @if ($meeting->description)
                            <span class="truncate max-w-[10rem] md:max-w-xs text-gray-400">{{ $meeting->description }}</span>
                        @endif
                    </div>
                </div>

                <a href="{{ route('siswa.classes.materials.index', $class) }}"
                   class="shrink-0 inline-flex items-center gap-1.5 text-xs md:text-sm font-medium text-[#4F46E5] hover:text-white hover:bg-gradient-to-r hover:from-[#4F46E5] hover:to-[#818CF8] border border-indigo-200 hover:border-[#4F46E5] px-2.5 md:px-3 py-1.5 rounded-lg transition-all hover:shadow-md hover:shadow-indigo-200">
                    📄 Materi
                </a>
            </div>
        @empty
            <div class="px-6 py-12 md:py-14 text-center text-gray-400">
                <div class="text-3xl md:text-4xl mb-2.5 md:mb-3">🌱</div>
                <p class="font-semibold text-gray-500 text-sm md:text-base">Belum ada pertemuan</p>
                <p class="text-xs md:text-sm mt-1">Pertemuan akan muncul setelah guru mempublikasikannya.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection