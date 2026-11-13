<?php

namespace Database\Seeders;

use App\Models\LaporanKinerja;
use App\Models\RencanaMingguan;
use App\Models\RiwayatVerifikasi;
use App\Models\SkpTahunan;
use App\Models\SkpTargetBulanan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleKinerjaSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = User::where('email', 'pegawai@bgtk.go.id')->first();
        $pokjaUser = User::where('email', 'pokja@bgtk.go.id')->first();
        $katim = User::where('email', 'endang@bgtk.go.id')->first();

        if (! $pegawai) {
            return;
        }

        // 1. Buat SKP Tahunan Pegawai
        $skp = SkpTahunan::firstOrCreate(
            [
                'user_id' => $pegawai->id,
                'tahun' => 2026,
                'rencana_kinerja' => 'Terlaksananya fasilitasi dan penyusunan modul peningkatan kompetensi guru penggerak wilayah binaan',
            ],
            [
                'aspek' => 'Kuantitas',
                'indikator_kinerja' => 'Jumlah modul pelatihan dan laporan pendampingan yang diselesaikan',
                'target_min' => 4,
                'target_max' => 6,
                'satuan' => 'Modul & Laporan',
                'status' => 'disetujui',
            ]
        );

        if ($katim) {
            SkpTahunan::firstOrCreate(
                [
                    'user_id' => $katim->id,
                    'tahun' => 2026,
                    'rencana_kinerja' => 'Terselenggaranya koordinasi program prioritas transformasi kepemimpinan sekolah dan monev berkala',
                ],
                [
                    'aspek' => 'Kuantitas',
                    'indikator_kinerja' => 'Jumlah laporan koordinasi dan monev program kepemimpinan sekolah',
                    'target_min' => 8,
                    'target_max' => 12,
                    'satuan' => 'Laporan Monev',
                    'status' => 'disetujui',
                ]
            );
        }

        if ($pokjaUser) {
            SkpTahunan::firstOrCreate(
                [
                    'user_id' => $pokjaUser->id,
                    'tahun' => 2026,
                    'rencana_kinerja' => 'Terlaksananya verifikasi dan pengelolaan administrasi kelayakan program guru penggerak DKI Jakarta',
                ],
                [
                    'aspek' => 'Kuantitas',
                    'indikator_kinerja' => 'Jumlah berkas verifikasi administrasi dan berita acara kegiatan',
                    'target_min' => 5,
                    'target_max' => 10,
                    'satuan' => 'Dokumen Berita Acara',
                    'status' => 'disetujui',
                ]
            );
        }

        // 2. Buat Target Bulanan (Oktober 2026)
        $targetBulanan = SkpTargetBulanan::firstOrCreate(
            [
                'skp_tahunan_id' => $skp->id,
                'user_id' => $pegawai->id,
                'bulan' => 10,
                'tahun' => 2026,
            ],
            [
                'uraian_target' => 'Penyusunan modul pelatihan bab 1 - bab 3 dan instrumen monev',
                'target_kuantitas' => 1,
                'satuan' => 'Dokumen Modul',
                'status' => 'aktif',
            ]
        );

        // 3. Buat Rencana Mingguan (Minggu ke-3 Oktober 2026)
        $rencanaMingguan = RencanaMingguan::firstOrCreate(
            [
                'user_id' => $pegawai->id,
                'skp_target_bulanan_id' => $targetBulanan->id,
                'tahun' => 2026,
                'bulan' => 10,
                'minggu_ke' => 3,
            ],
            [
                'tanggal_mulai' => '2026-10-15',
                'tanggal_selesai' => '2026-10-21',
                'rencana_aksi' => 'Melakukan telaah bahan ajar dan merancang lembar kerja peserta bab 2',
                'target_output' => '1 Draft Lembar Kerja Peserta',
                'status' => 'berjalan',
            ]
        );

        // 4. Buat Contoh Laporan Kinerja 1: Sudah Disetujui Katim
        $laporan1 = LaporanKinerja::firstOrCreate(
            [
                'user_id' => $pegawai->id,
                'tanggal_kegiatan' => '2026-10-18',
                'uraian_kegiatan' => 'Menyusun draf bahan ajar modul pelatihan fasilitator guru penggerak sesi 1 dan 2',
            ],
            [
                'pokja_id' => $pegawai->pokja_id,
                'skp_target_bulanan_id' => $targetBulanan->id,
                'rencana_mingguan_id' => $rencanaMingguan->id,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'bulan' => 10,
                'tahun' => 2026,
                'output_kegiatan' => 'Dokumen Draf Modul Sesi 1 & 2',
                'volume_capaian' => 1,
                'satuan' => 'Dokumen',
                'status' => 'disetujui_katim',
                'verifikator_pokja_id' => $pokjaUser?->id,
                'catatan_pokja' => 'Kelengkapan dokumen telah sesuai dengan standar Pokja.',
                'tanggal_verifikasi_pokja' => '2026-10-19 10:15:00',
                'katim_id' => $katim?->id,
                'nilai_kualitas_katim' => 92.50,
                'angka_kredit' => 0.50,
                'capaian_iku' => 'IKU-3: Persentase Guru Berpredikat Baik',
                'catatan_katim' => 'Hasil pekerjaan sangat baik dan terstruktur rapi. Disetujui untuk konsolidasi bulanan.',
                'tanggal_evaluasi_katim' => '2026-10-20 09:30:00',
            ]
        );

        // Log riwayat verifikasi untuk laporan 1
        RiwayatVerifikasi::firstOrCreate([
            'laporan_kinerja_id' => $laporan1->id,
            'aksi' => 'ajukan',
        ], [
            'user_id' => $pegawai->id,
            'role_verifikator' => 'pegawai',
            'status_sebelumnya' => 'draft',
            'status_baru' => 'diajukan',
            'catatan' => 'Laporan kinerja harian diajukan untuk verifikasi.',
        ]);

        if ($pokjaUser) {
            RiwayatVerifikasi::firstOrCreate([
                'laporan_kinerja_id' => $laporan1->id,
                'aksi' => 'verifikasi_pokja',
            ], [
                'user_id' => $pokjaUser->id,
                'role_verifikator' => 'tim_pokja',
                'status_sebelumnya' => 'diajukan',
                'status_baru' => 'disetujui_pokja',
                'catatan' => 'Kelengkapan dokumen telah sesuai standar Pokja.',
            ]);
        }

        if ($katim) {
            RiwayatVerifikasi::firstOrCreate([
                'laporan_kinerja_id' => $laporan1->id,
                'aksi' => 'evaluasi_katim',
            ], [
                'user_id' => $katim->id,
                'role_verifikator' => 'katim_korjafung',
                'status_sebelumnya' => 'disetujui_pokja',
                'status_baru' => 'disetujui_katim',
                'catatan' => 'Hasil pekerjaan sangat baik dan terstruktur rapi. Disetujui.',
            ]);
        }

        // 5. Buat Contoh Laporan Kinerja 2: Status Masih Diajukan (menunggu verifikasi)
        $laporan2 = LaporanKinerja::firstOrCreate(
            [
                'user_id' => $pegawai->id,
                'tanggal_kegiatan' => '2026-10-20',
                'uraian_kegiatan' => 'Fasilitasi koordinasi teknis persiapan monev dengan Dinas Pendidikan Kabupaten/Kota',
            ],
            [
                'pokja_id' => $pegawai->pokja_id,
                'skp_target_bulanan_id' => $targetBulanan->id,
                'rencana_mingguan_id' => $rencanaMingguan->id,
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '15:30:00',
                'bulan' => 10,
                'tahun' => 2026,
                'output_kegiatan' => 'Notula Hasil Rapat dan Daftar Hadir',
                'volume_capaian' => 1,
                'satuan' => 'Laporan',
                'status' => 'diajukan',
            ]
        );

        RiwayatVerifikasi::firstOrCreate([
            'laporan_kinerja_id' => $laporan2->id,
            'aksi' => 'ajukan',
        ], [
            'user_id' => $pegawai->id,
            'role_verifikator' => 'pegawai',
            'status_sebelumnya' => 'draft',
            'status_baru' => 'diajukan',
            'catatan' => 'Laporan kegiatan koordinasi diajukan.',
        ]);
    }
}
