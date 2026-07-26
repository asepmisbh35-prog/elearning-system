{{-- resources/views/siswa/materials/show.blade.php --}}
@extends('layouts.app')

@section('title', $material->title)

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-5 md:px-6 md:py-8 pb-28" x-data="materialReader({
        materialId: {{ $material->id }},
        initialPercentage: {{ $progress->progress_percent }},
        isCompleted: {{ $progress->status === 'completed' ? 'true' : 'false' }},
        unlockMethod: '{{ $material->unlock_method }}',
        progressUrl: '{{ route('siswa.materials.progress', $material) }}',
        completeUrl: '{{ route('siswa.materials.complete', $material) }}',
        blockCompleteUrlTemplate: '{{ route('siswa.materials.blocks.complete', [$material, '__BLOCK_ID__']) }}',
        blocks: {{ $material->contentBlocks->map(fn($b) => [
            'id' => $b->id,
            'type' => $b->type,
            'completed' => optional($blockProgresses->get($b->id))->status === 'completed',
            'locked' => $blockLocks[$b->id],
        ])->toJson() }},
        csrfToken: '{{ csrf_token() }}'
    })" @scroll.window="onScroll">

        {{-- Sticky header: bar progres baca, polos & jelas --}}
        <div
            class="sticky top-0 z-30 bg-white/90 backdrop-blur-sm border-b border-gray-100 -mx-4 md:-mx-6 px-4 md:px-6 py-3 mb-5 md:mb-6">
            <div class="flex items-center justify-between mb-1.5">
                <p class="text-xs md:text-sm font-semibold text-gray-600 truncate"
                    x-text="isCompleted ? 'Materi ini sudah selesai dibaca' : 'Progres membaca materi'"></p>
                <span class="text-xs md:text-sm font-bold shrink-0 ml-2"
                    :class="isCompleted ? 'text-emerald-600' : 'text-[#4F46E5]'"
                    x-text="percentage + '%'"></span>
            </div>
            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700"
                    :class="isCompleted ? 'bg-emerald-500' : 'bg-[#4F46E5]'"
                    :style="'width: ' + percentage + '%'"></div>
            </div>
        </div>

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-400 mb-4 md:mb-5 flex items-center gap-2 flex-wrap">
            <a href="{{ route('siswa.classes.materials.index', $schoolClass) }}"
                class="hover:text-[#4F46E5] font-medium transition">Materi</a>
            <span>/</span>
            <span>Pertemuan {{ $meeting->order }}</span>
            <span>/</span>
            <span class="text-gray-700 font-semibold">{{ $material->title }}</span>
        </nav>

        {{-- Hero header materi (pola resmi: gradient indigo + lingkaran blur) --}}
        <div
            class="mb-6 md:mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-6 md:p-8 text-white shadow-xl shadow-indigo-200/60">
            <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
            <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
            <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

            <div class="relative">
                <span class="inline-block text-xs font-bold text-white/85 bg-white/15 px-3 py-1 rounded-full mb-3">
                    Pertemuan {{ $meeting->order }} · {{ $meeting->topic }}
                </span>
                <h1 class="text-xl md:text-2xl font-bold text-white">{{ $material->title }}</h1>
                @if ($material->description)
                    <p class="text-white/85 mt-2 text-sm md:text-base">{{ $material->description }}</p>
                @endif
                <p class="text-xs md:text-sm text-white/90 mt-3 flex flex-wrap items-center gap-1.5 md:gap-2">
                    <span class="inline-flex items-center gap-1 bg-white/15 px-2.5 py-1 rounded-full">⏱
                        {{ $material->estimated_minutes }} menit</span>
                    <span class="inline-flex items-center gap-1 bg-white/15 px-2.5 py-1 rounded-full">
                        {{ $material->contentBlocks->count() }} bagian</span>
                </p>
            </div>
        </div>

        {{-- Navigasi cepat antar bagian materi --}}
        @if ($material->contentBlocks->count() > 1)
            <div class="mb-6 md:mb-8 sticky top-[52px] md:top-[56px] z-20 -mx-1">
                <div class="relative flex items-center gap-2 overflow-x-auto pb-2 px-1 scrollbar-thin bg-white/90 backdrop-blur-sm">
                    <template x-for="(b, idx) in blocks" :key="b.id">
                        <button type="button" @click="scrollToBlock(b)" :disabled="b.locked"
                            class="shrink-0 inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full transition shadow-sm border-2 bg-white"
                            :class="{
                                'border-emerald-300 text-emerald-700': b.completed,
                                'border-[#818CF8] text-[#4338CA]': !b.completed && !b.locked,
                                'border-gray-200 text-gray-400 cursor-not-allowed': b.locked,
                            }">
                            <span x-text="b.completed ? '✓' : (b.locked ? '🔒' : idx + 1)"></span>
                            <span x-text="'Bagian ' + (idx + 1)"></span>
                        </button>
                    </template>
                </div>
            </div>
        @endif

        {{-- Blok Konten --}}
        <div id="material-content" class="space-y-5 md:space-y-6">
            @foreach ($material->contentBlocks as $block)
                <div id="block-{{ $block->id }}" class="scroll-mt-32" x-show="!isBlockLocked({{ $block->id }})"
                    x-cloak>
                    @if ($block->type === 'text')
                        <div
                            class="bg-white border-2 border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm hover:border-indigo-200 transition-colors">
                            <div class="flex items-center gap-2 mb-4">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#4F46E5] to-[#818CF8] flex items-center justify-center text-white text-sm shrink-0">
                                    📝</div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Bacaan</span>
                            </div>
                            <div
                                class="prose prose-gray max-w-none text-sm md:text-base prose-headings:text-gray-900 prose-a:text-[#4F46E5] prose-p:leading-relaxed">
                                {!! $block->content !!}
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="button" @click="completeBlock({{ $block->id }})"
                                    x-show="!blocks.find(b => b.id === {{ $block->id }})?.completed"
                                    class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition">
                                    ✓ Tandai Sudah Dibaca
                                </button>
                            </div>
                        </div>
                    @elseif ($block->type === 'video')
                        <div
                            class="bg-white border-2 border-gray-100 rounded-2xl md:rounded-3xl p-4 shadow-sm hover:border-sky-200 transition-colors">
                            <div class="flex items-center gap-2 mb-4 px-2">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm shrink-0">
                                    🎥</div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Video
                                    Pembelajaran</span>
                            </div>
                            <div class="rounded-xl md:rounded-2xl overflow-hidden aspect-video bg-black shadow-lg ring-1 ring-black/5">
                                @if ($block->video_platform === 'youtube')
                                    <iframe class="w-full h-full"
                                        src="https://www.youtube.com/embed/{{ $block->video_embed_id }}"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                @elseif ($block->video_platform === 'vimeo')
                                    <iframe class="w-full h-full"
                                        src="https://player.vimeo.com/video/{{ $block->video_embed_id }}"
                                        allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                                @else
                                    <div class="flex items-center justify-center h-full text-white">
                                        <a href="{{ $block->video_url }}" target="_blank" class="underline">Buka video di
                                            tab baru</a>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-4 flex justify-end px-2">
                                <button type="button" @click="completeBlock({{ $block->id }})"
                                    x-show="!blocks.find(b => b.id === {{ $block->id }})?.completed"
                                    class="text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200 hover:bg-sky-100 px-3 py-1.5 rounded-lg transition">
                                    ✓ Tandai Video Selesai
                                </button>
                            </div>
                        </div>
                    @elseif ($block->type === 'pdf_viewer')
                        <div x-data="pdfCard()"
                            class="bg-white border-2 border-gray-100 rounded-2xl md:rounded-3xl overflow-hidden shadow-sm"
                            :class="fullscreen ? 'fixed inset-4 z-50 shadow-2xl' : ''">
                            <div
                                class="flex items-center justify-between gap-3 px-4 md:px-5 py-4 bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-indigo-100">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#4338CA] to-[#818CF8] flex items-center justify-center text-white text-base shrink-0 shadow-sm">
                                        📄</div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $block->original_filename }}
                                        </p>
                                        <p class="text-xs text-gray-400">Dokumen PDF</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button @click="fullscreen = !fullscreen"
                                        class="text-xs font-semibold text-[#4338CA] bg-white border border-indigo-200 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition">
                                        <span x-show="!fullscreen">⛶ Layar Penuh</span>
                                        <span x-show="fullscreen" x-cloak>✕ Tutup</span>
                                    </button>
                                    <a href="{{ Storage::url($block->file_path) }}" target="_blank"
                                        @click="completeBlock({{ $block->id }})"
                                        class="text-xs font-semibold text-[#4338CA] bg-white border border-indigo-200 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition">Tab
                                        Baru ↗</a>
                                </div>
                            </div>
                            <div :class="fullscreen ? 'h-[calc(100%-64px)]' : 'h-[65vh]'" class="bg-gray-100 relative"
                                x-init="$el.querySelector('iframe').addEventListener('load', () => completeBlock({{ $block->id }}), { once: true })">
                                <iframe src="{{ Storage::url($block->file_path) }}#toolbar=0&navpanes=0&scrollbar=1"
                                    class="w-full h-full border-0"></iframe>
                            </div>
                        </div>
                    @elseif ($block->type === 'pdf_flipbook')
                        <div class="bg-white border-2 border-gray-100 rounded-2xl md:rounded-3xl overflow-hidden shadow-lg">
                            <div class="flex items-center gap-2 px-4 md:px-5 py-3 bg-gradient-to-r from-[#4F46E5] to-[#818CF8]">
                                <div
                                    class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm shrink-0">
                                    📖</div>
                                <span class="text-xs font-bold text-white uppercase tracking-wide">Flipbook —
                                    {{ $block->original_filename }}</span>
                            </div>

                            <div class="bg-gray-900 p-2 sm:p-4 relative h-[52vh] min-h-[300px] max-h-[420px] sm:h-[60vh] sm:max-h-[560px] md:h-[68vh] md:max-h-[680px] overflow-hidden"
                                x-init="$nextTick(() => { setTimeout(() => completeBlock({{ $block->id }}), 1500) })">
                                <div class="_df_book" id="dflip-{{ $block->id }}"
                                    style="width:100%;height:100%;"
                                    source="{{ Storage::url($block->file_path) }}"></div>
                            </div>

                            <div class="bg-gray-800 px-4 py-3 flex items-center justify-center gap-3">
                                <button type="button" onclick="dflipAction({{ $block->id }}, 'zoomOut')"
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4338CA] to-[#818CF8] hover:scale-110 active:scale-95 text-white flex items-center justify-center transition-all text-lg font-bold shadow-md">−</button>
                                <button type="button" onclick="dflipAction({{ $block->id }}, 'zoomIn')"
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4338CA] to-[#818CF8] hover:scale-110 active:scale-95 text-white flex items-center justify-center transition-all text-lg font-bold shadow-md">+</button>
                                <button type="button" onclick="dflipAction({{ $block->id }}, 'thumbnail')"
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 hover:scale-110 active:scale-95 text-white flex items-center justify-center transition-all text-sm shadow-md">▦</button>
                                <button type="button" onclick="dflipAction({{ $block->id }}, 'fullScreen')"
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 hover:scale-110 active:scale-95 text-white flex items-center justify-center transition-all text-sm shadow-md">⛶</button>
                                <a href="{{ Storage::url($block->file_path) }}" download="{{ $block->original_filename }}"
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 hover:scale-110 active:scale-95 text-white flex items-center justify-center transition-all text-sm shadow-md">⬇</a>
                            </div>
                        </div>
                    @elseif ($block->type === 'link')
                        <a href="{{ $block->link_url }}" target="_blank" rel="noopener noreferrer"
                            @click="completeBlock({{ $block->id }})"
                            class="flex items-center gap-4 border-2 border-gray-100 rounded-2xl p-4 hover:border-indigo-300 hover:shadow-md hover:-translate-y-0.5 transition-all group bg-white">
                            <div
                                class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-50 to-violet-50 group-hover:from-[#4F46E5] group-hover:to-[#818CF8] border border-indigo-100 flex items-center justify-center shrink-0 transition-all">
                                <svg class="w-5 h-5 text-[#4F46E5] group-hover:text-white transition" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 group-hover:text-[#4338CA] truncate">
                                    {{ $block->link_title }}</p>
                                <p class="text-sm text-gray-400">{{ $block->link_domain }} ↗</p>
                            </div>
                        </a>
                    @endif
                </div>
                @if ($blockLocks[$block->id])
                    <div id="block-locked-{{ $block->id }}" class="scroll-mt-32" x-show="isBlockLocked({{ $block->id }})">
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl md:rounded-3xl p-8 text-center">
                            <div class="text-3xl mb-2">🔒</div>
                            <p class="text-sm font-semibold text-gray-500">Selesaikan bagian sebelumnya untuk membuka ini
                            </p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Navigasi antar materi — muncul begitu materi ini tuntas dibaca --}}
        @php
            $nextLocked = $nextMaterial ? $nextMaterial->isLockedFor($student) : false;
            $crossesMeeting = $nextMaterial && isset($nextMaterial->meeting) && $nextMaterial->meeting->id !== $meeting->id;
        @endphp
        <div class="mt-8 md:mt-10 border-t border-gray-100 pt-6" x-show="isCompleted" x-transition x-cloak>
                {{-- Banner khusus: materi di pertemuan ini sudah habis, lanjut ke pertemuan berikutnya --}}
                @if ($crossesMeeting && !$nextLocked)
                    <div
                        class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 md:p-5 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 justify-between mb-4">
                        <div>
                            <p class="text-sm font-bold text-[#3730A3]">Semua materi di Pertemuan {{ $meeting->order }} sudah selesai</p>
                            <p class="text-xs md:text-sm text-indigo-700/80 mt-0.5">Lanjutkan ke Pertemuan
                                {{ $nextMaterial->meeting->order }}: {{ $nextMaterial->meeting->topic }}</p>
                        </div>
                        <a href="{{ route('siswa.classes.materials.show', [$schoolClass, $nextMaterial->meeting, $nextMaterial]) }}"
                            class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-5 py-3 rounded-xl font-semibold shadow-md shadow-indigo-200/60 hover:-translate-y-0.5 transition-all text-sm shrink-0">
                            Lanjut ke Pertemuan Berikutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    {{-- Materi sebelumnya --}}
                    <div class="flex-1">
                        @isset($prevMaterial)
                            <a href="{{ route('siswa.classes.materials.show', [$schoolClass, $prevMaterial->meeting ?? $meeting, $prevMaterial]) }}"
                                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 border-2 border-gray-100 text-gray-600 hover:border-indigo-200 hover:text-[#4338CA] px-5 py-3 md:py-3.5 rounded-2xl font-semibold transition-all text-sm md:text-base">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                <span class="truncate">Materi Sebelumnya</span>
                            </a>
                        @endisset
                    </div>

                    {{-- Materi berikutnya (dalam pertemuan yang sama) --}}
                    <div class="flex-1 flex sm:justify-end">
                        @if ($nextMaterial && !$crossesMeeting)
                            @if (!$nextLocked)
                                <a href="{{ route('siswa.classes.materials.show', [$schoolClass, $meeting, $nextMaterial]) }}"
                                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-5 md:px-6 py-3 md:py-3.5 rounded-2xl hover:-translate-y-0.5 transition-all font-semibold shadow-lg shadow-indigo-200/60 text-sm md:text-base">
                                    <span class="truncate">Materi Berikutnya: {{ $nextMaterial->title }}</span>
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <button type="button" disabled title="Selesaikan syarat sebelumnya untuk membuka materi ini"
                                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-gray-100 text-gray-400 px-5 md:px-6 py-3 md:py-3.5 rounded-2xl font-semibold cursor-not-allowed text-sm md:text-base">
                                    <span class="text-base">🔒</span>
                                    <span class="truncate">{{ $nextMaterial->title }}</span>
                                </button>
                            @endif
                        @elseif ($nextMaterial && $crossesMeeting && $nextLocked)
                            <button type="button" disabled title="Selesaikan syarat sebelumnya untuk membuka pertemuan berikutnya"
                                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-gray-100 text-gray-400 px-5 md:px-6 py-3 md:py-3.5 rounded-2xl font-semibold cursor-not-allowed text-sm md:text-base">
                                <span class="text-base">🔒</span>
                                <span class="truncate">Pertemuan {{ $nextMaterial->meeting->order }}</span>
                            </button>
                        @elseif (!$nextMaterial)
                            <p class="text-sm text-gray-400 italic">Kamu sudah menyelesaikan seluruh materi yang tersedia saat ini.</p>
                        @endif
                    </div>
                </div>
        </div>

        {{-- Floating "Tandai Selesai" — hanya muncul selama materi BELUM selesai --}}
        @if ($material->sequential_unlock)
            <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 w-full max-w-xs px-4" x-show="!isCompleted"
                x-transition x-cloak>
                <button @click="markComplete"
                    class="w-full text-white px-6 py-3.5 rounded-2xl font-bold transition-all shadow-xl shadow-indigo-300/50 flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] hover:-translate-y-0.5">
                    Tandai Selesai
                </button>
            </div>
        @endif

        {{-- Konfirmasi saat materi selesai --}}
        <div x-show="showReward" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#1E1B4B]/60 backdrop-blur-sm px-4"
            @click.self="showReward = false">
            <div x-show="showReward" x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="bg-white rounded-3xl p-8 text-center max-w-xs w-full shadow-2xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-emerald-50 opacity-70"></div>
                <div
                    class="relative w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 relative">Materi Selesai</h3>
                <p class="text-gray-500 text-sm mt-1 relative">Kerja bagus, terus lanjutkan progres belajarmu.</p>
                <button @click="showReward = false"
                    class="mt-5 w-full bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white font-bold py-3 rounded-2xl relative">Lanjut
                    Belajar</button>
            </div>
        </div>
    </div>

    @include('materials._discussion', [
        'material' => $material,
        'storeRoute' => 'siswa.discussions.store',
        'destroyRouteName' => 'siswa.discussions.destroy',
    ])

    @push('scripts')
        <script>
            function dflipAction(blockId, action) {
                const instance = window['dflip-' + blockId];
                if (!instance || !instance.ui || !instance.ui[action]) {
                    console.warn('DFLIP UI element tidak ditemukan:', action, instance);
                    return;
                }
                instance.ui[action].trigger('click');
            }

            document.addEventListener('alpine:init', () => {
                Alpine.data('materialReader', (config) => ({
                    materialId: config.materialId,
                    percentage: config.initialPercentage || 0,
                    isCompleted: config.isCompleted,
                    unlockMethod: config.unlockMethod,
                    progressUrl: config.progressUrl,
                    completeUrl: config.completeUrl,
                    blockCompleteUrlTemplate: config.blockCompleteUrlTemplate,
                    blocks: config.blocks || [],
                    csrfToken: config.csrfToken,
                    showReward: false,
                    debounceTimer: null,

                    isBlockLocked(blockId) {
                        const b = this.blocks.find(x => x.id === blockId);
                        return b ? b.locked : false;
                    },

                    scrollToBlock(b) {
                        if (b.locked) return;
                        const el = document.getElementById('block-' + b.id);
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    },

                    async completeBlock(blockId) {
                        const block = this.blocks.find(b => b.id === blockId);
                        if (!block || block.completed || block.locked) return;

                        try {
                            const url = this.blockCompleteUrlTemplate.replace('__BLOCK_ID__', blockId);
                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': this.csrfToken
                                },
                            });
                            const data = await res.json();

                            block.completed = true;
                            this.percentage = data.material_percentage;
                            this.isCompleted = data.material_completed;

                            const idx = this.blocks.findIndex(b => b.id === blockId);
                            if (idx !== -1 && this.blocks[idx + 1]) {
                                this.blocks[idx + 1].locked = false;
                                this.$nextTick(() => this.scrollToBlock(this.blocks[idx + 1]));
                            }

                            if (data.material_completed) {
                                this.showReward = true;
                                this.fireConfetti();
                            }
                        } catch (e) {
                            console.warn('Complete block failed:', e);
                        }
                    },

                    onScroll() {
                        if (this.isCompleted) return;
                        const el = document.getElementById('material-content');
                        if (!el) return;
                        const rect = el.getBoundingClientRect();
                        const elBottom = rect.bottom + window.scrollY;
                        const scrolled = window.scrollY + window.innerHeight;
                        const raw = Math.min(100, Math.round((scrolled / elBottom) * 100));
                        const newPct = Math.max(this.percentage, raw);
                        if (newPct === this.percentage) return;
                        this.percentage = newPct;
                        clearTimeout(this.debounceTimer);
                        this.debounceTimer = setTimeout(() => this.sendProgress(newPct), 2000);
                    },
                    async sendProgress(pct) {
                        try {
                            const res = await fetch(this.progressUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken
                                },
                                body: JSON.stringify({
                                    percentage: pct
                                }),
                            });
                            const data = await res.json();
                            this.percentage = data.percentage;
                            this.isCompleted = data.is_completed;
                        } catch (e) {
                            console.warn('Progress sync failed:', e);
                        }
                    },
                    async markComplete() {
                        if (this.isCompleted) return;
                        try {
                            await fetch(this.completeUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': this.csrfToken
                                },
                            });
                            this.percentage = 100;
                            this.isCompleted = true;
                            this.showReward = true;
                            this.fireConfetti();
                        } catch (e) {
                            console.warn('Mark complete failed:', e);
                        }
                    },
                    fireConfetti() {
                        const colors = ['#4F46E5', '#818CF8', '#4338CA', '#38BDF8', '#10B981'];
                        for (let i = 0; i < 40; i++) {
                            const el = document.createElement('div');
                            el.style.cssText = `position:fixed;top:-10px;left:${Math.random()*100}vw;width:8px;height:8px;
                    background:${colors[Math.floor(Math.random()*colors.length)]};
                    z-index:60;pointer-events:none;border-radius:2px;`;
                            document.body.appendChild(el);
                            const fall = el.animate([{
                                    transform: 'translateY(0) rotate(0deg)',
                                    opacity: 1
                                },
                                {
                                    transform: `translateY(100vh) rotate(${360 + Math.random()*360}deg)`,
                                    opacity: 0
                                }
                            ], {
                                duration: 2000 + Math.random() * 1500,
                                easing: 'ease-in'
                            });
                            fall.onfinish = () => el.remove();
                        }
                    },
                }));

                Alpine.data('pdfCard', () => ({
                    fullscreen: false
                }));
            });

            document.addEventListener('DOMContentLoaded', () => {
                const dflipEls = document.querySelectorAll('[id^="dflip-"]');

                dflipEls.forEach((el) => {
                    if (el.id.startsWith('dflip-page-info-')) return;
                    const instanceId = el.id;
                    const infoEl = document.getElementById('dflip-page-info-' + instanceId.replace('dflip-', ''));
                    if (!infoEl) return;

                    const checkReady = setInterval(() => {
                        const instance = window.DFLIP && DFLIP.getInstance(instanceId);
                        if (instance) {
                            clearInterval(checkReady);
                            const updateInfo = () => {
                                infoEl.textContent = `Halaman ${instance.target.pageNum} dari ${instance.pages.length}`;
                            };
                            updateInfo();
                            instance.pageChangeCallback = updateInfo;
                        }
                    }, 300);
                });

                // Pastikan flipbook menyesuaikan ulang ukuran saat kontainer responsif berubah
                // (rotasi layar, resize window, dsb) supaya tidak menyisakan area kosong.
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        dflipEls.forEach((el) => {
                            if (el.id.startsWith('dflip-page-info-')) return;
                            const instance = window.DFLIP && DFLIP.getInstance(el.id);
                            if (instance && typeof instance.resize === 'function') {
                                instance.resize();
                            }
                        });
                    }, 250);
                });
            });
        </script>
    @endpush
@endsection