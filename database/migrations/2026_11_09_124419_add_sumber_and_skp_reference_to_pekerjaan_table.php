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
            // Sumber pekerjaan: 'manual' (input bebas) atau 'skp' (terhubung ke SKP)
            $table->string('sumber_pekerjaan', 20)->default('manual')->after('kode_tampilan');
            
            // Referensi ke RHK / SKP Tahunan
            $table->foreignId('skp_tahunan_id')
                ->nullable()
                ->after('sumber_pekerjaan')
                ->constrained('skp_tahunan')
                ->nullOnDelete();

            // Referensi opsional ke Target Bulanan SKP
            $table->foreignId('skp_target_bulanan_id')
                ->nullable()
                ->after('skp_tahunan_id')
                ->constrained('skp_target_bulanan')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->dropForeign(['skp_target_bulanan_id']);
            $table->dropForeign(['skp_tahunan_id']);
            $table->dropColumn(['sumber_pekerjaan', 'skp_tahunan_id', 'skp_target_bulanan_id']);
        });
    }
};
