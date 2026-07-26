{{-- resources/views/guru/quizzes/questions/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Soal')

@section('content')
<div class="max-w-2xl mx-auto px-4 md:px-6 py-6 md:py-8" x-data="{ saving: false }">

    {{-- Hero card --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <a href="{{ route('guru.quizzes.questions.index') }}"
           class="relative inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition-colors duration-150 group">
            <svg class="w-4 h-4 transition-transform duration-150 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke bank soal
        </a>
        <h1 class="relative text-xl md:text-2xl font-bold mt-2">Tambah Soal Baru</h1>
        <p class="relative text-sm text-indigo-100 mt-1">Lengkapi detail soal dan pilih tipe interaksi yang sesuai.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-[#EF4444] text-sm rounded-xl p-3 mb-4 rise-in">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('guru.quizzes.questions.store') }}" enctype="multipart/form-data"
          id="questionForm" @submit="saving = true"
          class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-5 md:p-6 space-y-5 rise-in" style="animation-delay: 80ms">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-[#EF4444]">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: IPA"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition-colors duration-150" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori/Topik</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="Opsional"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition-colors duration-150">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal <span class="text-[#EF4444]">*</span></label>
            <select name="type" id="typeSelect" onchange="toggleTypeSections()"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition-colors duration-150">
                <option value="multiple_choice" @selected(old('type', 'multiple_choice') === 'multiple_choice')>Pilihan Ganda</option>
                <option value="true_false" @selected(old('type') === 'true_false')>Benar/Salah</option>
                <option value="short_answer" @selected(old('type') === 'short_answer')>Isian Singkat</option>
                <option value="true_false_swipe" @selected(old('type') === 'true_false_swipe')>True/False Swipe</option>
                <option value="sorting" @selected(old('type') === 'sorting')>Sorting/Urutan</option>
                <option value="fill_blank" @selected(old('type') === 'fill_blank')>Fill in the Blank</option>
                <option value="timer_challenge" @selected(old('type') === 'timer_challenge')>Timer Challenge</option>
                <option value="drag_drop" @selected(old('type') === 'drag_drop')>Drag & Drop (Mencocokkan)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Teks Soal <span class="text-[#EF4444]">*</span></label>
            <textarea name="question_text" id="questionText" rows="3"
                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition-colors duration-150" required>{{ old('question_text') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-[#4F46E5] file:font-medium hover:file:bg-indigo-100 file:transition-colors file:duration-150">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Poin</label>
            <input type="number" name="points" value="{{ old('points', 1) }}" min="1" max="100"
                   class="w-32 border border-gray-300 rounded-xl px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent transition-colors duration-150">
        </div>

        {{-- Pilihan Ganda --}}
        <div id="section-multiple_choice" class="border border-gray-200 rounded-xl p-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Jawaban (2-5, pilih yang benar)</label>
            <div id="optionsList" class="space-y-2">
                @php $oldOptions = old('options', ['', '']); @endphp
                @foreach ($oldOptions as $i => $val)
                    <div class="flex items-center gap-2 option-row">
                        <input type="radio" name="correct_option" value="{{ $i }}" {{ old('correct_option') == $i ? 'checked' : '' }} class="text-[#4F46E5] focus:ring-[#4F46E5]">
                        <input type="text" name="options[{{ $i }}]" value="{{ $val }}"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Pilihan {{ $i + 1 }}">
                        <button type="button" onclick="removeOption(this)" aria-label="Hapus pilihan"
                                class="text-gray-400 hover:text-[#EF4444] hover:scale-110 active:scale-95 transition-transform duration-150 p-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addOption()" class="inline-flex items-center gap-1 text-sm text-[#4F46E5] hover:text-[#4338CA] mt-2 transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah pilihan
            </button>
        </div>

        {{-- Benar/Salah --}}
        <div id="section-true_false" class="border border-gray-200 rounded-xl p-4" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban Benar</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="true_false_answer" value="true" {{ old('true_false_answer') === 'true' ? 'checked' : '' }} class="text-[#4F46E5] focus:ring-[#4F46E5]"> <span class="text-sm">Benar</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="true_false_answer" value="false" {{ old('true_false_answer') === 'false' ? 'checked' : '' }} class="text-[#4F46E5] focus:ring-[#4F46E5]"> <span class="text-sm">Salah</span>
                </label>
            </div>
        </div>

        {{-- Isian Singkat --}}
        <div id="section-short_answer" class="border border-gray-200 rounded-xl p-4" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci Jawaban (pisahkan dengan koma)</label>
            <input type="text" name="answer_keywords" value="{{ old('answer_keywords') }}"
                   placeholder="Contoh: fotosintesis, photosynthesis"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150">
            <p class="text-xs text-gray-400 mt-1">
                Case-insensitive partial match. Kosongkan kalau soal ini butuh koreksi manual guru.
            </p>
        </div>

        {{-- Sorting/Urutan --}}
        <div id="section-sorting" class="border border-gray-200 rounded-xl p-4" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-2">Urutan yang Benar (dari atas = urutan pertama)</label>
            <div id="sortingList" class="space-y-2">
                @php $oldSorting = old('sorting_items', ['', '']); @endphp
                @foreach ($oldSorting as $i => $val)
                    <div class="flex items-center gap-2 sorting-row">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-50 text-[#4338CA] text-xs font-semibold shrink-0 sorting-number">{{ $i + 1 }}</span>
                        <input type="text" name="sorting_items[{{ $i }}]" value="{{ $val }}"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Item urutan {{ $i + 1 }}">
                        <button type="button" onclick="removeSortingItem(this)" aria-label="Hapus item"
                                class="text-gray-400 hover:text-[#EF4444] hover:scale-110 active:scale-95 transition-transform duration-150 p-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addSortingItem()" class="inline-flex items-center gap-1 text-sm text-[#4F46E5] hover:text-[#4338CA] mt-2 transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah item
            </button>
        </div>

        {{-- Fill in the Blank --}}
        <div id="section-fill_blank" class="border border-gray-200 rounded-xl p-4" style="display:none;">
            <p class="text-xs text-gray-500 mb-3">
                Tulis kalimat soal di kotak "Teks Soal" di atas, pakai <code class="bg-indigo-50 text-[#4338CA] px-1 rounded">___</code> (3 garis bawah) di posisi yang harus diisi siswa. Contoh: <em>"Proses ___ terjadi di daun."</em>
            </p>
            <p id="blankCountLabel" class="text-xs font-medium text-[#4F46E5] mb-2">0 blank terdeteksi</p>
            <div id="blankKeywordsList"></div>
        </div>

        {{-- Timer Challenge --}}
        <div id="section-timer_challenge" class="border border-gray-200 rounded-xl p-4" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu (detik)</label>
            <input type="number" name="time_limit_seconds" value="{{ old('time_limit_seconds', 15) }}" min="5" max="300"
                   class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bonus Poin Maks (%)</label>
            <input type="number" name="bonus_max_percent" value="{{ old('bonus_max_percent', 50) }}" min="0" max="100"
                   class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150">
            <p class="text-xs text-gray-400 mt-1">Semakin cepat siswa jawab benar, semakin besar bonus (maks. % ini dari poin soal). Pakai bagian "Pilihan Jawaban" di atas untuk opsi PG-nya.</p>
        </div>

        {{-- Drag & Drop --}}
        <div id="section-drag_drop" class="border border-gray-200 rounded-xl p-4" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pasangan yang Benar (istilah ↔ definisi)</label>
            <div id="pairsList" class="space-y-2">
                @php $oldLeft = old('pairs_left', ['', '']); $oldRight = old('pairs_right', ['', '']); @endphp
                @foreach ($oldLeft as $i => $val)
                    <div class="flex items-center gap-2 pair-row">
                        <input type="text" name="pairs_left[{{ $i }}]" value="{{ $val }}"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Istilah {{ $i + 1 }}">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h18M16.5 3L21 7.5m0 0L16.5 12M21 7.5H3" />
                        </svg>
                        <input type="text" name="pairs_right[{{ $i }}]" value="{{ $oldRight[$i] ?? '' }}"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Definisi {{ $i + 1 }}">
                        <button type="button" onclick="removePair(this)" aria-label="Hapus pasangan"
                                class="text-gray-400 hover:text-[#EF4444] hover:scale-110 active:scale-95 transition-transform duration-150 p-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addPair()" class="inline-flex items-center gap-1 text-sm text-[#4F46E5] hover:text-[#4338CA] mt-2 transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah pasangan
            </button>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 bg-[#4F46E5] text-white px-6 py-2.5 rounded-xl hover:bg-[#4338CA] disabled:opacity-70 disabled:cursor-not-allowed transition-colors duration-150 font-medium text-sm md:text-base">
                <svg x-show="saving" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Soal'"></span>
            </button>
            <a href="{{ route('guru.quizzes.questions.index') }}" class="text-gray-500 hover:text-gray-700 text-sm transition-colors duration-150">Batal</a>
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

<script>
    const sectionMap = {
        multiple_choice:   'multiple_choice',
        timer_challenge:   'multiple_choice',
        true_false:        'true_false',
        true_false_swipe:  'true_false',
        short_answer:      'short_answer',
        sorting:           'sorting',
        fill_blank:        'fill_blank',
        drag_drop:         'drag_drop',
    };

    function toggleTypeSections() {
        const type = document.getElementById('typeSelect').value;
        const activeSection = sectionMap[type];
        ['multiple_choice', 'true_false', 'short_answer', 'sorting', 'fill_blank', 'drag_drop'].forEach(t => {
            const section = document.getElementById('section-' + t);
            const isActive = (t === activeSection);
            section.style.display = isActive ? 'block' : 'none';
            section.querySelectorAll('input, textarea, select').forEach(el => {
                el.disabled = !isActive;
            });
        });

        const timerSection = document.getElementById('section-timer_challenge');
        const isTimer = (type === 'timer_challenge');
        timerSection.style.display = isTimer ? 'block' : 'none';
        timerSection.querySelectorAll('input').forEach(el => el.disabled = !isTimer);

        if (type === 'fill_blank') updateBlankRows();
    }

    function addOption() {
        const list = document.getElementById('optionsList');
        const count = list.querySelectorAll('.option-row').length;
        if (count >= 5) return;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 option-row rise-in';
        row.innerHTML = `
            <input type="radio" name="correct_option" value="${count}" class="text-[#4F46E5] focus:ring-[#4F46E5]">
            <input type="text" name="options[${count}]" class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Pilihan ${count + 1}">
            <button type="button" onclick="removeOption(this)" aria-label="Hapus pilihan" class="text-gray-400 hover:text-[#EF4444] hover:scale-110 active:scale-95 transition-transform duration-150 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;
        list.appendChild(row);
    }

    function addSortingItem() {
        const list = document.getElementById('sortingList');
        const count = list.querySelectorAll('.sorting-row').length;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 sorting-row rise-in';
        row.innerHTML = `
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-50 text-[#4338CA] text-xs font-semibold shrink-0 sorting-number">${count + 1}</span>
            <input type="text" name="sorting_items[${count}]" class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Item urutan ${count + 1}">
            <button type="button" onclick="removeSortingItem(this)" aria-label="Hapus item" class="text-gray-400 hover:text-[#EF4444] hover:scale-110 active:scale-95 transition-transform duration-150 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;
        list.appendChild(row);
    }

    function removeSortingItem(btn) {
        const list = document.getElementById('sortingList');
        if (list.querySelectorAll('.sorting-row').length <= 2) return;
        btn.closest('.sorting-row').remove();
        list.querySelectorAll('.sorting-row').forEach((row, i) => {
            row.querySelector('.sorting-number').textContent = `${i + 1}`;
            row.querySelector('input[type="text"]').setAttribute('name', `sorting_items[${i}]`);
            row.querySelector('input[type="text"]').setAttribute('placeholder', `Item urutan ${i + 1}`);
        });
    }

    function removeOption(btn) {
        const list = document.getElementById('optionsList');
        if (list.querySelectorAll('.option-row').length <= 2) return;
        btn.closest('.option-row').remove();
        list.querySelectorAll('.option-row').forEach((row, i) => {
            row.querySelector('input[type="radio"]').value = i;
            row.querySelector('input[type="text"]').setAttribute('name', `options[${i}]`);
            row.querySelector('input[type="text"]').setAttribute('placeholder', `Pilihan ${i + 1}`);
        });
    }

    function updateBlankRows() {
        const text = document.getElementById('questionText').value;
        const count = (text.match(/___/g) || []).length;
        const container = document.getElementById('blankKeywordsList');
        const existingValues = Array.from(container.querySelectorAll('.blank-keyword-input')).map(i => i.value);

        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 mb-2';
            row.innerHTML = `
                <span class="text-xs text-gray-400 w-16 shrink-0">Blank ${i + 1}</span>
                <input type="text" name="blank_keywords[${i}]" class="blank-keyword-input flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Jawaban benar, pisah koma" value="${existingValues[i] || ''}">
            `;
            container.appendChild(row);
        }
        document.getElementById('blankCountLabel').textContent = count + ' blank terdeteksi';
    }

    document.getElementById('questionText').addEventListener('input', () => {
        if (document.getElementById('typeSelect').value === 'fill_blank') updateBlankRows();
    });

    document.addEventListener('DOMContentLoaded', toggleTypeSections);

    function addPair() {
        const list = document.getElementById('pairsList');
        const count = list.querySelectorAll('.pair-row').length;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 pair-row rise-in';
        row.innerHTML = `
            <input type="text" name="pairs_left[${count}]" class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Istilah ${count + 1}">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h18M16.5 3L21 7.5m0 0L16.5 12M21 7.5H3" /></svg>
            <input type="text" name="pairs_right[${count}]" class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] transition-colors duration-150" placeholder="Definisi ${count + 1}">
            <button type="button" onclick="removePair(this)" aria-label="Hapus pasangan" class="text-gray-400 hover:text-[#EF4444] hover:scale-110 active:scale-95 transition-transform duration-150 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;
        list.appendChild(row);
    }

    function removePair(btn) {
        const list = document.getElementById('pairsList');
        if (list.querySelectorAll('.pair-row').length <= 2) return;
        btn.closest('.pair-row').remove();
        list.querySelectorAll('.pair-row').forEach((row, i) => {
            row.querySelector('input[name^="pairs_left"]').setAttribute('name', `pairs_left[${i}]`);
            row.querySelector('input[name^="pairs_right"]').setAttribute('name', `pairs_right[${i}]`);
        });
    }
</script>
@endsection