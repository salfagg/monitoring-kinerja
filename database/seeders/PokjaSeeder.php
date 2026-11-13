<?php

namespace Database\Seeders;

use App\Models\Pokja;
use Illuminate\Database\Seeder;

class PokjaSeeder extends Seeder
{
    public function run(): void
    {
        $pokjas = [
            [
                'nama_pokja' => 'Tim Kerja 1: Pemetaan & Evaluasi',
                'kode_pokja' => 'TIM-01',
                'deskripsi' => 'Tema: Analisis Data Dasar. Fokus pada pemetaan mutu, evaluasi data dasar pendidik dan tenaga kependidikan wilayah binaan.',
            ],
            [
                'nama_pokja' => 'Tim Kerja 2: Inovasi',
                'kode_pokja' => 'TIM-02',
                'deskripsi' => 'Tema: Pengembangan Gagasan. Perancangan inovasi model pembinaan, riset terapan, dan pengembangan gagasan baru BGTK.',
            ],
            [
                'nama_pokja' => 'Tim Kerja 3: Pembelajaran',
                'kode_pokja' => 'TIM-03',
                'deskripsi' => 'Tema: Eksekusi Program. Penyelenggaraan pelatihan teknis, fasilitasi kurikulum, dan eksekusi program peningkatan kompetensi GTK.',
            ],
            [
                'nama_pokja' => 'Tim Kerja 4: Data, Informasi, dan Publikasi',
                'kode_pokja' => 'TIM-04',
                'deskripsi' => 'Tema: Amplifikasi Informasi. Pengelolaan portal satu data, publikasi media, transformasi digital, dan sistem informasi balai.',
            ],
            [
                'nama_pokja' => 'Tim Kerja 5: Kemitraan & Fasilitasi',
                'kode_pokja' => 'TIM-05',
                'deskripsi' => 'Tema: Ekspansi Jaringan. Kolaborasi dengan pemerintah daerah, dinas pendidikan, komunitas belajar, dan mitra strategis.',
            ],
            [
                'nama_pokja' => 'Sub Bagian Umum (Operasional & Administrasi)',
                'kode_pokja' => 'SUBBAG-UMUM',
                'deskripsi' => 'Pengelolaan Perencanaan & Keuangan, OSDM (Kepegawaian), Administrasi & Persuratan, BMN, Sistem Informasi & Jaringan, serta Keamanan & Kebersihan.',
            ],
        ];

        foreach ($pokjas as $item) {
            Pokja::updateOrCreate(
                ['kode_pokja' => $item['kode_pokja']],
                $item
            );
        }
    }
}
