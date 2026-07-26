{{-- resources/views/guru/assignments/grade.blade.php --}}
@extends('layouts.app')
@section('title', 'Nilai — ' . $assignment->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-0 space-y-5 md:space-y-6 pb-10">

    {{-- HERO HEADER — sesuai Panduan UI §4 --}}
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white">
        <div class="absolute -top-10 -right-10 w-40 h-40 md:w-56 md:h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-44 h-44 md:w-64 md:h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-28 h-28 md:w-40 md:h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex items-start justify-between gap-4 flex-wrap">
            <div class="min-w-0">
                <a href="{{ route('guru.classes.assignments.index', $class) }}"
                   class="inline-flex items-center gap-1.5 text-xs md:text-sm text-indigo-100 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke daftar tugas
                </a>
                <h1 class="text-lg md:text-xl font-bold mt-2 truncate">{{ $assignment->title }}</h1>
                <p class="text-indigo-100 text-xs md:text-sm flex items-center gap-1.5 flex-wrap mt-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $assignment->due_date->translatedFormat('D, d M Y H:i') }}
                    <span class="text-indigo-300">&middot;</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.9 6.26L21.6 9l-4.8 4.4L18.2 21 12 17.4 5.8 21l1.4-7.6L2.4 9l6.7-.74L12 2z"/></svg>
                    Nilai maks {{ $assignment->max_score }}
                </p>
            </div>

            <div class="shrink-0">
                @if ($zipReady)
                    <a href="{{ route('guru.classes.assignments.grade.zip.download', [$class, $assignment]) }}"
                       class="inline-flex items-center gap-1.5 bg-white text-[#059669] px-4 py-2.5 rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                        Download ZIP
                    </a>
                @else
                    <form method="POST" action="{{ route('guru.classes.assignments.grade.zip.generate', [$class, $assignment]) }}">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur text-white px-4 py-2.5 rounded-xl hover:bg-white/25 transition text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Generate ZIP
                        </button>
                    </form>
                @endif
            </div>
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
    @if ($errors->any())
        <div class="flex items-start gap-2 bg-[#FEF2F2] border border-[#FECACA] text-[#B91C1C] text-sm rounded-xl md:rounded-2xl p-4">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <ul class="list-disc list-inside space-y-1 flex-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-3">
        @foreach ($rows as $row)
            @php
                $student = $row['student'];
                $sub = $row['submission'];
                $initial = strtoupper(substr($student->user->name ?? $student->nama_lengkap ?? '?', 0, 1));
            @endphp
            <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-4 md:p-5 shadow-sm shadow-indigo-100/40" x-data="{ open: false }">
                <div class="flex items-center justify-between gap-3 md:gap-4 cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 md:w-10 md:h-10 shrink-0 rounded-full bg-gradient-to-br from-[#4F46E5] to-[#818CF8] flex items-center justify-center text-sm font-semibold text-white">
                            {{ $initial }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 text-sm md:text-base truncate">{{ $student->user->name ?? $student->nama_lengkap ?? '-' }}</p>
                            @if ($sub && $sub->isSubmitted())
                                <p class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Dikumpulkan: {{ $sub->submitted_at->translatedFormat('d M Y H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
                        @if (! $sub || ! $sub->isSubmitted())
                            <span class="text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 font-medium whitespace-nowrap">Belum Mengumpulkan</span>
                        @else
                            @php $isLate = $sub->timing_status === 'late'; @endphp
                            <span class="text-[11px] md:text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap"
                                  style="background-color: {{ $isLate ? '#FEF2F2' : '#EEF2FF' }}; color: {{ $isLate ? '#DC2626' : '#4338CA' }};">
                                {{ $isLate ? 'Terlambat' : 'Tepat Waktu' }}
                            </span>
                            @if ($sub->isGraded())
                                <span class="text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-[#ECFDF5] text-[#059669] font-semibold whitespace-nowrap">
                                    {{ $sub->score }}/{{ $assignment->max_score }}
                                </span>
                            @endif
                            @if ($sub->revision_status === 'requested')
                                <span class="text-[11px] md:text-xs px-2.5 py-1 rounded-full bg-[#FFFBEB] text-[#92400E] font-medium whitespace-nowrap">Revisi Diminta</span>
                            @endif
                        @endif
                        <svg class="w-4 h-4 text-gray-400 transition shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                @if ($sub && $sub->isSubmitted())
                    <div x-show="open" x-cloak x-transition class="mt-4 pt-4 border-t border-gray-100 space-y-4">
                        @if ($sub->text_answer)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1.5">Jawaban teks:</p>
                                <p class="text-sm text-gray-700 bg-gray-50 rounded-xl p-3">{{ $sub->text_answer }}</p>
                            </div>
                        @endif

                        @if ($sub->attachments)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1.5">File:</p>
                                <div class="space-y-1">
                                    @foreach ($sub->attachments as $file)
                                        <a href="{{ Storage::url($file['path']) }}" target="_blank"
                                           class="flex items-center gap-2 text-sm text-[#4338CA] hover:text-[#3730A3] bg-indigo-50/60 hover:bg-indigo-100 rounded-lg px-3 py-2 transition w-fit">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            {{ $file['original_name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('guru.classes.assignments.grade.store', [$class, $assignment, $sub]) }}"
                              class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                            @csrf
                            <div class="w-full sm:w-24">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Nilai</label>
                                <input type="number" name="score" value="{{ $sub->score }}" min="0" max="{{ $assignment->max_score }}"
                                       class="w-full border-2 border-gray-200 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Komentar</label>
                                <input type="text" name="feedback" value="{{ $sub->feedback }}"
                                       class="w-full border-2 border-gray-200 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-[#4F46E5] transition">
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-1.5 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-indigo-300/50 transition-all text-sm font-semibold shrink-0">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan
                            </button>
                        </form>

                        @if ($sub->revision_status === 'requested')
                            <div class="bg-[#FFFBEB] border border-[#FDE68A] rounded-xl p-3.5 md:p-4">
                                <p class="text-sm text-[#92400E] mb-2.5 flex items-start gap-1.5">
                                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Revisi diminta — batas waktu: {{ $sub->revision_deadline->translatedFormat('D, d M Y H:i') }} ({{ $sub->revision_count }}/3 revisi terpakai)</span>
                                </p>
                                <form method="POST" action="{{ route('guru.classes.assignments.grade.revision-deadline.update', [$class, $assignment, $sub]) }}"
                                      class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                    @csrf @method('PATCH')
                                    <input type="datetime-local" name="revision_deadline"
                                           value="{{ $sub->revision_deadline->format('Y-m-d\TH:i') }}"
                                           class="border-2 border-[#FDE68A] bg-white rounded-lg px-2.5 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-100 transition">
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-1.5 text-sm text-[#92400E] bg-[#FEF3C7] hover:bg-[#FDE68A] font-medium px-3 py-1.5 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Perbarui Batas Waktu
                                    </button>
                                </form>
                            </div>
                        @elseif ($sub->revision_count < 3)
                            <form method="POST" action="{{ route('guru.classes.assignments.grade.revise', [$class, $assignment, $sub]) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 text-sm text-[#92400E] bg-[#FFFBEB] hover:bg-[#FEF3C7] font-medium px-3 py-1.5 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Minta Revisi ({{ $sub->revision_count }}/3 terpakai)
                                </button>
                            </form>
                        @else
                            <p class="text-xs text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                Batas maksimal 3x revisi sudah tercapai.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection