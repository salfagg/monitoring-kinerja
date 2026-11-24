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
        return $this->sumber_pekerjaan === 'skp' || ! is_null($this->skp_tahunan_id) || ! is_null($this->skp_target_bulanan_id);
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
     * Selaraskan status pekerjaan induk secara otomatis dari status anak-anak yang disetujui.
     * Jika semua anak tuntas, induk otomatis tuntas terverifikasi.
     */
    public function recalculateProgresFromChildren(): void
    {
        $activeChildren = $this->children()
            ->where('status_persetujuan', 'disetujui')
            ->get();

        if ($activeChildren->isNotEmpty()) {
            // Update status pelaksanaan induk: aktif jika ada anak yang mulai dikerjakan
            $anyActive = $activeChildren->contains(fn ($c) => in_array($c->status_pelaksanaan, ['dikerjakan', 'hasil_diajukan', 'selesai_terverifikasi']));
            if ($anyActive && $this->status_pelaksanaan === 'belum_dimulai') {
                $this->status_pelaksanaan = 'dikerjakan';
            }

            // Jika semua anak sudah selesai terverifikasi, tandai pelaksanaan induk selesai
            $allDone = $activeChildren->every(fn ($c) => $c->status_pelaksanaan === 'selesai_terverifikasi');
            if ($allDone && $this->status_pelaksanaan !== 'selesai_terverifikasi') {
                $this->status_pelaksanaan = 'selesai_terverifikasi';
                $this->tanggal_selesai_terverifikasi = now();
            } elseif (! $allDone && $this->status_pelaksanaan === 'selesai_terverifikasi') {
                $this->status_pelaksanaan = 'dikerjakan';
                $this->tanggal_selesai_terverifikasi = null;
            }

            $this->saveQuietly();
        }

        // Rekursif ke induk di atasnya
        if ($this->parent) {
            $this->parent->recalculateProgresFromChildren();
        }
    }

    /**
     * Hitung ulang status pekerjaan dari realisasi pelaksana yang telah disetujui oleh pembuat tugas
     */
    public function recalculateProgresFromRealisasi(): void
    {
        $hasApprovedPenyelesaian = $this->realisasi()
            ->where('status_approval', 'disetujui')
            ->where('jenis_laporan', 'penyelesaian')
            ->exists();

        $hasApprovedKemajuan = $this->realisasi()
            ->where('status_approval', 'disetujui')
            ->exists();

        if ($hasApprovedPenyelesaian) {
            $this->status_pelaksanaan = 'selesai_terverifikasi';
            if (! $this->tanggal_selesai_terverifikasi) {
                $this->tanggal_selesai_terverifikasi = now();
            }
        } elseif ($hasApprovedKemajuan && in_array($this->status_pelaksanaan, ['belum_dimulai', 'perlu_perbaikan_hasil'])) {
            $this->status_pelaksanaan = 'dikerjakan';
        } elseif (! $hasApprovedPenyelesaian && $this->status_pelaksanaan === 'selesai_terverifikasi') {
            $this->status_pelaksanaan = 'dikerjakan';
            $this->tanggal_selesai_terverifikasi = null;
        }

        $this->saveQuietly();

        if ($this->parent) {
            $this->parent->recalculateProgresFromChildren();
        }
    }

    /**
     * Label resmi status pelaksanaan untuk antarmuka alur kerja.
     */
    public function getStatusPelaksanaanLabelAttribute(): string
    {
        return match ($this->status_pelaksanaan) {
            'selesai_terverifikasi' => 'Selesai Terverifikasi',
            'hasil_diajukan' => 'Menunggu Verifikasi',
            'dikerjakan' => 'Sedang Dikerjakan',
            'perlu_perbaikan_hasil' => 'Perlu Perbaikan',
            default => 'Belum Dimulai',
        };
    }
}
