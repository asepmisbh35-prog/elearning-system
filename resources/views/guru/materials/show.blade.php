{{-- resources/views/guru/materials/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Preview — ' . $material->title)

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
    @keyframes soft-pulse { 0%,100% { opacity:.5; transform:scale(1); } 50% { opacity:1; transform:scale(1.08); } }
    .soft-pulse { animation: soft-pulse 2.2s ease-in-out infinite; }
</style>

<div class="max-w-3xl mx-auto px-4 py-6 md:py-8 space-y-5 md:space-y-6">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <a href="{{ route('guru.meetings.materials.edit', [$meeting, $material]) }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 hover:text-[#4F46E5] transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke edit
        </a>
        <span class="inline-flex items-center gap-1.5 text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full font-medium">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Mode Preview
        </span>
    </div>

    {{-- ═══ Sampul Materi (Hero) ═══ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] px-6 sm:px-8 py-8 sm:py-10 text-white">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <p class="text-indigo-200 text-xs font-semibold tracking-wider uppercase mb-2">
                Pertemuan {{ $meeting->order }} &middot; {{ $meeting->topic }}
            </p>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $material->title }}</h1>
            @if ($material->description)
                <p class="text-indigo-100 max-w-xl text-sm sm:text-base">{{ $material->description }}</p>
            @endif
            <div class="flex items-center gap-2 sm:gap-3 mt-5 flex-wrap">
                <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-indigo-100 bg-white/10 backdrop-blur px-3 py-1.5 rounded-full border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $material->estimated_minutes }} menit
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm bg-white/10 backdrop-blur px-3 py-1.5 rounded-full border border-white/10">
                    <span class="w-1.5 h-1.5 rounded-full {{ $material->status === 'published' ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                    {{ $material->status === 'published' ? 'Dipublish' : 'Draft' }}
                </span>
                @if ($material->sequential_unlock)
                    <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-indigo-100 bg-white/10 backdrop-blur px-3 py-1.5 rounded-full border border-white/10">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Berurutan
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ Isi Materi ═══ --}}
    <div class="space-y-4">
        @forelse ($material->contentBlocks as $i => $block)
            @php
                // Palet rumpun indigo/violet/sky/blue — dibedakan per tipe blok, bukan warna hangat
                $meta = [
                    'text'         => ['label' => 'Bacaan',      'ring' => '#4F46E5', 'soft' => 'bg-indigo-50', 'text' => 'text-[#4F46E5]', 'shadow' => 'shadow-indigo-200/60', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
                    'video'        => ['label' => 'Video',       'ring' => '#7C3AED', 'soft' => 'bg-violet-50', 'text' => 'text-violet-600', 'shadow' => 'shadow-violet-200/60', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    'pdf_viewer'   => ['label' => 'Dokumen PDF', 'ring' => '#0EA5E9', 'soft' => 'bg-sky-50',    'text' => 'text-sky-600',    'shadow' => 'shadow-sky-200/60',    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    'pdf_flipbook' => ['label' => 'Flipbook',    'ring' => '#3B82F6', 'soft' => 'bg-blue-50',   'text' => 'text-blue-600',   'shadow' => 'shadow-blue-200/60',   'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    'link'         => ['label' => 'Tautan',      'ring' => '#4338CA', 'soft' => 'bg-indigo-50', 'text' => 'text-[#4338CA]', 'shadow' => 'shadow-indigo-200/60', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.101'],
                ][$block->type];
            @endphp

            <div class="rise-in bg-white border border-gray-100 rounded-2xl overflow-hidden transition-all duration-200 hover:shadow-xl {{ $meta['shadow'] }}" style="animation-delay: {{ $i * 80 }}ms">
                <div class="flex items-center gap-2.5 px-4 sm:px-5 py-3 border-b border-gray-50">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl {{ $meta['soft'] }}">
                        <svg class="w-4 h-4 {{ $meta['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold text-gray-700">{{ $meta['label'] }}</span>

                    @if ($block->type === 'pdf_flipbook')
                        @php
                            $status = $block->flipbook_status ?? 'pending';
                            $statusMeta = [
                                'pending'    => ['label' => 'Pending',    'color' => '#F59E0B', 'bg' => 'bg-amber-50'],
                                'processing' => ['label' => 'Processing', 'color' => '#4F46E5', 'bg' => 'bg-indigo-50'],
                                'ready'      => ['label' => 'Ready',      'color' => '#10B981', 'bg' => 'bg-emerald-50'],
                                'failed'     => ['label' => 'Failed',     'color' => '#EF4444', 'bg' => 'bg-red-50'],
                            ][$status];
                        @endphp
                        <span class="ml-auto inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $statusMeta['bg'] }}" style="color:{{ $statusMeta['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $status === 'processing' ? 'soft-pulse' : '' }}" style="background:{{ $statusMeta['color'] }}"></span>
                            {{ $statusMeta['label'] }}
                        </span>
                    @endif
                </div>

                <div class="p-4 sm:p-5">
                    @if ($block->type === 'text')
                        <div class="prose prose-indigo max-w-none text-gray-700 leading-relaxed text-sm sm:text-base">
                            {!! $block->content !!}
                        </div>

                    @elseif ($block->type === 'video')
                        <div class="relative aspect-video bg-gray-900 rounded-xl overflow-hidden shadow-inner group">
                            @if ($block->video_platform === 'youtube')
                                <iframe class="w-full h-full"
                                        src="https://www.youtube.com/embed/{{ $block->video_embed_id }}"
                                        allowfullscreen></iframe>
                            @else
                                <a href="{{ $block->video_url }}" target="_blank"
                                   class="text-white flex items-center justify-center h-full gap-2 hover:bg-gray-800 transition-colors duration-200">
                                    <span class="w-12 h-12 rounded-full bg-white/15 group-hover:bg-white/25 flex items-center justify-center transition-all duration-200 group-hover:scale-110">
                                        <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </span>
                                    <span class="text-sm font-medium">Buka video eksternal</span>
                                </a>
                            @endif
                        </div>

                    @elseif ($block->type === 'pdf_viewer')
                        <div class="border border-gray-100 rounded-xl overflow-hidden">
                            <iframe src="{{ Storage::url($block->file_path) }}"
                                    class="w-full" style="height: 600px;"></iframe>
                            <div class="bg-indigo-50/60 px-4 py-2.5 text-xs text-gray-500 flex items-center justify-between">
                                <span class="truncate">{{ $block->original_filename }}</span>
                                <a href="{{ Storage::url($block->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[#4F46E5] hover:text-[#4338CA] font-medium shrink-0">
                                    Buka di tab baru
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </div>
                        </div>

                    @elseif ($block->type === 'pdf_flipbook')
                        <div class="border border-dashed border-blue-200 rounded-xl p-8 text-center bg-blue-50/30">
                            @if (($block->flipbook_status ?? 'pending') === 'ready')
                                <div class="w-10 h-10 mx-auto rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Flipbook siap ditampilkan</p>
                                <a href="{{ Storage::url($block->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-sm text-white px-4 py-2 rounded-xl shadow-md shadow-blue-200 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                                   style="background:linear-gradient(135deg,#3B82F6,#2563EB)">
                                    Buka file sumber
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            @else
                                <div class="relative w-10 h-10 mx-auto mb-3">
                                    <div class="absolute inset-0 rounded-full bg-amber-200 soft-pulse"></div>
                                    <div class="relative w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </div>
                                </div>
                                <p class="text-sm text-amber-700">
                                    Sedang diproses ({{ $block->flipbook_status ?? 'pending' }})...
                                </p>
                            @endif
                        </div>

                    @elseif ($block->type === 'link')
                        <a href="{{ $block->link_url }}" target="_blank"
                           class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl p-4 hover:border-indigo-200 hover:bg-indigo-50/40 transition-all duration-200 group">
                            <div class="min-w-0 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.101"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 group-hover:text-[#4F46E5] truncate transition-colors duration-150">{{ $block->link_title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $block->link_domain }}</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-[#4F46E5] group-hover:translate-x-0.5 shrink-0 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="rise-in text-center py-16 bg-white border border-dashed border-indigo-200 rounded-2xl">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="font-medium text-gray-600">Belum ada blok konten di materi ini</p>
                <a href="{{ route('guru.meetings.materials.edit', [$meeting, $material]) }}"
                   class="inline-flex items-center gap-1 mt-2 text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium">
                    Tambahkan sekarang
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @endforelse
    </div>

    @include('materials._discussion', ['material' => $material, 'storeRoute' => 'guru.discussions.store', 'destroyRouteName' => 'guru.discussions.destroy'])
</div>
@endsection