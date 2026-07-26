@extends('layouts.app')

@section('title', 'Identitas Sekolah')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-6 md:p-8 text-white rise-in">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>
        <div class="relative">
            <h1 class="text-lg md:text-2xl font-bold">Identitas Sekolah</h1>
            <p class="text-indigo-100 text-sm mt-1">Atur nama & logo sekolah yang tampil di halaman login dan sidebar.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl md:rounded-3xl border border-[#E2E8F0] p-5 md:p-7 rise-in" style="animation-delay:80ms">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
              x-data="{ saving: false, preview: @js($setting->logo_url) }"
              @submit="saving = true">
            @csrf
            @method('PATCH')

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Logo Sekolah</label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center overflow-hidden shrink-0">
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-full object-cover" alt="Preview logo">
                        </template>
                        <template x-if="!preview">
                            <svg class="w-8 h-8 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                               @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)"
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-[#4F46E5] hover:file:bg-indigo-100 transition">
                        <p class="text-xs text-gray-400 mt-1.5">PNG, JPG, WEBP, atau SVG. Maks 2MB.</p>
                        @error('logo')
                            <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="school_name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Sekolah</label>
                <input type="text" id="school_name" name="school_name"
                       value="{{ old('school_name', $setting->school_name) }}"
                       required maxlength="100"
                       class="w-full px-4 py-2.5 border-2 rounded-xl text-sm font-medium focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 transition-all duration-200
                              {{ $errors->has('school_name') ? 'border-red-300' : 'border-gray-200' }}"
                       placeholder="Contoh: SMA Negeri 1 Bandung">
                @error('school_name')
                    <p class="text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1.5">Nama ini akan tampil di halaman login dan sidebar semua role.</p>
            </div>

            <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 disabled:opacity-70 disabled:hover:translate-y-0 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-all duration-200">
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </form>
    </div>
</div>

<style>
@keyframes rise-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection