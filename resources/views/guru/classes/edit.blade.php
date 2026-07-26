@extends('layouts.app')
@section('title', 'Edit Kelas')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 md:space-y-6"
     x-data="{
        name: '{{ old('name', $class->name) }}',
        active: {{ old('is_active', $class->is_active) ? 'true' : 'false' }},
        focused: null,
     }">

    {{-- ============ HEADER + BACK BUTTON ============ --}}
    <div class="flex items-center gap-3" x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)">
        <a href="{{ route('guru.classes.show', $class) }}"
           class="group flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div x-show="show" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Edit Kelas</h2>
            <p class="text-xs md:text-sm text-gray-500">{{ $class->name }}</p>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs md:text-sm text-indigo-100">Mengedit kelas</p>
                <p class="text-base md:text-lg font-semibold" x-text="name ? name : 'Nama kelas...'"></p>
            </div>
        </div>

        {{-- Status badge live --}}
        <div class="relative mt-4 md:mt-5 inline-flex items-center gap-2 bg-white/10 backdrop-blur rounded-xl px-3 py-2 border border-white/10 transition-colors duration-300">
            <span class="w-2 h-2 rounded-full transition-colors duration-300" :class="active ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            <span class="text-xs md:text-sm font-medium" x-text="active ? 'Kelas Aktif' : 'Kelas Nonaktif'"></span>
            <span class="text-[10px] md:text-xs text-indigo-200">(kode: {{ $class->code ?? '-' }})</span>
        </div>
    </div>

    {{-- ============ FORM CARD ============ --}}
    <form method="POST" action="{{ route('guru.classes.update', $class) }}"
          class="bg-white rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm p-5 md:p-7 space-y-5">
        @csrf @method('PATCH')

        {{-- Nama Kelas --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Kelas <span class="text-[#EF4444]">*</span>
            </label>
            <div class="relative">
                <input type="text" name="name" x-model="name"
                       @focus="focused = 'name'" @blur="focused = null"
                       value="{{ old('name', $class->name) }}"
                       class="w-full border rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200
                              @error('name') border-[#EF4444] ring-2 ring-red-100 @else border-gray-200 @enderror
                              focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none"
                     x-show="focused === 'name'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
                    <span class="w-2 h-2 rounded-full bg-[#4F46E5] animate-ping absolute"></span>
                    <span class="w-2 h-2 rounded-full bg-[#4F46E5] relative"></span>
                </div>
            </div>
            @error('name') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3"
                      @focus="focused = 'desc'" @blur="focused = null"
                      class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm resize-none outline-none transition-all duration-200
                             focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">{{ old('description', $class->description) }}</textarea>
        </div>

        {{-- Toggle Kelas Aktif (animated switch) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Kelas</label>
            <button type="button" @click="active = !active"
                    class="w-full flex items-center justify-between gap-3 border border-gray-200 rounded-xl px-3.5 py-3 hover:border-indigo-200 transition-colors duration-200">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 transition-colors duration-300" :class="active ? 'text-[#10B981]' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Kelas Aktif</span>
                </div>

                {{-- switch --}}
                <span class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300"
                      :class="active ? 'bg-[#4F46E5]' : 'bg-gray-300'">
                    <span class="inline-block h-4.5 w-4.5 h-[18px] w-[18px] transform rounded-full bg-white shadow transition-transform duration-300"
                          :class="active ? 'translate-x-[22px]' : 'translate-x-[3px]'"></span>
                </span>
            </button>
            <input type="hidden" name="is_active" :value="active ? 1 : 0">
        </div>

        {{-- Tombol aksi --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('guru.classes.show', $class) }}"
               class="px-5 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all duration-200">
                Batal
            </a>
            <button type="submit"
                    x-data="{ loading: false }"
                    @click="loading = true"
                    :disabled="loading"
                    class="relative px-6 py-2.5 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white rounded-xl text-sm font-medium
                           shadow-md shadow-indigo-200 hover:shadow-lg hover:shadow-indigo-300 hover:-translate-y-0.5
                           active:translate-y-0 transition-all duration-200 disabled:opacity-70 flex items-center gap-2">
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </div>
    </form>
</div>
@endsection