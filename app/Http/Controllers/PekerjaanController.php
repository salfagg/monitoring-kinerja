<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RealisasiPekerjaan;
use App\Models\RiwayatPersetujuanPekerjaan;
use App\Models\SkpTahunan;
use App\Models\User;
use App\Services\PekerjaanPersetujuanService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PekerjaanController extends Controller
{
    /**
     * Tampilan Daftar Pekerjaan & Penugasan BGTK
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Hanya Kepala Balai, Kasubag, Korjafung, Koor, Katim, dan Admin yang boleh mengakses daftar pekerjaan
        if (! $user->canManagePekerjaan()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Pimpinan, Koordinator, atau Ketua Tim.');
        }

        $query = Pekerjaan::with([
            'pokja',
            'penanggungJawab',
            'pembuat',
            'pelaksana',
            'skpTahunan.user',
            'skpTargetBulanan',
            'realisasi' => fn ($r) => $r->latest(),
        ]);

        // ── Pembatasan Lingkup Data berdasarkan Role ──────────────────────────────
        // Kepala Balai, Kasubag TU, dan Admin: lihat SEMUA pekerjaan seluruh unit
        // Katim, Korjafung, Korlayanan       : hanya melihat pekerjaan dalam pokja
        //                                      sendiri, ATAU yang mereka buat/jadi PIC
        if ($user->isKabal() || $user->isKasubag() || $user->isAdmin()) {
            // Tidak ada pembatasan — tampilkan semua
        } else {
            // Katim, Korjafung, Korlayanan: lingkup pokja sendiri + yang dibuat/jadi PIC
            $query->where(function ($q) use ($user) {
                // Pekerjaan dalam pokja yang sama (tim kerja sendiri)
                if ($user->pokja_id) {
                    $q->where('pokja_id', $user->pokja_id);
                }
                // ATAU pekerjaan yang dibuat sendiri (pembuat_id)
                $q->orWhere('pembuat_id', $user->id);
                // ATAU pekerjaan yang dia jadi PIC Utama (penanggung_jawab_id)
                $q->orWhere('penanggung_jawab_id', $user->id);
            });
        }

        // Filter berdasarkan Tim Kerja (hanya untuk role yang bisa lihat semua)
        if ($request->filled('pokja_id')) {
            $query->where('pokja_id', $request->pokja_id);
        }

        // Filter pencarian teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('kode_tampilan', 'like', "%{$search}%")
                    ->orWhere('nomor_st', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Filter status pelaksanaan
        if ($request->filled('status_pelaksanaan')) {
            $query->where('status_pelaksanaan', $request->status_pelaksanaan);
        }

        // Filter sumber pekerjaan (manual atau skp)
        if ($request->filled('sumber_pekerjaan')) {
            $query->where('sumber_pekerjaan', $request->sumber_pekerjaan);
        }

        $pekerjaanList = $query->orderBy('tenggat', 'asc')->paginate(15);
        $pokjas = Pokja::orderBy('kode_pokja')->get();

        // Metrik KPI: sesuaikan lingkup hitung berdasarkan role
        $baseMetrik = Pekerjaan::query();
        if (! ($user->isKabal() || $user->isKasubag() || $user->isAdmin())) {
            $baseMetrik->where(function ($q) use ($user) {
                if ($user->pokja_id) {
                    $q->where('pokja_id', $user->pokja_id);
                }
                $q->orWhere('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id);
            });
        }

        $totalPekerjaan = (clone $baseMetrik)->count();
        $pekerjaanBerjalan = (clone $baseMetrik)->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil'])->count();
        $antreanHasil = (clone $baseMetrik)->where('status_pelaksanaan', 'hasil_diajukan')->count();
        $pekerjaanSelesai = (clone $baseMetrik)->where('status_pelaksanaan', 'selesai_terverifikasi')->count();

        return view('pekerjaan.index', compact(
            'pekerjaanList',
            'pokjas',
            'totalPekerjaan',
            'pekerjaanBerjalan',
            'antreanHasil',
            'pekerjaanSelesai'
        ));
    }

    /**
     * Form Buat Pekerjaan & Penugasan Baru
     */
    public function create(Request $request): View
    {
        $count = Pekerjaan::count();
        $suggestedKode = 'TGS-'.str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $users = User::with('pokja')->orderBy('name')->get();
        $pokjas = Pokja::orderBy('kode_pokja')->get();

        // Daftar Rujukan SKP Tahunan & Target Bulanan Pegawai
        $skpList = SkpTahunan::with(['user', 'targetBulanan'])
            ->orderByDesc('tahun')
            ->get();

        return view('pekerjaan.create', compact('suggestedKode', 'users', 'pokjas', 'skpList'));
    }

    /**
     * Simpan Pekerjaan Baru & Tugaskan ke Pegawai
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_tampilan' => ['nullable', 'string', 'max:50'],
            'sumber_pekerjaan' => ['nullable', 'string', 'in:manual,skp'],
            'skp_tahunan_id' => ['nullable', 'exists:skp_tahunan,id'],
            'skp_target_bulanan_id' => ['nullable', 'exists:skp_target_bulanan,id'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'hasil_diharapkan' => ['required', 'string'],
            'target' => ['required', 'numeric', 'min:0.01'],
            'satuan' => ['required', 'string', 'max:50'],
            'tenggat' => ['required', 'date'],
            'nomor_st' => ['nullable', 'string', 'max:100'],
            'tanggal_st' => ['nullable', 'date'],
            'file_st' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'penanggung_jawab_id' => ['required', 'exists:users,id'],
            'pelaksana_ids' => ['nullable', 'array'],
            'pelaksana_ids.*' => ['exists:users,id'],
            'pokja_id' => ['nullable', 'exists:pokjas,id'],
        ]);

        if (empty($validated['kode_tampilan'])) {
            $validated['kode_tampilan'] = 'TGS-'.str_pad(Pekerjaan::count() + 1, 3, '0', STR_PAD_LEFT);
        }

        // Tentukan Sumber Pekerjaan (Manual atau SKP)
        $validated['sumber_pekerjaan'] = $request->input('sumber_pekerjaan', 'manual');
        if ($validated['sumber_pekerjaan'] === 'manual') {
            $validated['skp_tahunan_id'] = null;
            $validated['skp_target_bulanan_id'] = null;
        } else {
            $validated['skp_tahunan_id'] = $request->input('skp_tahunan_id') ?: null;
            $validated['skp_target_bulanan_id'] = $request->input('skp_target_bulanan_id') ?: null;
        }

        $user = Auth::user();
        $validated['pembuat_id'] = $user->id;
        $validated['parent_id'] = null;
        // Penugasan oleh Katim/Koor/Pimpinan langsung berstatus disetujui agar anggota dapat langsung update progres!
        $validated['status_persetujuan'] = 'disetujui';
        $validated['status_pelaksanaan'] = 'belum_dimulai';

        $validated['deskripsi'] = $validated['deskripsi'] ?? null;
        $validated['nomor_st'] = $validated['nomor_st'] ?? null;
        $validated['tanggal_st'] = $validated['tanggal_st'] ?? null;

        if (empty($validated['pokja_id'])) {
            $pic = User::find($validated['penanggung_jawab_id']);
            $validated['pokja_id'] = $user->pokja_id ?? ($pic ? $pic->pokja_id : null);
        }

        // Upload berkas Surat Tugas jika ada
        if ($request->hasFile('file_st')) {
            $path = $request->file('file_st')->store('surat_tugas', 'public');
            $validated['file_st'] = $path;
        }

        $pekerjaan = Pekerjaan::create($validated);

        // Sinkronisasi Seluruh Tim Pelaksana yang Ditugaskan
        $pelaksanaIds = $request->input('pelaksana_ids', []);
        if (! in_array($pekerjaan->penanggung_jawab_id, $pelaksanaIds)) {
            $pelaksanaIds[] = $pekerjaan->penanggung_jawab_id;
        }
        $syncData = [];
        foreach ($pelaksanaIds as $uId) {
            $syncData[$uId] = [
                'peran' => ((int) $uId === (int) $pekerjaan->penanggung_jawab_id) ? 'pic_utama' : 'anggota',
            ];
        }
        $pekerjaan->pelaksana()->sync($syncData);

        return redirect()->route('pekerjaan.index')
            ->with('success', "Pekerjaan '{$pekerjaan->judul}' berhasil dibuat dan langsung ditugaskan ke pegawai pelaksana.");
        // Catat riwayat audit penugasan
        $catatanAwal = ! empty($validated['nomor_st'])
            ? "Pekerjaan ditugaskan oleh {$user->name} ({$user->role_label}) berdasarkan Surat Tugas No: {$validated['nomor_st']}."
            : "Pekerjaan ditugaskan oleh {$user->name} ({$user->role_label}).";

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'penugasan_langsung',
            'status_sebelumnya' => null,
            'status_baru' => 'disetujui',
            'catatan' => $catatanAwal,
        ]);

        return redirect()->route('pekerjaan.index')
            ->with('success', "Pekerjaan '{$pekerjaan->judul}' berhasil dibuat dan langsung ditugaskan ke pegawai pelaksana.");
    }

    /**
     * Tampilan Rincian Pekerjaan (PRD Bagian 10)
     */
    public function show(Pekerjaan $pekerjaan): View
    {
        $pekerjaan->load([
            'penanggungJawab',
            'pelaksana',
            'pembuat',
            'pokja',
            'skpTahunan.user',
            'skpTargetBulanan',
            'realisasi.user',
            'riwayatPersetujuan.user',
        ]);

        $user = Auth::user();

        $isPemberiTugas = ($pekerjaan->pembuat_id === $user->id);
        $isPelaksana = ($user->id === $pekerjaan->penanggung_jawab_id || $pekerjaan->pelaksana->contains('id', $user->id));
        $canEdit = ($user->id === $pekerjaan->pembuat_id || $isPelaksana || $user->canManagePekerjaan());
        $canDelete = ($user->id === $pekerjaan->pembuat_id || $user->canManagePekerjaan());
        $canReview = $user->canReviewPekerjaan() || $isPemberiTugas;
        $canUpdateProgres = ($isPelaksana || $user->id === $pekerjaan->pembuat_id || $user->isAdmin());
        $canEvaluate = $user->canEvaluatePekerjaan();

        return view('pekerjaan.show', compact('pekerjaan', 'canEdit', 'canDelete', 'canReview', 'canUpdateProgres', 'canEvaluate'));
    }

    /**
     * Form Edit Pekerjaan / Subpekerjaan
     */
    public function edit(Pekerjaan $pekerjaan): View
    {
        $user = Auth::user();
        $isPicUtama = ($user->id === $pekerjaan->penanggung_jawab_id);
        if (! ($user->id === $pekerjaan->pembuat_id || $isPicUtama || $user->canManagePekerjaan())) {
            abort(403, 'Hanya PIC Utama atau pembuat penugasan yang dapat mengedit pekerjaan ini.');
        }

        $pekerjaan->load(['penanggungJawab', 'pelaksana', 'pokja', 'skpTahunan', 'skpTargetBulanan']);
        $users = User::with('pokja')->orderBy('name')->get();
        $pokjas = Pokja::orderBy('kode_pokja')->get();
        $skpList = SkpTahunan::with(['user', 'targetBulanan'])->orderByDesc('tahun')->get();

        return view('pekerjaan.edit', compact('pekerjaan', 'users', 'pokjas', 'skpList'));
    }

    /**
     * Perbarui Data Pekerjaan
     */
    public function update(Request $request, Pekerjaan $pekerjaan): RedirectResponse
    {
        $user = Auth::user();
        $isPicUtama = ($user->id === $pekerjaan->penanggung_jawab_id);
        if (! ($user->id === $pekerjaan->pembuat_id || $isPicUtama || $user->canManagePekerjaan())) {
            abort(403, 'Hanya PIC Utama atau pembuat penugasan yang dapat memperbarui pekerjaan ini.');
        }

        $validated = $request->validate([
            'kode_tampilan' => ['nullable', 'string', 'max:50'],
            'sumber_pekerjaan' => ['nullable', 'string', 'in:manual,skp'],
            'skp_tahunan_id' => ['nullable', 'exists:skp_tahunan,id'],
            'skp_target_bulanan_id' => ['nullable', 'exists:skp_target_bulanan,id'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'hasil_diharapkan' => ['required', 'string'],
            'target' => ['required', 'numeric', 'min:0.01'],
            'satuan' => ['required', 'string', 'max:50'],
            'tenggat' => ['required', 'date'],
            'nomor_st' => ['nullable', 'string', 'max:100'],
            'tanggal_st' => ['nullable', 'date'],
            'file_st' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'penanggung_jawab_id' => ['required', 'exists:users,id'],
            'pelaksana_ids' => ['nullable', 'array'],
            'pelaksana_ids.*' => ['exists:users,id'],
            'pokja_id' => ['nullable', 'exists:pokjas,id'],
            'status_pelaksanaan' => ['nullable', 'string'],
        ]);

        $validated['sumber_pekerjaan'] = $request->input('sumber_pekerjaan', $pekerjaan->sumber_pekerjaan ?? 'manual');
        if ($validated['sumber_pekerjaan'] === 'manual') {
            $validated['skp_tahunan_id'] = null;
            $validated['skp_target_bulanan_id'] = null;
        } else {
            $validated['skp_tahunan_id'] = $request->input('skp_tahunan_id') ?: null;
            $validated['skp_target_bulanan_id'] = $request->input('skp_target_bulanan_id') ?: null;
        }

        $validated['pokja_id'] = $validated['pokja_id'] ?? $pekerjaan->pokja_id;
        $validated['deskripsi'] = $validated['deskripsi'] ?? null;
        $validated['nomor_st'] = $validated['nomor_st'] ?? null;
        $validated['tanggal_st'] = $validated['tanggal_st'] ?? null;

        // Upload berkas Surat Tugas baru jika ada
        if ($request->hasFile('file_st')) {
            if ($pekerjaan->file_st && Storage::disk('public')->exists($pekerjaan->file_st)) {
                Storage::disk('public')->delete($pekerjaan->file_st);
            }
            $path = $request->file('file_st')->store('surat_tugas', 'public');
            $validated['file_st'] = $path;
        }

        $pekerjaan->update($validated);

        // Sinkronisasi Tim Pelaksana yang Ditugaskan
        $pelaksanaIds = $request->input('pelaksana_ids', []);
        if (! in_array($pekerjaan->penanggung_jawab_id, $pelaksanaIds)) {
            $pelaksanaIds[] = $pekerjaan->penanggung_jawab_id;
        }
        $syncData = [];
        foreach ($pelaksanaIds as $uId) {
            $syncData[$uId] = [
                'peran' => ((int) $uId === (int) $pekerjaan->penanggung_jawab_id) ? 'pic_utama' : 'anggota',
            ];
        }
        $pekerjaan->pelaksana()->sync($syncData);

        return redirect()->route('pekerjaan.show', $pekerjaan)
            ->with('success', "Pekerjaan '{$pekerjaan->judul}' berhasil diperbarui.");
    }

    /**
     * Hapus Pekerjaan / Subpekerjaan
     */
    public function destroy(Pekerjaan $pekerjaan): RedirectResponse
    {
        $user = Auth::user();
        if (! ($user->id === $pekerjaan->pembuat_id || $user->canManagePekerjaan())) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus pekerjaan ini.');
        }

        // Hapus berkas ST fisik jika ada
        if ($pekerjaan->file_st && Storage::disk('public')->exists($pekerjaan->file_st)) {
            Storage::disk('public')->delete($pekerjaan->file_st);
        }

        // Hapus berkas bukti realisasi jika ada
        foreach ($pekerjaan->realisasi as $realisasi) {
            if ($realisasi->file_bukti && Storage::disk('public')->exists($realisasi->file_bukti)) {
                Storage::disk('public')->delete($realisasi->file_bukti);
            }
        }

        $judulPekerjaan = $pekerjaan->judul;
        $pekerjaan->delete();

        return redirect()->route('pekerjaan.index')
            ->with('success', "Pekerjaan '{$judulPekerjaan}' berhasil dihapus.");
    }

    /**
     * Halaman Tugas & Pekerjaan Saya untuk Pelaksana / Pengaju (PRD US05 & FR06)
     */
    public function pekerjaanSaya(Request $request): View
    {
        $user = Auth::user();

        $query = Pekerjaan::where(function ($q) use ($user) {
            $q->where('penanggung_jawab_id', $user->id)
                ->orWhere('pembuat_id', $user->id)
                ->orWhereHas('pelaksana', fn ($sub) => $sub->where('users.id', $user->id));
        })->with(['parent', 'pokja', 'penanggungJawab', 'pelaksana', 'realisasi' => fn ($r) => $r->latest()]);

        if ($request->filled('q')) {
            $qText = $request->q;
            $query->where(function ($q) use ($qText) {
                $q->where('judul', 'like', "%{$qText}%")
                    ->orWhere('kode_tampilan', 'like', "%{$qText}%");
            });
        }

        $activeTab = $request->get('tab', 'berjalan');

        // Filter tab status pelaksanaan
        if ($activeTab === 'berjalan') {
            $query->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
        } elseif ($activeTab === 'selesai') {
            $query->where('status_pelaksanaan', 'selesai_terverifikasi');
        }

        $pekerjaanList = $query->orderBy('tenggat', 'asc')->paginate(12);

        // Hitung total metrik tugas saya
        $baseCountQuery = Pekerjaan::where(function ($q) use ($user) {
            $q->where('penanggung_jawab_id', $user->id)
                ->orWhere('pembuat_id', $user->id)
                ->orWhereHas('pelaksana', fn ($sub) => $sub->where('users.id', $user->id));
        });

        $countBerjalan = (clone $baseCountQuery)
            ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi')
            ->count();

        $countSelesai = (clone $baseCountQuery)
            ->where('status_pelaksanaan', 'selesai_terverifikasi')
            ->count();

        return view('pekerjaan.saya', compact(
            'pekerjaanList',
            'activeTab',
            'countBerjalan',
            'countSelesai'
        ));
    }

    /**
     * Update Progres Realisasi & Bukti Dukung oleh Pelaksana (PRD FR06)
     */
    public function updateProgres(Request $request, Pekerjaan $pekerjaan): RedirectResponse
    {
        $user = Auth::user();

        // Pastikan pengguna berwenang (PIC penanggung jawab, tim pelaksana, pembuat/pengaju pekerjaan, atau admin)
        $pekerjaan->loadMissing('pelaksana');
        $isPelaksana = ($user->id === $pekerjaan->penanggung_jawab_id || $pekerjaan->pelaksana->contains('id', $user->id));
        $canUpdate = ($isPelaksana || $user->id === $pekerjaan->pembuat_id || $user->isAdmin());
        if (! $canUpdate) {
            abort(403, 'Hanya pelaksana atau pengaju penugasan yang berwenang melaporkan progres pekerjaan ini.');
        }

        // Pastikan rincian struktur telah disetujui (Tahap 1 selesai)
        if (! $pekerjaan->isDisetujui()) {
            return redirect()->back()->with('error', 'Pekerjaan '.$pekerjaan->kode_tampilan.' belum disetujui strukturnya oleh Katim/Pimpinan. Progres baru dapat dilaporkan setelah rincian disetujui.');
        }

        // 1. Aksi Cepat: Mulai Kerjakan (Belum Dimulai -> Dikerjakan)
        if ($request->input('aksi_status') === 'mulai_kerja') {
            $pekerjaan->status_pelaksanaan = 'dikerjakan';
            $pekerjaan->save();

            if ($pekerjaan->parent) {
                $pekerjaan->parent->recalculateProgresFromChildren();
            }

            return redirect()->back()->with('success', 'Penugasan '.$pekerjaan->kode_tampilan.' berhasil dimulai. Status kini Sedang Dikerjakan.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis_laporan' => ['nullable', 'string', 'in:kemajuan,penyelesaian'],
            'deskripsi_progres' => ['required', 'string'],
            'kendala' => ['nullable', 'string'],
            'tindak_lanjut' => ['nullable', 'string'],
            'nama_bukti' => ['nullable', 'string', 'max:255'],
            'link_bukti' => ['nullable', 'url'],
            'file_bukti' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx,xlsx,zip'],
        ], [
            'tanggal.required' => 'Tanggal kegiatan/pelaporan wajib diisi.',
            'tanggal.date' => 'Format tanggal kegiatan tidak valid.',
            'deskripsi_progres.required' => 'Uraian kemajuan pekerjaan wajib diisi.',
            'link_bukti.url' => 'Format tautan bukti harus berupa URL yang valid (contoh: https://drive.google.com/...).',
            'file_bukti.max' => 'Ukuran berkas bukti maksimal 10 MB.',
            'file_bukti.mimes' => 'Format berkas bukti harus berupa PDF, JPG, PNG, DOCX, XLSX, atau ZIP.',
        ]);

        $jenisLaporan = $request->input('jenis_laporan', 'kemajuan');
        $validated['jenis_laporan'] = $jenisLaporan;

        // Upload berkas bukti dukung jika ada
        if ($request->hasFile('file_bukti')) {
            $path = $request->file('file_bukti')->store('bukti_pekerjaan', 'public');
            $validated['file_bukti'] = $path;
            if (empty($validated['nama_bukti'])) {
                $validated['nama_bukti'] = $request->file('file_bukti')->getClientOriginalName();
            }
        }

        $validated['pekerjaan_id'] = $pekerjaan->id;
        $validated['user_id'] = $user->id;
        $validated['status_approval'] = 'menunggu_persetujuan';

        RealisasiPekerjaan::create($validated);

        if ($jenisLaporan === 'penyelesaian') {
            $pekerjaan->status_pelaksanaan = 'hasil_diajukan';
            $pekerjaan->save();
            $pesan = 'Laporan penyelesaian tugas '.$pekerjaan->kode_tampilan.' berhasil diajukan! Menunggu peninjauan & verifikasi akhir dari Katim/Pemberi Tugas.';
        } else {
            if ($pekerjaan->status_pelaksanaan === 'belum_dimulai') {
                $pekerjaan->status_pelaksanaan = 'dikerjakan';
                $pekerjaan->save();
            }
            $pesan = 'Laporan kemajuan penugasan '.$pekerjaan->kode_tampilan.' berhasil diajukan! Menunggu persetujuan (ACC) dari pembuat tugas/Katim.';
        }

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * Setujui Laporan Progres Pegawai oleh Katim / Koor / Pembuat Tugas
     */
    public function approveRealisasi(Request $request, RealisasiPekerjaan $realisasi): RedirectResponse
    {
        $user = Auth::user();
        $pekerjaan = $realisasi->pekerjaan;

        $pekerjaan->loadMissing(['parent', 'pokja']);
        $isPembuat = ($pekerjaan->pembuat_id === $user->id)
            || ($pekerjaan->parent && ($pekerjaan->parent->pembuat_id === $user->id || $pekerjaan->parent->penanggung_jawab_id === $user->id));
        $isKatimPokja = ($user->isKatim() && $pekerjaan->pokja && ($pekerjaan->pokja->katim_id === $user->id || $pekerjaan->pokja_id === $user->pokja_id));
        $isKoorUnit = (($user->isKorlayanan() || $user->isKorjafung()) && $pekerjaan->pokja_id === $user->pokja_id);

        if (! $isPembuat && ! $isKatimPokja && ! $isKoorUnit && ! $user->isAdmin()) {
            abort(403, 'Hanya Katim / Koordinator pembuat tugas yang berwenang menyetujui laporan progres ini.');
        }

        $realisasi->update([
            'status_approval' => 'disetujui',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'catatan_approval' => $request->input('catatan'),
        ]);

        if ($realisasi->jenis_laporan === 'penyelesaian') {
            $pekerjaan->status_pelaksanaan = 'selesai_terverifikasi';
            $pekerjaan->tanggal_selesai_terverifikasi = now();
            $pekerjaan->save();

            if ($pekerjaan->parent) {
                $pekerjaan->parent->recalculateProgresFromChildren();
            }

            RiwayatPersetujuanPekerjaan::create([
                'pekerjaan_id' => $pekerjaan->id,
                'user_id' => $user->id,
                'jenis_aksi' => 'approval_penyelesaian_disetujui',
                'status_sebelumnya' => 'hasil_diajukan',
                'status_baru' => 'selesai_terverifikasi',
                'catatan' => 'Laporan penyelesaian tugas oleh '.$realisasi->user->name.' disetujui. Tugas dinyatakan Selesai Terverifikasi.',
            ]);

            $pesan = 'Laporan penyelesaian tugas dari '.$realisasi->user->name.' berhasil disetujui! Penugasan resmi dinyatakan Selesai Terverifikasi.';
        } else {
            if ($pekerjaan->status_pelaksanaan === 'belum_dimulai') {
                $pekerjaan->status_pelaksanaan = 'dikerjakan';
                $pekerjaan->save();
            }

            RiwayatPersetujuanPekerjaan::create([
                'pekerjaan_id' => $pekerjaan->id,
                'user_id' => $user->id,
                'jenis_aksi' => 'approval_progres_disetujui',
                'status_sebelumnya' => 'menunggu_persetujuan',
                'status_baru' => 'disetujui',
                'catatan' => 'Laporan kemajuan berkala oleh '.$realisasi->user->name.' disetujui.',
            ]);

            $pesan = 'Laporan kemajuan dari '.$realisasi->user->name.' berhasil disetujui (ACC).';
        }

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * Tolak Laporan Progres Pegawai oleh Katim / Koor / Pembuat Tugas
     */
    public function rejectRealisasi(Request $request, RealisasiPekerjaan $realisasi): RedirectResponse
    {
        $user = Auth::user();
        $pekerjaan = $realisasi->pekerjaan;

        $pekerjaan->loadMissing(['parent', 'pokja']);
        $isPembuat = ($pekerjaan->pembuat_id === $user->id)
            || ($pekerjaan->parent && ($pekerjaan->parent->pembuat_id === $user->id || $pekerjaan->parent->penanggung_jawab_id === $user->id));
        $isKatimPokja = ($user->isKatim() && $pekerjaan->pokja && ($pekerjaan->pokja->katim_id === $user->id || $pekerjaan->pokja_id === $user->pokja_id));
        $isKoorUnit = (($user->isKorlayanan() || $user->isKorjafung()) && $pekerjaan->pokja_id === $user->pokja_id);

        if (! $isPembuat && ! $isKatimPokja && ! $isKoorUnit && ! $user->isAdmin()) {
            abort(403, 'Hanya Katim / Koordinator pembuat tugas yang berwenang menolak laporan progres ini.');
        }

        $validated = $request->validate([
            'catatan' => ['required', 'string', 'max:1000'],
        ]);

        $realisasi->update([
            'status_approval' => 'ditolak',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'catatan_approval' => $validated['catatan'],
        ]);

        if ($realisasi->jenis_laporan === 'penyelesaian') {
            $pekerjaan->status_pelaksanaan = 'perlu_perbaikan_hasil';
            $pekerjaan->save();
        }

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'approval_progres_ditolak',
            'status_sebelumnya' => 'menunggu_persetujuan',
            'status_baru' => 'ditolak',
            'catatan' => 'Laporan oleh '.$realisasi->user->name.' ditolak: '.$validated['catatan'],
        ]);

        return redirect()->back()->with('success', 'Laporan dari '.$realisasi->user->name.' telah ditolak dengan catatan evaluasi.');

        // Rekalkulasi progres pekerjaan
        $pekerjaan->recalculateProgresFromRealisasi();
        $pekerjaan->refresh();

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'approval_progres_ditolak',
            'status_sebelumnya' => 'menunggu_persetujuan',
            'status_baru' => 'ditolak',
            'catatan' => 'Laporan progres oleh '.$realisasi->user->name.' ditolak: '.$validated['catatan'],
        ]);

        return redirect()->back()->with('info', 'Laporan progres dari '.$realisasi->user->name.' ditolak dengan catatan perbaikan.');
    }

    /**
     * Pusat Persetujuan & Verifikasi Pekerjaan (PRD FR04 & FR07)
     */
    public function persetujuanIndex(Request $request, PekerjaanPersetujuanService $persetujuanService): View
    {
        $data = $persetujuanService->getPersetujuanData(Auth::user(), $request);

        return view('pekerjaan.persetujuan', $data);
    }

    /**
     * Persetujuan / Permintaan Perbaikan Struktur Rincian oleh Katim (PRD FR04)
     */
    public function reviewRincian(Request $request, Pekerjaan $pekerjaan): RedirectResponse
    {
        $user = Auth::user();
        $isPemberiTugas = ($pekerjaan->pembuat_id === $user->id)
            || ($pekerjaan->parent && ($pekerjaan->parent->pembuat_id === $user->id || $pekerjaan->parent->penanggung_jawab_id === $user->id));

        if (! $user->canReviewPekerjaan()) {
            abort(403, 'Anda tidak memiliki hak wewenang untuk menyetujui rincian pekerjaan.');
        }

        $validated = $request->validate([
            'aksi' => ['required', 'in:setujui,minta_perbaikan,batalkan'],
            'catatan' => ['nullable', 'string'],
        ]);

        $statusSebelumnya = $pekerjaan->status_persetujuan;

        if ($validated['aksi'] === 'setujui') {
            $pekerjaan->status_persetujuan = 'disetujui';
            $pekerjaan->status_pelaksanaan = $pekerjaan->status_pelaksanaan === 'belum_dimulai' ? 'dikerjakan' : $pekerjaan->status_pelaksanaan;
            $pesan = 'Rincian pekerjaan '.$pekerjaan->kode_tampilan.' berhasil disetujui. Penugasan kini aktif.';
        } elseif ($validated['aksi'] === 'minta_perbaikan') {
            $pekerjaan->status_persetujuan = 'perlu_perbaikan';
            $pesan = 'Rincian pekerjaan '.$pekerjaan->kode_tampilan.' dikembalikan untuk diperbaiki oleh pelaksana.';
        } else {
            $pekerjaan->status_persetujuan = 'dibatalkan';
            $pesan = 'Rincian pekerjaan '.$pekerjaan->kode_tampilan.' telah dibatalkan.';
        }

        $pekerjaan->catatan_persetujuan = $validated['catatan'] ?? null;
        $pekerjaan->save();

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'review_rincian_'.$validated['aksi'],
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $pekerjaan->status_persetujuan,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Hitung ulang progres induk
        if ($pekerjaan->parent) {
            $pekerjaan->parent->recalculateProgresFromChildren();
        }

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * Verifikasi & Penerimaan Hasil Akhir Pekerjaan oleh Pemeriksa (PRD FR07)
     */
    public function reviewHasil(Request $request, Pekerjaan $pekerjaan): RedirectResponse
    {
        $user = Auth::user();
        $isPemberiTugas = ($pekerjaan->pembuat_id === $user->id)
            || ($pekerjaan->parent && ($pekerjaan->parent->pembuat_id === $user->id || $pekerjaan->parent->penanggung_jawab_id === $user->id));

        if (! $user->canReviewPekerjaan() && ! $isPemberiTugas) {
            abort(403, 'Anda tidak memiliki hak wewenang untuk memverifikasi hasil pekerjaan.');
        }

        $validated = $request->validate([
            'aksi' => ['required', 'in:terima,minta_perbaikan'],
            'catatan' => ['nullable', 'string'],
        ]);

        $statusSebelumnya = $pekerjaan->status_pelaksanaan;

        if ($validated['aksi'] === 'terima') {
            $pekerjaan->status_pelaksanaan = 'selesai_terverifikasi';
            $pekerjaan->tanggal_selesai_terverifikasi = Carbon::now();
            $pesan = 'Hasil pekerjaan '.$pekerjaan->kode_tampilan.' telah diterima dan dinyatakan Selesai Terverifikasi.';
        } else {
            $pekerjaan->status_pelaksanaan = 'perlu_perbaikan_hasil';
            $pesan = 'Hasil pekerjaan '.$pekerjaan->kode_tampilan.' dikembalikan untuk diperbaiki oleh pelaksana.';
        }

        $pekerjaan->catatan_hasil = $validated['catatan'] ?? null;
        $pekerjaan->save();

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'review_hasil_'.$validated['aksi'],
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $pekerjaan->status_pelaksanaan,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Hitung ulang induk jika ada
        if ($pekerjaan->parent) {
            $pekerjaan->parent->recalculateProgresFromChildren();
        }

        return redirect()->back()->with('success', $pesan);
    }
}
