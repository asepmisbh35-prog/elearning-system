{{-- resources/views/announcements/show.blade.php --}}
@extends('layouts.app')
@section('title', $announcement->title)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <a href="{{ route('announcements.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Kembali ke pengumuman</a>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mt-4">
        <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $announcement->title }}</h1>
        <p class="text-xs text-gray-400 mb-4">
            {{ $announcement->creator->name ?? '-' }} &middot; {{ $announcement->created_at->translatedFormat('d M Y H:i') }}
        </p>

        @if ($announcement->image_path)
            <img src="{{ Storage::url($announcement->image_path) }}" class="rounded-lg mb-4 max-h-80 object-cover w-full">
        @endif

        <div class="prose prose-sm max-w-none text-gray-700">
            {!! nl2br(e($announcement->content)) !!}
        </div>
    </div>
</div>
@endsection