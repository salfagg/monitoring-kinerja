<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_kinerja_id')->constrained('laporan_kinerja')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // User yang melakukan aksi
            $table->string('role_verifikator'); // pegawai, tim_pokja, katim_korjafung, kasubag, kabal
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->string('aksi'); // ajukan, verifikasi_pokja, kembalikan_pokja, evaluasi_katim, tolak_katim, ttd_kasubag, approve_kabal, tolak_kabal
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_verifikasi');
    }
};
