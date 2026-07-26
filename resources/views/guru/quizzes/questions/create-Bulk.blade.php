{{-- resources/views/guru/quizzes/questions/create-bulk.blade.php --}}
@extends('layouts.app')
@section('title', 'Buat Banyak Soal')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-5 md:py-8 pb-28 md:pb-8">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-5 md:mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-44 h-44 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.quizzes.questions.index') }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke bank soal
            </a>

            <div class="flex items-start justify-between gap-4 mt-2">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Buat Banyak Soal Sekaligus</h1>
                    <p class="text-indigo-100 text-sm md:text-base mt-0.5 max-w-md">Isi mapel & kategori sekali, lalu tambahkan soal sebanyak yang kamu mau — tipe soal boleh berbeda-beda tiap soal.</p>
                </div>

                {{-- Featured icon w/ living animation --}}
                <div class="relative hidden sm:flex items-center justify-center shrink-0 w-16 h-16">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-white/20" style="animation: soft-pulse 2.8s ease-in-out infinite;"></span>
                    <span class="relative flex items-center justify-center w-14 h-14 rounded-2xl bg-white/15 backdrop-blur">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v11.25a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z" />
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Stat chip --}}
            <div class="mt-4">
                <span id="questionCountChip" class="inline-flex items-center gap-1.5 bg-white border border-white/60 rounded-lg px-2.5 py-1 text-xs font-semibold text-[#4338CA]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span id="questionCountNum">0</span> soal ditambahkan
                </span>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-[#EF4444] text-sm rounded-xl p-4 mb-4 rise-in flex gap-2.5">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('guru.quizzes.questions.store-bulk') }}" id="bulkForm">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-5 md:p-6 mb-5 space-y-4 rise-in" style="animation-delay: 60ms">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket Soal <span class="text-[#EF4444]">*</span></label>
                <input type="text" name="batch_name" value="{{ old('batch_name') }}" placeholder="Contoh: UAS IPA Kelas 9"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" required>
                <p class="text-xs text-gray-400 mt-1">Semua soal di bawah ini akan dikelompokkan dalam paket ini di Bank Soal.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-[#EF4444]">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: IPA"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori/Topik</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="Opsional"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
                </div>
            </div>
        </div>

        {{-- Timeline of question blocks --}}
        <div id="questionsList" class="relative space-y-5 mb-5"></div>

        {{-- Desktop add button --}}
        <div class="hidden md:flex items-center gap-3 mb-6 rise-in" style="animation-delay: 100ms">
            <button type="button" onclick="addQuestionBlock()"
                    class="inline-flex items-center gap-1.5 bg-[#3730A3] hover:bg-[#312e81] text-white px-5 py-2.5 rounded-xl font-medium text-sm transition-all hover:-translate-y-0.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Soal
            </button>
        </div>

        <div class="hidden md:flex items-center gap-3 rise-in" style="animation-delay: 140ms">
            <button type="submit" id="submitBtnDesktop"
                    class="inline-flex items-center gap-2 bg-[#4F46E5] text-white px-6 py-2.5 rounded-xl hover:bg-[#4338CA] hover:-translate-y-0.5 active:scale-95 transition-all font-medium disabled:opacity-70 disabled:pointer-events-none">
                <svg class="submit-spinner hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="submit-label">Simpan Semua Soal</span>
            </button>
            <a href="{{ route('guru.quizzes.questions.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Batal</a>
        </div>
    </form>

    {{-- Mobile sticky action bar --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur border-t border-gray-200 px-4 py-3 flex items-center gap-2">
        <button type="button" onclick="addQuestionBlock()"
                class="flex-1 inline-flex items-center justify-center gap-1.5 bg-indigo-50 text-[#4338CA] px-4 py-2.5 rounded-xl font-medium text-sm transition-transform active:scale-95">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah
        </button>
        <button type="submit" form="bulkForm" id="submitBtnMobile"
                class="flex-[1.4] inline-flex items-center justify-center gap-2 bg-[#4F46E5] text-white px-4 py-2.5 rounded-xl font-medium text-sm disabled:opacity-70 disabled:pointer-events-none transition-transform active:scale-95">
            <svg class="submit-spinner hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="submit-label">Simpan Semua</span>
        </button>
    </div>
</div>

{{-- ===================== TEMPLATE: TYPE OPTIONS (carousel) ===================== --}}
<template id="typeCarouselTemplate">
    <div class="mb-3">
        <label class="block text-xs font-medium text-gray-600 mb-2">Tipe Soal</label>
        <select class="type-select hidden"></select>
        <div class="-mx-4 sm:mx-0 px-4 sm:px-0">
            <div class="type-options flex sm:grid sm:grid-cols-4 gap-2 md:gap-2.5 overflow-x-auto sm:overflow-visible snap-x snap-mandatory pb-1 sm:pb-0 scrollbar-none"></div>
        </div>
    </div>
</template>

<template id="questionBlockTemplate">
    <div class="relative question-block rise-in">
        {{-- timeline connector --}}
        <div class="timeline-line absolute left-[19px] top-11 bottom-0 w-px bg-indigo-100 hidden"></div>

        <div class="flex gap-3">
            <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-[#4F46E5] to-[#818CF8] text-white text-sm font-bold shadow-md shadow-indigo-200/60 question-number">1</div>

            <div class="flex-1 bg-white border border-gray-200 rounded-2xl p-4 md:p-5 min-w-0">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Soal</span>
                    <button type="button" onclick="requestRemoveBlock(this)" class="inline-flex items-center gap-1 text-gray-400 hover:text-[#EF4444] text-sm font-medium transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Hapus
                    </button>
                </div>

                <div class="type-slot"></div>

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Teks Soal</label>
                    <textarea class="question-text w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" rows="2" oninput="updateBulkBlankRows(this)"></textarea>
                </div>

                <div class="mb-3 w-24">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Poin</label>
                    <input type="number" class="points-input w-full border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" value="1" min="1" max="100">
                </div>

                <div class="section-multiple_choice border border-indigo-100 bg-indigo-50/40 rounded-xl p-3">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Pilihan Jawaban (pilih yang benar)</label>
                    <div class="options-list space-y-2">
                        <div class="flex items-center gap-2 option-row">
                            <input type="radio" class="correct-radio text-[#4F46E5] focus:ring-[#4F46E5]" value="0" checked>
                            <input type="text" class="option-text flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Pilihan 1">
                        </div>
                        <div class="flex items-center gap-2 option-row">
                            <input type="radio" class="correct-radio text-[#4F46E5] focus:ring-[#4F46E5]" value="1">
                            <input type="text" class="option-text flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Pilihan 2">
                        </div>
                    </div>
                    <button type="button" onclick="addOptionRow(this)" class="inline-flex items-center gap-1 text-xs text-[#4F46E5] hover:text-[#4338CA] mt-2 font-medium">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah pilihan
                    </button>
                </div>

                <div class="section-true_false border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Jawaban Benar</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" class="tf-answer text-[#4F46E5] focus:ring-[#4F46E5]" value="true"> Benar
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" class="tf-answer text-[#4F46E5] focus:ring-[#4F46E5]" value="false"> Salah
                        </label>
                    </div>
                </div>

                <div class="section-short_answer border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kata Kunci Jawaban (pisahkan koma)</label>
                    <input type="text" class="answer-keywords w-full border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Contoh: fotosintesis, photosynthesis">
                </div>

                <div class="section-sorting border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Urutan yang Benar (dari atas = urutan pertama)</label>
                    <div class="sorting-list space-y-2">
                        <div class="flex items-center gap-2 sorting-row">
                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-100 text-[#4338CA] text-[10px] font-bold shrink-0 sorting-number">1</span>
                            <input type="text" class="sorting-text flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Item urutan 1">
                        </div>
                        <div class="flex items-center gap-2 sorting-row">
                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-100 text-[#4338CA] text-[10px] font-bold shrink-0 sorting-number">2</span>
                            <input type="text" class="sorting-text flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Item urutan 2">
                        </div>
                    </div>
                    <button type="button" onclick="addSortingRow(this)" class="inline-flex items-center gap-1 text-xs text-[#4F46E5] hover:text-[#4338CA] mt-2 font-medium">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah item
                    </button>
                </div>

                <div class="section-fill_blank border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <p class="text-xs text-gray-500 mb-2">Pakai <code class="bg-white px-1 rounded">___</code> di teks soal di atas sebagai penanda blank.</p>
                    <p class="blank-count-label text-xs font-medium text-[#4F46E5] mb-2">0 blank terdeteksi</p>
                    <div class="blank-keywords-list"></div>
                </div>

                <div class="section-timer_challenge border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <div class="flex gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Batas Waktu (detik)</label>
                            <input type="number" class="timer-limit w-24 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" value="15" min="5" max="300">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Bonus Poin Maks (%)</label>
                            <input type="number" class="timer-bonus w-24 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" value="50" min="0" max="100">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Pakai bagian "Pilihan Jawaban" di atas untuk opsi PG-nya.</p>
                </div>

                <div class="section-drag_drop border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Pasangan yang Benar (istilah ↔ definisi)</label>
                    <div class="pairs-list space-y-2">
                        <div class="flex items-center gap-2 pair-row">
                            <input type="text" class="pair-left flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Istilah 1">
                            <span class="text-gray-400 text-xs">↔</span>
                            <input type="text" class="pair-right flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Definisi 1">
                        </div>
                        <div class="flex items-center gap-2 pair-row">
                            <input type="text" class="pair-left flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Istilah 2">
                            <span class="text-gray-400 text-xs">↔</span>
                            <input type="text" class="pair-right flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Definisi 2">
                        </div>
                    </div>
                    <button type="button" onclick="addPairRow(this)" class="inline-flex items-center gap-1 text-xs text-[#4F46E5] hover:text-[#4338CA] mt-2 font-medium">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah pasangan
                    </button>
                </div>

                <div class="section-word_search border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Daftar Kata (pisahkan koma)</label>
                    <input type="text" class="word-search-words w-full border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Contoh: LARAVEL, ELOQUENT, BLADE, ROUTE">
                    <p class="text-xs text-gray-400 mt-1">Kata akan otomatis jadi huruf kapital. Siswa akan mencari kata-kata ini di grid huruf.</p>
                </div>

                <div class="section-crossword border border-indigo-100 bg-indigo-50/40 rounded-xl p-3" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Daftar Kata & Petunjuk</label>
                    <div class="crossword-list space-y-2">
                        <div class="flex items-start gap-2 crossword-row">
                            <input type="text" class="crossword-word border border-gray-300 rounded-xl px-3 py-1.5 text-sm w-28 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="KATA 1">
                            <input type="text" class="crossword-clue flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Petunjuk untuk kata 1">
                        </div>
                        <div class="flex items-start gap-2 crossword-row">
                            <input type="text" class="crossword-word border border-gray-300 rounded-xl px-3 py-1.5 text-sm w-28 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="KATA 2">
                            <input type="text" class="crossword-clue flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Petunjuk untuk kata 2">
                        </div>
                    </div>
                    <button type="button" onclick="addCrosswordRow(this)" class="inline-flex items-center gap-1 text-xs text-[#4F46E5] hover:text-[#4338CA] mt-2 font-medium">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah kata
                    </button>
                    <p class="text-xs text-gray-400 mt-1">Minimal 2 kata. Semakin banyak huruf yang sama antar kata, semakin rapat grid-nya nanti.</p>
                </div>
            </div>
        </div>
    </div>
</template>

{{-- Delete confirmation modal --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="deleteModalOverlay" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>
    <div id="deleteModalPanel" class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl opacity-0 scale-90 transition-all duration-200">
        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-[#EF4444]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hapus soal ini?</h3>
        <p class="text-sm text-gray-500 mb-5">Konten yang sudah kamu isi untuk soal ini akan hilang dan tidak bisa dikembalikan.</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">Batal</button>
            <button type="button" onclick="confirmDeleteBlock()" class="flex-1 px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium transition-colors">Ya, Hapus</button>
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
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
.type-option-btn { transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
.type-option-btn:active { transform: scale(0.95); }
.type-option-btn.is-selected { box-shadow: 0 8px 16px -6px rgba(79,70,229,.35); }
</style>

<script>
    // ===== Tipe soal: value, label, warna (palet baku bagian 18), ikon =====
    const typeDefs = [
        { value: 'multiple_choice',  label: 'Pilihan Ganda',   color: 0, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />' },
        { value: 'true_false',       label: 'Benar/Salah',     color: 1, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' },
        { value: 'short_answer',     label: 'Isian Singkat',   color: 2, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />' },
        { value: 'true_false_swipe', label: 'TF Swipe',        color: 3, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h18M16.5 3L21 7.5m0 0L16.5 12M21 7.5H3" />' },
        { value: 'sorting',          label: 'Sorting/Urutan',  color: 0, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5M21 16.5L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />' },
        { value: 'fill_blank',       label: 'Fill in Blank',   color: 1, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h5m-5 6h16" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 12h8" stroke-dasharray="2 2" />' },
        { value: 'timer_challenge',  label: 'Timer Challenge', color: 2, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />' },
        { value: 'drag_drop',        label: 'Drag & Drop',     color: 3, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4" />' },
        { value: 'word_search',      label: 'Word Search',     color: 0, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M19 11a8 8 0 11-16 0 8 8 0 0116 0z" />' },
        { value: 'crossword',        label: 'Teka-teki Silang',color: 1, icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18" />' },
    ];
    const palette = [
        { ring: '#4F46E5', soft: 'bg-indigo-50', text: 'text-[#4F46E5]', selectedBg: 'bg-[#4F46E5]' },
        { ring: '#7C3AED', soft: 'bg-violet-50', text: 'text-violet-600', selectedBg: 'bg-violet-600' },
        { ring: '#0EA5E9', soft: 'bg-sky-50',    text: 'text-sky-600',    selectedBg: 'bg-sky-600' },
        { ring: '#3B82F6', soft: 'bg-blue-50',   text: 'text-blue-600',   selectedBg: 'bg-blue-600' },
    ];

    let questionIndex = 0;
    let pendingRemoveBlock = null;

    function buildTypeCarousel(block, groupIndex) {
        const tplFrag = document.getElementById('typeCarouselTemplate').content.cloneNode(true);
        const wrap = tplFrag.querySelector('.type-options');
        const hiddenSelect = tplFrag.querySelector('.type-select');
        hiddenSelect.name = '';

        typeDefs.forEach((t, i) => {
            const p = palette[t.color];
            const opt = document.createElement('option');
            opt.value = t.value;
            hiddenSelect.appendChild(opt);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `type-option-btn shrink-0 w-[27vw] sm:w-auto snap-start flex flex-col items-center gap-1.5 rounded-xl border-2 border-gray-200 ${p.soft} p-2.5 ${i === 0 ? 'is-selected' : ''}`;
            btn.style.borderColor = i === 0 ? p.ring : '';
            btn.dataset.value = t.value;
            btn.innerHTML = `
                <span class="flex items-center justify-center w-8 h-8 rounded-lg ${i === 0 ? p.selectedBg + ' text-white' : 'bg-white ' + p.text}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">${t.icon}</svg>
                </span>
                <span class="text-[10px] md:text-xs font-medium text-gray-700 text-center leading-tight">${t.label}</span>
            `;
            btn.addEventListener('click', () => selectType(block, btn, t, p));
            wrap.appendChild(btn);
        });

        return tplFrag;
    }

    function selectType(block, btn, typeDef, p) {
        const carousel = btn.closest('.type-options');
        carousel.querySelectorAll('.type-option-btn').forEach(b => {
            b.classList.remove('is-selected');
            b.style.borderColor = '';
            const iconWrap = b.querySelector('span');
            iconWrap.className = 'flex items-center justify-center w-8 h-8 rounded-lg bg-white ' + palette[typeDefs.find(td => td.value === b.dataset.value).color].text;
        });
        btn.classList.add('is-selected');
        btn.style.borderColor = p.ring;
        const iconWrap = btn.querySelector('span');
        iconWrap.className = `flex items-center justify-center w-8 h-8 rounded-lg ${p.selectedBg} text-white`;

        const hiddenSelect = block.querySelector('.type-select');
        hiddenSelect.value = typeDef.value;
        toggleQuestionType(hiddenSelect);
    }

    function addQuestionBlock() {
        const template = document.getElementById('questionBlockTemplate');
        const clone = template.content.cloneNode(true);
        const block = clone.querySelector('.question-block');
        block.dataset.index = questionIndex;
        block.querySelector('.question-number').textContent = questionIndex + 1;

        const typeSlot = block.querySelector('.type-slot');
        typeSlot.appendChild(buildTypeCarousel(block, questionIndex));

        block.querySelectorAll('.correct-radio').forEach(r => r.name = `correct_${questionIndex}`);
        block.querySelectorAll('.tf-answer').forEach(r => r.name = `tf_${questionIndex}`);

        document.getElementById('questionsList').appendChild(clone);
        questionIndex++;
        renumberBlocks();
        updateQuestionCount();
    }

    function requestRemoveBlock(btn) {
        pendingRemoveBlock = btn.closest('.question-block');
        openDeleteModal();
    }

    function openDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            document.getElementById('deleteModalOverlay').classList.remove('opacity-0');
            const panel = document.getElementById('deleteModalPanel');
            panel.classList.remove('opacity-0', 'scale-90');
        });
    }

    function closeDeleteModal() {
        document.getElementById('deleteModalOverlay').classList.add('opacity-0');
        const panel = document.getElementById('deleteModalPanel');
        panel.classList.add('opacity-0', 'scale-90');
        setTimeout(() => {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 180);
        pendingRemoveBlock = null;
    }

    function confirmDeleteBlock() {
        if (pendingRemoveBlock) {
            pendingRemoveBlock.remove();
            renumberBlocks();
            updateQuestionCount();
        }
        closeDeleteModal();
    }

    function renumberBlocks() {
        const blocks = document.querySelectorAll('.question-block');
        blocks.forEach((block, i) => {
            block.querySelector('.question-number').textContent = i + 1;
            const line = block.querySelector('.timeline-line');
            line.classList.toggle('hidden', i === blocks.length - 1);
        });
    }

    function updateQuestionCount() {
        const count = document.querySelectorAll('.question-block').length;
        document.getElementById('questionCountNum').textContent = count;
    }

    const bulkSectionMap = {
        multiple_choice:   'multiple_choice',
        timer_challenge:   'multiple_choice',
        true_false:        'true_false',
        true_false_swipe:  'true_false',
        short_answer:      'short_answer',
        sorting:           'sorting',
        fill_blank:        'fill_blank',
        drag_drop:         'drag_drop',
        word_search:       'word_search',
        crossword:         'crossword',
    };

    function toggleQuestionType(select) {
        const block = select.closest('.question-block');
        const type = select.value;
        const activeSection = bulkSectionMap[type];
        ['multiple_choice', 'true_false', 'short_answer', 'sorting', 'fill_blank', 'drag_drop', 'word_search', 'crossword'].forEach(t => {
            block.querySelector('.section-' + t).style.display = (t === activeSection) ? 'block' : 'none';
        });

        const timerSection = block.querySelector('.section-timer_challenge');
        timerSection.style.display = (type === 'timer_challenge') ? 'block' : 'none';

        if (type === 'fill_blank') updateBulkBlankRows(block.querySelector('.question-text'));
    }

    function addOptionRow(btn) {
        const list = btn.previousElementSibling;
        const count = list.querySelectorAll('.option-row').length;
        if (count >= 5) return;
        const block = btn.closest('.question-block');
        const groupIndex = block.dataset.index;

        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 option-row';
        row.innerHTML = `
            <input type="radio" class="correct-radio text-[#4F46E5] focus:ring-[#4F46E5]" name="correct_${groupIndex}" value="${count}">
            <input type="text" class="option-text flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Pilihan ${count + 1}">
        `;
        list.appendChild(row);
    }

    function addSortingRow(btn) {
        const list = btn.previousElementSibling;
        const count = list.querySelectorAll('.sorting-row').length;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 sorting-row';
        row.innerHTML = `
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-100 text-[#4338CA] text-[10px] font-bold shrink-0 sorting-number">${count + 1}</span>
            <input type="text" class="sorting-text flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Item urutan ${count + 1}">
        `;
        list.appendChild(row);
    }

    function addCrosswordRow(btn) {
        const list = btn.previousElementSibling;
        const count = list.querySelectorAll('.crossword-row').length;
        const row = document.createElement('div');
        row.className = 'flex items-start gap-2 crossword-row';
        row.innerHTML = `
            <input type="text" class="crossword-word border border-gray-300 rounded-xl px-3 py-1.5 text-sm w-28 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="KATA ${count + 1}">
            <input type="text" class="crossword-clue flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Petunjuk untuk kata ${count + 1}">
        `;
        list.appendChild(row);
    }

    function addPairRow(btn) {
        const list = btn.previousElementSibling;
        const count = list.querySelectorAll('.pair-row').length;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 pair-row';
        row.innerHTML = `
            <input type="text" class="pair-left flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Istilah ${count + 1}">
            <span class="text-gray-400 text-xs">↔</span>
            <input type="text" class="pair-right flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Definisi ${count + 1}">
        `;
        list.appendChild(row);
    }

    function updateBulkBlankRows(textarea) {
        const block = textarea.closest('.question-block');
        if (block.querySelector('.type-select').value !== 'fill_blank') return;

        const count = (textarea.value.match(/___/g) || []).length;
        const container = block.querySelector('.blank-keywords-list');
        const existingValues = Array.from(container.querySelectorAll('.blank-keyword-input')).map(i => i.value);

        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 mb-2';
            row.innerHTML = `
                <span class="text-xs text-gray-400 w-16 shrink-0">Blank ${i + 1}</span>
                <input type="text" class="blank-keyword-input flex-1 border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" placeholder="Jawaban benar, pisah koma" value="${existingValues[i] || ''}">
            `;
            container.appendChild(row);
        }
        block.querySelector('.blank-count-label').textContent = count + ' blank terdeteksi';
    }

    document.getElementById('bulkForm').addEventListener('submit', function (e) {
        const blocks = document.querySelectorAll('.question-block');

        if (blocks.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 soal terlebih dahulu.');
            return;
        }

        blocks.forEach((block, i) => {
            const type = block.querySelector('.type-select').value;
            const text = block.querySelector('.question-text').value;
            const points = block.querySelector('.points-input').value;

            appendHidden(`questions[${i}][type]`, type);
            appendHidden(`questions[${i}][question_text]`, text);
            appendHidden(`questions[${i}][points]`, points);

            if (type === 'multiple_choice' || type === 'timer_challenge') {
                const options = block.querySelectorAll('.option-text');
                const correctRadio = block.querySelector('.correct-radio:checked');
                options.forEach((opt, oi) => {
                    appendHidden(`questions[${i}][options][${oi}]`, opt.value);
                });
                appendHidden(`questions[${i}][correct_option]`, correctRadio ? correctRadio.value : '0');

                if (type === 'timer_challenge') {
                    const timeLimit = block.querySelector('.timer-limit').value;
                    const bonusMax = block.querySelector('.timer-bonus').value;
                    appendHidden(`questions[${i}][time_limit_seconds]`, timeLimit);
                    appendHidden(`questions[${i}][bonus_max_percent]`, bonusMax);
                }
            } else if (type === 'true_false' || type === 'true_false_swipe') {
                const tfRadio = block.querySelector('.tf-answer:checked');
                appendHidden(`questions[${i}][true_false_answer]`, tfRadio ? tfRadio.value : 'true');
            } else if (type === 'short_answer') {
                const keywords = block.querySelector('.answer-keywords').value;
                appendHidden(`questions[${i}][answer_keywords]`, keywords);
            } else if (type === 'sorting') {
                const items = block.querySelectorAll('.sorting-text');
                items.forEach((item, si) => {
                    appendHidden(`questions[${i}][sorting_items][${si}]`, item.value);
                });
            } else if (type === 'fill_blank') {
                const keywords = block.querySelectorAll('.blank-keyword-input');
                keywords.forEach((kw, ki) => {
                    appendHidden(`questions[${i}][blank_keywords][${ki}]`, kw.value);
                });
            } else if (type === 'drag_drop') {
                const lefts = block.querySelectorAll('.pair-left');
                const rights = block.querySelectorAll('.pair-right');
                lefts.forEach((left, pi) => {
                    appendHidden(`questions[${i}][pairs_left][${pi}]`, left.value);
                    appendHidden(`questions[${i}][pairs_right][${pi}]`, rights[pi] ? rights[pi].value : '');
                });
            } else if (type === 'word_search') {
                const words = block.querySelector('.word-search-words').value;
                appendHidden(`questions[${i}][word_search_words]`, words);
            } else if (type === 'crossword') {
                const words = block.querySelectorAll('.crossword-word');
                const clues = block.querySelectorAll('.crossword-clue');
                words.forEach((w, wi) => {
                    appendHidden(`questions[${i}][crossword_words][${wi}]`, w.value);
                    appendHidden(`questions[${i}][crossword_clues][${wi}]`, clues[wi] ? clues[wi].value : '');
                });
            }
        });

        document.querySelectorAll('.submit-spinner').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.submit-label').forEach(el => el.textContent = 'Menyimpan...');
        document.querySelectorAll('#submitBtnDesktop, #submitBtnMobile').forEach(el => el.disabled = true);
    });

    function appendHidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        document.getElementById('bulkForm').appendChild(input);
    }

    document.addEventListener('DOMContentLoaded', () => addQuestionBlock());
</script>
@endsection