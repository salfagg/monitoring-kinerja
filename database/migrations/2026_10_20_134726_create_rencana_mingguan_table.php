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
        Schema::create('rencana_mingguan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('skp_target_bulanan_id')->nullable()->constrained('skp_target_bulanan')->nullOnDelete();
            $table->integer('tahun');
            $table->unsignedTinyInteger('bulan'); // 1 - 12
            $table->unsignedTinyInteger('minggu_ke'); // 1 - 5
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('rencana_aksi');
            $table->string('target_output');
            $table->enum('status', ['rencana', 'berjalan', 'selesai'])->default('rencana');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_mingguan');
    }
};
