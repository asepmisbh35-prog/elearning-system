{{-- resources/views/admin/kkm/index.blade.php --}}
@extends('layouts.app')
@section('title', 'KKM Sekolah')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" x-data="{ applyHistory: false }">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">KKM Sekolah</h1>
        <p class="text-gray-500">Kriteria Ketuntasan Minimal berlaku untuk semua kelas, kecuali guru mengatur override.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 text-center">
        <p class="text-sm text-gray-500 mb-1">KKM Aktif Saat Ini</p>
        <p class="text-4xl font-bold text-indigo-600">{{ $currentSchoolKkm->value ?? 70 }}</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">Ubah KKM Sekolah</h2>
        <form method="POST" action="{{ route('admin.kkm.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai KKM Baru (0-100)</label>
                <input type="number" name="value" min="0" max="100" required
                       class="w-32 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Mulai</label>
                <input type="datetime-local" name="effective_from" value="{{ now()->format('Y-m-d\TH:i') }}" required
                       x-bind:disabled="applyHistory"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-50">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="apply_to_history" value="1" x-model="applyHistory" class="rounded text-indigo-600">
                <span class="text-sm text-gray-700">Terapkan ke semua nilai historis (bukan cuma ke depan)</span>
            </label>
            <p class="text-xs text-amber-600" x-show="applyHistory" x-cloak>
                ⚠️ Ini akan mengubah status kelulusan nilai yang sudah tercatat sebelumnya.
            </p>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                Simpan KKM
            </button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Histori Perubahan</h2>
        <div class="space-y-2">
            @forelse ($history as $item)
                <div class="flex items-center justify-between text-sm border-b border-gray-50 pb-2">
                    <span class="text-gray-700">KKM = {{ $item->value }}</span>
                    <span class="text-gray-400">Berlaku sejak {{ $item->effective_from->translatedFormat('d M Y HH:mm') }} — oleh {{ $item->setter->name ?? '-' }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada histori perubahan KKM.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection