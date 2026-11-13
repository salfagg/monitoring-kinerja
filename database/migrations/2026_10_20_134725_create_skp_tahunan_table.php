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
        Schema::create('skp_tahunan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('tahun');
            $table->text('rencana_kinerja'); // Rencana Hasil Kerja (RHK)
            $table->string('aspek')->default('Kuantitas'); // Kuantitas, Kualitas, Waktu
            $table->text('indikator_kinerja'); // Indikator Kinerja Individu (IKI)
            $table->decimal('target_min', 8, 2)->default(0);
            $table->decimal('target_max', 8, 2)->default(0);
            $table->string('satuan'); // Dokumen, Laporan, Kegiatan, dll
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'revisi'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_tahunan');
    }
};
