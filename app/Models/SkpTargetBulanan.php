<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkpTargetBulanan extends Model
{
    use HasFactory;

    protected $table = 'skp_target_bulanan';

    protected $fillable = [
        'skp_tahunan_id',
        'user_id',
        'bulan',
        'tahun',
        'uraian_target',
        'target_kuantitas',
        'satuan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
            'target_kuantitas' => 'decimal:2',
        ];
    }

    public function skpTahunan(): BelongsTo
    {
        return $this->belongsTo(SkpTahunan::class, 'skp_tahunan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rencanaMingguan(): HasMany
    {
        return $this->hasMany(RencanaMingguan::class, 'skp_target_bulanan_id');
    }

    public function laporanKinerja(): HasMany
    {
        return $this->hasMany(LaporanKinerja::class, 'skp_target_bulanan_id');
    }

    public function pekerjaan(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'skp_target_bulanan_id');
    }
}
