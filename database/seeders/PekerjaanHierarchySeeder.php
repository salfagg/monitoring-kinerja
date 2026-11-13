<?php

namespace Database\Seeders;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RealisasiPekerjaan;
use App\Models\RiwayatPersetujuanPekerjaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PekerjaanHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $katim4 = User::where('email', 'widyatmo@bgtk.go.id')->first();
        $katim3 = User::where('email', 'endang@bgtk.go.id')->first();
        $rina = User::where('email', 'pokja@bgtk.go.id')->first();
        $dedi = User::where('email', 'pegawai@bgtk.go.id')->first();
        $tim4 = Pokja::where('kode_pokja', 'TIM-04')->first();
        $tim3 = Pokja::where('kode_pokja', 'TIM-03')->first();

        if (! $katim4 || ! $dedi) {
            return;
        }

        // -------------------------------------------------------------
        // CONTOH SESUAI DOKUMEN PRD HALAMAN 4 & 7:
        // Hierarki: 1 -> 1.1 -> 1.1.1 -> (1.1.1.1, 1.1.1.2, 1.1.1.3)
        // -------------------------------------------------------------

        // 1. Pekerjaan Utama: Pengembangan Layanan Digital
        $p1 = Pekerjaan::create([
            'parent_id' => null,
            'kode_tampilan' => '1',
            'judul' => 'Pengembangan Layanan Digital BGTK',
            'deskripsi' => 'Pengembangan dan optimalisasi portal sistem informasi layanan terpadu BGTK DKI Jakarta.',
            'hasil_diharapkan' => 'Aplikasi Layanan Digital Terpadu Beroperasi Penuh',
            'target' => 1,
            'satuan' => 'Sistem Aplikasi',
            'tenggat' => Carbon::now()->addMonths(3)->toDateString(),
            'pembuat_id' => $katim4->id,
            'penanggung_jawab_id' => $rina ? $rina->id : $katim4->id,
            'pokja_id' => $tim4?->id,
            'bobot' => 100.00,
            'progres' => 50.00,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'dikerjakan',
        ]);

        // 1.1: Pengembangan SILAT
        $p1_1 = Pekerjaan::create([
            'parent_id' => $p1->id,
            'kode_tampilan' => '1.1',
            'judul' => 'Pengembangan Portal SILAT (Sistem Informasi Layanan Terpadu)',
            'deskripsi' => 'Pengembangan modul-modul utama operasional layanan pada platform SILAT.',
            'hasil_diharapkan' => 'Rilis Portal SILAT Versi 2.0',
            'target' => 1,
            'satuan' => 'Modul Inti',
            'tenggat' => Carbon::now()->addMonths(2)->toDateString(),
            'pembuat_id' => $rina ? $rina->id : $katim4->id,
            'penanggung_jawab_id' => $rina ? $rina->id : $katim4->id,
            'pokja_id' => $tim4?->id,
            'bobot' => 100.00,
            'progres' => 50.00,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'dikerjakan',
        ]);

        // 1.1.1: Modul Pendaftaran
        $p1_1_1 = Pekerjaan::create([
            'parent_id' => $p1_1->id,
            'kode_tampilan' => '1.1.1',
            'judul' => 'Modul Pendaftaran Peserta Pelatihan',
            'deskripsi' => 'Alur registrasi, validasi biodata SIMPKB/Dapodik, dan integrasi pengumuman.',
            'hasil_diharapkan' => 'Sub-modul Pendaftaran Peserta Siap Pakai',
            'target' => 1,
            'satuan' => 'Modul',
            'tenggat' => Carbon::now()->addMonth()->toDateString(),
            'pembuat_id' => $dedi->id,
            'penanggung_jawab_id' => $dedi->id,
            'pokja_id' => $tim4?->id,
            'bobot' => 100.00,
            'progres' => 50.00,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'dikerjakan',
        ]);

        // 1.1.1.1: Analisis Kebutuhan (Bobot 20%, Progres 100% -> Kontribusi 20%)
        $p1_1_1_1 = Pekerjaan::create([
            'parent_id' => $p1_1_1->id,
            'kode_tampilan' => '1.1.1.1',
            'judul' => 'Analisis Kebutuhan Pengguna & Alur Registrasi',
            'deskripsi' => 'Wawancara stakeholder dan dokumentasi requirement spesifikasi sistem.',
            'hasil_diharapkan' => 'Dokumen SRS (Software Requirement Specification)',
            'target' => 1,
            'satuan' => 'Dokumen SRS',
            'tenggat' => Carbon::now()->subDays(5)->toDateString(),
            'pembuat_id' => $dedi->id,
            'penanggung_jawab_id' => $dedi->id,
            'pokja_id' => $tim4?->id,
            'bobot' => 20.00,
            'progres' => 100.00,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'selesai_terverifikasi',
            'tanggal_selesai_terverifikasi' => Carbon::now()->subDays(3),
        ]);

        RealisasiPekerjaan::create([
            'pekerjaan_id' => $p1_1_1_1->id,
            'user_id' => $dedi->id,
            'tanggal' => Carbon::now()->subDays(4)->toDateString(),
            'progres_capaian' => 100.00,
            'deskripsi_progres' => 'Telah menyelesaikan dokumen SRS dan disepakati oleh tim teknis.',
            'nama_bukti' => 'Dokumen SRS SILAT v2.pdf',
            'link_bukti' => 'https://drive.google.com/sample-srs-silat',
        ]);

        // 1.1.1.2: Pengembangan Formulir (Bobot 50%, Progres 60% -> Kontribusi 30%)
        $p1_1_1_2 = Pekerjaan::create([
            'parent_id' => $p1_1_1->id,
            'kode_tampilan' => '1.1.1.2',
            'judul' => 'Pengembangan Formulir Pendaftaran Dinamis',
            'deskripsi' => 'Coding frontend formulir pendaftaran, validasi NIK/NUPTK, dan upload syarat.',
            'hasil_diharapkan' => 'Kode Program & Tampilan Form Registrasi',
            'target' => 1,
            'satuan' => 'Fitur Form',
            'tenggat' => Carbon::now()->addDays(10)->toDateString(),
            'pembuat_id' => $dedi->id,
            'penanggung_jawab_id' => $dedi->id,
            'pokja_id' => $tim4?->id,
            'bobot' => 50.00,
            'progres' => 60.00,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'dikerjakan',
        ]);

        RealisasiPekerjaan::create([
            'pekerjaan_id' => $p1_1_1_2->id,
            'user_id' => $dedi->id,
            'tanggal' => Carbon::now()->subDay()->toDateString(),
            'progres_capaian' => 60.00,
            'deskripsi_progres' => 'Form pendaftaran dan validasi step 1-3 telah selesai diimplementasikan.',
            'kendala' => 'Menunggu integrasi API Dapodik untuk validasi otomatis.',
            'tindak_lanjut' => 'Koordinasi teknis dengan admin database.',
            'nama_bukti' => 'Screenshot Implementasi Form.png',
        ]);

        // 1.1.1.3: Pengujian (Bobot 30%, Progres 0% -> Kontribusi 0%)
        Pekerjaan::create([
            'parent_id' => $p1_1_1->id,
            'kode_tampilan' => '1.1.1.3',
            'judul' => 'Pengujian Fungsional & UAT Pendaftaran',
            'deskripsi' => 'Pengujian end-to-end simulasi pendaftaran oleh 50 pengguna uji coba.',
            'hasil_diharapkan' => 'Berita Acara UAT & Laporan Bebas Bug',
            'target' => 1,
            'satuan' => 'Laporan UAT',
            'tenggat' => Carbon::now()->addDays(20)->toDateString(),
            'pembuat_id' => $dedi->id,
            'penanggung_jawab_id' => $dedi->id,
            'pokja_id' => $tim4?->id,
            'bobot' => 30.00,
            'progres' => 0.00,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'belum_dimulai',
        ]);

        // Hitung ulang progres induk otomatis
        $p1_1_1->recalculateProgresFromChildren();

        // -------------------------------------------------------------
        // CONTOH TIM KERJA 3: PEMBELAJARAN (Ketua: Endang Kurniawan)
        // -------------------------------------------------------------
        if ($katim3) {
            $p2 = Pekerjaan::create([
                'parent_id' => null,
                'kode_tampilan' => '2',
                'judul' => 'Program Fasilitasi Peningkatan Kompetensi Guru Penggerak 2026',
                'deskripsi' => 'Penyelenggaraan pendampingan dan pembekalan bagi guru penggerak angkatan berjalan.',
                'hasil_diharapkan' => 'Terlaksananya 4 Angkatan Bimtek Fasilitasi GTK',
                'target' => 4,
                'satuan' => 'Angkatan',
                'tenggat' => Carbon::now()->addMonths(4)->toDateString(),
                'pembuat_id' => $katim3->id,
                'penanggung_jawab_id' => $dedi->id,
                'pokja_id' => $tim3?->id,
                'bobot' => 100.00,
                'progres' => 25.00,
                'status_persetujuan' => 'disetujui',
                'status_pelaksanaan' => 'dikerjakan',
            ]);

            Pekerjaan::create([
                'parent_id' => $p2->id,
                'kode_tampilan' => '2.1',
                'judul' => 'Penyusunan Modul Ajar dan Lembar Kerja Fasilitator',
                'deskripsi' => 'Penyusunan naskah bahan ajar sesi 1 s.d 4.',
                'hasil_diharapkan' => '1 Buku Panduan Modul Fasilitator',
                'target' => 1,
                'satuan' => 'Buku Modul',
                'tenggat' => Carbon::now()->addDays(15)->toDateString(),
                'pembuat_id' => $dedi->id,
                'penanggung_jawab_id' => $dedi->id,
                'pokja_id' => $tim3?->id,
                'bobot' => 40.00,
                'progres' => 62.50,
                'status_persetujuan' => 'disetujui',
                'status_pelaksanaan' => 'dikerjakan',
            ]);

            Pekerjaan::create([
                'parent_id' => $p2->id,
                'kode_tampilan' => '2.2',
                'judul' => 'Eksekusi Bimtek Fasilitasi Tatap Muka Wilayah 1',
                'deskripsi' => 'Penyelenggaraan workshop 3 hari di wilayah binaan Jakarta Timur.',
                'hasil_diharapkan' => 'Laporan Hasil Workshop & Daftar Kehadiran 100 Guru',
                'target' => 1,
                'satuan' => 'Laporan Kegiatan',
                'tenggat' => Carbon::now()->addMonth()->toDateString(),
                'pembuat_id' => $dedi->id,
                'penanggung_jawab_id' => $dedi->id,
                'pokja_id' => $tim3?->id,
                'bobot' => 60.00,
                'progres' => 0.00,
                'status_persetujuan' => 'disetujui',
                'status_pelaksanaan' => 'belum_dimulai',
            ]);

            $p2->recalculateProgresFromChildren();
        }
    }
}
