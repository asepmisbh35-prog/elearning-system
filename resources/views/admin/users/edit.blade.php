@extends('layouts.app')
@section('title', 'Edit Pengguna')

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
            <h2 class="text-xl font-bold text-gray-900">Edit Pengguna</h2>
            <p class="text-sm text-gray-500">{{ $user->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf @method('PATCH')

        {{-- Badge role --}}
        <div class="flex items-center gap-2 pb-4 border-b border-gray-100">
            @if($user->role === 'guru')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800">Guru</span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Siswa</span>
            @endif
            <span class="text-sm text-gray-500">{{ $user->email }}</span>
        </div>

        {{-- Nama & HP --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border @error('name') border-red-400 @else border-gray-300 @enderror
                              rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP/WA</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                       placeholder="08xxxxxxxxxx">
            </div>
        </div>

        {{-- Data Guru --}}
        @if($user->isGuru() && $user->teacher)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-100">Data Guru</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $user->teacher->nip) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject" value="{{ old('subject', $user->teacher->subject) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
            </div>
        </div>
        @endif

        {{-- Data Siswa --}}
        @if($user->isSiswa() && $user->student)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-100">Data Siswa</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                    <input type="text" value="{{ $user->student->nisn }}" disabled
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                    <p class="mt-1 text-xs text-gray-400">NISN tidak dapat diubah</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rombel</label>
                    <select name="rombel_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                        <option value="">-- Pilih Rombel --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}"
                                {{ old('rombel_id', $user->student->rombel_id) == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endif

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection