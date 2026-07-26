{{-- resources/views/siswa/attendance/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Absensi — ' . $schoolClass->name)

@section('content')
<div class="max-w-2xl mx-auto px-1 sm:px-4 py-6 md:py-8 space-y-4 md:space-y-6">

    {{-- Header --}}
    <div>
        <a href="{{ route('siswa.classes.show', $schoolClass) }}"
           class="inline-flex items-center gap-1.5 text-xs md:text-sm text-gray-400 hover:text-[#4F46E5] transition">
            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke kelas
        </a>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mt-2 flex items-center gap-2">✅ Absensi</h1>
        <p class="text-sm md:text-base text-gray-500">{{ $schoolClass->name }}</p>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs md:text-sm rounded-xl px-3 md:px-4 py-2.5 md:py-3 flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-[#EF4444]/10 border border-[#EF4444]/30 text-[#7F1D1D] text-xs md:text-sm rounded-xl p-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Persentase kehadiran --}}
    @php
        $circumference = 2 * 3.14159265 * 54;
        $offset = $circumference - ($percentage / 100) * $circumference;
        $statusLabel = $percentage >= 90 ? 'Sangat Baik' : ($percentage >= 75 ? 'Baik' : 'Perlu Perhatian');
        $statusIcon = $percentage >= 90 ? '🌟' : ($percentage >= 75 ? '👍' : '⚠️');
    @endphp
    <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-6 md:p-8 text-white shadow-lg">
        {{-- motif lingkaran solid semi-transparan --}}
        <div class="absolute -top-12 -right-12 w-44 h-44 md:w-52 md:h-52 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 -left-10 w-40 h-40 md:w-48 md:h-48 rounded-full bg-white/10"></div>
        <div class="absolute top-1/4 right-1/4 w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-5 md:gap-8">
            {{-- Donut progress ring --}}
            <div class="relative w-32 h-32 md:w-36 md:h-36 shrink-0">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="54" fill="none" stroke="white" stroke-opacity="0.15" stroke-width="10"/>
                    <circle cx="60" cy="60" r="54" fill="none" stroke="white" stroke-width="10" stroke-linecap="round"
                            stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                            class="transition-all duration-700 ease-out"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl md:text-4xl font-extrabold">{{ $percentage }}%</span>
                    <span class="text-[10px] md:text-xs text-white/70 -mt-1">hadir</span>
                </div>
            </div>

            {{-- Info status --}}
            <div class="text-center sm:text-left">
                <p class="text-xs md:text-sm text-white/70">Persentase Kehadiran</p>
                <p class="text-lg md:text-xl font-bold mt-0.5 flex items-center gap-1.5 justify-center sm:justify-start">
                    <span>{{ $statusIcon }}</span> {{ $statusLabel }}
                </p>
                <p class="text-[11px] md:text-xs text-white/70 mt-1.5 max-w-xs">
                    @if ($percentage >= 90) Kehadiran kamu keren, terus dipertahankan!
                    @elseif ($percentage >= 75) Kehadiran cukup baik, tetap konsisten ya.
                    @else Yuk lebih rutin hadir di kelas biar makin optimal.
                    @endif
                </p>
                <div class="mt-3 inline-flex items-center gap-1.5 bg-white/15 backdrop-blur text-[11px] md:text-xs font-medium px-3 py-1 rounded-full">
                    📊 Target minimal 75%
                </div>
            </div>
        </div>
    </div>

    {{-- Sesi aktif — form check-in --}}
    @if ($activeSession)
        <div class="bg-white border border-gray-100 rounded-xl md:rounded-2xl p-4 md:p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="font-bold text-gray-800 text-sm md:text-base">Sesi Absensi Aktif</h2>
            </div>
            <p class="text-xs md:text-sm text-gray-500 mb-3 md:mb-4">
                {{ $activeSession->meeting->topic }} — batas check-in {{ $activeSession->check_in_end->format('H:i') }}
            </p>

            @if ($myAttendance && $myAttendance->status === 'hadir')
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-3 md:p-4 text-xs md:text-sm flex items-center gap-2">
                    <span class="text-base md:text-lg">✅</span> Kamu sudah check-in pukul {{ $myAttendance->checked_in_at->format('H:i') }}
                </div>
            @elseif ($myAttendance && in_array($myAttendance->status, ['izin', 'sakit']) && $myAttendance->approval_status === 'pending')
                <div class="bg-amber-50 border border-amber-200 text-amber-700 rounded-xl p-3 md:p-4 text-xs md:text-sm flex items-center gap-2">
                    <span class="text-base md:text-lg">⏳</span> Pengajuan {{ $myAttendance->status }} kamu sedang menunggu persetujuan guru.
                </div>
            @else
                <div x-data="{ mode: 'checkin' }">
                    <div class="inline-flex gap-1 mb-3 md:mb-4 bg-gray-100 rounded-xl p-1">
                        <button type="button" @click="mode = 'checkin'"
                                :class="mode === 'checkin' ? 'bg-[#4F46E5] text-white shadow-sm' : 'text-gray-500'"
                                class="px-3 md:px-4 py-1.5 rounded-lg text-xs md:text-sm font-medium transition">📲 Check-in</button>
                        <button type="button" @click="mode = 'izin'"
                                :class="mode === 'izin' ? 'bg-[#4F46E5] text-white shadow-sm' : 'text-gray-500'"
                                class="px-3 md:px-4 py-1.5 rounded-lg text-xs md:text-sm font-medium transition">📨 Ajukan Izin/Sakit</button>
                    </div>

                    {{-- Form Check-in --}}
                    <form x-show="mode === 'checkin'" method="POST" action="{{ route('siswa.classes.attendance.checkin', $schoolClass) }}"
                          class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="type" value="checkin">
                        <input type="text" name="code" maxlength="6" placeholder="Masukkan kode 6 digit" required
                               class="flex-1 border-2 border-gray-200 rounded-xl px-3 py-2.5 md:py-3 font-mono tracking-widest text-center text-base md:text-lg
                                      focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-[#4F46E5] transition-all">
                        <button type="submit"
                                class="bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:from-[#4338CA] hover:to-[#6366F1] text-white px-4 md:px-6 py-2.5 md:py-3 rounded-xl
                                       hover:shadow-lg hover:shadow-indigo-200 hover:-translate-y-0.5 transition-all font-semibold text-sm md:text-base">
                            Check-in
                        </button>
                    </form>

                    {{-- Form Izin/Sakit --}}
                    <form x-show="mode === 'izin'" x-cloak method="POST" action="{{ route('siswa.classes.attendance.checkin', $schoolClass) }}"
                          enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="attendance_session_id" value="{{ $activeSession->id }}">

                        <div>
                            <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Jenis</label>
                            <div class="flex gap-2 md:gap-3">
                                <label class="flex-1 flex items-center gap-2 cursor-pointer border-2 border-gray-200 rounded-xl px-3 py-2.5 hover:border-indigo-300 transition">
                                    <input type="radio" name="type" value="izin" required class="text-[#4F46E5] focus:ring-indigo-300"> <span class="text-xs md:text-sm">🙋 Izin</span>
                                </label>
                                <label class="flex-1 flex items-center gap-2 cursor-pointer border-2 border-gray-200 rounded-xl px-3 py-2.5 hover:border-indigo-300 transition">
                                    <input type="radio" name="type" value="sakit" required class="text-[#4F46E5] focus:ring-indigo-300"> <span class="text-xs md:text-sm">🤒 Sakit</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                            <textarea name="keterangan" rows="2" required
                                      class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-xs md:text-sm
                                             focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-[#4F46E5] transition-all"
                                      placeholder="Jelaskan alasan izin/sakit"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5">Dokumen Pendukung (opsional)</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 hover:border-indigo-300 transition">
                                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                                       class="w-full text-xs md:text-sm text-gray-600 file:mr-3 md:file:mr-4 file:py-2 file:px-3 md:file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-[#4338CA] hover:file:bg-indigo-100">
                            </div>
                            <p class="text-[11px] md:text-xs text-gray-400 mt-1.5">Surat dokter atau surat izin dari orang tua/wali.</p>
                        </div>

                        <button type="submit"
                                class="bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:from-[#4338CA] hover:to-[#6366F1] text-white px-4 md:px-6 py-2.5 rounded-xl
                                       hover:shadow-lg hover:shadow-indigo-200 hover:-translate-y-0.5 transition-all font-semibold text-xs md:text-sm">
                            📨 Kirim Pengajuan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endif

    {{-- Histori kehadiran --}}
    <div class="bg-white border border-gray-100 rounded-xl md:rounded-2xl overflow-hidden shadow-sm">
        <div class="px-4 md:px-5 py-3.5 md:py-4 border-b border-gray-100 bg-gray-50/60">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 text-sm md:text-base">🗓️ Riwayat Kehadiran</h3>
        </div>
        @forelse ($history as $attendance)
            @php
                $statusIcon = [
                    'hadir' => '✅',
                    'izin'  => '🙋',
                    'sakit' => '🤒',
                    'alfa'  => '❌',
                ][$attendance->status];
                // hadir = Success token, izin = variasi Indigo/sky, sakit = Warning token, alfa = Danger token
                $statusColor = [
                    'hadir' => 'bg-emerald-100 text-emerald-700',
                    'izin'  => 'bg-sky-100 text-sky-700',
                    'sakit' => 'bg-amber-100 text-amber-700',
                    'alfa'  => 'bg-[#EF4444]/10 text-[#EF4444]',
                ][$attendance->status];
            @endphp
            <div class="px-4 md:px-5 py-3 md:py-3.5 border-b border-gray-50 last:border-0 flex items-center justify-between hover:bg-indigo-50/40 transition">
                <div class="min-w-0">
                    <p class="text-xs md:text-sm font-medium text-gray-800 truncate">{{ $attendance->session->meeting->topic }}</p>
                    <p class="text-[11px] md:text-xs text-gray-400">{{ $attendance->created_at->translatedFormat('d F Y') }}</p>
                </div>
                <span class="text-[11px] md:text-xs px-2 md:px-2.5 py-1 rounded-full font-medium {{ $statusColor }} flex items-center gap-1 shrink-0">
                    {{ $statusIcon }} {{ ucfirst($attendance->status) }}
                </span>
            </div>
        @empty
            <div class="px-5 py-12 md:py-14 text-center text-gray-400">
                <div class="text-3xl md:text-4xl mb-2">🗂️</div>
                <p class="text-xs md:text-sm">Belum ada riwayat kehadiran.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection