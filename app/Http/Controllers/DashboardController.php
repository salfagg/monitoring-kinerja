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
use App\Services\DashboardMetricService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardMetricService $metricService
    ) {}

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

        $totalPekerjaanBalai = Pekerjaan::count();
        $statusCounts = $this->metricService->getStatusCounts();
        $selesaiBalai = $statusCounts['selesai'] ?? 0;
        $avgProgresBalai = $totalPekerjaanBalai > 0 ? round(($selesaiBalai / $totalPekerjaanBalai) * 100, 1) : 0;

        $pekerjaanTerlambatCount = Pekerjaan::where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
            ->whereDate('tenggat', '<', Carbon::today())
            ->count();

        // Data Unit & Tim Kerja untuk Bar Chart Kinerja
        $chartPokjas = $this->metricService->getChartPokjas();

        $data = [
            'user' => $user,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'tugasAktifSaya' => $tugasAktifSaya,
            'totalPekerjaanBalai' => $totalPekerjaanBalai,
            'avgProgresBalai' => $avgProgresBalai,
            'statusCounts' => $statusCounts,
            'pekerjaanTerlambatCount' => $pekerjaanTerlambatCount,
            'chartPokjas' => $chartPokjas,
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
                        'pekerjaanSebagaiPj as active_pic_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                        'pekerjaanDitugaskan as active_member_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                    ]),
                    'pekerjaan',
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
                        'pekerjaanSebagaiPj as active_pic_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                        'pekerjaanDitugaskan as active_member_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                    ]),
                    'pekerjaan',
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
                        'pekerjaanSebagaiPj as active_pic_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                        'pekerjaanDitugaskan as active_member_count' => fn ($sub) => $sub->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
                    ]),
                    'pekerjaan',
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
                'pekerjaanDitugaskan as tugas_anggota_count' => fn ($q) => $q->where('status_pelaksanaan', '!=', 'selesai_terverifikasi'),
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
        $countPekerjaanMenungguVerifikasi = Pekerjaan::where('status_pelaksanaan', 'hasil_diajukan')->count();
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
            'realisasi' => fn ($r) => $r->latest(),
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
                $query->where('status_pelaksanaan', 'hasil_diajukan');
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
                },
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
        $commandCenterData = $this->metricService->getCommandCenterData($request);

        return view('dashboard.command_center', $commandCenterData);
    }
}
