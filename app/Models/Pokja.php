<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pokja extends Model
{
    use HasFactory;

    protected $table = 'pokjas';

    protected $fillable = [
        'nama_pokja',
        'kode_pokja',
        'tipe',
        'deskripsi',
        'katim_id',
    ];

    public function katim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'katim_id');
    }

    public function anggota(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pokja_user')->withPivot('peran')->withTimestamps();
    }

    public function anggotaHomebase(): HasMany
    {
        return $this->hasMany(User::class, 'pokja_id');
    }

    public function isKelompokKerja(): bool
    {
        return $this->tipe === 'kelompok_kerja';
    }

    public function isTimKerja(): bool
    {
        return $this->tipe !== 'kelompok_kerja';
    }

    public function laporanKinerja(): HasMany
    {
        return $this->hasMany(LaporanKinerja::class, 'pokja_id');
    }

    public function pekerjaan(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'pokja_id');
    }
}
