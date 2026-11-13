<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanKinerja extends Model
{
    use HasFactory;

    protected $table = 'laporan_kinerja';

    protected $fillable = [
        'user_id',
        'pokja_id',
        'pekerjaan_id',
        'skp_tahunan_id',
        'skp_target_bulanan_id',
        'rencana_mingguan_id',
        'tanggal_kegiatan',
        'jam_mulai',
        'jam_selesai',
        'bulan',
        'tahun',
        'uraian_kegiatan',
        'output_kegiatan',
        'volume_capaian',
        'satuan',
        'status',
        'verifikator_pokja_id',
        'catatan_pokja',
        'tanggal_verifikasi_pokja',
        'katim_id',
        'nilai_kualitas_katim',
        'angka_kredit',
        'capaian_iku',
        'catatan_katim',
        'tanggal_evaluasi_katim',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
            'bulan' => 'integer',
            'tahun' => 'integer',
            'volume_capaian' => 'decimal:2',
            'nilai_kualitas_katim' => 'decimal:2',
            'angka_kredit' => 'decimal:2',
            'tanggal_verifikasi_pokja' => 'datetime',
            'tanggal_evaluasi_katim' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pokja(): BelongsTo
    {
        return $this->belongsTo(Pokja::class);
    }

    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }

    public function skpTahunan(): BelongsTo
    {
        return $this->belongsTo(SkpTahunan::class, 'skp_tahunan_id');
    }

    public function targetBulanan(): BelongsTo
    {
        return $this->belongsTo(SkpTargetBulanan::class, 'skp_target_bulanan_id');
    }

    public function rencanaMingguan(): BelongsTo
    {
        return $this->belongsTo(RencanaMingguan::class, 'rencana_mingguan_id');
    }

    public function verifikatorPokja(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_pokja_id');
    }

    public function katim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'katim_id');
    }

    public function buktiDukung(): HasMany
    {
        return $this->hasMany(BuktiDukung::class, 'laporan_kinerja_id');
    }

    public function riwayatVerifikasi(): HasMany
    {
        return $this->hasMany(RiwayatVerifikasi::class, 'laporan_kinerja_id');
    }

    public function getDurasiFormatAttribute(): string
    {
        if (!$this->jam_mulai || !$this->jam_selesai) {
            return '-';
        }
        $start = \Carbon\Carbon::parse($this->jam_mulai);
        $end = \Carbon\Carbon::parse($this->jam_selesai);
        $diffMin = $start->diffInMinutes($end);
        $hours = floor($diffMin / 60);
        $mins = $diffMin % 60;
        if ($hours > 0 && $mins > 0) {
            return "{$hours} jam {$mins} mnt";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        }
        return "{$mins} mnt";
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'revisi_pokja' => 'Revisi Pokja',
            'disetujui_pokja' => 'Diverifikasi Pokja',
            'revisi_katim' => 'Revisi Katim',
            'disetujui_katim' => 'Disetujui Katim',
            'dikonsolidasi_kasubag' => 'Konsolidasi Kasubag',
            'disetujui_kabal' => 'Disetujui Kabal',
            'ditolak_kabal' => 'Ditolak Kabal',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
