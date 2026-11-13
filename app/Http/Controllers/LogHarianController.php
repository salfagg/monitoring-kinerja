<?php

namespace App\Http\Controllers;

use App\Models\BuktiDukung;
use App\Models\LogHarian;
use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\SkpTahunan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogHarianController extends Controller
{
    /**
     * Tampilan Kalender Log Harian & Ringkasan Bulanan
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();

        // 1. Tentukan Bulan dan Tahun (default: saat ini)
        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        if ($month < 1 || $month > 12) {
            $month = Carbon::now()->month;
        }
        if ($year < 2020 || $year > 2035) {
            $year = Carbon::now()->year;
        }

        // 2. Tentukan Pegawai yang Ditampilkan
        // Pimpinan / Katim / Admin bisa memilih pegawai lain
        $targetUserId = $currentUser->id;
        $canManage = $currentUser->canManagePekerjaan() || $currentUser->isAdmin();

        if ($canManage && $request->filled('user_id')) {
            $targetUserId = (int) $request->user_id;
        }

        $targetUser = User::with('pokja')->findOrFail($targetUserId);

        // Jika bukan pimpinan/admin dan mencoba melihat user lain, kembalikan ke diri sendiri
        if (!$canManage && $targetUserId !== $currentUser->id) {
            $targetUser = $currentUser;
            $targetUserId = $currentUser->id;
        }

        // 3. Ambil data log harian untuk bulan & tahun terpilih
        $logs = LogHarian::with(['pekerjaan', 'skpTahunan', 'buktiDukung', 'verifikatorPokja', 'katim'])
            ->where('user_id', $targetUserId)
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Kelompokkan log berdasarkan tanggal (format Y-m-d)
        $logsByDate = $logs->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_kegiatan)->format('Y-m-d');
        });

        // 4. Hitung Statistik Rekap Bulanan
        $totalLogs = $logs->count();
        $totalHariAktif = $logsByDate->count();
        $totalVolume = $logs->sum('volume_capaian');

        // Hitung total durasi menit
        $totalDurasiMenit = $logs->reduce(function ($carry, $log) {
            if ($log->jam_mulai && $log->jam_selesai) {
                $start = Carbon::parse($log->jam_mulai);
                $end = Carbon::parse($log->jam_selesai);
                return $carry + max(0, $start->diffInMinutes($end));
            }
            return $carry;
        }, 0);

        $totalJamKerja = round($totalDurasiMenit / 60, 1);

        $countDisetujui = $logs->whereIn('status', ['disetujui_katim', 'disetujui_kabal', 'disetujui_pokja'])->count();
        $countDiajukan = $logs->where('status', 'diajukan')->count();
        $countDraft = $logs->where('status', 'draft')->count();
        $countRevisi = $logs->whereIn('status', ['revisi_pokja', 'revisi_katim'])->count();

        // 5. Susun Grid Kalender
        $firstDayOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        $daysInMonth = $firstDayOfMonth->daysInMonth;

        // Hari dalam seminggu untuk tanggal 1 (1 = Senin, ..., 7 = Minggu)
        $firstDayOfWeek = $firstDayOfMonth->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)

        // Previous month filler days
        $prevMonthDays = [];
        if ($firstDayOfWeek > 1) {
            $prevMonthLastDay = $firstDayOfMonth->copy()->subDay();
            $numFiller = $firstDayOfWeek - 1;
            for ($i = $numFiller - 1; $i >= 0; $i--) {
                $d = $prevMonthLastDay->copy()->subDays($i);
                $prevMonthDays[] = [
                    'date' => $d->format('Y-m-d'),
                    'day' => $d->day,
                    'isCurrentMonth' => false,
                ];
            }
        }

        // Current month days
        $currentMonthDays = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            $currentMonthDays[] = [
                'date' => $currentDate,
                'day' => $day,
                'isCurrentMonth' => true,
                'isToday' => ($currentDate === Carbon::today()->format('Y-m-d')),
                'logs' => $logsByDate->get($currentDate, collect()),
            ];
        }

        // Next month filler days (agar kelipatan 7)
        $totalCellsSoFar = count($prevMonthDays) + count($currentMonthDays);
        $remainingCells = (7 - ($totalCellsSoFar % 7)) % 7;
        $nextMonthDays = [];
        if ($remainingCells > 0) {
            $nextMonthStart = $lastDayOfMonth->copy()->addDay();
            for ($i = 0; $i < $remainingCells; $i++) {
                $d = $nextMonthStart->copy()->addDays($i);
                $nextMonthDays[] = [
                    'date' => $d->format('Y-m-d'),
                    'day' => $d->day,
                    'isCurrentMonth' => false,
                ];
            }
        }

        // Daftar Pegawai untuk Filter Dropdown (Pimpinan/Katim/Admin)
        $pegawaiList = collect();
        if ($canManage) {
            if ($currentUser->isAdmin() || $currentUser->isKabal() || $currentUser->isKasubag()) {
                $pegawaiList = User::orderBy('name')->get();
            } elseif ($currentUser->isKatim() || $currentUser->isKorlayanan() || $currentUser->isKorjafung()) {
                // Pegawai di pokja katim
                $pegawaiList = User::where('pokja_id', $currentUser->pokja_id)
                    ->orWhere('id', $currentUser->id)
                    ->orderBy('name')
                    ->get();
            }
        }

        // Daftar Pekerjaan aktif untuk pilihan cepat modal
        $activePekerjaan = Pekerjaan::where(function ($q) use ($targetUserId) {
            $q->where('penanggung_jawab_id', $targetUserId)
              ->orWhere('pembuat_id', $targetUserId)
              ->orWhereHas('pelaksana', fn($p) => $p->where('users.id', $targetUserId));
        })->where('status_persetujuan', 'disetujui')
          ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
          ->orderBy('judul')
          ->get();

        // Daftar SKP untuk pilihan cepat modal
        $skpList = SkpTahunan::where('user_id', $targetUserId)
            ->where('tahun', $year)
            ->orderBy('id')
            ->get();

        // Nama Bulan Bahasa Indonesia
        $namaBulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('log_harian.index', compact(
            'targetUser',
            'month',
            'year',
            'namaBulanIndo',
            'prevMonthDays',
            'currentMonthDays',
            'nextMonthDays',
            'logs',
            'logsByDate',
            'totalLogs',
            'totalHariAktif',
            'totalVolume',
            'totalJamKerja',
            'countDisetujui',
            'countDiajukan',
            'countDraft',
            'countRevisi',
            'pegawaiList',
            'activePekerjaan',
            'skpList',
            'canManage'
        ));
    }

    /**
     * Tampilan Rekap Bulanan Siap Cetak & Tarik Data
     */
    public function rekap(Request $request): View
    {
        $currentUser = Auth::user();

        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        $targetUserId = $currentUser->id;
        $canManage = $currentUser->canManagePekerjaan() || $currentUser->isAdmin();

        if ($canManage && $request->filled('user_id')) {
            $targetUserId = (int) $request->user_id;
        }

        $targetUser = User::with(['pokja.katim'])->findOrFail($targetUserId);

        // Ambil seluruh log bulan ini
        $logs = LogHarian::with(['pekerjaan', 'skpTahunan', 'buktiDukung', 'verifikatorPokja', 'katim'])
            ->where('user_id', $targetUserId)
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Hitung statistik
        $totalLogs = $logs->count();
        $totalHariAktif = $logs->groupBy(fn($i) => Carbon::parse($i->tanggal_kegiatan)->format('Y-m-d'))->count();
        $totalVolume = $logs->sum('volume_capaian');

        $totalDurasiMenit = $logs->reduce(function ($carry, $log) {
            if ($log->jam_mulai && $log->jam_selesai) {
                $start = Carbon::parse($log->jam_mulai);
                $end = Carbon::parse($log->jam_selesai);
                return $carry + max(0, $start->diffInMinutes($end));
            }
            return $carry;
        }, 0);
        $totalJamKerja = round($totalDurasiMenit / 60, 1);

        $namaBulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Pejabat Penilai / Katim
        $pejabatPenilai = null;
        if ($targetUser->pokja && $targetUser->pokja->katim) {
            $pejabatPenilai = $targetUser->pokja->katim;
        } else {
            // Kasubag atau Kabal
            $pejabatPenilai = User::whereIn('role', ['kasubag', 'kabal'])->first();
        }

        return view('log_harian.rekap', compact(
            'targetUser',
            'month',
            'year',
            'namaBulanIndo',
            'logs',
            'totalLogs',
            'totalHariAktif',
            'totalVolume',
            'totalJamKerja',
            'pejabatPenilai'
        ));
    }

    /**
     * Ekspor Rekap Bulanan ke CSV (Bisa dibuka di Excel)
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $currentUser = Auth::user();

        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        $targetUserId = $currentUser->id;
        $canManage = $currentUser->canManagePekerjaan() || $currentUser->isAdmin();

        if ($canManage && $request->filled('user_id')) {
            $targetUserId = (int) $request->user_id;
        }

        $targetUser = User::with('pokja')->findOrFail($targetUserId);

        $logs = LogHarian::with(['pekerjaan', 'skpTahunan', 'buktiDukung'])
            ->where('user_id', $targetUserId)
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $filename = 'Log_Harian_' . str_replace(' ', '_', $targetUser->name) . '_' . sprintf('%02d', $month) . '_' . $year . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs, $targetUser, $month, $year) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk Excel Indonesia
            fputs($file, "\xEF\xBB\xBF");

            // Header Informasi Pegawai
            fputcsv($file, ['REKAPITULASI LOG HARIAN KINERJA PEGAWAI']);
            fputcsv($file, ['BGTK PROVINSI DKI JAKARTA']);
            fputcsv($file, ['Periode', sprintf('%02d/%d', $month, $year)]);
            fputcsv($file, ['Nama Pegawai', $targetUser->name]);
            fputcsv($file, ['NIP', $targetUser->nip ?? '-']);
            fputcsv($file, ['Jabatan', $targetUser->jabatan ?? 'Pegawai']);
            fputcsv($file, ['Unit Kerja / Pokja', $targetUser->pokja?->nama_pokja ?? '-']);
            fputcsv($file, []);

            // Header Tabel
            fputcsv($file, [
                'No',
                'Tanggal',
                'Jam Mulai',
                'Jam Selesai',
                'Durasi',
                'Uraian Kegiatan',
                'Output / Hasil',
                'Volume',
                'Satuan',
                'Rujukan Pekerjaan',
                'Rujukan SKP',
                'Status',
                'Bukti Dukung',
                'Catatan Katim'
            ]);

            $no = 1;
            foreach ($logs as $log) {
                $durasi = '-';
                if ($log->jam_mulai && $log->jam_selesai) {
                    $start = Carbon::parse($log->jam_mulai);
                    $end = Carbon::parse($log->jam_selesai);
                    $durasi = $start->diffInMinutes($end) . ' Menit';
                }

                $buktiText = '-';
                if ($log->buktiDukung->isNotEmpty()) {
                    $buktiText = $log->buktiDukung->pluck('nama_dokumen')->implode('; ');
                }

                fputcsv($file, [
                    $no++,
                    Carbon::parse($log->tanggal_kegiatan)->format('d/m/Y'),
                    $log->jam_mulai ? Carbon::parse($log->jam_mulai)->format('H:i') : '-',
                    $log->jam_selesai ? Carbon::parse($log->jam_selesai)->format('H:i') : '-',
                    $durasi,
                    $log->uraian_kegiatan,
                    $log->output_kegiatan,
                    $log->volume_capaian,
                    $log->satuan,
                    $log->pekerjaan?->judul ?? '-',
                    $log->skpTahunan?->rencana_hasil_kerja ?? '-',
                    $log->status_label,
                    $buktiText,
                    $log->catatan_katim ?? ($log->catatan_pokja ?? '-')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simpan Log Harian Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_kegiatan' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after_or_equal:jam_mulai',
            'uraian_kegiatan' => 'required|string',
            'output_kegiatan' => 'required|string|max:255',
            'volume_capaian' => 'required|numeric|min:0.1',
            'satuan' => 'required|string|max:100',
            'pekerjaan_id' => 'nullable|exists:pekerjaan,id',
            'skp_tahunan_id' => 'nullable|exists:skp_tahunan,id',
            'tipe_bukti' => 'nullable|in:file,link,none',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:10240',
            'link_bukti' => 'nullable|url|max:1000',
            'nama_bukti' => 'nullable|string|max:255',
            'aksi_status' => 'nullable|in:draft,diajukan',
        ]);

        $user = Auth::user();
        $tanggal = Carbon::parse($request->tanggal_kegiatan);

        $status = $request->input('aksi_status', 'diajukan');

        $log = LogHarian::create([
            'user_id' => $user->id,
            'pokja_id' => $user->pokja_id,
            'pekerjaan_id' => $request->pekerjaan_id ?: null,
            'skp_tahunan_id' => $request->skp_tahunan_id ?: null,
            'tanggal_kegiatan' => $tanggal->format('Y-m-d'),
            'jam_mulai' => $request->jam_mulai ?: null,
            'jam_selesai' => $request->jam_selesai ?: null,
            'bulan' => (int) $tanggal->format('m'),
            'tahun' => (int) $tanggal->format('Y'),
            'uraian_kegiatan' => $request->uraian_kegiatan,
            'output_kegiatan' => $request->output_kegiatan,
            'volume_capaian' => $request->volume_capaian,
            'satuan' => $request->satuan,
            'status' => $status,
        ]);

        // Simpan Bukti Dukung jika diunggah
        if ($request->tipe_bukti === 'file' && $request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $path = $file->store('bukti_dukung', 'public');

            BuktiDukung::create([
                'laporan_kinerja_id' => $log->id,
                'nama_dokumen' => $request->nama_bukti ?: $file->getClientOriginalName(),
                'tipe' => 'file',
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
            ]);
        } elseif ($request->tipe_bukti === 'link' && $request->filled('link_bukti')) {
            BuktiDukung::create([
                'laporan_kinerja_id' => $log->id,
                'nama_dokumen' => $request->nama_bukti ?: 'Tautan Bukti Dokumen',
                'tipe' => 'link',
                'link_url' => $request->link_bukti,
            ]);
        }

        return redirect()->route('log-harian.index', [
            'bulan' => $tanggal->month,
            'tahun' => $tanggal->year
        ])->with('success', 'Log harian berhasil disimpan.');
    }

    /**
     * Detail Log Harian (JSON untuk Modal atau View)
     */
    public function show(LogHarian $logHarian)
    {
        $logHarian->load(['pekerjaan', 'skpTahunan', 'buktiDukung', 'verifikatorPokja', 'katim', 'user.pokja']);

        if (request()->wantsJson()) {
            return response()->json($logHarian);
        }

        return view('log_harian.show', compact('logHarian'));
    }

    /**
     * Form Edit Log Harian
     */
    public function edit(LogHarian $logHarian): View
    {
        $user = Auth::user();

        // Hanya pemilik log atau admin yang boleh mengedit jika belum disetujui
        if ($logHarian->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak berhak mengedit log harian ini.');
        }

        $logHarian->load(['buktiDukung', 'pekerjaan', 'skpTahunan']);

        $activePekerjaan = Pekerjaan::where(function ($q) use ($user) {
            $q->where('penanggung_jawab_id', $user->id)
              ->orWhere('pembuat_id', $user->id)
              ->orWhereHas('pelaksana', fn($p) => $p->where('users.id', $user->id));
        })->where('status_persetujuan', 'disetujui')
          ->orderBy('judul')
          ->get();

        $skpList = SkpTahunan::where('user_id', $user->id)
            ->where('tahun', $logHarian->tahun)
            ->orderBy('id')
            ->get();

        return view('log_harian.edit', compact('logHarian', 'activePekerjaan', 'skpList'));
    }

    /**
     * Update Log Harian
     */
    public function update(Request $request, LogHarian $logHarian)
    {
        $user = Auth::user();

        if ($logHarian->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak berhak mengubah log harian ini.');
        }

        $request->validate([
            'tanggal_kegiatan' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after_or_equal:jam_mulai',
            'uraian_kegiatan' => 'required|string',
            'output_kegiatan' => 'required|string|max:255',
            'volume_capaian' => 'required|numeric|min:0.1',
            'satuan' => 'required|string|max:100',
            'pekerjaan_id' => 'nullable|exists:pekerjaan,id',
            'skp_tahunan_id' => 'nullable|exists:skp_tahunan,id',
            'tipe_bukti' => 'nullable|in:file,link,none',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:10240',
            'link_bukti' => 'nullable|url|max:1000',
            'nama_bukti' => 'nullable|string|max:255',
            'aksi_status' => 'nullable|in:draft,diajukan',
        ]);

        $tanggal = Carbon::parse($request->tanggal_kegiatan);

        $dataToUpdate = [
            'tanggal_kegiatan' => $tanggal->format('Y-m-d'),
            'jam_mulai' => $request->jam_mulai ?: null,
            'jam_selesai' => $request->jam_selesai ?: null,
            'bulan' => (int) $tanggal->format('m'),
            'tahun' => (int) $tanggal->format('Y'),
            'uraian_kegiatan' => $request->uraian_kegiatan,
            'output_kegiatan' => $request->output_kegiatan,
            'volume_capaian' => $request->volume_capaian,
            'satuan' => $request->satuan,
            'pekerjaan_id' => $request->pekerjaan_id ?: null,
            'skp_tahunan_id' => $request->skp_tahunan_id ?: null,
        ];

        if ($request->filled('aksi_status')) {
            $dataToUpdate['status'] = $request->aksi_status;
        }

        $logHarian->update($dataToUpdate);

        // Upload bukti baru jika ada
        if ($request->tipe_bukti === 'file' && $request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $path = $file->store('bukti_dukung', 'public');

            BuktiDukung::create([
                'laporan_kinerja_id' => $logHarian->id,
                'nama_dokumen' => $request->nama_bukti ?: $file->getClientOriginalName(),
                'tipe' => 'file',
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
            ]);
        } elseif ($request->tipe_bukti === 'link' && $request->filled('link_bukti')) {
            BuktiDukung::create([
                'laporan_kinerja_id' => $logHarian->id,
                'nama_dokumen' => $request->nama_bukti ?: 'Tautan Bukti Dokumen',
                'tipe' => 'link',
                'link_url' => $request->link_bukti,
            ]);
        }

        return redirect()->route('log-harian.index', [
            'bulan' => $tanggal->month,
            'tahun' => $tanggal->year
        ])->with('success', 'Log harian berhasil diperbarui.');
    }

    /**
     * Hapus Log Harian
     */
    public function destroy(LogHarian $logHarian)
    {
        $user = Auth::user();

        if ($logHarian->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak berhak menghapus log harian ini.');
        }

        // Hapus file bukti fisik jika ada
        foreach ($logHarian->buktiDukung as $bukti) {
            if ($bukti->tipe === 'file' && $bukti->file_path && Storage::disk('public')->exists($bukti->file_path)) {
                Storage::disk('public')->delete($bukti->file_path);
            }
            $bukti->delete();
        }

        $month = $logHarian->bulan;
        $year = $logHarian->tahun;

        $logHarian->delete();

        return redirect()->route('log-harian.index', [
            'bulan' => $month,
            'tahun' => $year
        ])->with('success', 'Log harian berhasil dihapus.');
    }

    /**
     * Ajukan Verifikasi Log Harian
     */
    public function ajukan(LogHarian $logHarian)
    {
        $user = Auth::user();

        if ($logHarian->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $logHarian->update([
            'status' => 'diajukan',
        ]);

        return back()->with('success', 'Log harian berhasil diajukan untuk verifikasi.');
    }

    /**
     * Ajukan Seluruh Log Harian Bulan Ini (Batch)
     */
    public function ajukanBulan(Request $request)
    {
        $user = Auth::user();
        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        $count = LogHarian::where('user_id', $user->id)
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->where('status', 'draft')
            ->update(['status' => 'diajukan']);

        return back()->with('success', "{$count} log harian berhasil diajukan.");
    }

    /**
     * Verifikasi / Setujui Log Harian (Untuk Katim / Pokja / Atasan)
     */
    public function approve(Request $request, LogHarian $logHarian)
    {
        $user = Auth::user();

        if (!$user->canManagePekerjaan() && !$user->isAdmin() && !$user->isTimPokja()) {
            abort(403, 'Anda tidak memiliki wewenang untuk menyetujui log harian.');
        }

        $request->validate([
            'nilai_kualitas' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $newStatus = $user->isKatim() || $user->isKasubag() || $user->isKabal() || $user->isAdmin()
            ? 'disetujui_katim'
            : 'disetujui_pokja';

        $logHarian->update([
            'status' => $newStatus,
            'katim_id' => $user->id,
            'nilai_kualitas_katim' => $request->nilai_kualitas ?: 100,
            'catatan_katim' => $request->catatan,
            'tanggal_evaluasi_katim' => Carbon::now(),
        ]);

        return back()->with('success', 'Log harian berhasil disetujui.');
    }
}

