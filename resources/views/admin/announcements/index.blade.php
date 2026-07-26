{{-- resources/views/admin/announcements/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pengumuman')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Pengumuman</h1>
        <a href="{{ route('admin.announcements.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium text-sm">
            + Buat Pengumuman
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @forelse ($announcements as $a)
            <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h3 class="font-semibold text-gray-900">{{ $a->title }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">
                            {{ match($a->target_type) {
                                'all' => 'Semua Pengguna',
                                'class' => 'Kelas: ' . ($a->schoolClass->name ?? '-'),
                                'role' => 'Role: ' . ucfirst($a->target_role),
                                'specific' => count($a->target_user_ids ?? []) . ' pengguna spesifik',
                            } }}
                        </span>
                        @if (! $a->isPublished())
                            <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium">Belum Tayang / Kedaluwarsa</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit(strip_tags($a->content), 150) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $a->created_at->translatedFormat('d M Y H:i') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-red-500 hover:text-red-700 shrink-0">Hapus</button>
                </form>
            </div>
        @empty
            <div class="text-center py-16 bg-white border border-dashed border-gray-200 rounded-xl text-gray-400">
                <p class="font-medium">Belum ada pengumuman</p>
            </div>
        @endforelse
    </div>
</div>
@endsection