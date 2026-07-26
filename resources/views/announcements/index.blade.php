{{-- resources/views/announcements/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pengumuman')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Pengumuman</h1>

    <div class="space-y-3">
        @forelse ($announcements as $a)
            <a href="{{ route('announcements.show', $a) }}"
               class="block bg-white border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition {{ ! $a->isReadBy(auth()->user()) ? 'ring-1 ring-blue-200' : '' }}">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-semibold text-gray-900">{{ $a->title }}</h3>
                    @if (! $a->isReadBy(auth()->user()))
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">Baru</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($a->content), 150) }}</p>
                <p class="text-xs text-gray-400 mt-2">{{ $a->created_at->diffForHumans() }}</p>
            </a>
        @empty
            <div class="text-center py-16 bg-white border border-dashed border-gray-200 rounded-xl text-gray-400">
                <p class="text-sm">Belum ada pengumuman.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection