{{-- resources/views/guru/grades/export-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        p.sub { color: #666; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: center; }
        th { background: #f3f4f6; }
        td:first-child, th:first-child { text-align: left; }
        .final { font-weight: bold; background: #ecfdf5; }
    </style>
</head>
<body>
    <h1>Rekap Nilai — {{ $class->name }}</h1>
    <p class="sub">{{ $class->subject }} &middot; KKM: {{ $kkm }} &middot; Dicetak: {{ now()->translatedFormat('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama Siswa</th>
                @foreach ($components as $component)
                    <th>{{ $component->name }}<br>({{ $component->weight }}%)</th>
                @endforeach
                <th>Nilai Akhir</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['student']->user->name ?? $row['student']->nama_lengkap ?? '-' }}</td>
                    @foreach ($components as $component)
                        <td>{{ $row['scores'][$component->id] ?? '-' }}</td>
                    @endforeach
                    <td class="final">{{ $row['final'] ?? '-' }}</td>
                    <td>{{ ! is_null($row['final']) ? \App\Models\KkmSetting::predikat($row['final']) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>