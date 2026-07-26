@extends('layouts.app')
@section('title', 'Tambah Guru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Back --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Tambah Guru</h2>
            <p class="text-sm text-gray-500">Buat akun guru baru</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.guru.store') }}"
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
                           placeholder="Nama lengkap guru">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
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

        {{-- Detail Guru --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                Data Guru <span class="font-normal text-gray-400">(opsional)</span>
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="Nomor Induk Pegawai">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP/WA</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="Matematika, Bahasa Indonesia, ...">
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                Simpan Guru
            </button>
        </div>
    </form>
</div>
@endsection