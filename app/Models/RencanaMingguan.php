<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RencanaMingguan extends Model
{
    use HasFactory;

    protected $table = 'rencana_mingguan';

    protected $fillable = [
        'user_id',
        'skp_target_bulanan_id',
        'tahun',
        'bulan',
        'minggu_ke',
        'tanggal_mulai',
        'tanggal_selesai',
        'rencana_aksi',
        'target_output',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'bulan' => 'integer',
            'minggu_ke' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetBulanan(): BelongsTo
    {
        return $this->belongsTo(SkpTargetBulanan::class, 'skp_target_bulanan_id');
    }

    public function laporanKinerja(): HasMany
    {
        return $this->hasMany(LaporanKinerja::class, 'rencana_mingguan_id');
    }
}
