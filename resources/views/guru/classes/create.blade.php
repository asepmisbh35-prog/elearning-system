@extends('layouts.app')
@section('title', 'Buat Kelas')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 md:space-y-6"
     x-data="{
        subject: '{{ old('subject', '') }}',
        name: '{{ old('name', '') }}',
        focused: null,
        showTip: false,
     }"
     x-init="setTimeout(() => showTip = true, 400)">

    {{-- ============ HEADER + BACK BUTTON ============ --}}
    <div class="flex items-center gap-3" x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)">
        <a href="{{ route('guru.classes.index') }}"
           class="group flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div x-show="show" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Buat Kelas Baru</h2>
            <p class="text-xs md:text-sm text-gray-500">Kode kelas akan digenerate otomatis</p>
        </div>
    </div>

    {{-- ============ HERO CARD ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-7 text-white shadow-lg shadow-indigo-200/60"
         x-show="true" x-transition:enter="transition ease-out duration-500 delay-100" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0">

        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08] animate-[pulse_6s_ease-in-out_infinite]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center">
                <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-xs md:text-sm text-indigo-100">Kelas baru untuk siswa Anda</p>
                <p class="text-base md:text-lg font-semibold" x-text="name ? name : 'Nama kelas akan tampil di sini...'"></p>
            </div>
        </div>

        {{-- Live preview kode kelas --}}
        <div class="relative mt-4 md:mt-5 inline-flex items-center gap-2 bg-white/10 backdrop-blur rounded-xl px-3 py-2 border border-white/10">
            <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-xs md:text-sm font-mono tracking-wider"
                  x-text="subject ? subject.substring(0,3).toUpperCase() + '-XXXX' : 'MAT-XXXX'"></span>
            <span class="text-[10px] md:text-xs text-indigo-200">(preview pola kode)</span>
        </div>
    </div>

    {{-- ============ FORM CARD ============ --}}
   <form method="POST" action="{{ route('guru.classes.store') }}"
      x-data="{ loading: false }"
      @submit="loading = true"
      class="bg-white rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm p-5 md:p-7 space-y-5">
        @csrf

        {{-- Nama Kelas --}}
        <div x-data="{ err: {{ $errors->has('name') ? 'true' : 'false' }} }">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Kelas <span class="text-[#EF4444]">*</span>
            </label>
            <div class="relative">
                <input type="text" name="name" x-model="name"
                       @focus="focused = 'name'" @blur="focused = null"
                       value="{{ old('name') }}"
                       class="w-full border rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200
                              @error('name') border-[#EF4444] ring-2 ring-red-100 @else border-gray-200 @enderror
                              focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                       placeholder="contoh: Matematika Kelas 10A">
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                     x-show="focused === 'name'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
                    <span class="w-2 h-2 rounded-full bg-[#4F46E5] animate-ping absolute"></span>
                    <span class="w-2 h-2 rounded-full bg-[#4F46E5] relative"></span>
                </div>
            </div>
            @error('name') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Mata Pelajaran --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Mata Pelajaran <span class="text-[#EF4444]">*</span>
                </label>
                <input type="text" name="subject" x-model="subject"
                       @focus="focused = 'subject'" @blur="focused = null"
                       value="{{ old('subject') }}"
                       class="w-full border rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200
                              @error('subject') border-[#EF4444] ring-2 ring-red-100 @else border-gray-200 @enderror
                              focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                       placeholder="Matematika">
                @error('subject') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-400">Dipakai untuk generate kode kelas otomatis.</p>
            </div>

            {{-- Rombel --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Rombel <span class="text-[#EF4444]">*</span>
                </label>
                <select name="rombel_id"
                        @focus="focused = 'rombel'" @blur="focused = null"
                        class="w-full border rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 bg-white
                               @error('rombel_id') border-[#EF4444] ring-2 ring-red-100 @else border-gray-200 @enderror
                               focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
                    <option value="">-- Pilih Rombel --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ old('rombel_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }} @if($r->academic_year)({{ $r->academic_year }})@endif
                        </option>
                    @endforeach
                </select>
                @error('rombel_id') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3"
                      @focus="focused = 'desc'" @blur="focused = null"
                      class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm resize-none outline-none transition-all duration-200
                             focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                      placeholder="Deskripsi singkat kelas (opsional)...">{{ old('description') }}</textarea>
        </div>

        {{-- Info box --}}
        <div x-show="showTip"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-indigo-50 border border-indigo-100 rounded-xl p-3.5 flex gap-3">
            <svg class="w-5 h-5 text-[#4F46E5] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-indigo-700">
                Setelah kelas dibuat, kode kelas akan digenerate otomatis (contoh: <strong>MAT-2F3G</strong>).
                Bagikan kode ini ke siswa agar mereka bisa bergabung.
            </p>
        </div>

        {{-- Tombol aksi --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('guru.classes.index') }}"
               class="px-5 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all duration-200">
                Batal
            </a>
            <button type="submit"
        :disabled="loading"
                    class="relative px-6 py-2.5 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white rounded-xl text-sm font-medium
                           shadow-md shadow-indigo-200 hover:shadow-lg hover:shadow-indigo-300 hover:-translate-y-0.5
                           active:translate-y-0 transition-all duration-200 disabled:opacity-70 flex items-center gap-2">
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : 'Buat Kelas'"></span>
            </button>
        </div>
    </form>
</div>
@endsection