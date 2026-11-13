<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPersetujuanPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_persetujuan_pekerjaan';

    protected $fillable = [
        'pekerjaan_id',
        'user_id',
        'jenis_aksi',
        'status_sebelumnya',
        'status_baru',
        'catatan',
    ];

    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
