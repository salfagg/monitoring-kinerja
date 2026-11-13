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
        Schema::create('bukti_dukung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_kinerja_id')->constrained('laporan_kinerja')->cascadeOnDelete();
            $table->string('nama_dokumen');
            $table->enum('tipe', ['file', 'link'])->default('file');
            $table->string('file_path')->nullable();
            $table->text('link_url')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->string('file_type')->nullable(); // pdf, jpg, docx, etc.
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_dukung');
    }
};
