<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Rombel;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        // ── Rombel ──────────────────────────────────────────
        $rombel = Rombel::firstOrCreate(
            ['name' => '10A', 'academic_year' => '2024/2025'],
            ['grade' => '10', 'is_active' => true]
        );

        // ── Admin (pakai email yang sama dengan AdminSeeder) ──
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name'              => 'Administrator',
                'password'          => 'password123', // cast 'hashed' di model yang akan hash otomatis
                'role'              => 'admin',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        Admin::firstOrCreate(['user_id' => $adminUser->id]);

        // ── Guru ─────────────────────────────────────────────
        $guruUser = User::firstOrCreate(
            ['email' => 'guru@elearning.test'],
            [
                'name'              => 'Budi Santoso',
                'password'          => 'password123',
                'role'              => 'guru',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        Teacher::firstOrCreate(
            ['user_id' => $guruUser->id],
            ['nip' => '198501012010011001', 'subject' => 'Matematika']
        );

        // ── Data Siswa belum registrasi ───────────────────────
        $dataSiswa = [
            ['nisn' => '0012345678', 'nama_lengkap' => 'Andi Pratama'],
            ['nisn' => '0012345679', 'nama_lengkap' => 'Siti Nurhaliza'],
            ['nisn' => '0012345680', 'nama_lengkap' => 'Rizky Maulana'],
        ];

        foreach ($dataSiswa as $data) {
            Student::firstOrCreate(
                ['nisn' => $data['nisn']],
                [
                    'rombel'        => '10A',
                    'nama_lengkap'  => $data['nama_lengkap'],
                    'is_registered' => false,
                ]
            );
        }

        // ── Siswa sudah registrasi ────────────────────────────
        $siswaUser = User::firstOrCreate(
            ['email' => 'siswa@elearning.test'],
            [
                'name'              => 'Dewi Rahayu',
                'password'          => 'password123',
                'role'              => 'siswa',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        Student::firstOrCreate(
            ['nisn' => '0012345681'],
            [
                'user_id'       => $siswaUser->id,
                'rombel'        => '10A',
                'nama_lengkap'  => 'Dewi Rahayu',
                'birth_date'    => '2008-05-15',
                'is_registered' => true,
            ]
        );
    }
}
