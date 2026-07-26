{{-- resources/views/siswa/quizzes/attempt.blade.php --}}
@extends('layouts.app')
@section('title', 'Mengerjakan — ' . $quiz->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6 md:py-8 pb-24 md:pb-8"
     x-data="quizAttempt({
        displayMode: @js($quiz->display_mode),
        deadline: @js($deadline->toIso8601String()),
        totalQuestions: @js($questions->count()),
        saveUrl: @js(route('siswa.classes.quizzes.answer', [$schoolClass, $quiz, $attempt])),
        tabSwitchUrl: @js(route('siswa.classes.quizzes.tab-switch', [$schoolClass, $quiz, $attempt])),
        submitUrl: @js(route('siswa.classes.quizzes.submit', [$schoolClass, $quiz, $attempt])),
     })"
     x-init="init()"
     @visibilitychange.window="onVisibilityChange()">

    {{-- Header sticky: judul + timer ring + progress --}}
    <div class="sticky top-0 bg-gray-50/95 backdrop-blur z-10 pb-4 pt-1 -mx-4 px-4 border-b border-gray-100 mb-5 md:mb-6">
        <div class="flex items-center justify-between gap-3 md:gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-base md:text-lg font-bold text-gray-900 truncate">{{ $quiz->title }}</h1>
                <p class="text-xs text-gray-500">{{ $schoolClass->name }} — Percobaan {{ $attempt->attempt_number }}</p>
            </div>

            {{-- Timer ring --}}
            <div class="relative w-14 h-14 md:w-16 md:h-16 shrink-0">
                <div class="absolute inset-0 rounded-full transition-all duration-500"
                     :style="`background: conic-gradient(${timeLeft <= 60 ? '#EF4444' : (timeLeft <= 300 ? '#F59E0B' : '#4F46E5')} ${timePercent}%, #E5E7EB 0)`"></div>
                <div class="absolute inset-[3px] rounded-full bg-white flex flex-col items-center justify-center">
                    <span class="font-mono font-bold text-xs md:text-sm"
                          :class="timeLeft <= 60 ? 'text-[#EF4444] animate-pulse' : 'text-[#4338CA]'"
                          x-text="formattedTime"></span>
                </div>
            </div>
        </div>

        @if ($quiz->instructions)
            <p class="text-xs md:text-sm text-gray-500 mt-2">{{ $quiz->instructions }}</p>
        @endif

        {{-- Progress bar jawaban --}}
        <div class="mt-3">
            <div class="flex items-center justify-between text-[11px] text-gray-400 mb-1">
                <span>Progres jawaban</span>
                <span x-text="`${answeredCount()} / {{ $questions->count() }} terjawab`"></span>
            </div>
            <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-[#4F46E5] to-[#10B981] transition-all duration-500"
                     :style="`width: ${(answeredCount() / {{ max($questions->count(), 1) }}) * 100}%`"></div>
            </div>
        </div>

        {{-- Dot-stepper mode satu-per-satu --}}
        <div class="flex items-center gap-1.5 mt-2.5 overflow-x-auto" x-show="displayMode === 'one_by_one'" x-cloak>
            @for ($qi = 0; $qi < $questions->count(); $qi++)
                <div class="h-2 rounded-full shrink-0 transition-all duration-300"
                     :class="currentIndex === {{ $qi }} ? 'bg-[#4F46E5] w-6' : (currentIndex > {{ $qi }} ? 'bg-[#10B981] w-2' : 'bg-gray-200 w-2')"></div>
            @endfor
        </div>

        {{-- Indikator pelanggaran tab-switch --}}
        <div x-show="tabSwitchCount > 0" x-cloak
             class="mt-2.5 flex items-start gap-2 text-xs px-3 py-2 rounded-xl bg-[#FFFBEB] text-[#92400E] border border-[#FDE68A]">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>Kamu terdeteksi keluar dari halaman <span class="font-semibold" x-text="tabSwitchCount"></span>x. Kuis akan otomatis dikumpulkan setelah 3x pelanggaran.</span>
        </div>
    </div>

   <form x-ref="quizForm" :action="submitUrl" method="POST" @submit="onSubmitForm">
        @csrf

        <div class="space-y-4">
            @foreach ($questions as $i => $question)
                @php
                    $existing = $existingAnswers[$question->id] ?? null;

                    // [label, gradient class, solid hex utk elemen kecil, ikon path, bg lembut utk area konten]
                    $typeMeta = match ($question->type) {
                        'multiple_choice'  => ['Pilihan Ganda', 'from-[#4F46E5] to-[#818CF8]', '#4F46E5', 'M9 5l7 7-7 7', 'from-indigo-50/60'],
                        'true_false'       => ['Benar / Salah', 'from-[#4F46E5] to-[#818CF8]', '#4F46E5', 'M5 13l4 4L19 7', 'from-indigo-50/60'],
                        'short_answer'     => ['Isian Singkat', 'from-[#6366F1] to-[#A5B4FC]', '#4F46E5', 'M15.232 5.232l3.536 3.536M9 11l6.293-6.293', 'from-indigo-50/60'],
                        'true_false_swipe' => ['Geser Benar/Salah', 'from-[#059669] to-[#34D399]', '#059669', 'M8 9l4-4 4 4m0 6l-4 4-4-4', 'from-emerald-50/60'],
                        'sorting'          => ['Urutkan', 'from-[#D97706] to-[#FBBF24]', '#B45309', 'M8 9l4-4 4 4m0 6l-4 4-4-4', 'from-amber-50/60'],
                        'fill_blank'       => ['Isi Bagian Kosong', 'from-[#6366F1] to-[#A5B4FC]', '#4F46E5', 'M15.232 5.232l3.536 3.536', 'from-indigo-50/60'],
                        'timer_challenge'  => ['Tantangan Waktu', 'from-[#DC2626] to-[#F87171]', '#DC2626', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'from-red-50/60'],
                        'drag_drop'        => ['Jodohkan', 'from-[#059669] to-[#34D399]', '#059669', 'M13.828 10.172a4 4 0 00-5.656 0', 'from-emerald-50/60'],
                        'word_search'      => ['Cari Kata', 'from-[#D97706] to-[#FBBF24]', '#B45309', 'M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z', 'from-amber-50/60'],
                        'crossword'        => ['Teka-Teki Silang', 'from-[#7C3AED] to-[#C4B5FD]', '#6D28D9', 'M4 6h16M4 12h16M4 18h16', 'from-violet-50/60'],
                        default            => ['Soal', 'from-gray-400 to-gray-500', '#4B5563', 'M9 5l7 7-7 7', 'from-gray-50'],
                    };
                @endphp

                <div class="relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl overflow-hidden shadow-sm shadow-indigo-100/40 hover:shadow-md hover:shadow-indigo-100/60 transition-all duration-300"
                     x-show="displayMode === 'all_at_once' || currentIndex === {{ $i }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak="{{ $i }}">

                    {{-- Aksen gradient tipis di atas card, sesuai warna tipe soal --}}
                    <div class="h-1.5 w-full bg-gradient-to-r {{ $typeMeta[1] }}"></div>

                    {{-- Wash warna lembut di background konten, biar tiap tipe punya "nuansa" beda tapi tetap ringan dibaca --}}
                    <div class="bg-gradient-to-b {{ $typeMeta[4] }} to-white p-4 md:p-6">

                        <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 text-[11px] md:text-xs font-bold px-3 py-1.5 rounded-full text-white bg-gradient-to-r {{ $typeMeta[1] }} shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $typeMeta[3] }}"/>
                                </svg>
                                {{ $typeMeta[0] }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400 font-medium">Soal <span x-text="currentIndex + 1"></span> / {{ $questions->count() }}</span>
                                <span class="text-xs px-2.5 py-1 rounded-full bg-white text-gray-600 font-bold border border-gray-200 shadow-sm">⭐ {{ $question->points }} poin</span>
                            </div>
                        </div>

                        @unless ($question->type === 'fill_blank')
                            <p class="text-gray-800 font-semibold mb-4 text-sm md:text-base leading-relaxed">{{ $question->question_text }}</p>
                        @endunless

                        @if (in_array($question->type, ['multiple_choice', 'true_false']))
                            <div class="space-y-2.5">
                                @foreach ($question->options as $option)
                                    @php
                                        $checked = ($existing['option_id'] ?? null) == $option->id;
                                        $letter = chr(65 + $loop->index);
                                    @endphp
                                    <label class="group flex items-center gap-3 border-2 border-gray-100 bg-white rounded-xl px-3.5 md:px-4 py-3 cursor-pointer hover:border-indigo-200 hover:bg-indigo-50/40 has-[:checked]:border-[#4F46E5] has-[:checked]:bg-gradient-to-r has-[:checked]:from-indigo-50 has-[:checked]:to-white has-[:checked]:shadow-md has-[:checked]:shadow-indigo-100 transition-all duration-200 active:scale-[0.98]">
                                        <span class="w-8 h-8 shrink-0 rounded-full border-2 border-gray-200 flex items-center justify-center text-xs font-bold text-gray-400 transition-all duration-200
                                                     [label:has(:checked)_&]:bg-gradient-to-br [label:has(:checked)_&]:from-[#4F46E5] [label:has(:checked)_&]:to-[#818CF8] [label:has(:checked)_&]:border-[#4F46E5] [label:has(:checked)_&]:text-white [label:has(:checked)_&]:scale-110 [label:has(:checked)_&]:shadow-md">
                                            {{ $letter }}
                                        </span>
                                        <input type="radio"
                                               name="answer_{{ $question->id }}"
                                               value="{{ $option->id }}"
                                               @checked($checked)
                                               class="hidden"
                                               @change="saveAnswer({{ $question->id }}, { option_id: {{ $option->id }} })">
                                        <span class="text-sm text-gray-700 flex-1 font-medium">{{ $option->option_text }}</span>
                                        <svg class="w-5 h-5 text-[#4F46E5] opacity-0 scale-50 [label:has(:checked)_&]:opacity-100 [label:has(:checked)_&]:scale-100 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </label>
                                @endforeach
                            </div>

                        @elseif ($question->type === 'short_answer')
                            <div class="relative group">
                                <div class="absolute -inset-0.5 rounded-xl bg-gradient-to-r from-indigo-200 to-violet-200 opacity-0 group-focus-within:opacity-60 blur transition-opacity duration-300"></div>
                                <div class="relative">
                                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.293-6.293a1 1 0 011.414 0l2.586 2.586a1 1 0 010 1.414L13 15l-4 1 1-4z"/>
                                    </svg>
                                    <input type="text"
                                           name="answer_{{ $question->id }}"
                                           value="{{ $existing['text'] ?? '' }}"
                                           placeholder="Ketik jawabanmu di sini..."
                                           class="w-full rounded-xl border-2 border-gray-200 pl-10 pr-3 py-3 text-sm font-medium focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 outline-none transition-all duration-200 bg-white"
                                           @input.debounce.600ms="saveAnswer({{ $question->id }}, { text: $event.target.value })">
                                </div>
                            </div>

                        @elseif ($question->type === 'true_false_swipe')
                            @php
                                $benarOption = $question->options->firstWhere('order', 0);
                                $salahOption = $question->options->firstWhere('order', 1);
                                $existingOptionId = $existing['option_id'] ?? null;
                            @endphp
                            <div x-data="{
                                    dx: 0, dragging: false, startX: 0,
                                    chosen: {{ $existingOptionId ?? 'null' }},
                                    benarId: {{ $benarOption->id ?? 'null' }},
                                    salahId: {{ $salahOption->id ?? 'null' }},
                                    start(e) { if (this.chosen !== null) return; this.dragging = true; this.startX = (e.touches ? e.touches[0].clientX : e.clientX); },
                                    move(e) {
                                        if (!this.dragging) return;
                                        const x = (e.touches ? e.touches[0].clientX : e.clientX);
                                        this.dx = x - this.startX;
                                    },
                                    end() {
                                        if (!this.dragging) return;
                                        this.dragging = false;
                                        if (this.dx > 80) { this.pick(this.benarId); }
                                        else if (this.dx < -80) { this.pick(this.salahId); }
                                        this.dx = 0;
                                    },
                                    pick(id) {
                                        if (this.chosen !== null) return;
                                        this.chosen = id;
                                        saveAnswer({{ $question->id }}, { option_id: id });
                                        this.$dispatch('tf-swipe-picked');
                                    }
                                }"
                                @tf-swipe-picked.window="if (displayMode === 'one_by_one' && currentIndex === {{ $i }} && currentIndex < totalQuestions - 1) { setTimeout(() => currentIndex++, 400); }"
                                class="select-none">
                                <div class="relative h-40 flex flex-col items-center justify-center rounded-2xl border-2 transition-all duration-150"
                                     :class="[chosen === benarId ? 'border-[#10B981] bg-gradient-to-br from-[#ECFDF5] to-white shadow-lg shadow-emerald-100' : (chosen === salahId ? 'border-[#EF4444] bg-gradient-to-br from-[#FEF2F2] to-white shadow-lg shadow-red-100' : 'border-emerald-200 bg-gradient-to-br from-emerald-50/50 to-white cursor-grab active:cursor-grabbing')]"
                                     :style="`transform: translateX(${dx}px) rotate(${dx / 20}deg)`"
                                     @mousedown="start($event)" @mousemove.window="move($event)" @mouseup.window="end()"
                                     @touchstart="start($event)" @touchmove.window="move($event)" @touchend.window="end()">
                                    <span class="absolute left-4 top-4 text-[#EF4444] font-bold text-xs transition-opacity flex items-center gap-1"
                                          :class="dx < -30 ? 'opacity-100' : 'opacity-0'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        SALAH
                                    </span>
                                    <span class="absolute right-4 top-4 text-[#10B981] font-bold text-xs transition-opacity flex items-center gap-1"
                                          :class="dx > 30 ? 'opacity-100' : 'opacity-0'">
                                        BENAR
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <template x-if="chosen === null">
                                        <svg class="w-7 h-7 text-emerald-300 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                        </svg>
                                    </template>
                                    <template x-if="chosen !== null">
                                        <svg class="w-9 h-9" :class="chosen === benarId ? 'text-[#10B981]' : 'text-[#EF4444]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                    <p class="text-gray-400 text-xs mt-1.5 font-medium">Geser kanan = Benar, kiri = Salah</p>
                                </div>
                                <div class="flex justify-center gap-4 mt-3">
                                    <span class="px-5 py-2.5 rounded-xl text-sm font-bold border-2 pointer-events-none select-none transition-all"
                                          :class="chosen === salahId ? 'bg-gradient-to-r from-[#EF4444] to-[#F87171] text-white border-transparent shadow-md' : 'border-red-200 text-red-400'">
                                        ✕ Salah
                                    </span>
                                    <span class="px-5 py-2.5 rounded-xl text-sm font-bold border-2 pointer-events-none select-none transition-all"
                                          :class="chosen === benarId ? 'bg-gradient-to-r from-[#10B981] to-[#34D399] text-white border-transparent shadow-md' : 'border-emerald-200 text-emerald-400'">
                                        ✓ Benar
                                    </span>
                                </div>
                            </div>

                        @elseif ($question->type === 'sorting')
                            @php
                                $existingOrder = $existing['order'] ?? $question->options->pluck('id')->toArray();
                                $initialItems = collect($existingOrder)
                                    ->map(fn($id) => ['id' => $id, 'text' => $question->options->firstWhere('id', $id)->option_text ?? ''])
                                    ->values();
                            @endphp
                            <div x-data="{
                                    items: @js($initialItems),
                                    dragIndex: null,
                                    dragStart(i) { this.dragIndex = i; },
                                    drop(i) {
                                        if (this.dragIndex === null || this.dragIndex === i) return;
                                        const moved = this.items.splice(this.dragIndex, 1)[0];
                                        this.items.splice(i, 0, moved);
                                        this.dragIndex = null;
                                        this.emit();
                                    },
                                    move(i, dir) {
                                        const j = i + dir;
                                        if (j < 0 || j >= this.items.length) return;
                                        [this.items[i], this.items[j]] = [this.items[j], this.items[i]];
                                        this.emit();
                                    },
                                    emit() {
                                        saveAnswer({{ $question->id }}, { order: this.items.map(it => it.id) });
                                    }
                                }"
                                class="space-y-2">
                                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-3 flex items-center gap-1.5 font-medium">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                    Seret untuk mengurutkan, atau pakai tombol panah
                                </p>
                                <template x-for="(item, i) in items" :key="item.id">
                                    <div class="flex items-center gap-3 bg-white border-2 border-amber-100 rounded-xl px-3 py-3 cursor-move hover:border-amber-300 hover:shadow-md transition-all duration-200"
                                         draggable="true"
                                         @dragstart="dragStart(i)"
                                         @dragover.prevent
                                         @drop.prevent="drop(i)">
                                        <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                        <span class="w-7 h-7 rounded-full bg-gradient-to-br from-[#D97706] to-[#FBBF24] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm" x-text="i + 1"></span>
                                        <span class="flex-1 text-sm text-gray-700 font-medium" x-text="item.text"></span>
                                        <div class="flex flex-col gap-0.5 shrink-0">
                                            <button type="button" @click="move(i, -1)" class="text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded p-1 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                            </button>
                                            <button type="button" @click="move(i, 1)" class="text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded p-1 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                        @elseif ($question->type === 'fill_blank')
                            @php
                                $segments = explode('___', $question->question_text);
                                $blankCount = count($segments) - 1;
                                $existingBlanks = $existing['blanks'] ?? array_fill(0, $blankCount, '');
                            @endphp
                            <div x-data="{
                                    blanks: @js($existingBlanks),
                                    emit() { saveAnswer({{ $question->id }}, { blanks: this.blanks }); }
                                }"
                                class="text-gray-800 font-semibold mb-2 leading-loose text-sm md:text-base bg-white border-2 border-indigo-100 rounded-xl px-4 py-4">
                                @foreach ($segments as $idx => $segment)
                                    {{ $segment }}
                                    @if ($idx < $blankCount)
                                        <input type="text"
                                               x-model="blanks[{{ $idx }}]"
                                               @input.debounce.600ms="emit()"
                                               class="inline-block w-32 border-b-[3px] border-indigo-300 focus:border-[#4F46E5] focus:outline-none focus:ring-2 focus:ring-indigo-100 px-2 py-1 text-center mx-1 bg-indigo-50/60 rounded-t font-bold text-[#4338CA] transition"
                                               placeholder="...">
                                    @endif
                                @endforeach
                            </div>

                        @elseif ($question->type === 'timer_challenge')
                            @php $timeLimit = $question->meta['time_limit_seconds'] ?? 15; @endphp
                            <div x-data="{
                                    picked: {{ $existing['option_id'] ?? 'null' }},
                                    questionIndex: {{ $i }},
                                    timeLimit: {{ $timeLimit }},
                                    secondsLeft: {{ $timeLimit }},
                                    timerId: null,
                                    active: false,
                                    startedAt: null,
                                    isActiveNow() {
                                        return displayMode === 'all_at_once' || currentIndex === this.questionIndex;
                                    },
                                    startTimer() {
                                        if (this.picked !== null || this.active) return;
                                        this.active = true;
                                        this.secondsLeft = this.timeLimit;
                                        this.startedAt = Date.now();
                                        this.timerId = setInterval(() => {
                                            this.secondsLeft = Math.max(0, this.timeLimit - Math.floor((Date.now() - this.startedAt) / 1000));
                                            if (this.secondsLeft <= 0) {
                                                clearInterval(this.timerId);
                                                this.timerId = null;
                                                this.autoSkip();
                                            }
                                        }, 250);
                                    },
                                    stopTimer() {
                                        if (this.timerId) { clearInterval(this.timerId); this.timerId = null; }
                                        this.active = false;
                                    },
                                    pick(optionId) {
                                        if (this.picked !== null) return;
                                        this.stopTimer();
                                        const timeTaken = Math.min(this.timeLimit, Math.round((Date.now() - (this.startedAt || Date.now())) / 1000));
                                        this.picked = optionId;
                                        saveAnswer({{ $question->id }}, { option_id: optionId, time_taken_seconds: timeTaken });
                                    },
                                    autoSkip() {
                                        if (this.picked !== null) return;
                                        this.stopTimer();
                                        this.picked = -1;
                                        saveAnswer({{ $question->id }}, { option_id: null, time_taken_seconds: this.timeLimit });
                                        if (displayMode === 'one_by_one' && currentIndex === this.questionIndex && currentIndex < totalQuestions - 1) {
                                            setTimeout(() => currentIndex++, 400);
                                        }
                                    },
                                }"
                               x-init="
                            $watch(() => isActiveNow(), (isNowActive) => {
                                if (isNowActive) { startTimer(); } else { stopTimer(); }
                            });
                            if (isActiveNow()) startTimer();
                        ">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs text-red-500 font-semibold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Jawab sebelum waktu habis
                                    </span>
                                    <span class="relative font-mono font-extrabold text-base px-3.5 py-1.5 rounded-full overflow-hidden shadow-sm"
                                          :class="secondsLeft <= 5 ? 'bg-gradient-to-r from-[#FEE2E2] to-[#FECACA] text-[#DC2626]' : 'bg-gradient-to-r from-[#EEF2FF] to-[#E0E7FF] text-[#4338CA]'"
                                          x-text="active || picked !== null ? secondsLeft + 's' : timeLimit + 's'"></span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden mb-4">
                                    <div class="h-full rounded-full transition-all duration-200 ease-linear"
                                         :class="secondsLeft <= 5 ? 'bg-gradient-to-r from-[#EF4444] to-[#F87171]' : 'bg-gradient-to-r from-[#4F46E5] to-[#818CF8]'"
                                         :style="`width: ${(secondsLeft / timeLimit) * 100}%`"></div>
                                </div>
                                <div class="space-y-2" :class="picked !== null ? 'pointer-events-none' : ''">
                                    @foreach ($question->options as $option)
                                        <label class="flex items-center gap-3 border-2 rounded-xl px-4 py-3 cursor-pointer transition-all duration-200"
                                               :class="picked === {{ $option->id }} ? 'border-[#4F46E5] bg-gradient-to-r from-indigo-50 to-white shadow-md shadow-indigo-100 scale-[1.01]' : (picked !== null ? 'border-gray-100 opacity-40' : 'border-gray-100 bg-white hover:border-indigo-200 hover:bg-indigo-50/30')"
                                               @click="pick({{ $option->id }})">
                                            <span class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                                                  :class="picked === {{ $option->id }} ? 'bg-[#4F46E5] border-[#4F46E5]' : 'border-gray-300'">
                                                <svg x-show="picked === {{ $option->id }}" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                            <span class="text-sm text-gray-700 font-medium">{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-xs text-[#059669] mt-3 flex items-center gap-1 font-semibold" x-show="picked !== null">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Jawaban terkunci & tersimpan.
                                </p>
                            </div>

                        @elseif ($question->type === 'drag_drop')
                            @php
                                $pairs = $question->meta['pairs'] ?? [];
                                $existingMatches = $existing['matches'] ?? [];
                                $rightItemsShuffled = collect($pairs)->values()->map(fn($p, $i) => ['index' => $i, 'text' => $p['right']])->shuffle()->values();
                            @endphp
                            <div x-data="{
                                    matches: @js((object) $existingMatches),
                                    rightItems: @js($rightItemsShuffled),
                                    dragging: null,
                                    dragStart(rightIndex) { this.dragging = rightIndex; },
                                    dropOn(leftIndex) {
                                        if (this.dragging === null) return;
                                        this.matches[leftIndex] = this.dragging;
                                        this.dragging = null;
                                        this.emit();
                                    },
                                    clear(leftIndex) {
                                        delete this.matches[leftIndex];
                                        this.emit();
                                    },
                                    usedRightIndexes() { return Object.values(this.matches); },
                                    rightTextFor(rightIndex) {
                                        const item = this.rightItems.find(r => r.index === rightIndex);
                                        return item ? item.text : '';
                                    },
                                    emit() { saveAnswer({{ $question->id }}, { matches: this.matches }); }
                                }"
                                class="space-y-4">
                                <p class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 flex items-center gap-1.5 font-medium">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.5-1.5M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.5 1.5"/></svg>
                                    Seret kartu definisi di bawah ke istilah yang cocok
                                </p>

                                <div class="space-y-2.5">
                                    @foreach ($pairs as $leftIndex => $pair)
                                        <div class="flex items-center gap-3">
                                            <span class="w-1/3 text-sm font-bold text-gray-700 shrink-0">{{ $pair['left'] }}</span>
                                            <div class="flex-1 min-h-[46px] border-2 border-dashed rounded-xl px-3 py-2.5 flex items-center justify-between transition-all duration-200"
                                                 :class="matches[{{ $leftIndex }}] !== undefined ? 'border-[#10B981] bg-gradient-to-r from-emerald-50 to-white shadow-sm' : 'border-gray-300 hover:border-emerald-300 hover:bg-emerald-50/30'"
                                                 @dragover.prevent
                                                 @drop.prevent="dropOn({{ $leftIndex }})">
                                                <span class="text-sm text-gray-700 font-medium" x-text="matches[{{ $leftIndex }}] !== undefined ? rightTextFor(matches[{{ $leftIndex }}]) : ''"></span>
                                                <button type="button" x-show="matches[{{ $leftIndex }}] !== undefined" @click="clear({{ $leftIndex }})" class="text-[#EF4444] hover:text-[#DC2626] text-xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex flex-wrap gap-2 pt-3 border-t border-emerald-100">
                                    <template x-for="item in rightItems" :key="item.index">
                                        <div x-show="!usedRightIndexes().includes(item.index)"
                                             draggable="true"
                                             @dragstart="dragStart(item.index)"
                                             class="bg-gradient-to-br from-emerald-50 to-white border-2 border-emerald-100 rounded-lg px-3.5 py-2.5 text-sm text-[#059669] font-semibold cursor-move hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                                             x-text="item.text"></div>
                                    </template>
                                </div>
                            </div>

                        @elseif ($question->type === 'word_search')
                            @php
                                $wsWords = collect($question->meta['words'] ?? [])->values();
                                $existingFound = $existing['found_words'] ?? [];
                            @endphp
                            <div x-data="{
                                    words: @js($wsWords),
                                    found: @js($existingFound),
                                    grid: [],
                                    gridSize: 0,
                                    selecting: false,
                                    selStart: null,
                                    selCells: [],
                                    foundCells: {},
                                    seedRng(seed) {
                                        return function () {
                                            seed |= 0; seed = (seed + 0x6D2B79F5) | 0;
                                            let t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
                                            t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
                                            return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
                                        };
                                    },
                                    hashSeed(str) {
                                        let h = 0;
                                        for (let i = 0; i < str.length; i++) { h = (Math.imul(31, h) + str.charCodeAt(i)) | 0; }
                                        return h;
                                    },
                                    buildGrid() {
                                        const rng = this.seedRng(this.hashSeed('{{ $question->id }}-' + this.words.join(',')));
                                        const longest = this.words.reduce((m, w) => Math.max(m, w.length), 0);
                                        this.gridSize = Math.max(10, longest + 1);
                                        const size = this.gridSize;
                                        const cells = Array.from({ length: size }, () => Array(size).fill(null));
                                        const dirs = [[0, 1], [1, 0], [1, 1], [1, -1]];

                                        this.words.forEach(word => {
                                            let placed = false;
                                            for (let attempt = 0; attempt < 200 && !placed; attempt++) {
                                                const dir = dirs[Math.floor(rng() * dirs.length)];
                                                const row = Math.floor(rng() * size);
                                                const col = Math.floor(rng() * size);
                                                const endRow = row + dir[0] * (word.length - 1);
                                                const endCol = col + dir[1] * (word.length - 1);
                                                if (endRow < 0 || endRow >= size || endCol < 0 || endCol >= size) continue;

                                                let fits = true;
                                                for (let i = 0; i < word.length; i++) {
                                                    const r = row + dir[0] * i, c = col + dir[1] * i;
                                                    const existingLetter = cells[r][c];
                                                    if (existingLetter !== null && existingLetter !== word[i]) { fits = false; break; }
                                                }
                                                if (!fits) continue;

                                                for (let i = 0; i < word.length; i++) {
                                                    const r = row + dir[0] * i, c = col + dir[1] * i;
                                                    cells[r][c] = word[i];
                                                }
                                                placed = true;
                                            }
                                        });

                                        const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                        for (let r = 0; r < size; r++) {
                                            for (let c = 0; c < size; c++) {
                                                if (cells[r][c] === null) {
                                                    cells[r][c] = alphabet[Math.floor(rng() * alphabet.length)];
                                                }
                                            }
                                        }
                                        this.grid = cells;
                                    },
                                    cellKey(r, c) { return r + '_' + c; },
                                    isSelected(r, c) { return this.selCells.some(cell => cell.r === r && cell.c === c); },
                                    isFound(r, c) { return !!this.foundCells[this.cellKey(r, c)]; },
                                    cellFromPoint(x, y) {
                                        const el = document.elementFromPoint(x, y);
                                        if (!el || !el.dataset || el.dataset.r === undefined) return null;
                                        return { r: parseInt(el.dataset.r), c: parseInt(el.dataset.c) };
                                    },
                                    startSelect(r, c) {
                                        this.selecting = true;
                                        this.selStart = { r, c };
                                        this.selCells = [{ r, c }];
                                    },
                                    extendSelect(r, c) {
                                        if (!this.selecting || !this.selStart) return;
                                        const dr = r - this.selStart.r, dc = c - this.selStart.c;
                                        if (!(dr === 0 || dc === 0 || Math.abs(dr) === Math.abs(dc))) return;
                                        const steps = Math.max(Math.abs(dr), Math.abs(dc));
                                        const stepR = steps === 0 ? 0 : dr / steps;
                                        const stepC = steps === 0 ? 0 : dc / steps;
                                        const path = [];
                                        for (let i = 0; i <= steps; i++) {
                                            path.push({ r: this.selStart.r + stepR * i, c: this.selStart.c + stepC * i });
                                        }
                                        this.selCells = path;
                                    },
                                    endSelect() {
                                        if (!this.selecting) return;
                                        this.selecting = false;
                                        const str = this.selCells.map(cell => this.grid[cell.r][cell.c]).join('');
                                        const reversed = str.split('').reverse().join('');
                                        const match = this.words.find(w => (w === str || w === reversed) && !this.found.includes(w));
                                        if (match) {
                                            this.found.push(match);
                                            this.selCells.forEach(cell => { this.foundCells[this.cellKey(cell.r, cell.c)] = true; });
                                            saveAnswer({{ $question->id }}, { found_words: this.found });
                                        }
                                        this.selCells = [];
                                        this.selStart = null;
                                    },
                                    touchMoveHandler(e) {
                                        const t = e.touches[0];
                                        const cell = this.cellFromPoint(t.clientX, t.clientY);
                                        if (cell) this.extendSelect(cell.r, cell.c);
                                    },
                                }"
                                x-init="buildGrid()"
                                @mouseup.window="endSelect()"
                                @touchend.window="endSelect()"
                                class="select-none">

                                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-3 flex items-center gap-1.5 font-medium">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                                    Klik &amp; seret huruf untuk menemukan kata (horizontal, vertikal, atau diagonal)
                                </p>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    <template x-for="word in words" :key="word">
                                        <span class="text-xs px-3 py-1.5 rounded-full border-2 font-semibold transition-all duration-200"
                                              :class="found.includes(word) ? 'bg-gradient-to-r from-emerald-50 to-white text-[#059669] border-[#A7F3D0] line-through scale-95' : 'bg-white text-gray-600 border-gray-200'"
                                              x-text="word"></span>
                                    </template>
                                </div>

                                <div class="inline-grid gap-1 bg-gradient-to-br from-amber-50 to-indigo-50 p-3 rounded-2xl overflow-x-auto max-w-full shadow-inner"
                                     :style="`grid-template-columns: repeat(${gridSize}, minmax(0, 1fr));`">
                                    <template x-for="(row, r) in grid" :key="r">
                                        <template x-for="(letter, c) in row" :key="c">
                                            <div :data-r="r" :data-c="c"
                                                 class="w-7 h-7 flex items-center justify-center text-xs font-extrabold rounded-md cursor-pointer select-none transition-all duration-150"
                                                 :class="isFound(r, c) ? 'bg-gradient-to-br from-[#10B981] to-[#34D399] text-white scale-105 shadow-sm' : (isSelected(r, c) ? 'bg-gradient-to-br from-[#4F46E5] to-[#818CF8] text-white scale-110 shadow-md' : 'bg-white text-gray-700 hover:bg-indigo-50')"
                                                 @mousedown.prevent="startSelect(r, c)"
                                                 @mouseenter="if (selecting) extendSelect(r, c)"
                                                 @touchstart.prevent="startSelect(r, c)"
                                                 @touchmove.prevent="touchMoveHandler($event)"
                                                 x-text="letter"></div>
                                        </template>
                                    </template>
                                </div>

                                <p class="text-xs text-[#059669] mt-3 flex items-center gap-1.5 font-bold" x-show="found.length === words.length && words.length > 0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    🎉 Semua kata ditemukan!
                                </p>
                            </div>

                        @elseif ($question->type === 'crossword')
                            @php
                                $cwEntries = collect($question->meta['entries'] ?? [])->values();
                                $existingEntries = $existing['entries'] ?? [];
                            @endphp
                            <div x-data="{
                                    entries: @js($cwEntries),
                                    existingEntries: @js($existingEntries),
                                    rows: 0, cols: 0, grid: [], placements: [], numbering: {}, board: {},
                                    seedRng(seed) {
                                        return function () {
                                            seed |= 0; seed = (seed + 0x6D2B79F5) | 0;
                                            let t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
                                            t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
                                            return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
                                        };
                                    },
                                    hashSeed(str) {
                                        let h = 0;
                                        for (let i = 0; i < str.length; i++) { h = (Math.imul(31, h) + str.charCodeAt(i)) | 0; }
                                        return h;
                                    },
                                    buildCrossword() {
                                        const words = this.entries.map((e, i) => ({ index: i, word: (e.word || '').toUpperCase() })).filter(w => w.word.length > 0);
                                        const order = [...words].sort((a, b) => b.word.length - a.word.length);
                                        const placements = [];
                                        const occupied = {};
                                        const key = (r, c) => r + '_' + c;

                                        const canPlace = (word, row, col, dir) => {
                                            for (let i = 0; i < word.length; i++) {
                                                const r = dir === 1 ? row + i : row;
                                                const c = dir === 0 ? col + i : col;
                                                const existing = occupied[key(r, c)];
                                                if (existing !== undefined && existing !== word[i]) return false;
                                            }
                                            return true;
                                        };
                                        const place = (item, row, col, dir) => {
                                            for (let i = 0; i < item.word.length; i++) {
                                                const r = dir === 1 ? row + i : row;
                                                const c = dir === 0 ? col + i : col;
                                                occupied[key(r, c)] = item.word[i];
                                            }
                                            placements.push({ index: item.index, row, col, dir, length: item.word.length });
                                        };

                                        if (order.length === 0) { this.rows = 0; this.cols = 0; this.grid = []; return; }
                                        place(order[0], 0, 0, 0);

                                        for (let oi = 1; oi < order.length; oi++) {
                                            const item = order[oi];
                                            let bestPlacement = null;
                                            for (let li = 0; li < item.word.length && !bestPlacement; li++) {
                                                const letter = item.word[li];
                                                for (const cellKey in occupied) {
                                                    if (occupied[cellKey] !== letter) continue;
                                                    const [er, ec] = cellKey.split('_').map(Number);
                                                    const rowV = er - li, colV = ec;
                                                    if (canPlace(item.word, rowV, colV, 1)) { bestPlacement = { row: rowV, col: colV, dir: 1 }; break; }
                                                    const rowH = er, colH = ec - li;
                                                    if (canPlace(item.word, rowH, colH, 0)) { bestPlacement = { row: rowH, col: colH, dir: 0 }; break; }
                                                }
                                            }
                                            if (bestPlacement) {
                                                place(item, bestPlacement.row, bestPlacement.col, bestPlacement.dir);
                                            } else {
                                                const rowsUsed = Object.keys(occupied).map(k => parseInt(k.split('_')[0]));
                                                const maxRow = rowsUsed.length ? Math.max(...rowsUsed) : 0;
                                                place(item, maxRow + 2, 0, 0);
                                            }
                                        }

                                        const allR = placements.flatMap(p => Array.from({ length: p.length }, (_, i) => p.dir === 1 ? p.row + i : p.row));
                                        const allC = placements.flatMap(p => Array.from({ length: p.length }, (_, i) => p.dir === 0 ? p.col + i : p.col));
                                        const minR = Math.min(...allR), minC = Math.min(...allC);
                                        placements.forEach(p => { p.row -= minR; p.col -= minC; });
                                        const rows = Math.max(...allR) - minR + 1;
                                        const cols = Math.max(...allC) - minC + 1;

                                        const grid = Array.from({ length: rows }, () => Array(cols).fill(null));
                                        placements.forEach(p => {
                                            const word = order.find(o => o.index === p.index).word;
                                            for (let i = 0; i < word.length; i++) {
                                                const r = p.dir === 1 ? p.row + i : p.row;
                                                const c = p.dir === 0 ? p.col + i : p.col;
                                                grid[r][c] = word[i];
                                            }
                                        });

                                        const sorted = [...placements].sort((a, b) => a.row - b.row || a.col - b.col);
                                        const numbering = {};
                                        let nextNumber = 1;
                                        sorted.forEach(p => {
                                            const k = key(p.row, p.col);
                                            if (!(k in numbering)) { numbering[k] = nextNumber++; }
                                            p.number = numbering[k];
                                        });

                                        this.rows = rows;
                                        this.cols = cols;
                                        this.grid = grid;
                                        this.placements = placements;
                                        this.numbering = numbering;
                                    },
                                    initBoard() {
                                        this.board = {};
                                        this.placements.forEach(p => {
                                            const val = (this.existingEntries[p.index] || '').toUpperCase();
                                            for (let i = 0; i < p.length; i++) {
                                                const r = p.dir === 1 ? p.row + i : p.row;
                                                const c = p.dir === 0 ? p.col + i : p.col;
                                                if (val[i]) this.board[r + '_' + c] = val[i];
                                            }
                                        });
                                    },
                                    isActive(r, c) { return this.grid[r] && this.grid[r][c] !== null; },
                                    numberAt(r, c) { return this.numbering[r + '_' + c] || null; },
                                    cellValue(r, c) { return this.board[r + '_' + c] || ''; },
                                    acrossPlacements() { return this.placements.filter(p => p.dir === 0).sort((a, b) => a.number - b.number); },
                                    downPlacements() { return this.placements.filter(p => p.dir === 1).sort((a, b) => a.number - b.number); },
                                    clueFor(index) { return this.entries[index] ? this.entries[index].clue : ''; },
                                    onCellInput(e, r, c) {
                                        const val = e.target.value.replace(/[^A-Za-z]/g, '').toUpperCase().slice(-1);
                                        this.board[r + '_' + c] = val;
                                        e.target.value = val;
                                        this.emit();
                                        if (val) {
                                            const nextEl = document.querySelector(`[data-cwq='{{ $question->id }}'][data-r='${r}'][data-c='${c + 1}']`)
                                                || document.querySelector(`[data-cwq='{{ $question->id }}'][data-r='${r + 1}'][data-c='${c}']`);
                                            if (nextEl) nextEl.focus();
                                        }
                                    },
                                    emit() {
                                        const entriesOut = {};
                                        this.placements.forEach(p => {
                                            let str = '';
                                            for (let i = 0; i < p.length; i++) {
                                                const r = p.dir === 1 ? p.row + i : p.row;
                                                const c = p.dir === 0 ? p.col + i : p.col;
                                                str += this.board[r + '_' + c] || '';
                                            }
                                            entriesOut[p.index] = str;
                                        });
                                        saveAnswer({{ $question->id }}, { entries: entriesOut });
                                    },
                                }"
                                x-init="buildCrossword(); initBoard();"
                                class="select-none">

                                <p class="text-xs text-violet-700 bg-violet-50 border border-violet-100 rounded-lg px-3 py-2 mb-3 flex items-center gap-1.5 font-medium">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.293-6.293a1 1 0 011.414 0l2.586 2.586a1 1 0 010 1.414L13 15l-4 1 1-4z"/></svg>
                                    Isi setiap kotak dengan satu huruf sesuai petunjuk Mendatar &amp; Menurun
                                </p>

                                <div class="inline-grid gap-0.5 bg-gradient-to-br from-violet-100 to-indigo-100 p-2.5 rounded-2xl overflow-x-auto max-w-full mb-4 shadow-inner"
                                     :style="`grid-template-columns: repeat(${cols}, minmax(0, 1fr));`">
                                    <template x-for="(row, r) in grid" :key="r">
                                        <template x-for="(letter, c) in row" :key="c">
                                            <div class="relative w-8 h-8">
                                                <template x-if="isActive(r, c)">
                                                    <div>
                                                        <span x-show="numberAt(r, c)" x-text="numberAt(r, c)"
                                                              class="absolute top-0 left-0.5 text-[8px] leading-none text-violet-400 font-bold z-10"></span>
                                                        <input type="text" maxlength="1"
                                                               :data-r="r" :data-c="c" data-cwq="{{ $question->id }}"
                                                               :value="cellValue(r, c)"
                                                               @input="onCellInput($event, r, c)"
                                                               class="w-8 h-8 text-center text-sm font-extrabold border-2 border-violet-200 rounded-md uppercase focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-[#7C3AED] focus:z-10 relative bg-white text-[#6D28D9]">
                                                    </div>
                                                </template>
                                                <template x-if="!isActive(r, c)">
                                                    <div class="w-8 h-8 bg-transparent"></div>
                                                </template>
                                            </div>
                                        </template>
                                    </template>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div class="bg-gradient-to-br from-violet-50 to-white rounded-xl p-3.5 border border-violet-100">
                                        <p class="font-bold text-[#6D28D9] text-xs mb-2 flex items-center gap-1">➡️ Mendatar</p>
                                        <ul class="space-y-1.5">
                                            <template x-for="p in acrossPlacements()" :key="'a' + p.index">
                                                <li class="text-gray-600 text-xs"><span class="font-bold text-[#6D28D9]" x-text="p.number"></span>. <span x-text="clueFor(p.index)"></span></li>
                                            </template>
                                        </ul>
                                    </div>
                                    <div class="bg-gradient-to-br from-violet-50 to-white rounded-xl p-3.5 border border-violet-100">
                                        <p class="font-bold text-[#6D28D9] text-xs mb-2 flex items-center gap-1">⬇️ Menurun</p>
                                        <ul class="space-y-1.5">
                                            <template x-for="p in downPlacements()" :key="'d' + p.index">
                                                <li class="text-gray-600 text-xs"><span class="font-bold text-[#6D28D9]" x-text="p.number"></span>. <span x-text="clueFor(p.index)"></span></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        @else
                            <p class="text-sm text-gray-400 italic">Tipe soal ini belum didukung di halaman pengerjaan.</p>
                        @endif

                        <p class="text-[11px] text-gray-300 mt-3 flex items-center gap-1" x-show="savingStatus[{{ $question->id }}]">
                            <span x-text="savingStatus[{{ $question->id }}]"></span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Navigasi mode satu-per-satu --}}
        <div class="flex items-center justify-between mt-6" x-show="displayMode === 'one_by_one'" x-cloak>
            <span class="text-xs text-gray-400 shrink-0 w-16 md:w-20 font-medium" x-text="`Soal ${currentIndex + 1} / {{ $questions->count() }}`"></span>

            <button type="button"
                    x-show="currentIndex < totalQuestions - 1"
                    @click="currentIndex++"
                    class="inline-flex items-center gap-1.5 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 transition-all shrink-0">
                Soal Berikutnya
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button type="button"
                    x-show="currentIndex === totalQuestions - 1"
                    @click="confirmSubmit()"
                    class="inline-flex items-center gap-1.5 bg-gradient-to-r from-[#10B981] to-[#34D399] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-emerald-300/50 hover:-translate-y-0.5 transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Selesai &amp; Kumpulkan
            </button>
        </div>

        {{-- Keterangan soal terkunci — baris sendiri, bukan ikut flex justify-between --}}
        <p class="text-xs text-gray-400 text-right mt-1.5" x-show="displayMode === 'one_by_one' && currentIndex > 0" x-cloak>
            (Soal sebelumnya tidak bisa diubah lagi)
        </p>

        {{-- Tombol submit mode semua sekaligus --}}
        <div class="mt-6" x-show="displayMode === 'all_at_once'" x-cloak>
            <button type="button" @click="confirmSubmit()"
                    class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#10B981] to-[#34D399] text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-emerald-300/50 hover:-translate-y-0.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Selesai &amp; Kumpulkan Semua Jawaban
            </button>
        </div>
    </form>
</div>

<script>
function quizAttempt(config) {
    return {
        displayMode: config.displayMode,
        deadline: new Date(config.deadline),
        totalQuestions: config.totalQuestions,
        saveUrl: config.saveUrl,
        tabSwitchUrl: config.tabSwitchUrl,
        submitUrl: config.submitUrl,
        currentIndex: 0,
        timeLeft: 0,
        totalSeconds: 0,
        timePercent: 100,
        formattedTime: '00:00',
        tabSwitchCount: 0,
        savingStatus: {},
        submitted: false,

        init() {
            this.tick();
            if (!this.totalSeconds) this.totalSeconds = this.timeLeft || 1;
            setInterval(() => this.tick(), 1000);
        },

        tick() {
            const now = new Date();
            const diff = Math.max(0, Math.floor((this.deadline - now) / 1000));
            this.timeLeft = diff;
            const m = String(Math.floor(diff / 60)).padStart(2, '0');
            const s = String(diff % 60).padStart(2, '0');
            this.formattedTime = `${m}:${s}`;
            this.timePercent = this.totalSeconds ? Math.max(0, Math.min(100, (diff / this.totalSeconds) * 100)) : 100;

            if (diff <= 0 && !this.submitted) {
                this.submitted = true;
                this.$refs.quizForm.submit();
            }
        },

        answeredCount() {
            return Object.keys(this.savingStatus).filter(k => (this.savingStatus[k] || '').includes('Tersimpan')).length;
        },

        async saveAnswer(questionId, answerData) {
            this.savingStatus[questionId] = 'Menyimpan...';
            try {
                const res = await fetch(this.saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            ?? document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ question_id: questionId, answer_data: answerData }),
                });
                const data = await res.json();
                this.savingStatus[questionId] = data.status === 'saved' ? '✓ Tersimpan' : 'Gagal menyimpan';

                if (data.status === 'closed') {
                    window.location.reload();
                }
            } catch (e) {
                this.savingStatus[questionId] = 'Gagal menyimpan (periksa koneksi)';
            }
        },

        async onVisibilityChange() {
            if (document.hidden && !this.submitted) {
                try {
                    const res = await fetch(this.tabSwitchUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    this.tabSwitchCount = data.count ?? this.tabSwitchCount;

                    if (data.status === 'auto_submitted') {
                        this.submitted = true;
                        window.location.href = this.submitUrl.replace('/submit', '/result');
                    }
                } catch (e) {
                    // diamkan kalau gagal, jangan blokir siswa
                }
            }
        },

        confirmSubmit() {
            if (confirm('Yakin sudah selesai? Jawaban tidak bisa diubah lagi setelah dikumpulkan.')) {
                this.submitted = true;
                this.$refs.quizForm.submit();
            }
        },

        onSubmitForm(e) {
            // Form submit native ke route submit, tidak perlu preventDefault
        },
    }
}
</script>
@endsection