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
            $table->foreignId('pekerjaan_id')->nullable()->after('pokja_id')->constrained('pekerjaan')->nullOnDelete();
            $table->foreignId('skp_tahunan_id')->nullable()->after('pekerjaan_id')->constrained('skp_tahunan')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_kinerja', function (Blueprint $table) {
            $table->dropForeign(['pekerjaan_id']);
            $table->dropColumn('pekerjaan_id');
            $table->dropForeign(['skp_tahunan_id']);
            $table->dropColumn('skp_tahunan_id');
        });
    }
};

