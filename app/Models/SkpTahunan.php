<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkpTahunan extends Model
{
    use HasFactory;

    protected $table = 'skp_tahunan';

    protected $fillable = [
        'user_id',
        'tahun',
        'rencana_kinerja',
        'aspek',
        'indikator_kinerja',
        'target_min',
        'target_max',
        'satuan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'target_min' => 'decimal:2',
            'target_max' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetBulanan(): HasMany
    {
        return $this->hasMany(SkpTargetBulanan::class, 'skp_tahunan_id');
    }

    public function pekerjaan(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'skp_tahunan_id');
    }
}
