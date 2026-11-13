<?php

namespace App\Http\Controllers;

use App\Models\LaporanKinerja;
use App\Models\LaporanKonsolidasiBgk;
use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RencanaMingguan;
use App\Models\SkpTahunan;
use App\Models\SkpTargetBulanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user()->load('pokja');
        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = (int) Carbon::now()->format('Y');

        $tugasAktifSaya = Pekerjaan::where(function ($q) use ($user) {
            $q->where('penanggung_jawab_id', $user->id)
              ->orWhere('pembuat_id', $user->id)
              ->orWhereHas('pelaksana', fn ($sub) => $sub->where('users.id', $user->id));
        })->where('status_persetujuan', 'disetujui')
          ->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil'])
          ->with(['parent', 'pokja'])
          ->orderBy('tenggat')
          ->take(5)
          ->get();

        $data = [
            'user' => $user,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'tugasAktifSaya' => $tugasAktifSaya,
        ];

        switch ($user->role) {
            case 'admin':
                $data['totalUsers'] = User::count();
                $data['totalPokjas'] = Pokja::count();
                $data['totalLaporan'] = LaporanKinerja::count();
                $data['recentUsers'] = User::with('pokja')->latest()->take(5)->get();
                $data['pokjas'] = Pokja::with([
                    'katim',
                    'anggota' => fn ($q) => $q->withCount([
                        'pekerjaanSebagaiPj as active_pic_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                        'pekerjaanDitugaskan as active_member_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
                    ]),
                    'pekerjaan'
                ])->withCount('anggota')->orderBy('id')->get();
                $data['bebanKerjaPegawai'] = $this->getWorkloadList();
                break;

            case 'kabal':
                $data['totalPegawai'] = User::count();
                $data['totalPokjas'] = Pokja::count();
                $data['konsolidasiBulanIni'] = LaporanKonsolidasiBgk::where('tahun', $currentYear)
                    ->where('bulan', $currentMonth)
                    ->first();
                $data['laporanSelesaiCount'] = LaporanKinerja::whereIn('status', ['disetujui_katim', 'dikonsolidasi_kasubag', 'disetujui_kabal'])->count();
                $data['laporanAntreanApproval'] = LaporanKonsolidasiBgk::where('status_kabal', 'diajukan')->get();
                $data['pokjas'] = Pokja::with([
                    'katim',
                    'anggota' => fn ($q) => $q->withCount([
                        'pekerjaanSebagaiPj as active_pic_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                        'pekerjaanDitugaskan as active_member_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
                    ]),
                    'pekerjaan'
                ])->withCount('anggota')->orderBy('id')->get();
                $data['bebanKerjaPegawai'] = $this->getWorkloadList();
                break;

            case 'kasubag':
                $data['totalPegawai'] = User::count();
                $data['totalPokjas'] = Pokja::count();
                $data['konsolidasiBulanIni'] = LaporanKonsolidasiBgk::where('tahun', $currentYear)
                    ->where('bulan', $currentMonth)
                    ->first();
                $data['laporanSiapKonsolidasi'] = LaporanKinerja::where('status', 'disetujui_katim')
                    ->where('bulan', $currentMonth)
                    ->where('tahun', $currentYear)
                    ->count();
                $data['recentVerifiedLaporan'] = LaporanKinerja::with(['user', 'pokja'])
                    ->whereIn('status', ['disetujui_katim', 'dikonsolidasi_kasubag', 'disetujui_kabal'])
                    ->latest()
                    ->take(6)
                    ->get();
                $data['pokjas'] = Pokja::with([
                    'katim',
                    'anggota' => fn ($q) => $q->withCount([
                        'pekerjaanSebagaiPj as active_pic_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                        'pekerjaanDitugaskan as active_member_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
                    ]),
                    'pekerjaan'
                ])->withCount('anggota')->orderBy('id')->get();
                $data['bebanKerjaPegawai'] = $this->getWorkloadList();
                break;

            case 'korjafung':
            case 'katim':
            case 'korlayanan':
            case 'katim_korjafung':
                $pokjaIds = Pokja::where('katim_id', $user->id)->pluck('id');
                if ($pokjaIds->isEmpty() && $user->pokja_id) {
                    $pokjaIds = collect([$user->pokja_id]);
                }
                if ($pokjaIds->isEmpty()) {
                    $pokjaIds = Pokja::pluck('id');
                }

                $data['antreanEvaluasi'] = LaporanKinerja::with(['user', 'targetBulanan', 'verifikatorPokja'])
                    ->whereIn('pokja_id', $pokjaIds)
                    ->where('status', 'disetujui_pokja')
                    ->latest()
                    ->get();

                $data['totalDievaluasi'] = LaporanKinerja::where('katim_id', $user->id)->count();
                $data['avgNilai'] = LaporanKinerja::where('katim_id', $user->id)
                    ->whereNotNull('nilai_kualitas_katim')
                    ->avg('nilai_kualitas_katim') ?? 0;
                $data['anggotaPokja'] = User::whereIn('pokja_id', $pokjaIds)->get();
                break;

            case 'tim_pokja':
                $pokjaId = $user->pokja_id;
                $data['antreanVerifikasi'] = LaporanKinerja::with(['user', 'targetBulanan'])
                    ->where('pokja_id', $pokjaId)
                    ->where('status', 'diajukan')
                    ->latest()
                    ->get();

                $data['sudahDiverifikasi'] = LaporanKinerja::where('verifikator_pokja_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get();

                $data['totalAnggota'] = User::where('pokja_id', $pokjaId)->count();
                $data['pokjaInfo'] = $user->pokja;
                break;

            case 'pegawai':
            default:
                $data['skpTahunan'] = SkpTahunan::where('user_id', $user->id)
                    ->where('tahun', $currentYear)
                    ->first();

                $data['targetBulanan'] = SkpTargetBulanan::where('user_id', $user->id)
                    ->where('tahun', $currentYear)
                    ->where('bulan', $currentMonth)
                    ->get();

                $data['rencanaMingguan'] = RencanaMingguan::where('user_id', $user->id)
                    ->where('tahun', $currentYear)
                    ->where('bulan', $currentMonth)
                    ->latest()
                    ->take(3)
                    ->get();

                $data['laporanTerbaru'] = LaporanKinerja::with(['targetBulanan', 'rencanaMingguan'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->take(6)
                    ->get();

                $data['countDraft'] = LaporanKinerja::where('user_id', $user->id)->where('status', 'draft')->count();
                $data['countDiajukan'] = LaporanKinerja::where('user_id', $user->id)->where('status', 'diajukan')->count();
                $data['countDisetujui'] = LaporanKinerja::where('user_id', $user->id)->whereIn('status', ['disetujui_katim', 'disetujui_kabal'])->count();
                $data['countRevisi'] = LaporanKinerja::where('user_id', $user->id)->whereIn('status', ['revisi_pokja', 'revisi_katim'])->count();
                break;
        }

        return view('dashboard.index', $data);
    }

    /**
     * Mengambil rekapitulasi beban kerja seluruh pegawai dan katim (pekerjaan aktif sebagai PIC maupun Anggota)
     */
    private function getWorkloadList()
    {
        return User::whereIn('role', ['pegawai', 'tim_pokja', 'katim', 'korjafung', 'korlayanan'])
            ->withCount([
                'pekerjaanSebagaiPj as tugas_pic_count' => fn ($q) => $q->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                'pekerjaanDitugaskan as tugas_anggota_count' => fn ($q) => $q->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
            ])
            ->with(['pokja'])
            ->get()
            ->map(function ($u) {
                $u->total_beban = $u->tugas_pic_count + $u->tugas_anggota_count;
                return $u;
            })
            ->sortByDesc(fn ($u) => [$u->total_beban, $u->tugas_pic_count, $u->name])
            ->values();
    }

    /**
     * Mempersiapkan Data Pemantauan & Monitoring Seluruh Pekerjaan Pegawai Balai
     * Untuk Pimpinan (Kepala Balai & Kasubag TU)
     */
    private function getMonitoringPekerjaanPegawai(Request $request): array
    {
        // 1. Metrik Statistik Global Pekerjaan Seluruh Pegawai Balai
        $totalSemuaPekerjaan = Pekerjaan::count();
        $countPekerjaanBerjalan = Pekerjaan::where('status_persetujuan', 'disetujui')
            ->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil'])
            ->count();
        $countPekerjaanMenungguVerifikasi = Pekerjaan::where(function ($q) {
            $q->where('status_pelaksanaan', 'hasil_diajukan')
              ->orWhere(function ($sub) {
                  $sub->where('progres', '>=', 100)
                      ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
              });
        })->count();
        $countPekerjaanSelesai = Pekerjaan::where('status_pelaksanaan', 'selesai_terverifikasi')->count();
        $countPekerjaanTerlambat = Pekerjaan::where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
            ->whereDate('tenggat', '<', Carbon::today())
            ->count();

        // 2. Query Utama Pemantauan Pekerjaan Seluruh Pegawai
        $query = Pekerjaan::with([
            'penanggungJawab.pokja',
            'pelaksana.pokja',
            'pembuat',
            'pokja',
            'parent',
            'realisasi' => fn ($r) => $r->latest()
        ]);

        // Filter Pencarian Teks (Judul / Kode / No ST)
        if ($request->filled('q_pekerjaan')) {
            $search = trim($request->q_pekerjaan);
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kode_tampilan', 'like', "%{$search}%")
                  ->orWhere('nomor_st', 'like', "%{$search}%");
            });
        }

        // Filter Unit Kerja / Pokja
        if ($request->filled('filter_pokja')) {
            $query->where('pokja_id', $request->filter_pokja);
        }

        // Filter Pegawai Pelaksana (baik sebagai PIC Utama maupun Anggota Pelaksana)
        if ($request->filled('filter_pegawai')) {
            $pegawaiId = $request->filter_pegawai;
            $query->where(function ($q) use ($pegawaiId) {
                $q->where('penanggung_jawab_id', $pegawaiId)
                  ->orWhereHas('pelaksana', fn ($sub) => $sub->where('users.id', $pegawaiId));
            });
        }

        // Filter Status Pelaksanaan
        if ($request->filled('filter_status')) {
            $status = $request->filter_status;
            if ($status === 'berjalan') {
                $query->where('status_persetujuan', 'disetujui')
                      ->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil']);
            } elseif ($status === 'menunggu_verifikasi') {
                $query->where(function ($q) {
                    $q->where('status_pelaksanaan', 'hasil_diajukan')
                      ->orWhere(function ($sub) {
                          $sub->where('progres', '>=', 100)
                              ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
                      });
                });
            } elseif ($status === 'selesai') {
                $query->where('status_pelaksanaan', 'selesai_terverifikasi');
            } elseif ($status === 'terlambat') {
                $query->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
                      ->whereDate('tenggat', '<', Carbon::today());
            } elseif ($status === 'diajukan') {
                $query->where('status_persetujuan', 'diajukan');
            }
        }

        $daftarSemuaPekerjaan = $query->orderBy('tenggat', 'asc')
            ->paginate(12, ['*'], 'page_pekerjaan')
            ->withQueryString();

        $allPokjas = Pokja::orderBy('kode_pokja')->get();
        $allPegawai = User::with('pokja')->orderBy('name')->get();

        // 3. Rekapitulasi Beban Kerja Pegawai (Workload Monitoring)
        $bebanKerjaPegawai = User::whereIn('role', ['pegawai', 'tim_pokja', 'katim', 'korjafung', 'korlayanan'])
            ->withCount([
                'pekerjaanSebagaiPj as tugas_pic_count' => function ($q) {
                    $q->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
                },
                'pekerjaanDitugaskan as tugas_anggota_count' => function ($q) {
                    $q->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
                }
            ])
            ->with(['pokja'])
            ->orderByDesc('tugas_pic_count')
            ->take(8)
            ->get();

        return [
            'totalSemuaPekerjaan' => $totalSemuaPekerjaan,
            'countPekerjaanBerjalan' => $countPekerjaanBerjalan,
            'countPekerjaanMenungguVerifikasi' => $countPekerjaanMenungguVerifikasi,
            'countPekerjaanSelesai' => $countPekerjaanSelesai,
            'countPekerjaanTerlambat' => $countPekerjaanTerlambat,
            'daftarSemuaPekerjaan' => $daftarSemuaPekerjaan,
            'allPokjas' => $allPokjas,
            'allPegawai' => $allPegawai,
            'bebanKerjaPegawai' => $bebanKerjaPegawai,
        ];
    }

    /**
     * Halaman Khusus Command Center & Monitoring Kinerja Terpadu Balai (Big Screen / TV Mode)
     * Didesain khusus untuk monitor besar rapat & pengawasan menyeluruh seluruh pekerjaan BGTK DKI
     */
    public function commandCenter(Request $request): View
    {
        // 1. Metrik Global Balai
        $totalPekerjaan = Pekerjaan::count();
        $pekerjaanBerjalan = Pekerjaan::where('status_persetujuan', 'disetujui')
            ->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil'])
            ->count();
        $menungguVerifikasi = Pekerjaan::where(function ($q) {
            $q->where('status_pelaksanaan', 'hasil_diajukan')
              ->orWhere(function ($sub) {
                  $sub->where('progres', '>=', 100)
                      ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
              });
        })->count();
        $selesaiTerverifikasi = Pekerjaan::where('status_pelaksanaan', 'selesai_terverifikasi')->count();
        $lewatTenggat = Pekerjaan::where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
            ->whereDate('tenggat', '<', Carbon::today())
            ->count();
        
        $avgProgres = $totalPekerjaan > 0 ? (float) Pekerjaan::avg('progres') : 0;

        // 2. Capaian Kinerja per Pokja / Tim Kerja
        $pokjas = Pokja::with([
            'katim',
            'anggota' => fn ($q) => $q->withCount([
                'pekerjaanSebagaiPj as pic_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                'pekerjaanDitugaskan as member_count' => fn($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
            ]),
            'pekerjaan' => fn ($q) => $q->select('id', 'pokja_id', 'progres', 'status_pelaksanaan')
        ])->orderBy('kode_pokja')->get()->map(function ($p) {
            $totalTugas = $p->pekerjaan->count();
            $avgPokja = $totalTugas > 0 ? $p->pekerjaan->avg('progres') : 0;
            $selesai = $p->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count();
            $berjalan = $p->pekerjaan->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan'])->count();
            return (object) [
                'id' => $p->id,
                'kode_pokja' => $p->kode_pokja,
                'nama_pokja' => $p->nama_pokja,
                'katim_name' => $p->katim?->name ?? 'Belum Ditunjuk',
                'total_tugas' => $totalTugas,
                'avg_progres' => round($avgPokja, 1),
                'tugas_selesai' => $selesai,
                'tugas_berjalan' => $berjalan,
                'anggota' => $p->anggota,
                'anggota_count' => $p->anggota->count(),
            ];
        });

        // 3. Query Utama Pekerjaan untuk Tampilan Big Screen
        $query = Pekerjaan::with([
            'penanggungJawab.pokja',
            'pelaksana.pokja',
            'pembuat',
            'pokja',
            'parent',
            'realisasi' => fn ($r) => $r->latest()
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
                $query->where(function ($q) {
                    $q->where('status_pelaksanaan', 'hasil_diajukan')
                      ->orWhere(function ($sub) {
                          $sub->where('progres', '>=', 100)
                              ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
                      });
                });
            } elseif ($st === 'selesai') {
                $query->where('status_pelaksanaan', 'selesai_terverifikasi');
            } elseif ($st === 'terlambat') {
                $query->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
                      ->whereDate('tenggat', '<', Carbon::today());
            }
        }

        if ($request->filled('q')) {
            $qText = trim($request->q);
            $query->where(function ($q) use ($qText) {
                $q->where('judul', 'like', "%{$qText}%")
                  ->orWhere('kode_tampilan', 'like', "%{$qText}%")
                  ->orWhere('nomor_st', 'like', "%{$qText}%")
                  ->orWhereHas('penanggungJawab', fn($p) => $p->where('name', 'like', "%{$qText}%"));
            });
        }

        // Ambil daftar pekerjaan untuk live display big screen
        $pekerjaanList = $query->orderBy('tenggat', 'asc')->get();

        // 4. Staf Teraktif & Beban Kerja Seluruh Pegawai & Katim
        $topAssignees = $this->getWorkloadList()->map(function ($u) {
            $u->pic_count = $u->tugas_pic_count;
            $u->member_count = $u->tugas_anggota_count;
            return $u;
        });

        $allPokjas = Pokja::orderBy('kode_pokja')->get();

        return view('dashboard.command_center', compact(
            'totalPekerjaan',
            'pekerjaanBerjalan',
            'menungguVerifikasi',
            'selesaiTerverifikasi',
            'lewatTenggat',
            'avgProgres',
            'pokjas',
            'pekerjaanList',
            'topAssignees',
            'allPokjas'
        ));
    }
}
