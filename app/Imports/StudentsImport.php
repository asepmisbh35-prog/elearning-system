<?php

namespace App\Imports;

use App\Models\Rombel;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];
    public int $imported = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // baris Excel mulai dari 2 (1 = header)

            // Skip baris kosong
            if (empty($row['nisn']) && empty($row['nama'])) continue;

            // Validasi kolom wajib
            if (empty($row['nisn'])) {
                $this->errors[] = "Baris {$rowNum}: NISN kosong.";
                continue;
            }
            if (empty($row['nama'])) {
                $this->errors[] = "Baris {$rowNum}: Nama kosong.";
                continue;
            }

            // Cek duplikat NISN
            if (Student::where('nisn', (string) $row['nisn'])->exists()) {
                $this->errors[] = "Baris {$rowNum}: NISN {$row['nisn']} sudah terdaftar, dilewati.";
                continue;
            }

            // Simpan data siswa (belum punya akun user)
            Student::create([
                'nisn'          => (string) $row['nisn'],
                'nama_lengkap'  => $row['nama'],
                'rombel'        => $row['rombel'] ?? null,
                'is_registered' => false,
            ]);

            $this->imported++;
        }
    }
}
