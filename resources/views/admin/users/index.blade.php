@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    {{-- Header action buttons --}}
<div class="flex gap-2">
    <a href="{{ route('admin.users.import') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Import Siswa
    </a>
    <a href="{{ route('admin.users.siswa.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Siswa
    </a>
    <a href="{{ route('admin.users.guru.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Guru
    </a>
</div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nama atau email..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
        </div>
        <div class="min-w-[130px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Role</label>
            <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Semua Role</option>
                <option value="guru"  {{ request('role') === 'guru'  ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
        </div>
        <div class="min-w-[130px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        @if($rombels->isNotEmpty())
        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Rombel</label>
            <select name="rombel_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Semua Rombel</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                Filter
            </button>
            @if(request()->hasAny(['search','role','status','rombel_id']))
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- Import errors --}}
    @if(session('import_errors'))
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <p class="text-sm font-medium text-amber-800 mb-2">Beberapa baris gagal diimport:</p>
        <ul class="text-xs text-amber-700 space-y-0.5 list-disc list-inside">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-8">#</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Role</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Info</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-400 text-xs">
                            {{ $users->firstItem() + $loop->index }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}"
                                     class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                <span class="font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if($user->role === 'guru')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">Guru</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Siswa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            @if($user->isGuru() && $user->teacher)
                                {{ $user->teacher->subject ?? '-' }}<br>
                                NIP: {{ $user->teacher->nip ?? '-' }}
                            @elseif($user->isSiswa() && $user->student)
                                NISN: {{ $user->student->nisn ?? '-' }}<br>
                                {{ $user->student->rombel?->name ?? 'Belum di rombel' }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    @if($user->is_active)
                                    <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                onclick="return confirm('Nonaktifkan {{ $user->name }}?')"
                                                class="text-xs text-red-600 hover:text-red-800 font-medium transition">
                                            Nonaktifkan
                                        </button>
                                    </form>
                                    @else
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs text-green-600 hover:text-green-800 font-medium transition">
                                            Aktifkan
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection