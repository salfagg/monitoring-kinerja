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
        Schema::create('laporan_kinerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pokja_id')->nullable()->constrained('pokjas')->nullOnDelete();
            $table->foreignId('skp_target_bulanan_id')->nullable()->constrained('skp_target_bulanan')->nullOnDelete();
            $table->foreignId('rencana_mingguan_id')->nullable()->constrained('rencana_mingguan')->nullOnDelete();
            
            $table->date('tanggal_kegiatan');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->unsignedTinyInteger('bulan');
            $table->integer('tahun');
            
            $table->text('uraian_kegiatan');
            $table->string('output_kegiatan');
            $table->decimal('volume_capaian', 8, 2)->default(1);
            $table->string('satuan')->default('Dokumen');
            
            // Workflow Status
            $table->enum('status', [
                'draft',
                'diajukan',             // Pegawai mengajukan ke Pokja
                'revisi_pokja',         // Pokja minta revisi
                'disetujui_pokja',      // Pokja teruskan ke Katim
                'revisi_katim',         // Katim tolak/minta revisi
                'disetujui_katim',      // Katim nilai & teruskan ke Kasubag
                'dikonsolidasi_kasubag',// Kasubag TTD draft laporan
                'disetujui_kabal',      // Kabal Approve Final & Rekomendasi
                'ditolak_kabal'         // Kabal kembalikan ke Kasubag
            ])->default('draft');

            // 1. Catatan Verifikasi Pokja
            $table->foreignId('verifikator_pokja_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_pokja')->nullable();
            $table->timestamp('tanggal_verifikasi_pokja')->nullable();

            // 2. Evaluasi Katim / Korjafung
            $table->foreignId('katim_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('nilai_kualitas_katim', 5, 2)->nullable(); // Nilai 0 - 100
            $table->decimal('angka_kredit', 5, 2)->nullable();
            $table->string('capaian_iku')->nullable();
            $table->text('catatan_katim')->nullable();
            $table->timestamp('tanggal_evaluasi_katim')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kinerja');
    }
};
