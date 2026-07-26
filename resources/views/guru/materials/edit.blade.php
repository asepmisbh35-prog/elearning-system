{{-- resources/views/guru/materials/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Materi — ' . $material->title)

@section('content')
<style>
    @keyframes rise-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
    [x-cloak] { display: none !important; }
    .field-icon:focus-within svg { color: #4F46E5; }
    @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
</style>

@php
    $typeMeta = [
        'text'         => ['label' => 'Teks',         'ring' => '#4F46E5', 'soft' => 'bg-indigo-50', 'text' => 'text-[#4F46E5]', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
        'video'        => ['label' => 'Video',        'ring' => '#7C3AED', 'soft' => 'bg-violet-50', 'text' => 'text-violet-600', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
        'pdf_viewer'   => ['label' => 'PDF Viewer',   'ring' => '#0EA5E9', 'soft' => 'bg-sky-50',    'text' => 'text-sky-600',    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'pdf_flipbook' => ['label' => 'PDF Flipbook', 'ring' => '#3B82F6', 'soft' => 'bg-blue-50',   'text' => 'text-blue-600',   'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        'link'         => ['label' => 'Link',         'ring' => '#4338CA', 'soft' => 'bg-indigo-50', 'text' => 'text-[#4338CA]', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.101'],
    ];
@endphp

<div class="max-w-4xl mx-auto px-4 py-6 md:py-8 space-y-5 md:space-y-6"
     x-data="{ confirmDelete: false, deleteForm: null, deleteLabel: '' }">

    <div>
        <a href="{{ route('guru.meetings.materials.index', $meeting) }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke daftar materi
        </a>
    </div>

    {{-- ============ HERO — identitas materi yang diedit ============ --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 sm:p-6 md:p-7 text-white">
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-40 h-40 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="relative flex items-center gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs sm:text-sm text-indigo-200 uppercase tracking-wide">Mengedit Materi</p>
                <h1 class="text-lg sm:text-2xl font-bold truncate">{{ $material->title }}</h1>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rise-in bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- ═══ BAGIAN 1: Data Materi ═══ --}}
    <div class="rise-in relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-7 overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#4F46E5] via-[#7C3AED] to-[#0EA5E9]"></div>

        <div class="flex items-center gap-2.5 mb-5">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-900 text-sm md:text-base">Data Materi</h2>
        </div>

        <form method="POST" action="{{ route('guru.meetings.materials.update', [$meeting, $material]) }}"
              x-data="{ saving: false }" @submit="saving = true"
              class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Materi</label>
                <div class="field-icon relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    <input type="text" name="title" value="{{ old('title', $material->title) }}"
                           class="w-full border border-gray-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">{{ old('description', $material->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Urutan Tampil</label>
                    <div class="field-icon relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <input type="number" name="order" value="{{ old('order', $material->order) }}" min="1"
                               class="w-full border border-gray-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estimasi Waktu (menit)</label>
                    <div class="field-icon relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', $material->estimated_minutes) }}" min="1" max="300"
                               class="w-full border border-gray-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]">
                    </div>
                </div>
            </div>

            {{-- Status — kartu pilihan, bukan pill kecil --}}
            <div x-data="{ status: '{{ old('status', $material->status) }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <input type="hidden" name="status" :value="status">
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="status = 'draft'"
                            :class="status === 'draft' ? 'border-amber-400 bg-amber-50 shadow-md shadow-amber-100' : 'border-gray-200 hover:border-amber-200'"
                            class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-2 transition-all duration-200 text-left">
                        <span class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </span>
                        <span class="text-sm font-medium text-gray-800">Draft</span>
                        <svg x-show="status === 'draft'" class="w-4 h-4 text-amber-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </button>
                    <button type="button" @click="status = 'published'"
                            :class="status === 'published' ? 'border-emerald-400 bg-emerald-50 shadow-md shadow-emerald-100' : 'border-gray-200 hover:border-emerald-200'"
                            class="flex items-center gap-2.5 px-4 py-3 rounded-xl border-2 transition-all duration-200 text-left">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm font-medium text-gray-800">Published</span>
                        <svg x-show="status === 'published'" class="w-4 h-4 text-emerald-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>

            {{-- Sequential Unlock — card dengan glow saat aktif --}}
            <div class="relative rounded-xl p-4 border-2 transition-all duration-300"
                 x-data="{ seq: {{ $material->sequential_unlock ? 'true' : 'false' }} }"
                 :class="seq ? 'border-indigo-200 bg-indigo-50/40' : 'border-gray-200'">
                <label class="flex items-center justify-between gap-3 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 transition-colors duration-300"
                              :class="seq ? 'bg-[#4F46E5]' : 'bg-gray-100'">
                            <svg class="w-4.5 h-4.5 transition-colors duration-300" :class="seq ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <div>
                            <span class="font-medium text-gray-800 text-sm">Sequential Unlock</span>
                            <p class="text-xs sm:text-sm text-gray-500">Siswa harus menyelesaikan materi sebelumnya</p>
                        </div>
                    </div>
                    <button type="button" @click="seq = !seq" role="switch" :aria-checked="seq"
                            class="relative shrink-0 w-12 h-6.5 rounded-full transition-colors duration-300"
                            :class="seq ? 'bg-[#4F46E5]' : 'bg-gray-200'">
                        <span class="absolute top-0.5 left-0.5 w-5.5 h-5.5 bg-white rounded-full shadow flex items-center justify-center transition-transform duration-300"
                              :class="seq ? 'translate-x-5.5' : 'translate-x-0'">
                            <svg x-show="seq" class="w-3 h-3 text-[#4F46E5]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    </button>
                    <input type="checkbox" name="sequential_unlock" value="1" class="hidden" x-model="seq">
                </label>
                <div x-show="seq" x-cloak
                     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-4 pl-12 space-y-2">
                    <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-lg border border-transparent hover:border-indigo-200 hover:bg-white transition-all duration-150">
                        <input type="radio" name="unlock_method" value="scroll"
                               {{ $material->unlock_method === 'scroll' ? 'checked' : '' }}
                               class="w-4 h-4 text-[#4F46E5] focus:ring-indigo-200">
                        <span class="text-sm text-gray-700">Scroll 80% halaman materi sebelumnya</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-lg border border-transparent hover:border-indigo-200 hover:bg-white transition-all duration-150">
                        <input type="radio" name="unlock_method" value="manual"
                               {{ $material->unlock_method === 'manual' ? 'checked' : '' }}
                               class="w-4 h-4 text-[#4F46E5] focus:ring-indigo-200">
                        <span class="text-sm text-gray-700">Siswa tekan tombol "Tandai Selesai"</span>
                    </label>
                </div>
            </div>

            <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-md shadow-indigo-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 disabled:opacity-70">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </form>
    </div>

    {{-- ═══ BAGIAN 2: Blok Konten ═══ --}}
    <div class="rise-in relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-7 overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#0EA5E9] via-[#3B82F6] to-[#4F46E5]"></div>

        <div class="flex items-center gap-2.5 mb-1">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-900 text-sm md:text-base">Blok Konten</h2>
        </div>
        <p class="text-xs sm:text-sm text-gray-500 mb-5 pl-11.5">Tambahkan teks, video, PDF, atau link sebagai isi materi.</p>

        {{-- Daftar blok yang sudah ada — timeline kecil --}}
        <div class="space-y-0 mb-2">
            @forelse($material->contentBlocks as $i => $block)
                @php $tm = $typeMeta[$block->type]; @endphp
                <div class="rise-in flex gap-3" style="animation-delay: {{ $i * 60 }}ms">
                    <div class="flex flex-col items-center shrink-0">
                        <div class="w-8 h-8 rounded-lg {{ $tm['soft'] }} {{ $tm['text'] }} flex items-center justify-center text-xs font-bold shrink-0 ring-2 ring-white">
                            {{ $block->order }}
                        </div>
                        @if (! $loop->last)
                            <div class="w-0.5 flex-1 my-1 rounded-full" style="background:{{ $tm['ring'] }}22; min-height:1.25rem"></div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 border border-gray-100 rounded-xl p-3.5 sm:p-4 mb-2.5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md flex items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $tm['soft'] }} {{ $tm['text'] }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tm['icon'] }}"/></svg>
                                    {{ $tm['label'] }}
                                </span>

                                @if($block->type === 'pdf_flipbook')
                                    @php
                                        $status = $block->flipbook_status ?? 'pending';
                                        $statusColor = ['pending' => '#F59E0B', 'processing' => '#4F46E5', 'ready' => '#10B981', 'failed' => '#EF4444'][$status];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 text-xs font-medium" style="color:{{ $statusColor }}">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $statusColor }}"></span>
                                        {{ ucfirst($status) }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs sm:text-sm text-gray-500 truncate">
                                @if($block->type === 'text') {!! Str::limit(strip_tags($block->content), 80) !!}
                                @elseif($block->type === 'video') {{ $block->video_url }}
                                @elseif(in_array($block->type, ['pdf_viewer','pdf_flipbook'])) {{ $block->original_filename }}
                                @elseif($block->type === 'link') {{ $block->link_title }} — {{ $block->link_domain }}
                                @endif
                            </p>
                        </div>

                        <button type="button"
                                @click="confirmDelete = true; deleteLabel = '{{ $tm['label'] }} #{{ $block->order }}'; deleteForm = 'delete-block-{{ $block->id }}'"
                                class="p-2 rounded-lg text-gray-400 hover:text-[#EF4444] hover:bg-red-50 hover:scale-110 active:scale-95 transition-all duration-150 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        <form id="delete-block-{{ $block->id }}" method="POST"
                              action="{{ route('guru.meetings.materials.blocks.destroy', [$meeting, $material, $block]) }}" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">Belum ada blok konten. Tambahkan lewat form di bawah.</p>
            @endforelse
        </div>

        {{-- ─── Form tambah blok baru ─── --}}
        <div class="mt-6 border-t border-gray-100 pt-6" x-data="{ type: 'text' }">
            <h3 class="font-medium text-gray-800 text-sm md:text-base mb-4">Tambah Blok Konten</h3>

             @if ($errors->any())
                <div class="rise-in bg-red-50 border border-red-200 text-[#EF4444] text-sm rounded-xl p-3.5 mb-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Pilih tipe — icon cards dengan gradient saat aktif --}}
            <div class="flex sm:grid sm:grid-cols-5 gap-2.5 mb-5 overflow-x-auto sm:overflow-visible pb-1 sm:pb-0 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none">
                @foreach($typeMeta as $val => $tm)
                    <button type="button"
                            @click="type = '{{ $val }}'"
                            :class="type === '{{ $val }}' ? 'border-transparent shadow-lg scale-[1.03]' : 'border-gray-200 hover:border-gray-300 hover:-translate-y-0.5'"
                            :style="type === '{{ $val }}' ? 'background:linear-gradient(135deg,{{ $tm['ring'] }},{{ $tm['ring'] }}cc)' : ''"
                            class="shrink-0 w-24 sm:w-auto flex flex-col items-center gap-1.5 px-3 py-3.5 rounded-xl border-2 transition-all duration-200 bg-white">
                        <svg class="w-5 h-5 transition-colors duration-200" :class="type === '{{ $val }}' ? 'text-white' : '{{ $tm['text'] }}'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tm['icon'] }}"/>
                        </svg>
                        <span class="text-xs font-medium transition-colors duration-200" :class="type === '{{ $val }}' ? 'text-white' : 'text-gray-600'">{{ $tm['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <style>.scrollbar-none::-webkit-scrollbar{display:none}.scrollbar-none{-ms-overflow-style:none;scrollbar-width:none}</style>

            <form method="POST"
                  action="{{ route('guru.meetings.materials.blocks.store', [$meeting, $material]) }}"
                  enctype="multipart/form-data"
                  x-data="{ submitting: false }" @submit="submitting = true"
                  class="space-y-4">
                @csrf
                <input type="hidden" name="type" :value="type">
                <input type="hidden" name="order" value="{{ $material->contentBlocks->count() + 1 }}">

                {{-- Teks --}}
                <div x-show="type === 'text'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konten Teks</label>
                    <textarea name="content" rows="6"
                              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 font-mono text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                              placeholder="Ketik atau paste konten teks. Mendukung HTML dasar: <b>, <i>, <ul>, <a>, <h3>"></textarea>
                </div>

                {{-- Video --}}
                <div x-show="type === 'video'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Video (YouTube / Vimeo)</label>
                    <div class="field-icon relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <input type="url" name="video_url"
                               class="w-full border border-gray-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                               placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                </div>

                {{-- PDF Viewer & PDF Flipbook — dropzone bersama --}}
                <div x-show="type === 'pdf_viewer' || type === 'pdf_flipbook'" x-cloak
                     x-data="{ fileName: '' }">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        <span x-show="type === 'pdf_viewer'">Upload PDF (maks 50MB)</span>
                        <span x-show="type === 'pdf_flipbook'">Upload PDF untuk Flipbook (maks 20MB, 100 halaman)</span>
                    </label>
                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-indigo-200 rounded-xl px-4 py-7 cursor-pointer hover:bg-indigo-50/40 hover:border-indigo-300 transition-all duration-200"
                           :class="fileName ? 'border-solid border-emerald-300 bg-emerald-50/40' : ''">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-colors duration-200"
                             :class="fileName ? 'bg-emerald-100' : 'bg-indigo-50'">
                            <svg x-show="!fileName" class="w-5 h-5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/></svg>
                            <svg x-show="fileName" x-cloak class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-600 font-medium" x-text="fileName || 'Klik untuk pilih file PDF'"></p>
                        <p class="text-xs text-gray-400" x-show="!fileName">atau drag & drop di sini</p>
                        <input type="file" name="file" accept=".pdf" class="hidden"
                               @change="fileName = $event.target.files[0]?.name || ''">
                    </label>
                    <p class="text-xs text-gray-500 mt-1.5 flex items-start gap-1.5" x-show="type === 'pdf_flipbook'" x-cloak>
                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Konversi ke flipbook berjalan di background. Guru akan mendapat notifikasi saat selesai.
                    </p>
                </div>

                {{-- Link --}}
                <div x-show="type === 'link'" x-cloak class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Tautan</label>
                        <div class="field-icon relative">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.101"/>
                            </svg>
                            <input type="url" name="link_url"
                                   class="w-full border border-gray-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                                   placeholder="https://...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Tautan</label>
                        <input type="text" name="link_title"
                               class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5]"
                               placeholder="Contoh: Referensi Wikipedia">
                    </div>
                </div>

                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#4338CA] text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-md shadow-indigo-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 disabled:opacity-70">
                    <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="submitting ? 'Menambah...' : 'Tambah Blok'"></span>
                </button>
            </form>
        </div>
    </div>

    {{-- ============ MODAL KONFIRMASI HAPUS BLOK ============ --}}
    <div x-show="confirmDelete" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmDelete = false"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hapus Blok Konten?</h3>
            <p class="text-sm text-gray-500 mb-5">Blok <span class="font-medium text-gray-700" x-text="deleteLabel"></span> akan dihapus permanen dari materi ini.</p>
            <div class="flex gap-3">
                <button type="button" @click="confirmDelete = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all duration-200">Batal</button>
                <button type="button"
                        @click="document.getElementById(deleteForm).submit()"
                        class="flex-1 px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-all duration-200">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection