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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->unique()->after('id');
            $table->string('role')->default('pegawai')->after('password'); // pegawai, tim_pokja, katim_korjafung, kasubag, kabal, admin
            $table->string('jabatan')->nullable()->after('role');
            $table->foreignId('pokja_id')->nullable()->after('jabatan')->constrained('pokjas')->nullOnDelete();
            $table->string('nomor_hp')->nullable()->after('pokja_id');
            $table->string('foto')->nullable()->after('nomor_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pokja_id']);
            $table->dropColumn(['nip', 'role', 'jabatan', 'pokja_id', 'nomor_hp', 'foto']);
        });
    }
};
