{{-- resources/views/siswa/assignments/show.blade.php --}}
@extends('layouts.app')
@section('title', $assignment->title)

@section('content')
<div class="max-w-2xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-24 md:pb-10">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative min-w-0">
            <a href="{{ route('siswa.classes.assignments.index', $class) }}"
               class="inline-flex items-center gap-1.5 text-xs md:text-sm text-indigo-100 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Tugas
            </a>

            <h1 class="text-lg md:text-2xl font-bold mt-3 truncate">{{ $assignment->title }}</h1>
            <p class="text-indigo-100 text-xs md:text-sm flex items-center gap-1.5 flex-wrap mt-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $assignment->due_date->translatedFormat('D, d M Y HH:mm') }}
                <span class="text-indigo-300">&middot;</span>
                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l2.9 6.26L21.6 9l-4.8 4.4L18.2 21 12 17.4 5.8 21l1.4-7.6L2.4 9l6.7-.74L12 2z"/>
                </svg>
                Nilai maks {{ $assignment->max_score }}
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-2 bg-[#ECFDF5] border border-[#A7F3D0] text-[#059669] text-sm rounded-xl md:rounded-2xl px-4 py-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Status pengumpulan / revisi / nilai --}}
    @if ($submission)
        @if ($submission->revision_status === 'requested')
            <div class="relative overflow-hidden bg-[#FFFBEB] border border-[#FDE68A] rounded-2xl md:rounded-3xl p-4 md:p-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#FEF3C7] flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-[#B45309]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-[#92400E]">Guru meminta revisi</p>
                        @if ($submission->feedback)
                            <p class="text-sm text-[#92400E]/90 mt-1">{{ $submission->feedback }}</p>
                        @endif
                        <div class="flex items-center gap-2 mt-2.5 flex-wrap">
                            @if ($submission->revision_deadline)
                                <span class="text-[11px] md:text-xs bg-[#FEF3C7] text-[#92400E] font-medium px-2.5 py-1 rounded-full">
                                    Batas revisi: {{ $submission->revision_deadline->translatedFormat('d M Y, HH:mm') }} ({{ $submission->revision_deadline->diffForHumans() }})
                                </span>
                            @endif
                            <span class="text-[11px] md:text-xs bg-[#FEF3C7] text-[#92400E] font-medium px-2.5 py-1 rounded-full">
                                Revisi ke-{{ $submission->revision_count + 1 }} dari 3
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($submission->isGraded())
            <div class="relative overflow-hidden bg-gradient-to-br from-[#4F46E5] to-[#3730A3] rounded-2xl md:rounded-3xl p-4 md:p-5 text-white shadow-md shadow-indigo-300/40">
                <div class="absolute -top-6 -right-6 w-28 h-28 bg-white/10 rounded-full blur-xl"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-white/15 backdrop-blur flex flex-col items-center justify-center shrink-0">
                        <span class="text-xl md:text-2xl font-extrabold leading-none">{{ $submission->score }}</span>
                        <span class="text-[10px] text-white/70">/ {{ $assignment->max_score }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                            Sudah dinilai
                        </p>
                        @if ($submission->feedback)
                            <p class="text-sm text-white/85 mt-1">{{ $submission->feedback }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($submission->isSubmitted())
            @php $isLate = $submission->timing_status === 'late'; @endphp
            <div class="border rounded-2xl md:rounded-3xl px-4 md:px-5 py-3.5 md:py-4 flex items-center gap-3"
                 style="background-color: {{ $isLate ? '#FEF2F2' : '#ECFDF5' }}; border-color: {{ $isLate ? '#FECACA' : '#A7F3D0' }};">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background-color: {{ $isLate ? '#FEE2E2' : '#D1FAE5' }};">
                    @if ($isLate)
                        <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-4 h-4 text-[#059669]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </div>
                <p class="text-sm font-medium" style="color: {{ $isLate ? '#B91C1C' : '#047857' }};">
                    Sudah dikumpulkan {{ $isLate ? '(terlambat)' : '(tepat waktu)' }}
                    &middot; {{ $submission->submitted_at->diffForHumans() }}
                </p>
            </div>
        @endif
    @endif

    {{-- Instruksi --}}
    <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-4 md:p-5 shadow-sm shadow-indigo-100/40">
        <h2 class="text-xs md:text-sm font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Instruksi
        </h2>
        <div class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $assignment->instructions ?: 'Tidak ada instruksi tambahan.' }}</div>

        @if ($assignment->attachments)
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-1.5">
                <p class="text-xs font-semibold text-gray-400 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    Lampiran dari guru
                </p>
                @foreach ($assignment->attachments as $file)
                    <a href="{{ Storage::url($file['path']) }}" target="_blank"
                       class="flex items-center gap-2 text-sm text-[#4338CA] hover:text-[#3730A3] bg-indigo-50/60 hover:bg-indigo-100 rounded-lg px-3 py-2 transition group">
                        <svg class="w-4 h-4 text-[#4F46E5] group-hover:text-[#3730A3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        {{ $file['original_name'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Form pengumpulan --}}
    <form method="POST" action="{{ route('siswa.classes.assignments.submit', [$class, $assignment]) }}"
          enctype="multipart/form-data" class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-4 md:p-5 space-y-4 shadow-sm shadow-indigo-100/40">
        @csrf

        @if ($errors->any())
            <div class="bg-[#FEF2F2] border border-[#FECACA] text-[#B91C1C] text-sm rounded-lg md:rounded-xl p-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-xs md:text-sm font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.293-6.293a1 1 0 011.414 0l2.586 2.586a1 1 0 010 1.414L13 15l-4 1 1-4z"/>
                </svg>
                {{ $submission?->isSubmitted() ? 'Edit pengumpulan' : 'Kumpulkan tugas' }}
            </h2>
            @if ($assignment->isPastDue())
                <span class="inline-flex items-center gap-1 text-[11px] md:text-xs bg-[#FEF2F2] text-[#DC2626] font-semibold px-2.5 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    Lewat batas waktu — akan ditandai terlambat
                </span>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jawaban teks</label>
            <textarea name="text_answer" rows="6"
                      class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-[#4F46E5]"
                      placeholder="Tulis jawabanmu di sini...">{{ old('text_answer', $submission?->text_answer) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Lampiran file</label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-indigo-300 transition">
                <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                       class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-[#4338CA] hover:file:bg-indigo-100 file:font-medium file:text-sm">
                <p class="text-xs text-gray-400 mt-1.5">Maks 5 file, total 20MB — PDF, Word, JPG, PNG, atau ZIP</p>
            </div>
            @if ($submission?->attachments)
                <div class="mt-2 space-y-1">
                    @foreach ($submission->attachments as $file)
                        <p class="text-xs text-gray-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            {{ $file['original_name'] }} <span class="text-gray-400">(saat ini tersimpan)</span>
                        </p>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-1">Upload file baru akan menggantikan semua file di atas.</p>
            @endif
        </div>

        <button type="submit"
                class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 transition-all font-semibold text-sm">
            @if ($submission?->isSubmitted())
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-6 4l3 3m0 0l3-3m-3 3V3"/></svg>
                Simpan perubahan
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 19.5l15-15m0 0h-9m9 0v9"/></svg>
                Kumpulkan tugas
            @endif
        </button>
    </form>
</div>
@endsection