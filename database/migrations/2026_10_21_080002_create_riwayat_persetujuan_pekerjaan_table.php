<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Riwayat Persetujuan & Keputusan Pekerjaan.
     */
    public function up(): void
    {
        Schema::create('riwayat_persetujuan_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Aktor pemeriksa / pengusul
            
            // Jenis Aksi: pengajuan_rincian, persetujuan_rincian, perbaikan_rincian, pengajuan_hasil, penerimaan_hasil, perbaikan_hasil
            $table->string('jenis_aksi');
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->text('catatan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_persetujuan_pekerjaan');
    }
};
