<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class RealisasiPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'realisasi_pekerjaan';

    protected static function booted(): void
    {
        static::created(function (RealisasiPekerjaan $realisasi) {
            $realisasi->syncToLogHarian();
        });

        static::updated(function (RealisasiPekerjaan $realisasi) {
            $realisasi->syncToLogHarian();
        });
    }

    public function syncToLogHarian(): void
    {
        $pekerjaan = $this->pekerjaan;
        $tanggal = Carbon::parse($this->tanggal);

        $statusLog = match ($this->status_approval) {
            'disetujui' => 'disetujui_katim',
            'ditolak' => 'revisi_katim',
            default => 'diajukan',
        };

        $kodePekerjaan = $pekerjaan ? $pekerjaan->kode_tampilan : 'TUGAS';
        $judulPekerjaan = $pekerjaan ? $pekerjaan->judul : 'Pekerjaan';

        $log = LogHarian::updateOrCreate(
            ['realisasi_pekerjaan_id' => $this->id],
            [
                'user_id' => $this->user_id,
                'pokja_id' => $pekerjaan?->pokja_id ?? $this->user?->pokja_id,
                'pekerjaan_id' => $this->pekerjaan_id,
                'skp_tahunan_id' => $pekerjaan?->skp_tahunan_id,
                'tanggal_kegiatan' => $tanggal->format('Y-m-d'),
                'bulan' => (int) $tanggal->format('m'),
                'tahun' => (int) $tanggal->format('Y'),
                'uraian_kegiatan' => "[{$kodePekerjaan}] " . $this->deskripsi_progres,
                'output_kegiatan' => "Progres +{$this->progres_capaian}% ({$judulPekerjaan})",
                'volume_capaian' => $this->progres_capaian,
                'satuan' => '%',
                'status' => $statusLog,
                'katim_id' => $this->approved_by,
                'catatan_katim' => $this->catatan_approval,
                'tanggal_evaluasi_katim' => $this->approved_at,
            ]
        );

        // Sinkronkan bukti dukung jika ada
        if ($this->file_bukti && !$log->buktiDukung()->where('file_path', $this->file_bukti)->exists()) {
            BuktiDukung::create([
                'laporan_kinerja_id' => $log->id,
                'nama_dokumen' => $this->nama_bukti ?: 'Bukti Progres ' . $kodePekerjaan,
                'tipe' => 'file',
                'file_path' => $this->file_bukti,
            ]);
        } elseif ($this->link_bukti && !$log->buktiDukung()->where('link_url', $this->link_bukti)->exists()) {
            BuktiDukung::create([
                'laporan_kinerja_id' => $log->id,
                'nama_dokumen' => $this->nama_bukti ?: 'Tautan Bukti Progres',
                'tipe' => 'link',
                'link_url' => $this->link_bukti,
            ]);
        }
    }

    protected $fillable = [
        'pekerjaan_id',
        'user_id',
        'tanggal',
        'progres_capaian',
        'status_approval',
        'approved_by',
        'approved_at',
        'catatan_approval',
        'deskripsi_progres',
        'kendala',
        'tindak_lanjut',
        'nama_bukti',
        'file_bukti',
        'link_bukti',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'progres_capaian' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->status_approval === 'disetujui';
    }

    public function isPending(): bool
    {
        return $this->status_approval === 'menunggu_persetujuan';
    }

    public function isDitolak(): bool
    {
        return $this->status_approval === 'ditolak';
    }

    public function logHarian(): HasOne
    {
        return $this->hasOne(LogHarian::class, 'realisasi_pekerjaan_id');
    }
}
