@extends('layouts.app')
@section('title', 'Import Siswa')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Import Siswa</h2>
            <p class="text-sm text-gray-500">Upload file Excel untuk import data siswa massal</p>
        </div>
    </div>

    {{-- Format panduan --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800 mb-1">Format File Excel</p>
                <p class="text-xs text-blue-700">
                    Kolom A: <strong>Nama Lengkap</strong> &nbsp;|&nbsp;
                    Kolom B: <strong>NISN</strong> (10 digit angka)<br>
                    Baris pertama adalah header (akan dilewati otomatis).
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.import.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Rombel Tujuan <span class="text-red-500">*</span>
            </label>
            <select name="rombel_id"
                    class="w-full border @error('rombel_id') border-red-400 @else border-gray-300 @enderror
                           rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                <option value="">-- Pilih Rombel --</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}" {{ old('rombel_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->name }}
                    </option>
                @endforeach
            </select>
            @error('rombel_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                File Excel (.xlsx / .xls) <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2
                        @error('file') border-red-400 @else border-gray-300 @enderror
                        border-dashed rounded-lg hover:border-indigo-400 transition cursor-pointer"
                 onclick="document.getElementById('file-input').click()">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-sm text-gray-600" id="file-label">
                        <span class="text-indigo-600 font-medium">Klik untuk pilih file</span>
                    </p>
                    <p class="text-xs text-gray-400">xlsx, xls — maks. 5 MB</p>
                </div>
            </div>
            <input id="file-input" name="file" type="file" accept=".xlsx,.xls" class="hidden"
                   onchange="document.getElementById('file-label').textContent = this.files[0]?.name || 'Klik untuk pilih file'">
            @error('file')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                Upload &amp; Import
            </button>
        </div>
    </form>
</div>
@endsection