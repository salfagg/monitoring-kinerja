<?php

namespace App\Services;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RealisasiPekerjaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardMetricService
{
    /**
     * Hitung ringkasan jumlah tugas berdasarkan status pelaksanaan.
     */
    public function getStatusCounts(): array
    {
        return [
            'selesai' => Pekerjaan::where('status_pelaksanaan', 'selesai_terverifikasi')->count(),
            'dikerjakan' => Pekerjaan::where('status_pelaksanaan', 'dikerjakan')->count(),
            'menunggu_verifikasi' => Pekerjaan::where('status_pelaksanaan', 'hasil_diajukan')->count(),
            'belum_dimulai' => Pekerjaan::where('status_pelaksanaan', 'belum_dimulai')->count(),
            'perlu_revisi' => Pekerjaan::where('status_pelaksanaan', 'perlu_perbaikan_hasil')->count(),
        ];
    }

    /**
     * Ambil data Pokja/Pilar untuk bar chart visualisasi kinerja.
     */
    public function getChartPokjas(): Collection
    {
        return Pokja::whereIn('tipe', ['tim_kerja', 'subbag_pilar'])
            ->with('pekerjaan')
            ->get()
            ->map(function ($p) {
                $totalTugas = $p->pekerjaan->count();
                $selesai = $p->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count();
                $ketuntasan = $totalTugas > 0 ? round(($selesai / $totalTugas) * 100, 1) : 0;

                return [
                    'id' => $p->id,
                    'kode' => $p->kode_pokja,
                    'nama' => $p->nama_pokja,
                    'total' => $totalTugas,
                    'selesai' => $selesai,
                    'progres' => $ketuntasan,
                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Ambil daftar beban kerja pegawai (ranking tugas).
     */
    public function getWorkloadList(int $limit = 50): Collection
    {
        return User::withCount('realisasiPekerjaan')
            ->with([
                'pokja',
                'pekerjaanSebagaiPj' => fn ($q) => $q->with(['pokja', 'realisasi.user'])->orderBy('tenggat', 'asc'),
                'pekerjaanDitugaskan' => fn ($q) => $q->with(['pokja', 'realisasi.user'])->orderBy('tenggat', 'asc'),
                'realisasiPekerjaan' => fn ($q) => $q->with(['pekerjaan.pokja'])->latest()->take(30),
            ])
            ->get()
            ->map(function ($u) {
                $picJobs = $u->pekerjaanSebagaiPj;
                $memberJobs = $u->pekerjaanDitugaskan;
                $allJobs = $picJobs->merge($memberJobs)->unique('id')->values();

                $activePic = $picJobs->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')->count();
                $activeMember = $memberJobs->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')->count();
                $totalActive = $activePic + $activeMember;

                $stJobs = $allJobs->filter(function ($job) {
                    $st = trim((string) $job->nomor_st);

                    return ! empty($st) && $st !== '-';
                });
                $stCount = $stJobs->count();
                $distinctStNumbers = $stJobs->map(fn ($job) => trim((string) $job->nomor_st))->unique()->values()->toArray();
                $totalLaporan = $u->realisasi_pekerjaan_count ?? $u->realisasiPekerjaan->count();

                $jobsPayload = $allJobs->map(function ($job) use ($u) {
                    $isPic = $job->penanggung_jawab_id === $u->id;
                    $isLate = $job->isTerlambat() && $job->status_pelaksanaan !== 'selesai_terverifikasi';
                    $nomorSt = trim((string) $job->nomor_st);
                    $hasSt = ! empty($nomorSt) && $nomorSt !== '-';

                    return [
                        'id' => $job->id,
                        'kode' => $job->kode_tampilan,
                        'judul' => $job->judul,
                        'nomor_st' => $hasSt ? $nomorSt : null,
                        'has_st' => $hasSt,
                        'peran_label' => $isPic ? 'PIC Utama' : 'Anggota Pelaksana',
                        'is_pic' => $isPic,
                        'pokja' => $job->pokja?->kode_pokja ?? 'BGTK',
                        'status_raw' => $job->status_pelaksanaan,
                        'status_label' => $job->status_pelaksanaan_label,
                        'tenggat' => $job->tenggat ? $job->tenggat->format('d/m/Y') : '-',
                        'is_terlambat' => $isLate,
                        'laporan_count' => $job->realisasi->count(),
                        'url_show' => route('pekerjaan.show', $job),
                    ];
                })->toArray();

                $laporanPayload = $u->realisasiPekerjaan->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'pekerjaan_id' => $r->pekerjaan_id,
                        'pekerjaan_kode' => $r->pekerjaan?->kode_tampilan ?? '-',
                        'pekerjaan_judul' => $r->pekerjaan?->judul ?? 'Tugas Tidak Ditemukan',
                        'tanggal' => $r->tanggal ? Carbon::parse($r->tanggal)->format('d M Y') : $r->created_at->format('d M Y'),
                        'jenis_laporan' => $r->jenis_laporan ?? 'kemajuan',
                        'is_penyelesaian' => $r->isPenyelesaian(),
                        'status_approval' => $r->status_approval,
                        'status_label' => match ($r->status_approval) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Perlu Perbaikan',
                            default => 'Menunggu Persetujuan'
                        },
                        'deskripsi' => $r->deskripsi_progres,
                        'kendala' => $r->kendala,
                        'tindak_lanjut' => $r->tindak_lanjut,
                        'catatan_penolakan' => $r->catatan_penolakan,
                        'file_bukti' => $r->file_bukti ? asset('storage/'.$r->file_bukti) : null,
                        'file_name' => $r->file_bukti ? basename($r->file_bukti) : null,
                        'link_bukti' => $r->link_bukti,
                    ];
                })->toArray();

                return (object) [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nip' => $u->nip ?? 'Non-PNS / Pegawai',
                    'jabatan' => $u->jabatan ?? $u->role_label,
                    'role' => $u->role,
                    'role_label' => $u->role_label,
                    'pokja_nama' => $u->pokja?->nama_pokja ?? 'Lintas Balai',
                    'pokja_kode' => $u->pokja?->kode_pokja ?? 'BGTK',
                    'pic_count' => $activePic,
                    'member_count' => $activeMember,
                    'total_active' => $totalActive,
                    'total_semua_tugas' => $allJobs->count(),
                    'tugas_selesai' => $allJobs->where('status_pelaksanaan', 'selesai_terverifikasi')->count(),
                    'st_count' => $stCount,
                    'st_distinct_count' => count($distinctStNumbers),
                    'st_numbers' => $distinctStNumbers,
                    'laporan_count' => $totalLaporan,
                    'jobs' => $jobsPayload,
                    'laporan' => $laporanPayload,
                ];
            })
            ->sortByDesc('total_active')
            ->values()
            ->take($limit);
    }

    /**
     * Ambil metrik dan data pelengkap untuk halaman Command Center / Monitoring Balai.
     */
    public function getCommandCenterData(Request $request): array
    {
        $statusCounts = $this->getStatusCounts();

        $totalPekerjaan = Pekerjaan::count();
        $pekerjaanBerjalan = Pekerjaan::where('status_persetujuan', 'disetujui')
            ->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil'])
            ->count();
        $sedangDikerjakan = $pekerjaanBerjalan;
        $menungguVerifikasi = $statusCounts['menunggu_verifikasi'];
        $selesaiTerverifikasi = $statusCounts['selesai'];
        $lewatTenggat = Pekerjaan::where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
            ->whereDate('tenggat', '<', Carbon::today())
            ->count();

        $tingkatKetuntasan = $totalPekerjaan > 0 ? round(($selesaiTerverifikasi / $totalPekerjaan) * 100, 1) : 0;
        $avgProgres = $tingkatKetuntasan;

        // Capaian Kinerja per Pokja
        $pokjas = Pokja::with([
            'katim',
            'anggota' => fn ($q) => $q->withCount([
                'pekerjaanSebagaiPj as pic_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                'pekerjaanDitugaskan as member_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
            ]),
            'pekerjaan' => fn ($q) => $q->select('id', 'pokja_id', 'status_pelaksanaan'),
        ])->orderBy('kode_pokja')->get()->map(function ($p) {
            $totalTugas = $p->pekerjaan->count();
            $selesai = $p->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count();
            $ketuntasanPokja = $totalTugas > 0 ? round(($selesai / $totalTugas) * 100, 1) : 0;
            $berjalan = $p->pekerjaan->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan'])->count();

            return (object) [
                'id' => $p->id,
                'kode_pokja' => $p->kode_pokja,
                'nama_pokja' => $p->nama_pokja,
                'katim_name' => $p->katim?->name ?? 'Belum Ditunjuk',
                'total_tugas' => $totalTugas,
                'avg_progres' => $ketuntasanPokja,
                'tugas_selesai' => $selesai,
                'tugas_berjalan' => $berjalan,
                'anggota' => $p->anggota,
                'anggota_count' => $p->anggota->count(),
            ];
        });

        // Data Pokja untuk Chart
        $pokjaChartData = $pokjas->filter(fn ($p) => $p->total_tugas > 0)
            ->sortByDesc('tugas_selesai')
            ->values()
            ->map(fn ($p) => [
                'kode' => $p->kode_pokja,
                'nama' => $p->nama_pokja,
                'total' => $p->total_tugas,
                'selesai' => $p->tugas_selesai,
                'berjalan' => $p->tugas_berjalan,
            ]);

        if ($pokjaChartData->isEmpty()) {
            $pokjaChartData = $pokjas->take(6)->values()->map(fn ($p) => [
                'kode' => $p->kode_pokja,
                'nama' => $p->nama_pokja,
                'total' => $p->total_tugas,
                'selesai' => $p->tugas_selesai,
                'berjalan' => $p->tugas_berjalan,
            ]);
        }

        // Top Assignees
        $topAssignees = $this->getWorkloadList(100);

        // Leaderboard Pegawai Paling Sering Ikut Kegiatan (Surat Tugas / ST)
        $topStPersonnel = $topAssignees->filter(fn ($u) => $u->st_count > 0)
            ->sortByDesc('st_count')
            ->values()
            ->take(8);

        if ($topStPersonnel->isEmpty()) {
            $topStPersonnel = $topAssignees->sortByDesc('total_semua_tugas')->values()->take(5);
        }

        // Leaderboard Pegawai Paling Aktif Melaporkan Progres Tugas
        $topReportPersonnel = $topAssignees->filter(fn ($u) => $u->laporan_count > 0)
            ->sortByDesc('laporan_count')
            ->values()
            ->take(8);

        if ($topReportPersonnel->isEmpty()) {
            $topReportPersonnel = $topAssignees->sortByDesc('total_active')->values()->take(5);
        }

        // Metrik Ringkas ST & Laporan Balai
        $totalKegiatanSt = Pekerjaan::whereNotNull('nomor_st')
            ->where('nomor_st', '!=', '')
            ->where('nomor_st', '!=', '-')
            ->count();

        $totalLaporanBalai = RealisasiPekerjaan::count();

        // Chart Data untuk Chart.js (ST & Laporan)
        $stChartData = $topAssignees->sortByDesc('st_count')
            ->take(8)
            ->values()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'short_name' => explode(' ', trim($u->name))[0].' '.(isset(explode(' ', trim($u->name))[1]) ? substr(explode(' ', trim($u->name))[1], 0, 1).'.' : ''),
                'pokja' => $u->pokja_kode,
                'st_count' => $u->st_count,
                'total_jobs' => $u->total_semua_tugas,
            ]);

        $reportChartData = $topAssignees->sortByDesc('laporan_count')
            ->take(8)
            ->values()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'short_name' => explode(' ', trim($u->name))[0].' '.(isset(explode(' ', trim($u->name))[1]) ? substr(explode(' ', trim($u->name))[1], 0, 1).'.' : ''),
                'pokja' => $u->pokja_kode,
                'laporan_count' => $u->laporan_count,
                'total_jobs' => $u->total_semua_tugas,
            ]);

        // Query Utama Pekerjaan
        $query = Pekerjaan::with([
            'penanggungJawab.pokja',
            'pelaksana.pokja',
            'pembuat',
            'pokja',
            'parent',
            'realisasi' => fn ($r) => $r->with('user')->latest(),
        ]);

        if ($request->filled('pokja_id')) {
            $query->where('pokja_id', $request->pokja_id);
        }

        if ($request->filled('status')) {
            $st = $request->status;
            if ($st === 'berjalan') {
                $query->where('status_persetujuan', 'disetujui')
                    ->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil']);
            } elseif ($st === 'menunggu_verifikasi') {
                $query->where('status_pelaksanaan', 'hasil_diajukan');
            } elseif ($st === 'selesai') {
                $query->where('status_pelaksanaan', 'selesai_terverifikasi');
            } elseif ($st === 'terlambat') {
                $query->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
                    ->whereDate('tenggat', '<', Carbon::today());
            }
        }

        if ($request->filled('q')) {
            $qText = $request->q;
            $query->where(function ($w) use ($qText) {
                $w->where('judul', 'like', "%{$qText}%")
                    ->orWhere('kode_tampilan', 'like', "%{$qText}%")
                    ->orWhere('nomor_st', 'like', "%{$qText}%")
                    ->orWhereHas('penanggungJawab', fn ($u) => $u->where('name', 'like', "%{$qText}%"))
                    ->orWhereHas('pelaksana', fn ($u) => $u->where('name', 'like', "%{$qText}%"));
            });
        }

        $pekerjaanList = $query->orderBy('tenggat', 'asc')->get();

        $allPokjas = Pokja::orderBy('kode_pokja')->get();

        return compact(
            'totalPekerjaan',
            'pekerjaanBerjalan',
            'sedangDikerjakan',
            'menungguVerifikasi',
            'selesaiTerverifikasi',
            'lewatTenggat',
            'avgProgres',
            'tingkatKetuntasan',
            'pokjas',
            'pokjaChartData',
            'statusCounts',
            'topAssignees',
            'topStPersonnel',
            'topReportPersonnel',
            'totalKegiatanSt',
            'totalLaporanBalai',
            'stChartData',
            'reportChartData',
            'allPokjas',
            'pekerjaanList'
        );
    }
}
