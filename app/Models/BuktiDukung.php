<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiDukung extends Model
{
    use HasFactory;

    protected $table = 'bukti_dukung';

    protected $fillable = [
        'laporan_kinerja_id',
        'nama_dokumen',
        'tipe',
        'file_path',
        'link_url',
        'file_size',
        'file_type',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function laporanKinerja(): BelongsTo
    {
        return $this->belongsTo(LaporanKinerja::class, 'laporan_kinerja_id');
    }
}
