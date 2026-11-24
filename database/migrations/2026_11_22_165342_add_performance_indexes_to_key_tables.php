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
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->index('status_pelaksanaan', 'idx_pekerjaan_status_pelaksanaan');
            $table->index('status_persetujuan', 'idx_pekerjaan_status_persetujuan');
            $table->index('nomor_st', 'idx_pekerjaan_nomor_st');
            $table->index('tenggat', 'idx_pekerjaan_tenggat');
        });

        Schema::table('realisasi_pekerjaan', function (Blueprint $table) {
            $table->index('status_approval', 'idx_realisasi_status_approval');
            $table->index('tanggal', 'idx_realisasi_tanggal');
        });

        Schema::table('laporan_kinerja', function (Blueprint $table) {
            $table->index('status', 'idx_laporan_status');
            $table->index('tanggal_kegiatan', 'idx_laporan_tanggal_kegiatan');
            $table->index(['bulan', 'tahun'], 'idx_laporan_bulan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->dropIndex('idx_pekerjaan_status_pelaksanaan');
            $table->dropIndex('idx_pekerjaan_status_persetujuan');
            $table->dropIndex('idx_pekerjaan_nomor_st');
            $table->dropIndex('idx_pekerjaan_tenggat');
        });

        Schema::table('realisasi_pekerjaan', function (Blueprint $table) {
            $table->dropIndex('idx_realisasi_status_approval');
            $table->dropIndex('idx_realisasi_tanggal');
        });

        Schema::table('laporan_kinerja', function (Blueprint $table) {
            $table->dropIndex('idx_laporan_status');
            $table->dropIndex('idx_laporan_tanggal_kegiatan');
            $table->dropIndex('idx_laporan_bulan_tahun');
        });
    }
};
