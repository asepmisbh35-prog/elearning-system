{{-- resources/views/guru/announcements/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Buat Pengumuman')

@section('content')
<div class="max-w-2xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-10"
     x-data="{
        title: @js(old('title', '')),
        content: @js(old('content', '')),
        imagePreview: null,
        imageName: null,
        dragOver: false,
        onFile(file) {
            if (!file) return;
            this.imageName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => { this.imagePreview = e.target.result; };
            reader.readAsDataURL(file);
        },
        handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            if (file) { this.$refs.imageInput.files = e.dataTransfer.files; this.onFile(file); }
        },
        clearImage() {
            this.imagePreview = null;
            this.imageName = null;
            this.$refs.imageInput.value = '';
        }
     }">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.announcements.index') }}"
               class="inline-flex items-center gap-1.5 text-xs md:text-sm text-indigo-100 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>

            <div class="flex items-center gap-3 mt-3">
                <div class="w-10 h-10 md:w-11 md:h-11 shrink-0 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold">Buat Pengumuman</h1>
                    <p class="text-indigo-100 text-xs md:text-sm">Kirim informasi ke kelas yang kamu ampu</p>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="flex items-start gap-2 bg-[#FEF2F2] border border-[#FECACA] text-[#B91C1C] text-sm rounded-xl md:rounded-2xl p-4">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <ul class="list-disc list-inside space-y-1 flex-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('guru.announcements.store') }}" enctype="multipart/form-data"
          id="announcementForm" class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 space-y-5 shadow-sm shadow-indigo-100/40">
        @csrf

        {{-- Judul --}}
        <div>
            <label class="flex items-center justify-between text-sm font-medium text-gray-700 mb-1.5">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10"/></svg>
                    Judul
                </span>
                <span class="text-[11px] text-gray-400" x-text="`${title.length} karakter`"></span>
            </label>
            <input type="text" name="title" x-model="title" required
                   placeholder="Contoh: Libur Ujian Tengah Semester"
                   class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition">
        </div>

        {{-- Isi pengumuman --}}
        <div>
            <label class="flex items-center justify-between text-sm font-medium text-gray-700 mb-1.5">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Isi Pengumuman
                </span>
                <span class="text-[11px] text-gray-400" x-text="`${content.length} karakter`"></span>
            </label>
            <textarea name="content" rows="5" x-model="content" required
                      placeholder="Tulis isi pengumuman di sini..."
                      class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition"></textarea>
        </div>

        {{-- Gambar (drag & drop preview) --}}
        <div>
            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg>
                Gambar (opsional)
            </label>

            <div x-show="!imagePreview"
                 @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop($event)"
                 @click="$refs.imageInput.click()"
                 class="cursor-pointer border-2 border-dashed rounded-xl p-6 text-center transition-all duration-200"
                 :class="dragOver ? 'border-[#4F46E5] bg-indigo-50 scale-[1.01]' : 'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/30'">
                <svg class="w-8 h-8 mx-auto text-indigo-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-sm text-gray-500">Seret gambar ke sini, atau <span class="text-[#4F46E5] font-medium">klik untuk pilih</span></p>
                <p class="text-xs text-gray-400 mt-1">PNG, JPG, atau WEBP</p>
            </div>

            <div x-show="imagePreview" x-transition class="relative rounded-xl overflow-hidden border-2 border-indigo-100">
                <img :src="imagePreview" class="w-full max-h-56 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                <div class="absolute bottom-2 left-3 right-3 flex items-center justify-between">
                    <span class="text-white text-xs font-medium truncate bg-black/30 backdrop-blur px-2 py-1 rounded-lg" x-text="imageName"></span>
                    <button type="button" @click="clearImage()"
                            class="w-7 h-7 rounded-full bg-white/90 hover:bg-white text-[#EF4444] flex items-center justify-center shrink-0 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <input type="file" name="image" accept="image/*" x-ref="imageInput" class="hidden"
                   @change="onFile($event.target.files[0])">
        </div>

        {{-- Guru hanya boleh mengirim pengumuman ke kelas yang diampu, jadi target_type dikunci ke "class" --}}
        <input type="hidden" name="target_type" value="class">

        {{-- Pilih kelas --}}
        <div>
            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.5v-9.75l-6-3.375m-6 3.375L12 14"/></svg>
                Pilih Kelas
            </label>
            <div class="relative">
                <select name="school_class_id" required
                        class="w-full appearance-none border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition bg-white">
                    @forelse ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @empty
                        <option value="" disabled>Anda belum memiliki kelas</option>
                    @endforelse
                </select>
                <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Jadwal --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jadwal Tayang <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <input type="datetime-local" name="publish_at"
                       class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition">
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                    <svg class="w-3.5 h-3.5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kedaluwarsa <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <input type="datetime-local" name="expires_at"
                       class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition">
            </div>
        </div>

        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 active:scale-[0.98] transition-all font-semibold text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            Terbitkan Pengumuman
        </button>
    </form>
</div>
@endsection