<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update semua pengguna Non-PNS (NIP kosong / null / bukan 18 digit) menjadi 'Tenaga Alih Daya'
        DB::statement("
            UPDATE users 
            SET jabatan = 'Tenaga Alih Daya' 
            WHERE nip IS NULL 
               OR TRIM(nip) = '' 
               OR LENGTH(TRIM(nip)) != 18
        ");

        // 2. Pembersihan jabatan PNS resmi (hilangkan penempatan Tim Kerja / Pokja / Koordinator Ad Hoc)
        $pnsUpdates = [
            // Admin Sistem
            1 => 'Pranata Komputer Ahli Pertama',

            // Pimpinan Balai & Struktural Utama (tetap jabatan murni)
            2 => 'Kepala Balai Guru dan Tenaga Kependidikan Provinsi DKI Jakarta',
            3 => 'Kepala Subbagian Umum (Pembina, IV/a)',

            // Para Ketua Tim Kerja (kembalikan ke jabatan fungsional murni PNS)
            5 => 'Widyaiswara (Pembina Utama Muda, IV/c)',
            6 => 'Widyaiswara (Pembina Tk. I, IV/b)',
            7 => 'Widyaiswara (Pembina Utama Muda, IV/c)',
            8 => 'Pengembang Teknologi Pembelajaran Ahli Muda (Pembina, IV/a)',
            9 => 'Arsiparis Muda (Pembina, IV/a)',

            // Akun Demo PNS
            10 => 'Verifikator Pelaporan',
            11 => 'Pelaksana Tugas',

            // Koordinator Jabatan Fungsional
            12 => 'Widyaiswara (Pembina Utama Muda, IV/c)',

            // Koordinator Layanan / Subbag Umum
            13 => 'Pengelola Layanan Umum dan Rumah Tangga',
            14 => 'Penelaah Teknis Kebijakan (Penata Muda Tk 1, III/b)',
            15 => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
            16 => 'Pengelola Layanan Operasional (Golongan VII)',
            20 => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
            24 => 'Penelaah Teknis Kebijakan (Penata Tk. I, III/d)',
            27 => 'Pengelola Layanan Operasional (Golongan VII)',
            28 => 'Operator Layanan Operasional',

            // Widyaiswara & Jabatan Fungsional Tim Kerja
            38 => 'Widyaiswara (Pembina Utama Muda, IV/c)',
            39 => 'Widyaiswara (Pembina Utama Muda, IV/c)',
            40 => 'Widyaiswara (Penata Tk. I, III/d)',
            41 => 'Widyaiswara (Pembina Tk. I, IV/b)',
            42 => 'Widyaiswara (Penata, III/c)',
            43 => 'Widyaiswara (Pembina, IV/a)',
            44 => 'Penelaah Teknis Kebijakan (Penata, III/c)',
            45 => 'Widyaiswara (Pembina Tk. I, IV/b)',
            46 => 'Widyaiswara (Pembina Tk. I, IV/b)',
            47 => 'Widyaiswara (Pembina Tk. I, IV/b)',
            48 => 'Widyaiswara (Penata Tk. I, III/d)',
            49 => 'Pengembang Teknologi Pembelajaran Ahli Muda (Penata Tk. I, III/d)',
            50 => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
            51 => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
            53 => 'Widyaiswara (Penata Tk. I, III/d)',
            54 => 'Widyaiswara (Penata Tk. I, III/d)',
            55 => 'Penelaah Teknis Kebijakan (Pembina, IV/a)',
        ];

        foreach ($pnsUpdates as $userId => $newJabatan) {
            DB::table('users')->where('id', $userId)->update([
                'jabatan' => $newJabatan,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nilai reversibel tidak kritis karena penataan jabatan baru adalah standarisasi data resmi
    }
};

