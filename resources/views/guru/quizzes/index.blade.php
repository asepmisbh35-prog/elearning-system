{{-- resources/views/guru/quizzes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Kuis — ' . $class->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 md:py-8" x-data="{ confirmDelete: false, deleteForm: null }">

    @php
        $publishedCount = $quizzes->where('status', 'published')->count();
        $ongoingCount   = $quizzes->filter(fn($q) => $q->isPublished() && $q->timeStatus() === 'ongoing')->count();
    @endphp

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-44 h-44 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.classes.show', $class) }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke kelas
            </a>

            <div class="flex items-start justify-between gap-4 mt-2">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-bold">Kuis</h1>
                    <p class="text-indigo-100 text-sm md:text-base truncate">{{ $class->name }}</p>
                </div>

                {{-- Featured icon w/ living animation --}}
                <div class="relative hidden sm:flex items-center justify-center shrink-0 w-14 h-14">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-white/20" style="animation: soft-pulse 2.8s ease-in-out infinite;"></span>
                    <span class="relative flex items-center justify-center w-12 h-12 rounded-2xl bg-white/15 backdrop-blur">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Stat chips --}}
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 rounded-lg px-2.5 py-1 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H6.75a2.25 2.25 0 01-2.25-2.25V5.25A2.25 2.25 0 016.75 3h6.879a1.5 1.5 0 011.06.44l4.122 4.12A1.5 1.5 0 0119.25 8.62v9.13a2.25 2.25 0 01-2.25 2.25z" />
                    </svg>
                    {{ $quizzes->count() }} total kuis
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 rounded-lg px-2.5 py-1 text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    {{ $publishedCount }} dipublish
                </span>
                @if ($ongoingCount > 0)
                    <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 rounded-lg px-2.5 py-1 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-300" style="animation: soft-pulse-dot 1.8s ease-in-out infinite;"></span>
                        {{ $ongoingCount }} berlangsung
                    </span>
                @endif
            </div>

            <a href="{{ route('guru.classes.quizzes.create', $class) }}"
               class="mt-4 inline-flex items-center gap-1.5 bg-white text-[#4338CA] px-4 py-2.5 rounded-xl font-medium text-sm shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buat Kuis
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm rise-in flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-[#10B981]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($quizzes as $i => $quiz)
            @php
                $timeStatus = $quiz->timeStatus();
                $statusColor = match($timeStatus) {
                    'upcoming' => 'bg-sky-50 text-sky-600',
                    'ongoing'  => 'bg-emerald-50 text-emerald-600',
                    'finished' => 'bg-gray-100 text-gray-500',
                };
                $statusDot = match($timeStatus) {
                    'upcoming' => 'bg-sky-500',
                    'ongoing'  => 'bg-emerald-500',
                    'finished' => 'bg-gray-400',
                };
                $statusLabel = match($timeStatus) {
                    'upcoming' => 'Akan Datang',
                    'ongoing'  => 'Sedang Berlangsung',
                    'finished' => 'Selesai',
                };
                $avatarColor = match(true) {
                    $quiz->status !== 'published' => ['bg' => 'bg-amber-50', 'text' => 'text-[#F59E0B]'],
                    $timeStatus === 'ongoing'      => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                    $timeStatus === 'upcoming'     => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600'],
                    default                          => ['bg' => 'bg-gray-100', 'text' => 'text-gray-400'],
                };

                $progressPercent = null;
                if ($quiz->isPublished() && $timeStatus === 'ongoing') {
                    $totalSecs   = $quiz->access_start_at->diffInSeconds($quiz->access_end_at) ?: 1;
                    $elapsedSecs = $quiz->access_start_at->diffInSeconds(now());
                    $progressPercent = max(0, min(100, round(($elapsedSecs / $totalSecs) * 100)));
                }
            @endphp
            <div class="bg-white border border-gray-200 rounded-2xl p-4 md:p-5 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-100 transition-all duration-200 rise-in" style="animation-delay: {{ min($i,10) * 50 }}ms">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 flex items-center justify-center w-10 h-10 md:w-11 md:h-11 rounded-xl {{ $avatarColor['bg'] }} {{ $avatarColor['text'] }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1.5">
                            <h3 class="font-semibold text-gray-900 truncate max-w-[220px] sm:max-w-none">{{ $quiz->title }}</h3>
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium {{ $quiz->status === 'published' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-[#F59E0B]' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $quiz->status === 'published' ? 'bg-emerald-500' : 'bg-[#F59E0B]' }}"></span>
                                {{ $quiz->status === 'published' ? 'Dipublish' : 'Draft' }}
                            </span>
                            @if ($quiz->isPublished())
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColor }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }} {{ $timeStatus === 'ongoing' ? 'animate-pulse' : '' }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            @endif
                        </div>

                        {{-- Meta as compact chip grid — replaces long wrapping middot line --}}
                        <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H6.75a2.25 2.25 0 01-2.25-2.25V5.25A2.25 2.25 0 016.75 3h6.879a1.5 1.5 0 011.06.44l4.122 4.12A1.5 1.5 0 0119.25 8.62v9.13a2.25 2.25 0 01-2.25 2.25z" />
                                </svg>
                                {{ $quiz->questions_count }} soal
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                                </svg>
                                {{ $quiz->duration_minutes }} menit
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                {{ $quiz->attempts_count }} percobaan
                            </span>
                            <span class="inline-flex items-center gap-1 col-span-2 sm:col-span-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25a2.25 2.25 0 012.25-2.25h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                </svg>
                                <span class="truncate">{{ $quiz->access_start_at->translatedFormat('d M H:i') }} — {{ $quiz->access_end_at->translatedFormat('d M H:i') }}</span>
                            </span>
                        </div>

                        {{-- Progres waktu untuk kuis yang sedang berlangsung --}}
                        @if (!is_null($progressPercent))
                            <div class="mt-2.5">
                                <div class="flex items-center justify-between text-[10px] text-gray-400 mb-1">
                                    <span>Waktu akses berjalan</span>
                                    <span class="font-semibold text-emerald-600">{{ $progressPercent }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#10B981] rounded-full transition-all duration-700" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3.5 pt-3 border-t border-gray-100 flex items-center justify-end sm:justify-start gap-1.5 sm:gap-2">
                    <a href="{{ route('guru.classes.quizzes.results.index', [$class, $quiz]) }}"
                       title="Hasil"
                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sky-600 bg-sky-50/0 hover:bg-sky-50 hover:scale-105 active:scale-95 transition-all text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        <span class="hidden sm:inline">Hasil</span>
                    </a>
                    <a href="{{ route('guru.classes.quizzes.edit', [$class, $quiz]) }}"
                       title="Edit"
                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-[#4F46E5] hover:bg-indigo-50 hover:scale-105 active:scale-95 transition-all text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                        </svg>
                        <span class="hidden sm:inline">Edit</span>
                    </a>

                    <form action="{{ route('guru.classes.quizzes.destroy', [$class, $quiz]) }}" method="POST" x-ref="deleteForm{{ $quiz->id }}">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button"
                            @click="confirmDelete = true; deleteForm = $refs['deleteForm{{ $quiz->id }}']"
                            title="Hapus"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-[#EF4444] hover:bg-red-50 hover:scale-105 active:scale-95 transition-all text-sm font-medium ml-auto sm:ml-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        <span class="hidden sm:inline">Hapus</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white border border-dashed border-gray-200 rounded-2xl rise-in">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="font-medium text-gray-600">Belum ada kuis</p>
                <p class="text-sm mt-1 text-gray-400">Klik "Buat Kuis" untuk membuat kuis pertama dari bank soal kamu.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="confirmDelete" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmDelete = false"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hapus Kuis?</h3>
            <p class="text-sm text-gray-500 mb-5">Semua data hasil dan percobaan siswa pada kuis ini juga akan ikut terhapus. Tindakan ini tidak bisa dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="confirmDelete = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                <button @click="deleteForm.submit()" class="flex-1 px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes soft-pulse {
    0%, 100% { transform: scale(1); opacity: .6; }
    50% { transform: scale(1.18); opacity: 0; }
}
@keyframes soft-pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: .35; }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection