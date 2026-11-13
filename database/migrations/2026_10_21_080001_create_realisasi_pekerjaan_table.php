<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Realisasi Pekerjaan.
     */
    public function up(): void
    {
        Schema::create('realisasi_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->date('tanggal');
            $table->decimal('progres_capaian', 5, 2); // Nilai progres pada update ini (0-100)
            $table->text('deskripsi_progres');
            $table->text('kendala')->nullable();
            $table->text('tindak_lanjut')->nullable();
            
            // Bukti Dukung (File / Link)
            $table->string('nama_bukti')->nullable();
            $table->string('file_bukti')->nullable();
            $table->text('link_bukti')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_pekerjaan');
    }
};
