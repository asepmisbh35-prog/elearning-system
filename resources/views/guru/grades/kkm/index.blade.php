{{-- resources/views/guru/grades/kkm/index.blade.php --}}
@extends('layouts.app')
@section('title', 'KKM — ' . $class->name)

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rise-in { animation: rise-in .45s ease both; }
</style>

<div class="max-w-2xl mx-auto px-4 py-6 md:py-8 space-y-5 md:space-y-6">
    <div>
        <a href="{{ route('guru.classes.show', $class) }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke kelas
        </a>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mt-2">KKM Kelas</h1>
        <p class="text-sm text-gray-500">{{ $class->name }}</p>
    </div>

    @if (session('success'))
        <div class="rise-in bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- ============ HERO KKM AKTIF ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-6 md:p-8 text-white text-center">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <div class="w-11 h-11 md:w-12 md:h-12 mx-auto rounded-2xl bg-white/15 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xs md:text-sm text-indigo-200 uppercase tracking-wide mb-1">KKM Aktif untuk Kelas Ini</p>
            <p class="text-4xl md:text-5xl font-bold tabular-nums">{{ $activeKkm }}</p>
            <p class="text-xs md:text-sm text-indigo-200 mt-2">
                @if ($history->isEmpty())
                    Mengikuti KKM sekolah ({{ $schoolDefault->value ?? 70 }})
                @else
                    Override khusus kelas ini
                @endif
            </p>
        </div>
    </div>

    {{-- ============ FORM OVERRIDE ============ --}}
    <div class="rise-in bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-7">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-900 text-sm md:text-base">Atur Override KKM untuk Kelas Ini</h2>
        </div>

        <form method="POST" action="{{ route('guru.classes.kkm.store', $class) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nilai KKM (0-100)</label>
                <input type="number" name="value" min="0" max="100" required
                       class="w-32 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Berlaku Mulai</label>
                <input type="datetime-local" name="effective_from" value="{{ now()->format('Y-m-d\TH:i') }}" required
                       class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
            </div>
            <p class="text-xs text-gray-400">Perubahan hanya berlaku untuk penilaian ke depan, tidak mengubah histori.</p>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-md shadow-indigo-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Override
            </button>
        </form>
    </div>

    {{-- ============ HISTORI ============ --}}
    <div class="rise-in bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-7">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-900 text-sm md:text-base">Histori Override Kelas Ini</h2>
        </div>

        <div class="space-y-1">
            @forelse ($history as $item)
                <div class="flex items-center justify-between gap-3 text-xs sm:text-sm px-3 py-2.5 rounded-xl hover:bg-indigo-50/40 transition-colors duration-150 border-b border-gray-50 last:border-b-0">
                    <span class="inline-flex items-center gap-1.5 font-medium text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#4F46E5] shrink-0"></span>
                        KKM = {{ $item->value }}
                    </span>
                    <span class="text-gray-400">Berlaku sejak {{ $item->effective_from->translatedFormat('d M Y HH:mm') }}</span>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="w-10 h-10 mx-auto rounded-2xl bg-indigo-50 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400">Belum ada override, mengikuti KKM sekolah.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection