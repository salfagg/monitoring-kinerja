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
        Schema::table('laporan_kinerja', function (Blueprint $table) {
            $table->foreignId('realisasi_pekerjaan_id')->nullable()->after('pekerjaan_id')->constrained('realisasi_pekerjaan')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_kinerja', function (Blueprint $table) {
            $table->dropForeign(['realisasi_pekerjaan_id']);
            $table->dropColumn('realisasi_pekerjaan_id');
        });
    }
};

