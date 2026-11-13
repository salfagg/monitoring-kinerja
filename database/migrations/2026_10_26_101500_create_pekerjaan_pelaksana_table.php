<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Multi-Assignee Pelaksana Pekerjaan.
     */
    public function up(): void
    {
        Schema::create('pekerjaan_pelaksana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('peran')->default('anggota'); // 'pic_utama' atau 'anggota'
            $table->timestamps();

            $table->unique(['pekerjaan_id', 'user_id']);
        });

        // Seed data lama dari penanggung_jawab_id jika tabel pekerjaan sudah berisi
        $jobs = DB::table('pekerjaan')->whereNotNull('penanggung_jawab_id')->get(['id', 'penanggung_jawab_id', 'created_at', 'updated_at']);
        $now = now();
        $inserts = [];
        foreach ($jobs as $job) {
            $inserts[] = [
                'pekerjaan_id' => $job->id,
                'user_id' => $job->penanggung_jawab_id,
                'peran' => 'pic_utama',
                'created_at' => $job->created_at ?? $now,
                'updated_at' => $job->updated_at ?? $now,
            ];
        }
        if (!empty($inserts)) {
            DB::table('pekerjaan_pelaksana')->insertOrIgnore($inserts);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pekerjaan_pelaksana');
    }
};
