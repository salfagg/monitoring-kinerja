<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'riwayat_verifikasi';

    protected $fillable = [
        'laporan_kinerja_id',
        'user_id',
        'role_verifikator',
        'status_sebelumnya',
        'status_baru',
        'aksi',
        'catatan',
    ];

    public function laporanKinerja(): BelongsTo
    {
        return $this->belongsTo(LaporanKinerja::class, 'laporan_kinerja_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
