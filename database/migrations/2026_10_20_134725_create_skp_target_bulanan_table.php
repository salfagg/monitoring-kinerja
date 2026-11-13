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
        Schema::create('skp_target_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_tahunan_id')->constrained('skp_tahunan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan'); // 1 - 12
            $table->integer('tahun');
            $table->text('uraian_target');
            $table->decimal('target_kuantitas', 8, 2)->default(1);
            $table->string('satuan');
            $table->enum('status', ['draft', 'aktif', 'tercapai'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_target_bulanan');
    }
};
