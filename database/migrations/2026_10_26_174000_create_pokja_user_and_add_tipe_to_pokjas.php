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
        // 1. Tambah kolom tipe ke tabel pokjas jika belum ada
        if (!Schema::hasColumn('pokjas', 'tipe')) {
            Schema::table('pokjas', function (Blueprint $table) {
                $table->string('tipe')->default('tim_kerja')->after('kode_pokja'); // 'tim_kerja' atau 'kelompok_kerja'
            });
        }

        // 2. Buat tabel pivot pokja_user untuk mendukung multiple keanggotaan
        if (!Schema::hasTable('pokja_user')) {
            Schema::create('pokja_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pokja_id')->constrained('pokjas')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('peran')->default('anggota'); // 'ketua', 'anggota'
                $table->timestamps();

                $table->unique(['pokja_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokja_user');

        if (Schema::hasColumn('pokjas', 'tipe')) {
            Schema::table('pokjas', function (Blueprint $table) {
                $table->dropColumn('tipe');
            });
        }
    }
};
