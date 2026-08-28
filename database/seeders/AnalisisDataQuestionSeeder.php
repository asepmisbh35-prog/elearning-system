<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: 30 soal "Analisis Data, Data Terbuka, Terpercaya, dan Legal"
 * Setiap soal asli (PG) di-generate ulang menjadi 3 tipe soal:
 *  - multiple_choice   (soal asli, 4 opsi)
 *  - true_false        (pernyataan benar/salah)
 *  - true_false_swipe  (pernyataan gaya swipe, benar/salah)
 *
 * Cara pakai:
 * 1. Salin file ini ke database/seeders/AnalisisDataQuestionSeeder.php
 * 2. Jalankan: php artisan db:seed --class=Database\\Seeders\\AnalisisDataQuestionSeeder
 *    (atau daftarkan di DatabaseSeeder.php lalu `php artisan db:seed`)
 */
class AnalisisDataQuestionSeeder extends Seeder
{
    // Sesuaikan jika perlu
    protected int $teacherId = 1;
    protected string $subject = 'Informatika';
    protected ?string $category = 'Analisis Data';
    protected ?string $batchName = 'Bab 2 - Data Terbuka, Terpercaya, Legal';

    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->questions() as $q) {
                $this->insertMultipleChoice($q);
                $this->insertTrueFalse($q);
                $this->insertTrueFalseSwipe($q);
            }
        });

        $this->command?->info('Selesai: 30 soal x 3 tipe (PG, True/False, True/False Swipe) berhasil ditambahkan.');
    }

    protected function insertMultipleChoice(array $q): void
    {
        $questionId = DB::table('questions')->insertGetId([
            'teacher_id'      => $this->teacherId,
            'subject'         => $this->subject,
            'category'        => $this->category,
            'batch_name'      => $this->batchName,
            'type'            => 'multiple_choice',
            'question_text'   => $q['stem'],
            'image_path'      => null,
            'points'          => 1,
            'answer_keywords' => null,
            'meta'            => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $order = 1;
        foreach ($q['options'] as $letter => $text) {
            DB::table('question_options')->insert([
                'question_id' => $questionId,
                'option_text' => $text,
                'is_correct'  => $letter === $q['correct'] ? 1 : 0,
                'order'       => $order++,
                'meta'        => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    protected function insertTrueFalse(array $q): void
    {
        $questionId = DB::table('questions')->insertGetId([
            'teacher_id'      => $this->teacherId,
            'subject'         => $this->subject,
            'category'        => $this->category,
            'batch_name'      => $this->batchName,
            'type'            => 'true_false',
            'question_text'   => $q['tf_statement'],
            'image_path'      => null,
            'points'          => 1,
            'answer_keywords' => null,
            'meta'            => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->insertBenarSalahOptions($questionId, $q['tf_answer']);
    }

    protected function insertTrueFalseSwipe(array $q): void
    {
        $questionId = DB::table('questions')->insertGetId([
            'teacher_id'      => $this->teacherId,
            'subject'         => $this->subject,
            'category'        => $this->category,
            'batch_name'      => $this->batchName,
            'type'            => 'true_false_swipe',
            'question_text'   => $q['swipe_statement'],
            'image_path'      => null,
            'points'          => 1,
            'answer_keywords' => null,
            'meta'            => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->insertBenarSalahOptions($questionId, $q['swipe_answer']);
    }

    protected function insertBenarSalahOptions(int $questionId, bool $isTrue): void
    {
        DB::table('question_options')->insert([
            [
                'question_id' => $questionId,
                'option_text' => 'Benar',
                'is_correct'  => $isTrue ? 1 : 0,
                'order'       => 1,
                'meta'        => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'question_id' => $questionId,
                'option_text' => 'Salah',
                'is_correct'  => $isTrue ? 0 : 1,
                'order'       => 2,
                'meta'        => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    /**
     * 30 soal sumber, masing-masing dengan:
     * - stem + options + correct (untuk multiple_choice)
     * - tf_statement + tf_answer (untuk true_false)
     * - swipe_statement + swipe_answer (untuk true_false_swipe)
     */
    protected function questions(): array
    {
        return [
            [
                'stem' => 'Dalam kehidupan sehari-hari, kita sering menemukan data dalam berbagai bentuk, seperti data nilai siswa, data cuaca, data penjualan, maupun data penduduk. Agar data tersebut menjadi informasi yang bermanfaat, diperlukan suatu proses yang sistematis mulai dari mengumpulkan, mengolah, mengorganisasi, hingga menyajikan data. Berdasarkan pernyataan tersebut, yang dimaksud dengan analisis data adalah ....',
                'options' => [
                    'A' => 'Proses menyimpan data ke dalam komputer agar tidak hilang',
                    'B' => 'Proses mencari, mengumpulkan, mengolah, menginterpretasikan, dan menyajikan data menjadi informasi yang bermanfaat',
                    'C' => 'Proses menghapus data yang tidak diperlukan',
                    'D' => 'Proses memindahkan data dari satu perangkat ke perangkat lain',
                ],
                'correct' => 'B',
                'tf_statement' => 'Analisis data adalah proses mencari, mengumpulkan, mengolah, menginterpretasikan, dan menyajikan data menjadi informasi yang bermanfaat.',
                'tf_answer' => true,
                'swipe_statement' => 'Analisis data hanya berarti menyimpan data ke dalam komputer agar tidak hilang.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Seorang kepala sekolah ingin mengetahui penyebab menurunnya nilai rata-rata siswa pada mata pelajaran Informatika. Ia kemudian mengumpulkan nilai seluruh siswa, mengelompokkannya, membuat grafik, dan menarik kesimpulan dari data tersebut. Tujuan utama kegiatan yang dilakukan kepala sekolah tersebut adalah ....',
                'options' => [
                    'A' => 'Memperbanyak jumlah data',
                    'B' => 'Memperoleh pemahaman yang lebih baik terhadap data sehingga dapat digunakan sebagai dasar pengambilan keputusan',
                    'C' => 'Mengurangi jumlah siswa',
                    'D' => 'Mengubah data menjadi dokumen',
                ],
                'correct' => 'B',
                'tf_statement' => 'Tujuan kepala sekolah mengumpulkan, mengelompokkan, dan membuat grafik nilai siswa adalah memperoleh pemahaman yang lebih baik untuk pengambilan keputusan.',
                'tf_answer' => true,
                'swipe_statement' => 'Tujuan kepala sekolah menganalisis nilai siswa adalah untuk mengurangi jumlah siswa di sekolah.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Setelah melakukan analisis terhadap data penjualan selama satu tahun, seorang pemilik toko dapat mengetahui produk yang paling banyak diminati pelanggan sehingga ia dapat menentukan jumlah stok yang harus disediakan. Manfaat analisis data berdasarkan ilustrasi tersebut adalah ....',
                'options' => [
                    'A' => 'Menghilangkan seluruh data penjualan',
                    'B' => 'Membantu pengambilan keputusan berdasarkan informasi yang diperoleh',
                    'C' => 'Memperbesar ukuran penyimpanan data',
                    'D' => 'Mengurangi jumlah transaksi',
                ],
                'correct' => 'B',
                'tf_statement' => 'Pemilik toko yang menganalisis data penjualan setahun bisa mengetahui produk yang diminati sehingga membantu pengambilan keputusan stok.',
                'tf_answer' => true,
                'swipe_statement' => 'Manfaat analisis data penjualan bagi pemilik toko adalah menghilangkan seluruh data penjualan yang ada.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Rina akan bepergian ke luar kota. Sebelum berangkat, ia membuka aplikasi cuaca di telepon genggamnya untuk mengetahui apakah di daerah tujuan akan turun hujan atau tidak. Informasi yang ditampilkan aplikasi tersebut merupakan hasil dari proses analisis data. Contoh tersebut menunjukkan bahwa hasil analisis data dapat dimanfaatkan untuk ....',
                'options' => [
                    'A' => 'Bermain media sosial',
                    'B' => 'Membantu seseorang mengambil keputusan dalam kehidupan sehari-hari',
                    'C' => 'Mempercepat koneksi internet',
                    'D' => 'Membuat perangkat keras baru',
                ],
                'correct' => 'B',
                'tf_statement' => 'Aplikasi cuaca yang dibuka Rina sebelum bepergian adalah contoh pemanfaatan hasil analisis data untuk membantu pengambilan keputusan sehari-hari.',
                'tf_answer' => true,
                'swipe_statement' => 'Informasi cuaca yang muncul di aplikasi ponsel tidak ada hubungannya sama sekali dengan proses analisis data.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Hasil akhir dari suatu proses analisis data dapat berupa laporan, grafik, tabel, maupun pola tertentu yang nantinya digunakan untuk mendukung kegiatan berikutnya. Berdasarkan pernyataan tersebut, hasil analisis data pada umumnya digunakan sebagai ....',
                'options' => [
                    'A' => 'Perangkat lunak',
                    'B' => 'Informasi yang berguna dalam pengambilan keputusan',
                    'C' => 'Alat penyimpanan data',
                    'D' => 'Jaringan komputer',
                ],
                'correct' => 'B',
                'tf_statement' => 'Hasil analisis data seperti laporan, grafik, tabel, atau pola digunakan sebagai informasi yang berguna dalam pengambilan keputusan.',
                'tf_answer' => true,
                'swipe_statement' => 'Hasil analisis data pada umumnya digunakan sebagai perangkat lunak baru untuk komputer.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Pemerintah menyediakan berbagai informasi yang dapat digunakan kembali oleh masyarakat, misalnya data cuaca, data gempa bumi, dan data statistik. Data tersebut dapat dimanfaatkan oleh siapa saja selama tetap mengikuti ketentuan yang berlaku. Jenis data tersebut disebut ....',
                'options' => [
                    'A' => 'Data pribadi',
                    'B' => 'Data rahasia',
                    'C' => 'Data terbuka',
                    'D' => 'Data ilegal',
                ],
                'correct' => 'C',
                'tf_statement' => 'Data cuaca, gempa bumi, dan statistik yang disediakan pemerintah untuk digunakan kembali masyarakat sesuai ketentuan disebut data terbuka.',
                'tf_answer' => true,
                'swipe_statement' => 'Data yang bisa dipakai ulang oleh siapa saja sesuai ketentuan yang berlaku disebut data rahasia.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Seorang mahasiswa menggunakan data curah hujan dari BMKG sebagai bahan penelitian. Dalam laporan penelitiannya, ia mencantumkan bahwa data tersebut berasal dari BMKG. Tindakan mahasiswa tersebut menunjukkan bahwa penggunaan data terbuka harus ....',
                'options' => [
                    'A' => 'Mengubah isi data',
                    'B' => 'Mencantumkan sumber data yang digunakan',
                    'C' => 'Menghapus identitas pembuat data',
                    'D' => 'Menjual kembali data tersebut',
                ],
                'correct' => 'B',
                'tf_statement' => 'Saat memakai data curah hujan dari BMKG untuk penelitian, mahasiswa wajib mencantumkan BMKG sebagai sumber datanya.',
                'tf_answer' => true,
                'swipe_statement' => 'Penggunaan data terbuka membolehkan kita menghapus identitas pembuat data tersebut.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Pemerintah menyediakan data terbuka agar masyarakat dapat ikut mengawasi penyelenggaraan pemerintahan serta memperoleh informasi yang dibutuhkan secara mudah. Tujuan utama penyediaan data terbuka adalah ....',
                'options' => [
                    'A' => 'Membatasi akses masyarakat terhadap informasi',
                    'B' => 'Meningkatkan transparansi dan akuntabilitas pemerintah',
                    'C' => 'Menyembunyikan informasi publik',
                    'D' => 'Mengurangi jumlah pengguna internet',
                ],
                'correct' => 'B',
                'tf_statement' => 'Tujuan utama pemerintah menyediakan data terbuka adalah meningkatkan transparansi dan akuntabilitas pemerintahan.',
                'tf_answer' => true,
                'swipe_statement' => 'Pemerintah menyediakan data terbuka dengan tujuan membatasi akses masyarakat terhadap informasi.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'BMKG menyediakan berbagai jenis data yang dapat diakses masyarakat melalui situs resminya, seperti data prakiraan cuaca dan data gempa bumi. Data tersebut merupakan contoh ....',
                'options' => [
                    'A' => 'Data pribadi',
                    'B' => 'Data terbuka',
                    'C' => 'Data rahasia',
                    'D' => 'Data perusahaan',
                ],
                'correct' => 'B',
                'tf_statement' => 'Data prakiraan cuaca dan data gempa bumi yang disediakan BMKG melalui situs resminya termasuk contoh data terbuka.',
                'tf_answer' => true,
                'swipe_statement' => 'Data prakiraan cuaca yang disediakan BMKG termasuk kategori data pribadi yang tidak boleh diakses masyarakat.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Seseorang ingin membuat aplikasi prakiraan cuaca dengan memanfaatkan data yang disediakan oleh BMKG. Agar penggunaan data tersebut sesuai dengan ketentuan, maka pengembang aplikasi harus ....',
                'options' => [
                    'A' => 'Menghapus nama BMKG sebagai sumber',
                    'B' => 'Mencantumkan BMKG sebagai sumber data',
                    'C' => 'Mengubah seluruh isi data',
                    'D' => 'Menjual data kepada pengguna',
                ],
                'correct' => 'B',
                'tf_statement' => 'Pengembang aplikasi cuaca yang memakai data BMKG wajib mencantumkan BMKG sebagai sumber datanya.',
                'tf_answer' => true,
                'swipe_statement' => 'Pengembang aplikasi cuaca boleh menghapus nama BMKG sebagai sumber data yang ia gunakan.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Salah satu manfaat data terbuka bagi masyarakat adalah ....',
                'options' => [
                    'A' => 'Mempersulit masyarakat memperoleh informasi',
                    'B' => 'Meningkatkan partisipasi masyarakat dalam mengawasi kinerja pemerintah',
                    'C' => 'Membatasi penelitian ilmiah',
                    'D' => 'Mengurangi transparansi informasi',
                ],
                'correct' => 'B',
                'tf_statement' => 'Salah satu manfaat data terbuka adalah meningkatkan partisipasi masyarakat dalam mengawasi kinerja pemerintah.',
                'tf_answer' => true,
                'swipe_statement' => 'Data terbuka bagi masyarakat justru bermanfaat untuk mempersulit masyarakat memperoleh informasi.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Data terpercaya merupakan data yang berasal dari sumber yang dapat dipertanggungjawabkan sehingga dapat dijadikan dasar dalam pengambilan keputusan. Berikut yang termasuk sumber data terpercaya adalah ....',
                'options' => [
                    'A' => 'Jurnal penelitian ilmiah',
                    'B' => 'Komentar anonim di media sosial',
                    'C' => 'Pesan berantai',
                    'D' => 'Unggahan tanpa sumber yang jelas',
                ],
                'correct' => 'A',
                'tf_statement' => 'Jurnal penelitian ilmiah termasuk salah satu sumber data yang terpercaya.',
                'tf_answer' => true,
                'swipe_statement' => 'Pesan berantai di media sosial termasuk salah satu sumber data yang paling terpercaya.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Sebuah perusahaan akan menentukan lokasi pembangunan pabrik baru. Sebelum mengambil keputusan, perusahaan menggunakan data yang berasal dari Badan Pusat Statistik (BPS) dan hasil penelitian perguruan tinggi. Alasan perusahaan menggunakan sumber tersebut adalah karena ....',
                'options' => [
                    'A' => 'Datanya bersifat hiburan',
                    'B' => 'Datanya lebih terpercaya dan telah melalui proses yang dapat dipertanggungjawabkan',
                    'C' => 'Datanya mudah diubah',
                    'D' => 'Datanya berasal dari media sosial',
                ],
                'correct' => 'B',
                'tf_statement' => 'Perusahaan memakai data BPS dan hasil penelitian perguruan tinggi karena datanya lebih terpercaya dan dapat dipertanggungjawabkan.',
                'tf_answer' => true,
                'swipe_statement' => 'Perusahaan memilih data dari BPS dan perguruan tinggi karena datanya bersifat hiburan semata.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Menurut Asosiasi Manajemen Data Inggris, salah satu karakteristik data terpercaya adalah akurasi. Yang dimaksud dengan akurasi adalah ....',
                'options' => [
                    'A' => 'Data disimpan dalam jumlah banyak',
                    'B' => 'Data mampu menggambarkan objek atau peristiwa yang sebenarnya secara benar',
                    'C' => 'Data memiliki ukuran file kecil',
                    'D' => 'Data disimpan di internet',
                ],
                'correct' => 'B',
                'tf_statement' => 'Akurasi dalam karakteristik data terpercaya berarti data mampu menggambarkan objek atau peristiwa sebenarnya secara benar.',
                'tf_answer' => true,
                'swipe_statement' => 'Akurasi dalam karakteristik kualitas data berarti data disimpan dalam jumlah yang sangat banyak.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Sebuah sekolah memiliki database siswa. Setelah diperiksa, ternyata seluruh identitas siswa telah terisi lengkap tanpa ada kolom yang kosong. Karakteristik kualitas data yang ditunjukkan pada ilustrasi tersebut adalah ....',
                'options' => [
                    'A' => 'Keunikan',
                    'B' => 'Kelengkapan',
                    'C' => 'Ketepatan waktu',
                    'D' => 'Konsistensi',
                ],
                'correct' => 'B',
                'tf_statement' => 'Database siswa yang seluruh kolom identitasnya terisi lengkap tanpa ada yang kosong menunjukkan karakteristik kelengkapan data.',
                'tf_answer' => true,
                'swipe_statement' => 'Data siswa yang seluruh kolomnya terisi lengkap tanpa ada yang kosong menunjukkan karakteristik keunikan data.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Sebuah rumah sakit sedang memperbarui data pasiennya. Setelah dilakukan pemeriksaan, diketahui bahwa seluruh informasi pasien, seperti nama, alamat, nomor identitas, dan riwayat penyakit telah terisi lengkap tanpa ada data yang kosong. Dengan demikian, pihak rumah sakit dapat memberikan pelayanan yang lebih baik karena informasi yang dimiliki sudah lengkap. Karakteristik kualitas data yang ditunjukkan pada ilustrasi tersebut adalah ....',
                'options' => [
                    'A' => 'Akurasi',
                    'B' => 'Konsistensi',
                    'C' => 'Kelengkapan',
                    'D' => 'Validitas',
                ],
                'correct' => 'C',
                'tf_statement' => 'Data pasien rumah sakit yang seluruh informasinya terisi lengkap tanpa ada data kosong menunjukkan karakteristik kelengkapan data.',
                'tf_answer' => true,
                'swipe_statement' => 'Data pasien yang terisi lengkap tanpa ada yang kosong menunjukkan karakteristik akurasi data.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Dinas Kependudukan menyimpan data jumlah penduduk dalam dua sistem yang berbeda. Setelah dibandingkan, jumlah penduduk pada kedua sistem tersebut memiliki nilai yang sama sehingga tidak ditemukan perbedaan informasi. Karakteristik kualitas data yang ditunjukkan pada ilustrasi tersebut adalah ....',
                'options' => [
                    'A' => 'Konsistensi',
                    'B' => 'Akurasi',
                    'C' => 'Keunikan',
                    'D' => 'Ketepatan waktu',
                ],
                'correct' => 'A',
                'tf_statement' => 'Data penduduk pada dua sistem berbeda yang menunjukkan nilai sama menggambarkan karakteristik konsistensi data.',
                'tf_answer' => true,
                'swipe_statement' => 'Data penduduk yang bernilai sama pada dua sistem berbeda menunjukkan karakteristik ketepatan waktu data.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Sebuah aplikasi informasi cuaca selalu memperbarui data setiap tiga jam sekali. Dengan adanya pembaruan tersebut, masyarakat dapat memperoleh informasi cuaca terbaru sesuai kondisi yang sedang terjadi. Karakteristik kualitas data yang paling tepat berdasarkan ilustrasi tersebut adalah ....',
                'options' => [
                    'A' => 'Kelengkapan',
                    'B' => 'Ketepatan waktu',
                    'C' => 'Validitas',
                    'D' => 'Keunikan',
                ],
                'correct' => 'B',
                'tf_statement' => 'Aplikasi cuaca yang memperbarui datanya setiap tiga jam sekali menunjukkan karakteristik ketepatan waktu data.',
                'tf_answer' => true,
                'swipe_statement' => 'Aplikasi cuaca yang rutin memperbarui datanya menunjukkan karakteristik kelengkapan data.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Pada saat melakukan pemeriksaan data pelanggan, sebuah perusahaan menemukan bahwa satu orang pelanggan memiliki dua nomor identitas yang sama dalam database karena tercatat dua kali. Kesalahan tersebut menyebabkan jumlah pelanggan menjadi tidak akurat. Karakteristik kualitas data yang belum terpenuhi adalah ....',
                'options' => [
                    'A' => 'Akurasi',
                    'B' => 'Ketepatan waktu',
                    'C' => 'Keunikan',
                    'D' => 'Validitas',
                ],
                'correct' => 'C',
                'tf_statement' => 'Satu pelanggan yang tercatat dua kali dengan nomor identitas sama menunjukkan karakteristik keunikan data belum terpenuhi.',
                'tf_answer' => true,
                'swipe_statement' => 'Pelanggan yang tercatat dua kali dalam database menunjukkan data tersebut sudah memenuhi karakteristik keunikan.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Suatu aplikasi pendaftaran siswa hanya menerima tanggal lahir dengan format dd/mm/yyyy. Namun, beberapa pengguna mengisi tanggal lahir dengan format yyyy/mm/dd, sehingga data tidak dapat diproses oleh sistem. Karakteristik kualitas data yang berkaitan dengan masalah tersebut adalah ....',
                'options' => [
                    'A' => 'Konsistensi',
                    'B' => 'Validitas atau kesesuaian',
                    'C' => 'Kelengkapan',
                    'D' => 'Keunikan',
                ],
                'correct' => 'B',
                'tf_statement' => 'Kesalahan format tanggal lahir yang tidak sesuai ketentuan sistem berkaitan dengan karakteristik validitas atau kesesuaian data.',
                'tf_answer' => true,
                'swipe_statement' => 'Kesalahan format tanggal lahir pada sistem pendaftaran siswa berkaitan dengan karakteristik kelengkapan data.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Seorang peneliti memperoleh data dari berbagai sumber, kemudian memeriksa apakah data tersebut akurat, lengkap, konsisten, terbaru, tidak ada data ganda, dan sesuai dengan format yang telah ditentukan. Setelah semua syarat tersebut terpenuhi, data digunakan sebagai dasar penelitian. Tujuan utama peneliti melakukan pemeriksaan tersebut adalah ....',
                'options' => [
                    'A' => 'Memperbanyak jumlah data penelitian',
                    'B' => 'Memastikan kualitas data sehingga hasil penelitian lebih dapat dipercaya',
                    'C' => 'Mengurangi jumlah responden penelitian',
                    'D' => 'Mempercepat proses pencetakan laporan',
                ],
                'correct' => 'B',
                'tf_statement' => 'Peneliti memeriksa akurasi, kelengkapan, konsistensi, ketepatan waktu, keunikan, dan validitas data untuk memastikan kualitas data penelitian.',
                'tf_answer' => true,
                'swipe_statement' => 'Peneliti memeriksa kualitas data dengan tujuan mempercepat proses pencetakan laporan penelitian.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Perhatikan beberapa sumber informasi berikut. (1) Jurnal penelitian yang telah melalui proses penelaahan (peer review). (2) Artikel tanpa identitas penulis di internet. (3) Data yang diterbitkan oleh Badan Pusat Statistik (BPS). (4) Pesan berantai di media sosial. Sumber data yang paling terpercaya ditunjukkan oleh nomor ....',
                'options' => [
                    'A' => '1 dan 3',
                    'B' => '2 dan 4',
                    'C' => '1 dan 2',
                    'D' => '3 dan 4',
                ],
                'correct' => 'A',
                'tf_statement' => 'Jurnal penelitian yang telah melalui peer review dan data yang diterbitkan BPS termasuk sumber data yang paling terpercaya.',
                'tf_answer' => true,
                'swipe_statement' => 'Artikel tanpa identitas penulis dan pesan berantai di media sosial termasuk sumber data yang paling terpercaya.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Data yang dibuat dan disahkan berdasarkan peraturan perundang-undangan memiliki kekuatan hukum sehingga dapat dijadikan dasar dalam pelaksanaan pemerintahan maupun pelayanan kepada masyarakat. Jenis data tersebut disebut ....',
                'options' => [
                    'A' => 'Data pribadi',
                    'B' => 'Data legal',
                    'C' => 'Data terbuka',
                    'D' => 'Data sementara',
                ],
                'correct' => 'B',
                'tf_statement' => 'Data yang dibuat dan disahkan berdasarkan peraturan perundang-undangan disebut data legal.',
                'tf_answer' => true,
                'swipe_statement' => 'Data yang disahkan berdasarkan peraturan perundang-undangan disebut data pribadi.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Pemerintah mengeluarkan sebuah Peraturan Presiden yang mengatur tata kelola data nasional. Peraturan tersebut telah disahkan sesuai prosedur yang berlaku dan menjadi pedoman bagi seluruh instansi pemerintah. Berdasarkan ilustrasi tersebut, Peraturan Presiden merupakan contoh ....',
                'options' => [
                    'A' => 'Data pribadi',
                    'B' => 'Data tidak resmi',
                    'C' => 'Data legal',
                    'D' => 'Data sementara',
                ],
                'correct' => 'C',
                'tf_statement' => 'Peraturan Presiden yang mengatur tata kelola data nasional dan telah disahkan sesuai prosedur merupakan contoh data legal.',
                'tf_answer' => true,
                'swipe_statement' => 'Peraturan Presiden yang telah disahkan pemerintah termasuk contoh data sementara.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Sebuah dokumen resmi diterbitkan oleh pemerintah melalui proses yang telah diatur dalam peraturan perundang-undangan. Oleh karena itu, dokumen tersebut memiliki kekuatan hukum dan dapat dijadikan dasar dalam pengambilan kebijakan. Alasan utama dokumen tersebut disebut sebagai data legal adalah karena ....',
                'options' => [
                    'A' => 'Dibuat oleh masyarakat umum',
                    'B' => 'Telah disahkan oleh lembaga yang berwenang sesuai hukum yang berlaku',
                    'C' => 'Disimpan di internet',
                    'D' => 'Memiliki ukuran file yang besar',
                ],
                'correct' => 'B',
                'tf_statement' => 'Dokumen resmi disebut data legal karena telah disahkan oleh lembaga yang berwenang sesuai hukum yang berlaku.',
                'tf_answer' => true,
                'swipe_statement' => 'Dokumen resmi disebut data legal karena dibuat langsung oleh masyarakat umum tanpa proses pengesahan.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Seorang mahasiswa hukum ingin mencari peraturan pemerintah yang masih berlaku. Dosen menyarankan agar ia mengakses Jaringan Dokumentasi dan Informasi Hukum Nasional (JDIHN) karena menyediakan dokumen hukum resmi dari pemerintah. Tujuan penggunaan JDIHN pada ilustrasi tersebut adalah ....',
                'options' => [
                    'A' => 'Memperoleh informasi hiburan',
                    'B' => 'Memperoleh data legal yang telah disahkan oleh pemerintah',
                    'C' => 'Memperoleh data dari media sosial',
                    'D' => 'Memperoleh data yang belum diverifikasi',
                ],
                'correct' => 'B',
                'tf_statement' => 'JDIHN digunakan untuk memperoleh data legal yang telah disahkan oleh pemerintah.',
                'tf_answer' => true,
                'swipe_statement' => 'JDIHN digunakan masyarakat untuk memperoleh informasi hiburan semata.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Menurut Direktorat Jenderal Aplikasi Informatika (Dirjen Aptika), setiap pengolahan data harus memiliki dasar legalitas. Salah satu dasar tersebut adalah public interest. Yang dimaksud dengan public interest adalah ....',
                'options' => [
                    'A' => 'Pengolahan data untuk kepentingan pribadi',
                    'B' => 'Pengolahan data untuk kepentingan umum',
                    'C' => 'Pengolahan data untuk kepentingan perusahaan semata',
                    'D' => 'Pengolahan data tanpa tujuan tertentu',
                ],
                'correct' => 'B',
                'tf_statement' => 'Public interest adalah dasar legalitas pengolahan data yang dilakukan untuk kepentingan umum.',
                'tf_answer' => true,
                'swipe_statement' => 'Public interest adalah dasar legalitas pengolahan data yang dilakukan untuk kepentingan pribadi semata.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Sebuah instansi pemerintah mengolah data masyarakat untuk membantu penanganan korban bencana alam sehingga keselamatan masyarakat dapat segera ditangani. Pengolahan data tersebut dilakukan demi melindungi kepentingan yang sangat penting bagi masyarakat. Dasar legalitas pengolahan data pada kasus tersebut adalah ....',
                'options' => [
                    'A' => 'Legitimate interest',
                    'B' => 'Public interest',
                    'C' => 'Vital interest',
                    'D' => 'Personal interest',
                ],
                'correct' => 'C',
                'tf_statement' => 'Pengolahan data korban bencana alam demi keselamatan masyarakat termasuk dasar legalitas vital interest.',
                'tf_answer' => true,
                'swipe_statement' => 'Pengolahan data korban bencana alam demi keselamatan masyarakat termasuk dasar legalitas legitimate interest.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Perhatikan karakteristik berikut. (1) Data dapat digunakan kembali oleh masyarakat. (2) Data berasal dari lembaga resmi dan dapat dipercaya. (3) Data memiliki dasar hukum yang jelas. Apabila suatu data memenuhi ketiga karakteristik tersebut, maka data tersebut dapat dikategorikan sebagai ....',
                'options' => [
                    'A' => 'Hanya data terbuka',
                    'B' => 'Hanya data terpercaya',
                    'C' => 'Hanya data legal',
                    'D' => 'Data terbuka, terpercaya, dan legal',
                ],
                'correct' => 'D',
                'tf_statement' => 'Data yang dapat digunakan kembali, berasal dari lembaga resmi terpercaya, dan memiliki dasar hukum jelas dikategorikan sebagai data terbuka, terpercaya, dan legal sekaligus.',
                'tf_answer' => true,
                'swipe_statement' => 'Data yang memenuhi ketiga karakteristik tersebut hanya bisa dikategorikan sebagai data terbuka saja.',
                'swipe_answer' => false,
            ],
            [
                'stem' => 'Pemerintah melalui BMKG menyediakan data prakiraan cuaca yang dapat diunduh secara bebas oleh masyarakat. Data tersebut diterbitkan oleh lembaga resmi pemerintah, dapat digunakan kembali dengan mencantumkan sumber, serta menjadi rujukan dalam berbagai penelitian dan pengambilan keputusan. Berdasarkan ilustrasi tersebut, kesimpulan yang paling tepat adalah ....',
                'options' => [
                    'A' => 'Data BMKG hanya termasuk data terbuka karena dapat diunduh masyarakat',
                    'B' => 'Data BMKG hanya termasuk data legal karena diterbitkan pemerintah',
                    'C' => 'Data BMKG hanya termasuk data terpercaya karena digunakan dalam penelitian',
                    'D' => 'Data BMKG termasuk data terbuka, data terpercaya, dan data legal karena memenuhi ketiga karakteristik tersebut',
                ],
                'correct' => 'D',
                'tf_statement' => 'Data prakiraan cuaca BMKG termasuk data terbuka, terpercaya, dan legal karena memenuhi ketiga karakteristik tersebut sekaligus.',
                'tf_answer' => true,
                'swipe_statement' => 'Data prakiraan cuaca BMKG hanya termasuk data terbuka karena bisa diunduh masyarakat, tanpa memenuhi karakteristik lainnya.',
                'swipe_answer' => false,
            ],
        ];
    }
}