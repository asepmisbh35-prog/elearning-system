{{-- resources/views/guru/assignments/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Buat Tugas')

@section('content')
<div class="max-w-2xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-10">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.classes.assignments.index', $class) }}"
               class="inline-flex items-center gap-1.5 text-xs md:text-sm text-indigo-100 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke daftar tugas
            </a>

            <div class="flex items-center gap-3 mt-3">
                <div class="w-10 h-10 md:w-11 md:h-11 shrink-0 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold">Buat Tugas Baru</h1>
                    <p class="text-indigo-100 text-xs md:text-sm">{{ $class->name }}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-2 bg-[#FEF2F2] border border-[#FECACA] text-[#B91C1C] text-sm rounded-xl md:rounded-2xl p-4">
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

    <form method="POST" action="{{ route('guru.classes.assignments.store', $class) }}"
          enctype="multipart/form-data" x-data="{ targetType: 'all' }"
          class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 space-y-5 shadow-sm shadow-indigo-100/40">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Tugas <span class="text-[#EF4444]">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}"
                   placeholder="Contoh: Laporan Praktikum Fotosintesis"
                   class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Instruksi</label>
            <textarea name="instructions" rows="5"
                      placeholder="Jelaskan instruksi tugas secara lengkap"
                      class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition">{{ old('instructions') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Batas Waktu <span class="text-[#EF4444]">*</span>
                </label>
                <input type="datetime-local" name="due_date" value="{{ old('due_date') }}"
                       class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition" required>
            </div>
            <div>
                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.9 6.26L21.6 9l-4.8 4.4L18.2 21 12 17.4 5.8 21l1.4-7.6L2.4 9l6.7-.74L12 2z"/></svg>
                    Nilai Maksimal <span class="text-[#EF4444]">*</span>
                </label>
                <input type="number" name="max_score" value="{{ old('max_score', 100) }}" min="1" max="1000"
                       class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Komponen Penilaian</label>
            <div class="relative">
                <select name="grade_component_id"
                        class="w-full appearance-none border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition bg-white">
                    <option value="">— Tidak dikaitkan —</option>
                    @foreach ($gradeComponents as $gc)
                        <option value="{{ $gc->id }}" {{ old('grade_component_id') == $gc->id ? 'selected' : '' }}>{{ $gc->name }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            @if ($gradeComponents->isEmpty())
                <p class="text-xs text-gray-400 mt-1.5">Belum ada komponen penilaian di kelas ini.</p>
            @endif
        </div>

        <div>
            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                Lampiran File <span class="text-gray-400 font-normal">(opsional, maks 5 file, total 20MB)</span>
            </label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-indigo-300 transition">
                <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                       class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-[#4338CA] hover:file:bg-indigo-100 file:font-medium file:text-sm">
            </div>
            <p class="text-xs text-gray-400 mt-1.5">Format: PDF, Word, JPG, PNG, ZIP</p>
        </div>

        {{-- Target siswa --}}
        <div class="border-2 border-gray-100 rounded-xl p-4">
            <label class="block text-sm font-medium text-gray-700 mb-3">Untuk Siswa</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-1">
                <label class="flex items-center gap-2.5 border-2 rounded-xl px-3.5 py-2.5 cursor-pointer transition-all duration-200"
                       :class="targetType === 'all' ? 'border-[#4F46E5] bg-indigo-50' : 'border-gray-200 hover:border-indigo-200'">
                    <input type="radio" name="target_type" value="all" x-model="targetType" checked class="text-[#4F46E5] focus:ring-indigo-200">
                    <span class="text-sm text-gray-700">Semua siswa di kelas</span>
                </label>
                <label class="flex items-center gap-2.5 border-2 rounded-xl px-3.5 py-2.5 cursor-pointer transition-all duration-200"
                       :class="targetType === 'specific' ? 'border-[#4F46E5] bg-indigo-50' : 'border-gray-200 hover:border-indigo-200'">
                    <input type="radio" name="target_type" value="specific" x-model="targetType" class="text-[#4F46E5] focus:ring-indigo-200">
                    <span class="text-sm text-gray-700">Siswa tertentu</span>
                </label>
            </div>

            <div x-show="targetType === 'specific'" x-cloak x-transition class="space-y-1.5 max-h-48 overflow-y-auto border-t border-gray-100 pt-3 mt-2">
                @forelse ($students as $student)
                    <label class="flex items-center gap-2.5 cursor-pointer text-sm px-2 py-1.5 rounded-lg hover:bg-indigo-50/50 transition">
                        <input type="checkbox" name="target_student_ids[]" value="{{ $student->id }}" class="text-[#4F46E5] rounded focus:ring-indigo-200">
                        <span class="text-gray-700">{{ $student->nama_lengkap ?? $student->user->name ?? '-' }}</span>
                    </label>
                @empty
                    <p class="text-xs text-gray-400">Belum ada siswa terdaftar di kelas ini.</p>
                @endforelse
            </div>
            <p class="text-xs text-[#92400E] bg-[#FFFBEB] rounded-lg px-3 py-2 mt-2.5 flex items-start gap-1.5" x-show="targetType === 'specific'" x-cloak x-transition>
                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <span>Siswa yang tidak dipilih tidak akan melihat tugas ini sama sekali.</span>
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
            <div class="relative">
                <select name="status"
                        class="w-full appearance-none border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition bg-white">
                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (belum terlihat siswa)</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
                <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 active:scale-[0.98] transition-all font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-6 4l3 3m0 0l3-3m-3 3V3"/></svg>
                Simpan Tugas
            </button>
            <a href="{{ route('guru.classes.assignments.index', $class) }}"
               class="w-full sm:w-auto text-center text-gray-500 hover:text-gray-700 text-sm py-3 sm:py-0 transition">Batal</a>
        </div>
    </form>
</div>
@endsection