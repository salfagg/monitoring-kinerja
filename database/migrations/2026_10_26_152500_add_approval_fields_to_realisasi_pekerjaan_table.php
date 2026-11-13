<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('realisasi_pekerjaan', function (Blueprint $table) {
            $table->string('status_approval')->default('menunggu_persetujuan')->after('progres_capaian');
            $table->foreignId('approved_by')->nullable()->after('status_approval')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('catatan_approval')->nullable()->after('approved_at');
        });

        // Untuk data realisasi yang sudah ada sebelumnya, tandai sebagai disetujui agar progres eksisting tetap valid
        DB::table('realisasi_pekerjaan')->update([
            'status_approval' => 'disetujui',
            'approved_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_pekerjaan', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status_approval', 'approved_by', 'approved_at', 'catatan_approval']);
        });
    }
};
