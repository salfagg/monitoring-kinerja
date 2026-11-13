<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalisasi role pengguna lama ke 7 role resmi SIMON-K 2026:
        // 1. kabal: Kepala Balai
        // 2. kasubag: Kepala Subbagian Umum
        // 3. korjafung: Koordinator Jabatan Fungsional
        // 4. katim: Ketua Tim Kerja / Ketua Pokja
        // 5. korlayanan: Koordinator Bidang / Layanan
        // 6. pegawai: Pegawai
        // 7. admin: Administrator

        // Update Dr. Endah Ariani (Koordinator Jabatan Fungsional)
        DB::table('users')
            ->where('email', 'korjafung@bgtk.go.id')
            ->orWhere('jabatan', 'like', '%Koordinator Jabatan Fungsional%')
            ->update(['role' => 'korjafung']);

        // Update para Ketua Tim Kerja
        DB::table('users')
            ->where('role', 'katim_korjafung')
            ->update(['role' => 'katim']);

        // Update tim_pokja ke pegawai
        DB::table('users')
            ->where('role', 'tim_pokja')
            ->update(['role' => 'pegawai']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ke katim_korjafung jika di-rollback
        DB::table('users')
            ->whereIn('role', ['katim', 'korjafung'])
            ->update(['role' => 'katim_korjafung']);
    }
};
