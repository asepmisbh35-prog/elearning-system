@extends('layouts.app')
@section('title', 'Kelas Saya')

@section('content')
<div class="max-w-5xl mx-auto space-y-4 md:space-y-6">

  {{-- ══════════════ HERO HEADER ══════════════ --}}
  <div class="rise-in relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-8 text-white">
    <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08] animate-float-slow"></div>
    <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2 animate-float-medium"></div>
    <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07] animate-float-fast"></div>

    <div class="relative flex items-center justify-between flex-wrap gap-3 md:gap-4">
      <div>
        <h1 class="text-lg sm:text-xl md:text-3xl font-bold flex items-center gap-1.5 md:gap-2">
          Kelas Saya <span class="inline-block animate-wave">🎒</span>
        </h1>
        <p class="text-indigo-100 text-xs md:text-sm mt-0.5 md:mt-1">Semua kelas yang kamu ikuti ada di sini</p>
      </div>
      <a href="{{ route('siswa.classes.join') }}"
        class="group inline-flex items-center gap-1.5 md:gap-2 bg-white text-[#4F46E5] text-xs md:text-sm font-semibold
               px-3.5 md:px-5 py-2 md:py-2.5 rounded-lg md:rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95
               transition-all duration-200">
        <svg class="w-3.5 h-3.5 md:w-4 md:h-4 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="whitespace-nowrap">Bergabung ke Kelas</span>
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="rise-in bg-[#10B981]/10 border border-[#10B981]/30 text-[#065F46] text-xs md:text-sm rounded-lg md:rounded-xl px-3.5 md:px-4 py-2.5 md:py-3 flex items-center gap-2">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      {{ session('success') }}
    </div>
  @endif

  @if($enrollments->isEmpty())
    {{-- ══ Empty state ══ --}}
    <div class="rise-in relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-violet-50
                rounded-2xl md:rounded-3xl border border-indigo-100 p-8 sm:p-10 md:p-14 text-center">
      <div class="absolute -top-10 -right-10 w-32 h-32 md:w-40 md:h-40 bg-indigo-200/30 rounded-full blur-2xl animate-float-slow"></div>
      <div class="absolute -bottom-10 -left-10 w-32 h-32 md:w-40 md:h-40 bg-violet-200/30 rounded-full blur-2xl animate-float-medium"></div>
      <div class="relative">
        <div class="text-5xl md:text-6xl mb-3 md:mb-4 animate-bounce-gentle">📚</div>
        <p class="text-gray-700 font-medium text-sm md:text-base">Kamu belum bergabung di kelas manapun</p>
        <p class="text-gray-400 text-xs md:text-sm mt-1">Yuk mulai belajar dengan bergabung ke kelas pertamamu!</p>
        <a href="{{ route('siswa.classes.join') }}"
          class="inline-flex items-center gap-1.5 mt-4 md:mt-5 bg-[#4F46E5] hover:bg-[#4338CA] text-white
                 text-xs md:text-sm font-semibold px-4 md:px-5 py-2 md:py-2.5 rounded-lg md:rounded-xl transition-all duration-200 shadow-md shadow-indigo-200 hover:-translate-y-0.5 active:scale-95">
          Bergabung Sekarang →
        </a>
      </div>
    </div>
  @else
    {{-- ══ Summary stat card ══ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 md:gap-4"
         x-data="{ activeCount: 0 }"
         x-init="
            let target = {{ $enrollments->count() }}, start = null;
            const step = ts => {
                if (!start) start = ts;
                let p = Math.min((ts - start) / 600, 1);
                activeCount = Math.floor(p * target);
                if (p < 1) requestAnimationFrame(step); else activeCount = target;
            };
            requestAnimationFrame(step)
         ">
      <div class="stat-card rise-in bg-white rounded-xl md:rounded-2xl border border-gray-100 p-2.5 md:p-4 flex items-center gap-2 md:gap-3 shadow-sm"
           style="animation-delay: 60ms">
        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-indigo-50 flex items-center justify-center text-[#4F46E5] flex-shrink-0">
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-[10px] md:text-xs text-gray-400 truncate">Kelas Aktif</p>
          <p class="text-base md:text-lg font-bold text-gray-900 tabular-nums" x-text="activeCount"></p>
        </div>
      </div>

      <div class="stat-card rise-in bg-white rounded-xl md:rounded-2xl border border-gray-100 p-2.5 md:p-4 flex items-center gap-2 md:gap-3 shadow-sm"
           style="animation-delay: 120ms">
        <div class="relative w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
          <span class="absolute inset-0 rounded-lg md:rounded-xl bg-emerald-200 opacity-40 animate-ping-slow"></span>
          <svg class="relative w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-[10px] md:text-xs text-gray-400 truncate">Status</p>
          <p class="text-sm md:text-lg font-bold text-gray-900 whitespace-nowrap">Semua aktif</p>
        </div>
      </div>

      <div class="stat-card rise-in bg-white rounded-xl md:rounded-2xl border border-gray-100 p-2.5 md:p-4 items-center gap-2 md:gap-3 shadow-sm hidden md:flex"
           style="animation-delay: 180ms">
        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 flex-shrink-0">
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-[10px] md:text-xs text-gray-400 truncate">Terakhir diakses</p>
          <p class="text-base md:text-lg font-bold text-gray-900">Hari ini</p>
        </div>
      </div>
    </div>

    {{-- ══ Grid Kelas ══ --}}
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-5">
      @foreach($enrollments as $enrollment)
        @php
          $class = $enrollment->schoolClass;
          $colors = [
            ['from' => 'from-[#4F46E5]', 'to' => 'to-[#818CF8]', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'ring' => 'ring-indigo-100'],
            ['from' => 'from-violet-500', 'to' => 'to-purple-500', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'ring' => 'ring-violet-100'],
            ['from' => 'from-sky-500', 'to' => 'to-indigo-500', 'bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'ring' => 'ring-sky-100'],
            ['from' => 'from-blue-500', 'to' => 'to-violet-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'ring' => 'ring-blue-100'],
            ['from' => 'from-indigo-400', 'to' => 'to-blue-500', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'ring' => 'ring-indigo-100'],
          ];
          $c = $colors[$loop->index % count($colors)];
          $progress = $enrollment->progress_percentage ?? null;
        @endphp
        <a href="{{ route('siswa.classes.show', $class) }}"
          class="course-card rise-in group relative bg-white rounded-xl md:rounded-2xl border border-gray-100 overflow-hidden
                 hover:shadow-xl hover:shadow-indigo-200/60 hover:-translate-y-1 hover:ring-2 {{ $c['ring'] }}
                 transition-all duration-200"
          style="animation-delay: {{ 200 + $loop->index * 70 }}ms">

          {{-- Cover --}}
          <div class="relative h-12 sm:h-16 md:h-20 bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }} overflow-hidden">
            <div class="absolute -top-4 -right-4 md:-top-6 md:-right-6 w-16 h-16 md:w-24 md:h-24 rounded-full bg-white opacity-[0.12]"></div>
            <div class="absolute bottom-0 left-5 md:left-8 w-10 h-10 md:w-16 md:h-16 rounded-full bg-white opacity-[0.10] translate-y-1/2"></div>
            <span class="absolute top-1.5 right-1.5 md:top-3 md:right-3 text-[8px] md:text-[10px] font-bold uppercase tracking-wide bg-white/25 text-white px-1.5 md:px-2 py-0.5 md:py-1 rounded-full backdrop-blur-sm">
              {{ $class->rombel->name ?? '-' }}
            </span>
          </div>

          <div class="relative px-3 pb-3 md:px-5 md:pb-5">
            {{-- Avatar inisial, wiggle animasi --}}
            <div class="course-avatar w-9 h-9 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                        text-white font-bold flex items-center justify-center text-sm md:text-xl shrink-0
                        shadow-lg -mt-4.5 md:-mt-7 mb-2 md:mb-3 ring-[3px] md:ring-4 ring-white group-hover:scale-110 transition-transform duration-300">
              {{ strtoupper(substr($class->name, 0, 1)) }}
            </div>

            <h2 class="font-bold text-sm md:text-base text-gray-800 group-hover:text-[#4F46E5] transition-colors truncate">
              {{ $class->name }}
            </h2>
            <p class="text-[11px] md:text-sm text-gray-500 mt-0.5 flex items-center gap-1 md:gap-1.5">
              <svg class="w-3 h-3 md:w-3.5 md:h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 100-8 4 4 0 000 8zm6 4a4 4 0 10-8 0"/>
              </svg>
              <span class="truncate">{{ $class->teacher->user->name ?? '-' }}</span>
            </p>

            @if(!is_null($progress))
              <div class="mt-2.5 md:mt-4">
                <div class="flex items-center justify-between text-[10px] md:text-xs text-gray-500 mb-1">
                  <span>Progres</span>
                  <span class="font-semibold text-[#10B981]">{{ $progress }}%</span>
                </div>
                <div class="w-full h-1 md:h-1.5 bg-gray-100 rounded-full overflow-hidden">
                  <div class="progress-fill h-full bg-[#10B981] rounded-full" style="--target-width: {{ $progress }}%"></div>
                </div>
              </div>
            @endif

            <div class="flex items-center justify-between mt-2.5 md:mt-4 pt-2.5 md:pt-4 border-t border-gray-50 gap-1">
              <span class="inline-flex items-center gap-1 text-[10px] md:text-xs font-medium text-emerald-600 bg-emerald-50 px-1.5 md:px-2 py-0.5 md:py-1 rounded-full shrink-0">
                <span class="relative flex w-1.5 h-1.5 shrink-0">
                  <span class="absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70 animate-ping"></span>
                  <span class="relative inline-flex w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                </span>
                <span class="hidden xs:inline">Aktif</span>
              </span>
              <span class="text-[10px] md:text-xs font-mono font-semibold {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }} border px-1.5 md:px-2.5 py-0.5 md:py-1 rounded-md md:rounded-lg truncate">
                {{ $class->code }}
              </span>
            </div>
          </div>

          {{-- Panah hover — desktop/tablet saja --}}
          <div class="hidden sm:block absolute bottom-5 right-5 opacity-0 group-hover:opacity-100 translate-x-1 group-hover:translate-x-0 transition-all duration-300">
            <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>

<style>
/* ══ Entrance ══ */
@keyframes rise-in {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }

/* ══ Hero orb float ══ */
@keyframes float-slow {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(12px, -16px) scale(1.06); }
}
@keyframes float-medium {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(-14px, 10px) scale(1.04); }
}
@keyframes float-fast {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(8px, 8px) scale(0.96); }
}
.animate-float-slow   { animation: float-slow 8s ease-in-out infinite; }
.animate-float-medium { animation: float-medium 6.5s ease-in-out infinite; }
.animate-float-fast   { animation: float-fast 5.5s ease-in-out infinite; }

/* ══ Emoji lambai lucu di judul ══ */
@keyframes wave {
    0%, 60%, 100% { transform: rotate(0deg); }
    10% { transform: rotate(14deg); }
    20% { transform: rotate(-8deg); }
    30% { transform: rotate(14deg); }
    40% { transform: rotate(-4deg); }
    50% { transform: rotate(10deg); }
}
.animate-wave { animation: wave 2.5s ease-in-out infinite; transform-origin: 70% 70%; }

/* ══ Empty state bounce lembut ══ */
@keyframes bounce-gentle {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-8px); }
}
.animate-bounce-gentle { animation: bounce-gentle 2s ease-in-out infinite; }

/* ══ Ring pulsa status ══ */
@keyframes ping-slow {
    0%   { transform: scale(1);   opacity: .5; }
    75%, 100% { transform: scale(1.4); opacity: 0; }
}
.animate-ping-slow { animation: ping-slow 2.6s cubic-bezier(0,0,0.2,1) infinite; }

/* ══ Stat card hover ══ */
.stat-card { transition: transform .2s ease, box-shadow .2s ease; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -6px rgba(79,70,229,0.15); }

/* ══ Avatar wiggle sesekali ══ */
@keyframes avatar-wiggle {
    0%, 92%, 100% { transform: rotate(0deg); }
    93% { transform: rotate(-6deg); }
    95% { transform: rotate(6deg); }
    97% { transform: rotate(-4deg); }
    99% { transform: rotate(0deg); }
}
.course-avatar { animation: avatar-wiggle 6s ease-in-out infinite; }
.course-card:hover .course-avatar { animation-play-state: paused; }

/* ══ Progress bar mengisi dari 0 saat load ══ */
@keyframes fill-progress {
    from { width: 0%; }
    to   { width: var(--target-width); }
}
.progress-fill { width: var(--target-width); animation: fill-progress 1s cubic-bezier(.16,1,.3,1) both; animation-delay: .3s; }

@media (prefers-reduced-motion: reduce) {
    .rise-in, .animate-float-slow, .animate-float-medium, .animate-float-fast,
    .animate-wave, .animate-bounce-gentle, .animate-ping-slow, .course-avatar, .progress-fill {
        animation: none;
    }
}
</style>
@endsection