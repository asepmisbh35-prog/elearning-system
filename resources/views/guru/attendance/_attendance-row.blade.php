{{-- resources/views/guru/attendance/_attendance-row.blade.php --}}
@php
    // Token warna resmi — bukan lagi green-100/yellow-100 generik Tailwind
    $statusStyle = [
        'hadir' => ['bg' => '#D1FAE5', 'text' => '#047857', 'ring' => '#10B981'],
        'izin'  => ['bg' => '#DBEAFE', 'text' => '#1E40AF', 'ring' => '#2563EB'],
        'sakit' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'ring' => '#F59E0B'],
        'alfa'  => ['bg' => '#FEE2E2', 'text' => '#B91C1C', 'ring' => '#EF4444'],
    ][$attendance->status];

    $namaSiswa = $attendance->student->user->name ?? $attendance->student->nama_lengkap ?? '-';
@endphp
<div class="px-4 sm:px-5 py-2.5 sm:py-3 border-b border-gray-50 last:border-0 flex items-center justify-between gap-3 sm:gap-4 flex-wrap sm:flex-nowrap transition-colors hover:bg-indigo-50/40">
    <div class="flex items-center gap-3 min-w-0">
        <div class="relative shrink-0">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gray-100 flex items-center justify-center text-xs sm:text-sm font-medium text-gray-600 border-2"
                 style="border-color:{{ $statusStyle['ring'] }}66">
                {{ substr($namaSiswa, 0, 1) }}
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white" style="background:{{ $statusStyle['ring'] }}"></span>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate">{{ $namaSiswa }}</p>
            @if ($attendance->checked_in_at)
                <p class="text-xs text-gray-400">Check-in: {{ $attendance->checked_in_at->format('H:i') }}</p>
            @endif
            @if ($attendance->keterangan)
                <p class="text-xs text-gray-400 italic truncate max-w-[180px] sm:max-w-xs">"{{ $attendance->keterangan }}"</p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        @if ($attendance->approval_status === 'pending')
            <span class="flex items-center gap-1.5 text-[11px] sm:text-xs px-2 py-0.5 rounded-full font-medium"
                  style="background:#FEF3C7; color:#92400E">
                <span class="relative flex w-1.5 h-1.5">
                    <span class="absolute inline-flex w-full h-full rounded-full opacity-60 animate-ping" style="background:#F59E0B"></span>
                    <span class="relative inline-flex w-1.5 h-1.5 rounded-full" style="background:#F59E0B"></span>
                </span>
                Menunggu Persetujuan
            </span>
        @endif

        @if ($closed)
            <form method="POST" action="{{ route('guru.attendances.status.update', $attendance) }}">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()"
                        class="text-xs sm:text-sm px-2.5 py-1 rounded-full font-medium border-0 cursor-pointer transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                        style="background:{{ $statusStyle['bg'] }}; color:{{ $statusStyle['text'] }}">
                    <option value="hadir" {{ $attendance->status === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ $attendance->status === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ $attendance->status === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alfa" {{ $attendance->status === 'alfa' ? 'selected' : '' }}>Alfa</option>
                </select>
            </form>
        @else
            <span class="text-xs sm:text-sm px-2.5 py-1 rounded-full font-medium"
                  style="background:{{ $statusStyle['bg'] }}; color:{{ $statusStyle['text'] }}">
                {{ ucfirst($attendance->status) }}
            </span>
        @endif
    </div>
</div>