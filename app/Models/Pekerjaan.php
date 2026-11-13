<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pekerjaan extends Model
{
    use HasFactory;

    protected $table = 'pekerjaan';

    protected $fillable = [
        'parent_id',
        'kode_tampilan',
        'sumber_pekerjaan',
        'skp_tahunan_id',
        'skp_target_bulanan_id',
        'judul',
        'deskripsi',
        'hasil_diharapkan',
        'target',
        'satuan',
        'tenggat',
        'nomor_st',
        'tanggal_st',
        'file_st',
        'pembuat_id',
        'penanggung_jawab_id',
        'pokja_id',
        'bobot',
        'progres',
        'status_persetujuan',
        'catatan_persetujuan',
        'status_pelaksanaan',
        'catatan_hasil',
        'tanggal_selesai_terverifikasi',
    ];

    protected function casts(): array
    {
        return [
            'target' => 'decimal:2',
            'bobot' => 'decimal:2',
            'progres' => 'decimal:2',
            'tenggat' => 'date',
            'tanggal_st' => 'date',
            'tanggal_selesai_terverifikasi' => 'datetime',
        ];
    }

    // Relasi Hierarki
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'parent_id')->orderBy('kode_tampilan');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    // Relasi Pengguna & Tim
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function penanggungJawab(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    public function pelaksana(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pekerjaan_pelaksana', 'pekerjaan_id', 'user_id')
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function pokja(): BelongsTo
    {
        return $this->belongsTo(Pokja::class, 'pokja_id');
    }

    // Relasi Rujukan SKP (Sasaran Kinerja Pegawai)
    public function skpTahunan(): BelongsTo
    {
        return $this->belongsTo(SkpTahunan::class, 'skp_tahunan_id');
    }

    public function skpTargetBulanan(): BelongsTo
    {
        return $this->belongsTo(SkpTargetBulanan::class, 'skp_target_bulanan_id');
    }

    public function isFromSkp(): bool
    {
        return $this->sumber_pekerjaan === 'skp' || !is_null($this->skp_tahunan_id) || !is_null($this->skp_target_bulanan_id);
    }

    // Realisasi & Riwayat
    public function realisasi(): HasMany
    {
        return $this->hasMany(RealisasiPekerjaan::class, 'pekerjaan_id')->latest();
    }

    public function riwayatPersetujuan(): HasMany
    {
        return $this->hasMany(RiwayatPersetujuanPekerjaan::class, 'pekerjaan_id')->latest();
    }

    public function laporanKinerja(): HasMany
    {
        return $this->hasMany(LaporanKinerja::class, 'pekerjaan_id')->latest('tanggal_kegiatan');
    }

    public function logHarian(): HasMany
    {
        return $this->hasMany(LogHarian::class, 'pekerjaan_id')->latest('tanggal_kegiatan');
    }

    // Helper Status & Hierarki
    public function isMainJob(): bool
    {
        return is_null($this->parent_id);
    }

    public function isLeafNode(): bool
    {
        return $this->children()->count() === 0;
    }

    public function isDisetujui(): bool
    {
        return $this->status_persetujuan === 'disetujui';
    }

    public function isSelesai(): bool
    {
        return $this->status_pelaksanaan === 'selesai_terverifikasi';
    }

    public function isTerlambat(): bool
    {
        return ! $this->isSelesai() && $this->tenggat->isPast();
    }

    /**
     * Hitung ulang progres induk secara otomatis dari bobot anak-anak yang berstatus disetujui
     * (Sesuai Aturan PRD Bagian 9)
     */
    public function recalculateProgresFromChildren(): void
    {
        $activeChildren = $this->children()
            ->where('status_persetujuan', 'disetujui')
            ->get();

        if ($activeChildren->isNotEmpty()) {
            $totalBobot = $activeChildren->sum('bobot');
            if ($totalBobot > 0) {
                $weightedSum = $activeChildren->sum(function ($child) {
                    return ($child->bobot * $child->progres);
                });
                $this->progres = round($weightedSum / $totalBobot, 2);
            } else {
                $this->progres = round($activeChildren->avg('progres'), 2);
            }

            // Update status pelaksanaan induk
            if ($this->progres > 0 && $this->status_pelaksanaan === 'belum_dimulai') {
                $this->status_pelaksanaan = 'dikerjakan';
            }

            // Jika semua anak sudah selesai terverifikasi, tandai pelaksanaan induk
            $allDone = $activeChildren->every(fn($c) => $c->status_pelaksanaan === 'selesai_terverifikasi');
            if ($allDone && $this->status_pelaksanaan !== 'selesai_terverifikasi') {
                $this->status_pelaksanaan = 'hasil_diajukan';
            }

            $this->saveQuietly();
        }

        // Rekursif ke induk di atasnya
        if ($this->parent) {
            $this->parent->recalculateProgresFromChildren();
        }
    }

    /**
     * Hitung ulang progres pekerjaan dari akumulasi realisasi pelaksana yang telah disetujui oleh pembuat tugas
     */
    public function recalculateProgresFromRealisasi(): void
    {
        $totalCapaian = (float) $this->realisasi()
            ->where('status_approval', 'disetujui')
            ->sum('progres_capaian');

        $this->progres = min(100, round($totalCapaian, 2));

        if ($this->progres > 0 && in_array($this->status_pelaksanaan, ['belum_dimulai', 'perlu_perbaikan_hasil'])) {
            $this->status_pelaksanaan = 'dikerjakan';
        }

        if ($this->progres >= 100.00) {
            $this->status_pelaksanaan = 'selesai_terverifikasi';
            if (! $this->tanggal_selesai_terverifikasi) {
                $this->tanggal_selesai_terverifikasi = now();
            }
        } elseif ($this->progres < 100.00 && $this->status_pelaksanaan === 'selesai_terverifikasi') {
            $this->status_pelaksanaan = 'dikerjakan';
            $this->tanggal_selesai_terverifikasi = null;
        }

        $this->saveQuietly();
    }
}
