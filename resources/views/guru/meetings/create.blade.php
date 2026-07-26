{{-- resources/views/guru/meetings/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Pertemuan')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8" x-data="{ saving: false }">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.classes.meetings.index', $class) }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke daftar pertemuan
            </a>
            <h1 class="text-xl md:text-2xl font-bold mt-2">Tambah Pertemuan Baru</h1>
            <p class="text-indigo-100 text-sm md:text-base">{{ $class->name }}</p>

            <div class="mt-4">
                <div class="inline-flex bg-white/10 border border-white/15 rounded-lg px-2.5 py-1 text-xs font-semibold">
                    Akan menjadi Pertemuan ke-{{ $nextOrder }}
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('guru.classes.meetings.store', $class) }}"
          @submit="saving = true"
          class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-5 md:p-6 space-y-5 rise-in" style="animation-delay: 80ms">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Topik Pertemuan <span class="text-[#EF4444]">*</span></label>
            <input type="text" name="topic" value="{{ old('topic') }}"
                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition @error('topic') border-[#EF4444] @enderror"
                   placeholder="Contoh: Hukum Newton I" required>
            @error('topic')
                <p class="text-[#EF4444] text-sm mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="2"
                      class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition"
                      placeholder="Opsional">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal (opsional)</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
                <option value="draft"     {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (tidak terlihat siswa)</option>
                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (terlihat siswa)</option>
            </select>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 bg-[#4F46E5] text-white px-6 py-2.5 rounded-xl hover:bg-[#4338CA] hover:-translate-y-0.5 active:scale-95 transition-all font-medium disabled:opacity-70 disabled:pointer-events-none">
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Pertemuan'"></span>
            </button>
            <a href="{{ route('guru.classes.meetings.index', $class) }}"
               class="text-gray-500 hover:text-gray-700 text-sm">Batal</a>
        </div>
    </form>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection