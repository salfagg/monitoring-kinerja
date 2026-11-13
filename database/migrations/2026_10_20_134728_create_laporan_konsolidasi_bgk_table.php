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
        Schema::create('laporan_konsolidasi_bgk', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan'); // 1 - 12
            $table->integer('tahun');
            $table->string('judul_laporan');
            $table->foreignId('kasubag_id')->constrained('users')->cascadeOnDelete();
            
            // Rekapitulasi Data
            $table->integer('total_pegawai')->default(0);
            $table->integer('total_laporan_selesai')->default(0);
            $table->decimal('rata_rata_capaian', 5, 2)->default(0); // Nilai rata-rata kinerja BGK
            $table->text('catatan_kedisiplinan')->nullable(); // Ringkasan kedisiplinan pegawai
            $table->text('ringkasan_kinerja')->nullable();
            $table->string('file_draft_laporan')->nullable();

            // Status Tanda Tangan Kasubag
            $table->boolean('ttd_kasubag')->default(false);
            $table->timestamp('tanggal_ttd_kasubag')->nullable();
            
            // Approval & Evaluasi Kabal
            $table->foreignId('kabal_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status_kabal', ['draft', 'diajukan', 'disetujui', 'dikembalikan'])->default('draft');
            $table->timestamp('tanggal_approval_kabal')->nullable();
            $table->text('catatan_evaluasi_kabal')->nullable();
            
            // Rekomendasi / Tindak Lanjut Kabal
            $table->text('rekomendasi_reward')->nullable();
            $table->text('rekomendasi_punishment')->nullable();
            $table->text('rekomendasi_pengembangan_kompetensi')->nullable();
            $table->string('file_laporan_final')->nullable(); // Laporan yang sudah ditandatangani Kabal

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_konsolidasi_bgk');
    }
};
