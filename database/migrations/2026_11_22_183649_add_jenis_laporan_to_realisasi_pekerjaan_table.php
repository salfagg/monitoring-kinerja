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
        Schema::table('realisasi_pekerjaan', function (Blueprint $table) {
            $table->string('jenis_laporan')->default('kemajuan')->after('user_id');
            $table->decimal('progres_capaian', 5, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_pekerjaan', function (Blueprint $table) {
            $table->dropColumn('jenis_laporan');
        });
    }
};
