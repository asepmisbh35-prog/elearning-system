@extends('layouts.app')
@section('title', 'Kelas Saya')

@php
    // Variasi warna cover per kelas — tetap di rumpun indigo/violet/sky/blue
    $aksenKelas = [
        ['from' => 'from-[#4F46E5]', 'to' => 'to-[#4338CA]', 'text' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
        ['from' => 'from-violet-500', 'to' => 'to-violet-600', 'text' => 'text-violet-600', 'bg' => 'bg-violet-50'],
        ['from' => 'from-sky-500', 'to' => 'to-sky-600', 'text' => 'text-sky-600', 'bg' => 'bg-sky-50'],
        ['from' => 'from-blue-500', 'to' => 'to-blue-600', 'text' => 'text-blue-600', 'bg' => 'bg-blue-50'],
    ];
@endphp

@section('content')
    <div class="space-y-5 md:space-y-6">

        {{-- Hero header --}}
        <div
            class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-8 text-white">
            <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
            <div
                class="absolute bottom-0 left-1/3 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2">
            </div>
            <div class="absolute top-1/2 -left-16 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

            <div class="relative flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold">Kelas Saya</h2>
                    <p class="text-indigo-100 text-xs sm:text-sm mt-0.5">Kelola kelas mata pelajaran yang kamu ampu</p>
                </div>
                <a href="{{ route('guru.classes.create') }}"
                    class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 sm:py-2.5 bg-white text-[#4338CA] rounded-xl text-xs sm:text-sm font-semibold hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Kelas
                </a>
            </div>
        </div>

        @if ($classes->isEmpty())
            <div
                class="relative overflow-hidden bg-white rounded-2xl md:rounded-3xl border border-gray-100 p-8 sm:p-12 text-center">
                <div
                    class="absolute -top-8 -left-8 w-32 h-32 rounded-full bg-indigo-50 opacity-70 blur-2xl pointer-events-none">
                </div>
                <div class="relative">
                    <div
                        class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-[#4F46E5]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm sm:text-base">Belum ada kelas. Buat kelas pertamamu!</p>
                    <a href="{{ route('guru.classes.create') }}"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-indigo-200/60 hover:-translate-y-0.5 active:scale-95 transition-all">
                        Buat Kelas Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
                @foreach ($classes as $class)
                    @php $aksen = $aksenKelas[$loop->index % count($aksenKelas)]; @endphp
                    <a href="{{ route('guru.classes.show', $class) }}"
   class="group relative block bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-200/60">

    {{-- Cover berwarna --}}
    <div class="relative h-16 sm:h-20 bg-gradient-to-br {{ $aksen['from'] }} {{ $aksen['to'] }}">
        <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white opacity-10"></div>
        <div class="absolute top-2.5 sm:top-3 right-2.5 sm:right-3">
            @if ($class->is_active)
                <span class="text-[11px] sm:text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif
                </span>
            @else
                <span class="text-[11px] sm:text-xs px-2 py-0.5 rounded-full bg-white/80 text-gray-500 font-medium">Nonaktif</span>
            @endif
        </div>
    </div>

    {{-- Avatar inisial — posisi absolute, nempel di batas cover/body --}}
    <div class="absolute top-10 sm:top-14 left-4 sm:left-5 w-11 h-11 sm:w-12 sm:h-12 rounded-xl ring-4 ring-white shadow-sm flex items-center justify-center font-bold text-base sm:text-lg {{ $aksen['text'] }} {{ $aksen['bg'] }}">
        {{ strtoupper(substr($class->name, 0, 1)) }}
    </div>

    {{-- Body — pt diberi jarak ekstra supaya teks nggak ketiban avatar --}}
    <div class="px-4 sm:px-5 pt-7 sm:pt-8 pb-4 sm:pb-5">
        <h3 class="font-semibold text-gray-900 group-hover:text-[#4F46E5] transition leading-snug mb-1 text-sm sm:text-base truncate">
            {{ $class->name }}
        </h3>
        <p class="text-xs sm:text-sm text-gray-500 mb-3 truncate">{{ $class->rombel?->name ?? '-' }}</p>

        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <span class="text-[11px] sm:text-xs font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded-lg">
                {{ $class->code }}
            </span>
            <span class="text-[11px] sm:text-xs text-gray-500">
                {{ $class->enrollments_count }} siswa
            </span>
        </div>
    </div>
</a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
