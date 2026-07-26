{{-- resources/views/guru/quizzes/questions/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Bank Soal')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 md:py-8" x-data="{ confirmDelete: false, deleteForm: null }">

    @php
        $totalSoal   = $grouped->sum(fn($g) => $g->count());
        $batchCount  = $grouped->filter(fn($g) => $g->first()->batch_name)->count();
        $singleCount = $grouped->count() - $batchCount;

        // Palet warna baku (bagian 18) — dipetakan konsisten per tipe soal di seluruh halaman
        $typePalette = [
            'multiple_choice'   => ['soft' => 'bg-indigo-50', 'text' => 'text-[#4F46E5]'],
            'timer_challenge'   => ['soft' => 'bg-indigo-50', 'text' => 'text-[#4F46E5]'],
            'true_false'        => ['soft' => 'bg-violet-50', 'text' => 'text-violet-600'],
            'true_false_swipe'  => ['soft' => 'bg-violet-50', 'text' => 'text-violet-600'],
            'short_answer'      => ['soft' => 'bg-sky-50',    'text' => 'text-sky-600'],
            'sorting'           => ['soft' => 'bg-blue-50',   'text' => 'text-blue-600'],
            'fill_blank'        => ['soft' => 'bg-sky-50',    'text' => 'text-sky-600'],
            'drag_drop'         => ['soft' => 'bg-blue-50',   'text' => 'text-blue-600'],
            'word_search'       => ['soft' => 'bg-indigo-50', 'text' => 'text-[#4F46E5]'],
            'crossword'         => ['soft' => 'bg-violet-50', 'text' => 'text-violet-600'],
        ];
    @endphp

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-44 h-44 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-bold">Bank Soal</h1>
                    <p class="text-indigo-100 text-sm md:text-base mt-0.5">Soal bisa dipakai ulang di kuis manapun untuk mata pelajaran yang sama.</p>
                </div>

                {{-- Featured icon w/ living animation --}}
                <div class="relative hidden sm:flex items-center justify-center shrink-0 w-14 h-14">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-white/20" style="animation: soft-pulse 2.8s ease-in-out infinite;"></span>
                    <span class="relative flex items-center justify-center w-12 h-12 rounded-2xl bg-white/15 backdrop-blur">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Stat chips --}}
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 rounded-lg px-2.5 py-1 text-xs font-semibold">
                    {{ $totalSoal }} soal total
                </span>
                @if ($batchCount > 0)
                    <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 rounded-lg px-2.5 py-1 text-xs font-semibold">
                        {{ $batchCount }} paket
                    </span>
                @endif
                @if ($singleCount > 0)
                    <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 rounded-lg px-2.5 py-1 text-xs font-semibold">
                        {{ $singleCount }} soal tunggal
                    </span>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mt-4">
                <a href="{{ route('guru.quizzes.questions.create') }}"
                   class="inline-flex items-center justify-center gap-1.5 bg-white text-[#4338CA] px-4 py-2.5 rounded-xl font-medium text-sm shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Soal
                </a>
                <a href="{{ route('guru.quizzes.questions.create-bulk') }}"
                   class="inline-flex items-center justify-center gap-1.5 bg-white/10 border border-white/15 text-white px-4 py-2.5 rounded-xl font-medium text-sm hover:bg-white/20 hover:-translate-y-0.5 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    Buat Banyak Soal
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm rise-in flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-[#10B981]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-center gap-2 md:gap-3 mb-5 rise-in" style="animation-delay: 40ms">
        <div class="relative flex-1 min-w-[45%] sm:flex-none sm:min-w-0">
            <select name="subject" onchange="this.form.submit()"
                    class="w-full appearance-none border border-gray-300 rounded-xl pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition bg-white">
                <option value="">Semua Mapel</option>
                @foreach ($subjects as $s)
                    <option value="{{ $s }}" {{ $subject === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <svg class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        <div class="relative flex-1 min-w-[45%] sm:flex-none sm:min-w-0">
            <select name="category" onchange="this.form.submit()"
                    class="w-full appearance-none border border-gray-300 rounded-xl pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition bg-white">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            <svg class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        @if ($subject || $category)
            <a href="{{ route('guru.quizzes.questions.index') }}"
               class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-[#EF4444] transition-colors px-2 py-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reset
            </a>
        @endif
    </form>

    <div class="space-y-3">
        @forelse ($grouped as $gi => $groupQuestions)
            @php $isBatch = $groupQuestions->first()->batch_name; @endphp

            @if ($isBatch)
                {{-- Tampilan grup/paket --}}
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden rise-in" style="animation-delay: {{ min($gi,10) * 50 }}ms" x-data="{ open: false }">
                    <div class="p-4 md:p-5 flex items-center justify-between gap-3 cursor-pointer hover:bg-indigo-50/30 transition-colors duration-150" @click="open = !open">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#4F46E5] to-[#818CF8] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-[#4F46E5] font-semibold shrink-0">Paket</span>
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $isBatch }}</h3>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    {{ $groupQuestions->first()->subject }}
                                    @if ($groupQuestions->first()->category) &middot; {{ $groupQuestions->first()->category }} @endif
                                    &middot; {{ $groupQuestions->count() }} soal
                                </p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    <div x-show="open" x-cloak x-transition class="border-t border-gray-100 divide-y divide-gray-50">
                        @foreach ($groupQuestions as $question)
                            @php $tp = $typePalette[$question->type] ?? ['soft' => 'bg-gray-100', 'text' => 'text-gray-500']; @endphp
                            <div class="p-4 hover:bg-indigo-50/30 transition-colors duration-150">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $tp['soft'] }} {{ $tp['text'] }} font-medium">{{ $question->typeLabel() }}</span>
                                    @if ($question->type === 'short_answer' && empty($question->answer_keywords))
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-50 text-[#F59E0B] font-medium">Belum ada kata kunci</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-800">{{ Str::limit(strip_tags($question->question_text), 120) }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs text-gray-400">{{ $question->points }} poin</p>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('guru.quizzes.questions.edit', $question) }}" title="Edit"
                                           class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-[#4F46E5] hover:bg-indigo-50 hover:scale-105 active:scale-95 transition-all text-xs font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                                            </svg>
                                            <span class="hidden sm:inline">Edit</span>
                                        </a>
                                        <form action="{{ route('guru.quizzes.questions.destroy', $question) }}" method="POST" x-ref="deleteForm{{ $question->id }}">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button"
                                                @click="confirmDelete = true; deleteForm = $refs['deleteForm{{ $question->id }}']"
                                                title="Hapus"
                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-[#EF4444] hover:bg-red-50 hover:scale-105 active:scale-95 transition-all text-xs font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            <span class="hidden sm:inline">Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Tampilan soal tunggal --}}
                @php
                    $question = $groupQuestions->first();
                    $tp = $typePalette[$question->type] ?? ['soft' => 'bg-gray-100', 'text' => 'text-gray-500'];
                @endphp
                <div class="bg-white border border-gray-200 rounded-2xl p-4 md:p-5 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-100 transition-all duration-200 rise-in" style="animation-delay: {{ min($gi,10) * 50 }}ms">
                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $tp['soft'] }} {{ $tp['text'] }} font-medium">{{ $question->typeLabel() }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">{{ $question->subject }}</span>
                        @if ($question->category)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-violet-50 text-violet-600 font-medium">{{ $question->category }}</span>
                        @endif
                        @if ($question->type === 'short_answer' && empty($question->answer_keywords))
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-50 text-[#F59E0B] font-medium">Belum ada kata kunci</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-800">{{ Str::limit(strip_tags($question->question_text), 120) }}</p>

                    <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-gray-100">
                        <p class="text-xs text-gray-400">{{ $question->points }} poin</p>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('guru.quizzes.questions.edit', $question) }}" title="Edit"
                               class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-[#4F46E5] hover:bg-indigo-50 hover:scale-105 active:scale-95 transition-all text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                                </svg>
                                <span class="hidden sm:inline">Edit</span>
                            </a>
                            <form action="{{ route('guru.quizzes.questions.destroy', $question) }}" method="POST" x-ref="deleteForm{{ $question->id }}">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button"
                                    @click="confirmDelete = true; deleteForm = $refs['deleteForm{{ $question->id }}']"
                                    title="Hapus"
                                    class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-[#EF4444] hover:bg-red-50 hover:scale-105 active:scale-95 transition-all text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                <span class="hidden sm:inline">Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-16 bg-white border border-dashed border-gray-200 rounded-2xl rise-in">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <p class="font-medium text-gray-600">Bank soal masih kosong</p>
                <p class="text-sm mt-1 text-gray-400">Klik "Tambah Soal" untuk mulai membangun bank soal kamu.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal Konfirmasi Hapus (custom, satu untuk semua baris) --}}
    <div x-show="confirmDelete" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmDelete = false"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hapus Soal?</h3>
            <p class="text-sm text-gray-500 mb-5">Soal ini akan dihapus dari bank soal. Tindakan ini tidak bisa dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="confirmDelete = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                <button @click="deleteForm.submit()" class="flex-1 px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes soft-pulse {
    0%, 100% { transform: scale(1); opacity: .6; }
    50% { transform: scale(1.18); opacity: 0; }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection