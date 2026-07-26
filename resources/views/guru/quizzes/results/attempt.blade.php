{{-- resources/views/guru/quizzes/results/attempt.blade.php --}}
@extends('layouts.app')
@section('title', 'Koreksi — ' . $quiz->title)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-44 h-44 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-32 h-32 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.classes.quizzes.results.index', [$class, $quiz]) }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke rekap
            </a>

            <div class="flex items-start justify-between gap-4 mt-2">
                <div class="min-w-0">
                    <h1 class="text-lg md:text-xl font-bold truncate">
                        {{ $attempt->student->user->name ?? $attempt->student->nama_lengkap ?? '-' }}
                    </h1>
                    <p class="text-indigo-100 text-sm mt-0.5">Percobaan #{{ $attempt->attempt_number }} &middot; {{ $quiz->title }}</p>
                </div>

                <div class="relative shrink-0 flex items-center justify-center w-14 h-14">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-white/20" style="animation: soft-pulse 2.8s ease-in-out infinite;"></span>
                    <span class="relative flex flex-col items-center justify-center w-12 h-12 rounded-2xl bg-white/15 backdrop-blur">
                        <span class="text-sm font-bold leading-none">{{ $attempt->score ?? '-' }}</span>
                    </span>
                </div>
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

    <div class="space-y-3">
        @foreach ($attempt->answers as $i => $answer)
            @php
                $question = $answer->question;
                $borderColor = $answer->needs_manual_grading
                    ? 'border-l-[#F59E0B]'
                    : ($answer->is_correct === true ? 'border-l-[#10B981]' : ($answer->is_correct === false ? 'border-l-[#EF4444]' : 'border-l-gray-200'));
            @endphp
            <div class="bg-white border border-gray-200 {{ $borderColor }} border-l-4 rounded-xl md:rounded-2xl p-4 md:p-5 rise-in" style="animation-delay: {{ min($i,10) * 50 }}ms">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-[#4F46E5] font-medium">{{ $question->typeLabel() }}</span>
                    @if ($answer->needs_manual_grading)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-50 text-[#F59E0B] font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                            Perlu Koreksi
                        </span>
                    @elseif ($answer->is_correct === true)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                            Benar
                        </span>
                    @elseif ($answer->is_correct === false)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-red-50 text-[#EF4444] font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span>
                            Salah
                        </span>
                    @endif
                </div>

                <p class="text-gray-800 text-sm md:text-base mb-3">{{ $question->question_text }}</p>

                @if ($question->type === 'short_answer')
                    <div class="bg-indigo-50/60 rounded-xl p-3 mb-3">
                        <p class="text-xs text-gray-500 mb-1">Jawaban siswa:</p>
                        <p class="text-sm text-gray-800 font-medium">{{ $answer->answer_data['text'] ?? '-' }}</p>
                    </div>
                @elseif ($question->type === 'sorting')
                    @php
                        $orderIds = $answer->answer_data['order'] ?? [];
                        $itemsById = $question->options->keyBy('id');
                    @endphp
                    <div class="bg-indigo-50/60 rounded-xl p-3 mb-3">
                        <p class="text-xs text-gray-500 mb-1">Urutan jawaban siswa:</p>
                        @if (empty($orderIds))
                            <p class="text-sm text-gray-800 font-medium">(tidak dijawab)</p>
                        @else
                            <ol class="text-sm text-gray-800 list-decimal list-inside space-y-0.5">
                                @foreach ($orderIds as $id)
                                    <li>{{ $itemsById[$id]->option_text ?? '?' }}</li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                @elseif ($question->type === 'fill_blank')
                    @php $blanks = $answer->answer_data['blanks'] ?? []; @endphp
                    <div class="bg-indigo-50/60 rounded-xl p-3 mb-3">
                        <p class="text-xs text-gray-500 mb-1">Jawaban tiap blank:</p>
                        @if (collect($blanks)->filter(fn($b) => trim((string) $b) !== '')->isEmpty())
                            <p class="text-sm text-gray-800 font-medium">(tidak dijawab)</p>
                        @else
                            <ol class="text-sm text-gray-800 list-decimal list-inside space-y-0.5">
                                @foreach ($blanks as $b)
                                    <li>{{ trim((string) $b) !== '' ? $b : '(kosong)' }}</li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                @else
                    @php
                        $selectedOption = $question->options->firstWhere('id', $answer->answer_data['option_id'] ?? null);
                    @endphp
                    <div class="bg-indigo-50/60 rounded-xl p-3 mb-3">
                        <p class="text-xs text-gray-500 mb-1">Jawaban siswa:</p>
                        <p class="text-sm text-gray-800 font-medium">{{ $selectedOption->option_text ?? '(tidak dijawab)' }}</p>
                    </div>
                @endif

                @if ($answer->needs_manual_grading)
                    <div class="flex items-center gap-2 flex-wrap" x-data="{ saving: false }">
                        <form method="POST" action="{{ route('guru.classes.quizzes.results.grade-answer', [$class, $quiz, $attempt, $answer]) }}" @submit="saving = true">
                            @csrf
                            <input type="hidden" name="is_correct" value="1">
                            <button type="submit" :disabled="saving"
                                    class="inline-flex items-center gap-1.5 text-sm bg-[#10B981] text-white px-4 py-2 rounded-xl hover:bg-emerald-600 hover:-translate-y-0.5 active:scale-95 disabled:opacity-70 disabled:pointer-events-none transition-all font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Tandai Benar
                            </button>
                        </form>
                        <form method="POST" action="{{ route('guru.classes.quizzes.results.grade-answer', [$class, $quiz, $attempt, $answer]) }}" @submit="saving = true">
                            @csrf
                            <input type="hidden" name="is_correct" value="0">
                            <button type="submit" :disabled="saving"
                                    class="inline-flex items-center gap-1.5 text-sm bg-[#EF4444] text-white px-4 py-2 rounded-xl hover:bg-red-600 hover:-translate-y-0.5 active:scale-95 disabled:opacity-70 disabled:pointer-events-none transition-all font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tandai Salah
                            </button>
                        </form>
                    </div>
                @endif

                <p class="text-xs text-gray-400 mt-3 pt-2.5 border-t border-gray-100">{{ $answer->points_earned }} poin</p>
            </div>
        @endforeach
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