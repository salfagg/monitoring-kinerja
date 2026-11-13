<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for PRD Hierarki Pekerjaan.
     */
    public function up(): void
    {
        Schema::create('pekerjaan', function (Blueprint $table) {
            $table->id();
            
            // Relasi Self-Referencing Hierarki Induk & Anak
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('pekerjaan')
                ->nullOnDelete();

            // Kode Tampilan Hierarkis (contoh: 1, 1.1, 1.1.1)
            $table->string('kode_tampilan')->nullable();
            
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->text('hasil_diharapkan');
            
            $table->decimal('target', 8, 2)->default(1);
            $table->string('satuan')->default('Kegiatan');
            $table->date('tenggat');
            
            // Aktor & Organisasi
            $table->foreignId('pembuat_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penanggung_jawab_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pokja_id')->nullable()->constrained('pokjas')->nullOnDelete();
            
            // Bobot Relatif terhadap saudara (siblings) di bawah induk yang sama (total 100%)
            $table->decimal('bobot', 5, 2)->default(100.00);
            
            // Progres Capaian (0.00 - 100.00%)
            // Untuk induk: dihitung otomatis dari rata-rata berbobot anak aktif
            $table->decimal('progres', 5, 2)->default(0.00);
            
            // 1. Status Persetujuan Rincian Struktur (PRD Bagian 7)
            $table->enum('status_persetujuan', [
                'draft',
                'diajukan',
                'perlu_perbaikan',
                'disetujui',
                'dibatalkan'
            ])->default('draft');
            $table->text('catatan_persetujuan')->nullable();
            
            // 2. Status Pelaksanaan & Hasil (PRD Bagian 7)
            $table->enum('status_pelaksanaan', [
                'belum_dimulai',
                'dikerjakan',
                'hasil_diajukan',
                'perlu_perbaikan_hasil',
                'selesai_terverifikasi'
            ])->default('belum_dimulai');
            $table->text('catatan_hasil')->nullable();
            $table->timestamp('tanggal_selesai_terverifikasi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pekerjaan');
    }
};
