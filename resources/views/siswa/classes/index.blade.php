@extends('layouts.app')
@section('title', 'Kelas Saya')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  {{-- ══════════════ HERO HEADER — gaya sama dengan dashboard, sesuai Bab 3.1 ══════════════ --}}
  <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-8 text-white">
    {{-- Motif lingkaran, senada sidebar & hero dashboard --}}
    <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
    <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
    <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

    <div class="relative flex items-center justify-between flex-wrap gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2">Kelas Saya <span>🎒</span></h1>
        <p class="text-indigo-100 text-sm mt-1">Semua kelas yang kamu ikuti ada di sini</p>
      </div>
      <a href="{{ route('siswa.classes.join') }}"
        class="inline-flex items-center gap-2 bg-white text-[#4F46E5] text-sm font-semibold
               px-5 py-2.5 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5
               transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Bergabung ke Kelas
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-[#10B981]/10 border border-[#10B981]/30 text-[#065F46] text-sm rounded-xl px-4 py-3 flex items-center gap-2">
      <span>✅</span> {{ session('success') }}
    </div>
  @endif

  @if($enrollments->isEmpty())
    {{-- ══ Empty state — sesuai Bab 2.4 (Empty: ilustrasi flat + CTA) ══ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-violet-50
                rounded-3xl border border-indigo-100 p-14 text-center">
      <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200/30 rounded-full blur-2xl"></div>
      <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-violet-200/30 rounded-full blur-2xl"></div>
      <div class="relative">
        <div class="text-6xl mb-4 animate-bounce">📚</div>
        <p class="text-gray-700 font-medium">Kamu belum bergabung di kelas manapun</p>
        <p class="text-gray-400 text-sm mt-1">Yuk mulai belajar dengan bergabung ke kelas pertamamu!</p>
        <a href="{{ route('siswa.classes.join') }}"
          class="inline-flex items-center gap-1.5 mt-5 bg-[#4F46E5] hover:bg-[#4338CA] text-white
                 text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-md shadow-indigo-200">
          Bergabung Sekarang →
        </a>
      </div>
    </div>
  @else
    {{-- ══ Summary stat card — sesuai Bab 3.1 (Summary: 3-4 stat card) ══ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-[#4F46E5] flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400">Kelas Aktif</p>
          <p class="text-lg font-bold text-gray-900">{{ $enrollments->count() }}</p>
        </div>
      </div>
      <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400">Status</p>
          <p class="text-lg font-bold text-gray-900">Semua aktif</p>
        </div>
      </div>
      <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3 shadow-sm hidden md:flex">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-gray-400">Terakhir diakses</p>
          <p class="text-lg font-bold text-gray-900">Hari ini</p>
        </div>
      </div>
    </div>

    {{-- ══ Grid Kelas — x-course-card style sesuai Bab 2.4 ══ --}}
    <div class="grid sm:grid-cols-2 gap-5">
      @foreach($enrollments as $enrollment)
        @php
          $class = $enrollment->schoolClass;
          // Variasi warna per-card tetap dalam keluarga Indigo/violet/blue,
          // menjaga aksen visual tanpa keluar dari palet token spec.
          $colors = [
            ['from' => 'from-[#4F46E5]', 'to' => 'to-[#818CF8]', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'ring' => 'ring-indigo-100'],
            ['from' => 'from-violet-500', 'to' => 'to-purple-500', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'ring' => 'ring-violet-100'],
            ['from' => 'from-sky-500', 'to' => 'to-indigo-500', 'bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'ring' => 'ring-sky-100'],
            ['from' => 'from-blue-500', 'to' => 'to-violet-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'ring' => 'ring-blue-100'],
            ['from' => 'from-indigo-400', 'to' => 'to-blue-500', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'ring' => 'ring-indigo-100'],
          ];
          $c = $colors[$loop->index % count($colors)];
          $progress = $enrollment->progress_percentage ?? null; // sesuaikan kalau kolom berbeda
        @endphp
        <a href="{{ route('siswa.classes.show', $class) }}"
          class="group relative bg-white rounded-2xl border border-gray-100 overflow-hidden
                 hover:shadow-xl hover:shadow-indigo-200/60 hover:-translate-y-1 hover:ring-2 {{ $c['ring'] }}
                 transition-all duration-200">

          {{-- Cover / header gradient penuh warna, bukan cuma garis tipis --}}
          <div class="relative h-20 bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }} overflow-hidden">
            <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-white opacity-[0.12]"></div>
            <div class="absolute bottom-0 left-8 w-16 h-16 rounded-full bg-white opacity-[0.10] translate-y-1/2"></div>
            <span class="absolute top-3 right-3 text-[10px] font-bold uppercase tracking-wide bg-white/25 text-white px-2 py-1 rounded-full backdrop-blur-sm">
              {{ $class->rombel->name ?? '-' }}
            </span>
          </div>

          <div class="relative px-5 pb-5">
            {{-- Avatar inisial, overlap dengan cover --}}
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                        text-white font-bold flex items-center justify-center text-xl shrink-0
                        shadow-lg -mt-7 mb-3 ring-4 ring-white group-hover:scale-105 transition-transform">
              {{ strtoupper(substr($class->name, 0, 1)) }}
            </div>

            <h2 class="font-bold text-gray-800 group-hover:text-[#4F46E5] transition-colors truncate">
              {{ $class->name }}
            </h2>
            <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 100-8 4 4 0 000 8zm6 4a4 4 0 10-8 0"/>
              </svg>
              {{ $class->teacher->user->name ?? '-' }}
            </p>

            {{-- Progress bar — sesuai Bab 2.4 (In Progress: progress bar hijau parsial) --}}
            @if(!is_null($progress))
              <div class="mt-4">
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                  <span>Progres</span>
                  <span class="font-semibold text-[#10B981]">{{ $progress }}%</span>
                </div>
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full bg-[#10B981] rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
              </div>
            @endif

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
              <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
              </span>
              <span class="text-xs font-mono font-semibold {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }} border px-2.5 py-1 rounded-lg">
                {{ $class->code }}
              </span>
            </div>
          </div>

          {{-- Panah hover --}}
          <div class="absolute bottom-5 right-5 opacity-0 group-hover:opacity-100 translate-x-1 group-hover:translate-x-0 transition-all">
            <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection