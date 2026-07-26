@extends('layouts.app')
@section('title', 'Tambah Siswa')

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
            <h2 class="text-xl font-bold text-gray-900">Tambah Siswa</h2>
            <p class="text-sm text-gray-500">Buat akun siswa baru secara manual</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.siswa.store') }}"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        {{-- Akun --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                Informasi Akun
            </h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border @error('name') border-red-400 @else border-gray-300 @enderror
                                  rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="Nama lengkap siswa">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full border @error('email') border-red-400 @else border-gray-300 @enderror
                                      rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                               placeholder="email@sekolah.sch.id">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password"
                               class="w-full border @error('password') border-red-400 @else border-gray-300 @enderror
                                      rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                               placeholder="Min. 8 karakter, huruf & angka">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Siswa --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                Data Siswa
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        NISN <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}"
                           maxlength="10"
                           class="w-full border @error('nisn') border-red-400 @else border-gray-300 @enderror
                                  rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="10 digit angka">
                    @error('nisn')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP/WA Orang Tua</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rombel</label>
                    <select name="rombel_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                        <option value="">-- Belum ditentukan --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ old('rombel_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                                @if($r->academic_year) ({{ $r->academic_year }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">Bisa diisi belakangan lewat menu Edit.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                Simpan Siswa
            </button>
        </div>
    </form>
</div>
@endsection