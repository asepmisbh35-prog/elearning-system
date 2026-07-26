{{-- resources/views/guru/materials/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Materi')

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
    [x-cloak] { display: none !important; }
</style>

<div class="max-w-2xl mx-auto px-4 py-6 md:py-8 space-y-5 md:space-y-6">

    <div>
        <a href="{{ route('guru.meetings.materials.index', $meeting) }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke daftar materi
        </a>
    </div>

    {{-- ============ HERO ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-7 text-white">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-40 h-40 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="relative flex items-center gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs sm:text-sm text-indigo-200 uppercase tracking-wide">Tambah Materi Baru</p>
                <h1 class="text-lg sm:text-2xl font-bold truncate">Pertemuan {{ $meeting->order }} — {{ $meeting->topic }}</h1>
            </div>
        </div>
    </div>

    {{-- ============ FORM ============ --}}
    <div class="rise-in relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-7 overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#4F46E5] via-[#7C3AED] to-[#0EA5E9]"></div>

        <div class="flex items-center gap-2.5 mb-5">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-900 text-sm md:text-base">Data Materi</h2>
        </div>

        <form method="POST" action="{{ route('guru.meetings.materials.store', $meeting) }}"
              x-data="{ saving: false }" @submit="saving = true"
              class="space-y-5">
            @csrf

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Materi <span class="text-[#EF4444]">*</span></label>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full border rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] {{ $errors->has('title') ? 'border-[#EF4444]' : 'border-gray-200' }}"
                           placeholder="Contoh: Hukum Newton I" required>
                </div>
                @error('title') <p class="text-[#EF4444] text-xs sm:text-sm mt-1.5 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                          placeholder="Opsional — ringkasan singkat materi ini">{{ old('description') }}</textarea>
            </div>

            {{-- Urutan + Estimasi --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Urutan Tampil <span class="text-[#EF4444]">*</span></label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <input type="number" name="order" value="{{ old('order', $nextOrder) }}" min="1"
                               class="w-full border rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] {{ $errors->has('order') ? 'border-[#EF4444]' : 'border-gray-200' }}">
                    </div>
                    @error('order') <p class="text-[#EF4444] text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estimasi Waktu (menit) <span class="text-[#EF4444]">*</span></label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', 10) }}" min="1" max="300"
                               class="w-full border rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] {{ $errors->has('estimated_minutes') ? 'border-[#EF4444]' : 'border-gray-200' }}">
                    </div>
                    @error('estimated_minutes') <p class="text-[#EF4444] text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Status — kartu pilihan --}}
            <div x-data="{ status: '{{ old('status', 'draft') }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <input type="hidden" name="status" :value="status">
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="status = 'draft'"
                            :class="status === 'draft' ? 'border-amber-400 bg-amber-50 shadow-md shadow-amber-100' : 'border-gray-200 hover:border-amber-200'"
                            class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-2 transition-all duration-200 text-left">
                        <span class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <span class="text-sm font-medium text-gray-800 block">Draft</span>
                            <span class="text-[11px] text-gray-400">Tidak terlihat siswa</span>
                        </div>
                        <svg x-show="status === 'draft'" class="w-4 h-4 text-amber-500 ml-auto shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </button>
                    <button type="button" @click="status = 'published'"
                            :class="status === 'published' ? 'border-emerald-400 bg-emerald-50 shadow-md shadow-emerald-100' : 'border-gray-200 hover:border-emerald-200'"
                            class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-2 transition-all duration-200 text-left">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <span class="text-sm font-medium text-gray-800 block">Published</span>
                            <span class="text-[11px] text-gray-400">Terlihat siswa</span>
                        </div>
                        <svg x-show="status === 'published'" class="w-4 h-4 text-emerald-500 ml-auto shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>

            {{-- Sequential Unlock — card dengan glow saat aktif --}}
            <div class="relative rounded-xl p-4 border-2 transition-all duration-300"
                 x-data="{ seq: {{ old('sequential_unlock') ? 'true' : 'false' }} }"
                 :class="seq ? 'border-indigo-200 bg-indigo-50/40' : 'border-gray-200'">
                <label class="flex items-center justify-between gap-3 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 transition-colors duration-300"
                              :class="seq ? 'bg-[#4F46E5]' : 'bg-gray-100'">
                            <svg class="w-4.5 h-4.5 transition-colors duration-300" :class="seq ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <div>
                            <span class="font-medium text-gray-800 text-sm">Sequential Unlock</span>
                            <p class="text-xs sm:text-sm text-gray-500">Siswa harus menyelesaikan materi sebelumnya</p>
                        </div>
                    </div>
                    <button type="button" @click="seq = !seq" role="switch" :aria-checked="seq"
                            class="relative shrink-0 w-12 h-6.5 rounded-full transition-colors duration-300"
                            :class="seq ? 'bg-[#4F46E5]' : 'bg-gray-200'">
                        <span class="absolute top-0.5 left-0.5 w-5.5 h-5.5 bg-white rounded-full shadow flex items-center justify-center transition-transform duration-300"
                              :class="seq ? 'translate-x-5.5' : 'translate-x-0'">
                            <svg x-show="seq" class="w-3 h-3 text-[#4F46E5]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    </button>
                    <input type="checkbox" name="sequential_unlock" value="1" class="hidden" x-model="seq">
                </label>
                <div x-show="seq" x-cloak
                     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-4 pl-12 space-y-2">
                    <p class="text-xs font-medium text-gray-500 mb-1">Metode Unlock</p>
                    <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-lg border border-transparent hover:border-indigo-200 hover:bg-white transition-all duration-150">
                        <input type="radio" name="unlock_method" value="scroll"
                               {{ old('unlock_method', 'scroll') === 'scroll' ? 'checked' : '' }}
                               class="w-4 h-4 text-[#4F46E5] focus:ring-indigo-200">
                        <span class="text-sm text-gray-700">Scroll 80% halaman materi sebelumnya</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-lg border border-transparent hover:border-indigo-200 hover:bg-white transition-all duration-150">
                        <input type="radio" name="unlock_method" value="manual"
                               {{ old('unlock_method') === 'manual' ? 'checked' : '' }}
                               class="w-4 h-4 text-[#4F46E5] focus:ring-indigo-200">
                        <span class="text-sm text-gray-700">Siswa tekan tombol "Tandai Selesai"</span>
                    </label>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-1">
                <button type="submit" :disabled="saving"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-md shadow-indigo-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 disabled:opacity-70">
                    <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan Materi'"></span>
                </button>
                <a href="{{ route('guru.meetings.materials.index', $meeting) }}"
                   class="text-gray-500 hover:text-gray-700 text-sm font-medium px-3 py-2.5 rounded-xl hover:bg-gray-50 transition-colors duration-200">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection