<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKonsolidasiBgk extends Model
{
    use HasFactory;

    protected $table = 'laporan_konsolidasi_bgk';

    protected $fillable = [
        'bulan',
        'tahun',
        'judul_laporan',
        'kasubag_id',
        'total_pegawai',
        'total_laporan_selesai',
        'rata_rata_capaian',
        'catatan_kedisiplinan',
        'ringkasan_kinerja',
        'file_draft_laporan',
        'ttd_kasubag',
        'tanggal_ttd_kasubag',
        'kabal_id',
        'status_kabal',
        'tanggal_approval_kabal',
        'catatan_evaluasi_kabal',
        'rekomendasi_reward',
        'rekomendasi_punishment',
        'rekomendasi_pengembangan_kompetensi',
        'file_laporan_final',
    ];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
            'total_pegawai' => 'integer',
            'total_laporan_selesai' => 'integer',
            'rata_rata_capaian' => 'decimal:2',
            'ttd_kasubag' => 'boolean',
            'tanggal_ttd_kasubag' => 'datetime',
            'tanggal_approval_kabal' => 'datetime',
        ];
    }

    public function kasubag(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasubag_id');
    }

    public function kabal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kabal_id');
    }
}
