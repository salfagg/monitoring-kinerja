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
        Schema::create('pokjas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pokja');
            $table->string('kode_pokja')->unique();
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('katim_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokjas');
    }
};
