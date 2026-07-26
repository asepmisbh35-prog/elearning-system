{{-- resources/views/guru/quizzes/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Kuis')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8" x-data="{ saving: false }">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.classes.quizzes.index', $class) }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke daftar kuis
            </a>
            <h1 class="text-xl md:text-2xl font-bold mt-2">Edit Kuis</h1>
            <p class="text-indigo-100 text-sm md:text-base">{{ $quiz->title }}</p>
        </div>
    </div>

    @if ($hasAttempts)
        <div class="bg-amber-50 border border-amber-200 text-[#F59E0B] text-sm rounded-xl p-4 mb-4 rise-in flex gap-2.5">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <p>Sudah ada siswa yang mengerjakan kuis ini. Kamu hanya bisa mengubah <strong>jendela waktu akses</strong>.</p>
        </div>
    @endif

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

    <form method="POST" action="{{ route('guru.classes.quizzes.update', [$class, $quiz]) }}"
          @submit="saving = true"
          class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-5 md:p-6 space-y-5 rise-in" style="animation-delay: 60ms">
        @csrf @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kuis</label>
            <input type="text" name="title" value="{{ old('title', $quiz->title) }}" {{ $hasAttempts ? 'disabled' : '' }}
                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-400" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Instruksi</label>
            <textarea name="instructions" rows="3" {{ $hasAttempts ? 'disabled' : '' }}
                      class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-400">{{ old('instructions', $quiz->instructions) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="5" max="300" {{ $hasAttempts ? 'disabled' : '' }}
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-400" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Maks Percobaan</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" max="20" {{ $hasAttempts ? 'disabled' : '' }}
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition disabled:bg-gray-50 disabled:text-gray-400">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Akses Mulai</label>
                <input type="datetime-local" name="access_start_at" value="{{ old('access_start_at', $quiz->access_start_at->format('Y-m-d\TH:i')) }}"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Akses Selesai</label>
                <input type="datetime-local" name="access_end_at" value="{{ old('access_end_at', $quiz->access_end_at->format('Y-m-d\TH:i')) }}"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition" required>
            </div>
        </div>

        @if (! $hasAttempts)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode Nilai</label>
                    <select name="score_method" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
                        <option value="best" {{ old('score_method', $quiz->score_method) === 'best' ? 'selected' : '' }}>Nilai Terbaik</option>
                        <option value="last" {{ old('score_method', $quiz->score_method) === 'last' ? 'selected' : '' }}>Nilai Terakhir</option>
                        <option value="average" {{ old('score_method', $quiz->score_method) === 'average' ? 'selected' : '' }}>Rata-rata</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tampilan Soal</label>
                    <select name="display_mode" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
                        <option value="one_by_one" {{ old('display_mode', $quiz->display_mode) === 'one_by_one' ? 'selected' : '' }}>Satu per Satu</option>
                        <option value="all_at_once" {{ old('display_mode', $quiz->display_mode) === 'all_at_once' ? 'selected' : '' }}>Semua Sekaligus</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Komponen Penilaian</label>
                <select name="grade_component_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
                    <option value="">— Tidak dikaitkan —</option>
                    @foreach ($gradeComponents as $gc)
                        <option value="{{ $gc->id }}" {{ old('grade_component_id', $quiz->grade_component_id) == $gc->id ? 'selected' : '' }}>{{ $gc->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Toggle switch untuk opsi kuis --}}
            <div class="border border-gray-200 rounded-xl p-4 space-y-3.5" x-data="{
                    shuffleQ: {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'true' : 'false' }},
                    shuffleO: {{ old('shuffle_options', $quiz->shuffle_options) ? 'true' : 'false' }},
                    showReview: {{ old('show_review', $quiz->show_review) ? 'true' : 'false' }}
                 }">
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm text-gray-700">Acak urutan soal untuk tiap siswa</span>
                    <button type="button" @click="shuffleQ = !shuffleQ" :class="shuffleQ ? 'bg-[#4F46E5]' : 'bg-gray-200'" class="relative w-10 rounded-full transition-colors duration-200 shrink-0" style="height: 22px;">
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" :class="shuffleQ ? 'translate-x-4' : 'translate-x-0'"></span>
                    </button>
                    <input type="hidden" name="shuffle_questions" :value="shuffleQ ? 1 : 0">
                </label>
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm text-gray-700">Acak urutan pilihan jawaban</span>
                    <button type="button" @click="shuffleO = !shuffleO" :class="shuffleO ? 'bg-[#4F46E5]' : 'bg-gray-200'" class="relative w-10 rounded-full transition-colors duration-200 shrink-0" style="height: 22px;">
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" :class="shuffleO ? 'translate-x-4' : 'translate-x-0'"></span>
                    </button>
                    <input type="hidden" name="shuffle_options" :value="shuffleO ? 1 : 0">
                </label>
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm text-gray-700">Siswa bisa lihat pembahasan setelah selesai</span>
                    <button type="button" @click="showReview = !showReview" :class="showReview ? 'bg-[#4F46E5]' : 'bg-gray-200'" class="relative w-10 rounded-full transition-colors duration-200 shrink-0" style="height: 22px;">
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" :class="showReview ? 'translate-x-4' : 'translate-x-0'"></span>
                    </button>
                    <input type="hidden" name="show_review" :value="showReview ? 1 : 0">
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Tampil Nilai</label>
                <select name="show_score" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
                    <option value="immediately" {{ old('show_score', $quiz->show_score) === 'immediately' ? 'selected' : '' }}>Langsung setelah submit</option>
                    <option value="held" {{ old('show_score', $quiz->show_score) === 'held' ? 'selected' : '' }}>Ditahan (guru rilis manual nanti)</option>
                </select>
            </div>

            <div class="border border-gray-200 rounded-xl p-4" x-data="{ selectedCount: {{ count(old('question_ids', $selectedIds)) }} }">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Pilih Soal dari Bank Soal</label>
                    <span class="text-xs font-semibold text-[#4F46E5] bg-indigo-50 px-2 py-0.5 rounded-full" x-text="selectedCount + ' dipilih'"></span>
                </div>
                <div class="max-h-72 overflow-y-auto space-y-2 border-t border-gray-100 pt-3">
                    @foreach ($questions as $question)
                        <label class="flex items-start gap-3 p-2 hover:bg-indigo-50/40 rounded-lg cursor-pointer transition-colors duration-150">
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}"
                                   @change="selectedCount = $el.closest('div[x-data]').querySelectorAll('input[name=\'question_ids[]\']:checked').length"
                                   {{ in_array($question->id, old('question_ids', $selectedIds)) ? 'checked' : '' }}
                                   class="mt-1 rounded text-[#4F46E5] focus:ring-[#4F46E5]">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-sky-50 text-sky-600 font-medium">{{ $question->typeLabel() }}</span>
                                    <span class="text-xs text-gray-400">{{ $question->subject }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ Str::limit(strip_tags($question->question_text), 100) }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition">
                    <option value="draft" {{ old('status', $quiz->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $quiz->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 bg-[#4F46E5] text-white px-6 py-2.5 rounded-xl hover:bg-[#4338CA] hover:-translate-y-0.5 active:scale-95 transition-all font-medium disabled:opacity-70 disabled:pointer-events-none">
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
            <a href="{{ route('guru.classes.quizzes.index', $class) }}" class="text-gray-500 hover:text-gray-700 text-sm">Batal</a>
        </div>
    </form>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection