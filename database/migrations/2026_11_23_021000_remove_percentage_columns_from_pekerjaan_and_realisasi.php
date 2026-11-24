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
            if (Schema::hasColumn('realisasi_pekerjaan', 'progres_capaian')) {
                $table->dropColumn('progres_capaian');
            }
        });

        Schema::table('pekerjaan', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('pekerjaan', 'progres')) {
                $cols[] = 'progres';
            }
            if (Schema::hasColumn('pekerjaan', 'bobot')) {
                $cols[] = 'bobot';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_pekerjaan', function (Blueprint $table) {
            $table->decimal('progres_capaian', 5, 2)->default(0.00)->after('jenis_laporan');
        });

        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->decimal('bobot', 5, 2)->default(100.00)->after('status_persetujuan');
            $table->decimal('progres', 5, 2)->default(0.00)->after('bobot');
        });
    }
};
