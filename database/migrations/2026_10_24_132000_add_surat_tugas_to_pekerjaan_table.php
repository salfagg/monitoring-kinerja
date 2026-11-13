<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Surat Tugas penugasan pekerjaan.
     */
    public function up(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->string('nomor_st')->nullable()->after('tenggat');
            $table->date('tanggal_st')->nullable()->after('nomor_st');
            $table->string('file_st')->nullable()->after('tanggal_st');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->dropColumn(['nomor_st', 'tanggal_st', 'file_st']);
        });
    }
};
