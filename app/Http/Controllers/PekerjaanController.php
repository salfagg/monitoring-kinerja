<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RealisasiPekerjaan;
use App\Models\RiwayatPersetujuanPekerjaan;
use App\Models\SkpTahunan;
use App\Models\SkpTargetBulanan;
use App\Models\User;
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
            'realisasi' => fn ($r) => $r->latest()
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

        $totalPekerjaan    = (clone $baseMetrik)->count();
        $pekerjaanBerjalan = (clone $baseMetrik)->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan', 'perlu_perbaikan_hasil'])->count();
        $antreanHasil      = (clone $baseMetrik)->where('status_pelaksanaan', 'hasil_diajukan')->count();
        $pekerjaanSelesai  = (clone $baseMetrik)->where('status_pelaksanaan', 'selesai_terverifikasi')->count();

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
        $suggestedKode = 'TGS-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

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
            $validated['kode_tampilan'] = 'TGS-' . str_pad(Pekerjaan::count() + 1, 3, '0', STR_PAD_LEFT);
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
        $validated['bobot'] = 100.00;
        // Penugasan oleh Katim/Koor/Pimpinan langsung berstatus disetujui agar anggota dapat langsung update progres!
        $validated['status_persetujuan'] = 'disetujui';
        $validated['status_pelaksanaan'] = 'belum_dimulai';
        $validated['progres'] = 0.00;

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
        if (!in_array($pekerjaan->penanggung_jawab_id, $pelaksanaIds)) {
            $pelaksanaIds[] = $pekerjaan->penanggung_jawab_id;
        }
        $syncData = [];
        foreach ($pelaksanaIds as $uId) {
            $syncData[$uId] = [
                'peran' => ((int)$uId === (int)$pekerjaan->penanggung_jawab_id) ? 'pic_utama' : 'anggota',
            ];
        }
        $pekerjaan->pelaksana()->sync($syncData);

        return redirect()->route('pekerjaan.index')
            ->with('success', "Pekerjaan '{$pekerjaan->judul}' berhasil dibuat dan langsung ditugaskan ke pegawai pelaksana.");
        // Catat riwayat audit penugasan
        $catatanAwal = !empty($validated['nomor_st'])
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
            'riwayatPersetujuan.user'
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
        if (!in_array($pekerjaan->penanggung_jawab_id, $pelaksanaIds)) {
            $pelaksanaIds[] = $pekerjaan->penanggung_jawab_id;
        }
        $syncData = [];
        foreach ($pelaksanaIds as $uId) {
            $syncData[$uId] = [
                'peran' => ((int)$uId === (int)$pekerjaan->penanggung_jawab_id) ? 'pic_utama' : 'anggota',
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
            return redirect()->back()->with('error', 'Pekerjaan ' . $pekerjaan->kode_tampilan . ' belum disetujui strukturnya oleh Katim/Pimpinan. Progres baru dapat dilaporkan setelah rincian disetujui.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'progres_capaian' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'mode_input' => ['nullable', 'string', 'in:tambah,set_total'],
            'deskripsi_progres' => ['required', 'string'],
            'kendala' => ['nullable', 'string'],
            'tindak_lanjut' => ['nullable', 'string'],
            'nama_bukti' => ['nullable', 'string', 'max:255'],
            'link_bukti' => ['nullable', 'url'],
            'file_bukti' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx,xlsx,zip'],
        ]);

        $progresSaatIni = (float) $pekerjaan->progres;
        $sisaProgres = max(0, 100 - $progresSaatIni);

        if ($sisaProgres <= 0) {
            return redirect()->back()->with('info', 'Pekerjaan ' . $pekerjaan->kode_tampilan . ' sudah mencapai 100% dan tidak memerlukan penambahan progres lagi.');
        }

        // Tentukan nilai kontribusi / penambahan progres pada log ini
        if ($request->input('mode_input') === 'set_total') {
            // Mode set total langsung (misal klik Selesaikan / Set 100%)
            $targetTotal = min(100, max($progresSaatIni, (float) $validated['progres_capaian']));
            $nilaiKontribusi = round($targetTotal - $progresSaatIni, 2);
        } else {
            // Mode default: Akumulasi penambahan progres (+%)
            $nilaiKontribusi = round(min($sisaProgres, (float) $validated['progres_capaian']), 2);
        }

        if ($nilaiKontribusi <= 0) {
            return redirect()->back()->with('error', 'Nilai penambahan progres harus lebih dari 0%.');
        }

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
        $validated['progres_capaian'] = $nilaiKontribusi;
        unset($validated['mode_input']);

        // Jika pelapor adalah pembuat/pemberi tugas itu sendiri, langsung disetujui
        $isPembuat = ($user->id === $pekerjaan->pembuat_id);
        if ($isPembuat) {
            $validated['status_approval'] = 'disetujui';
            $validated['approved_by'] = $user->id;
            $validated['approved_at'] = now();
        } else {
            $validated['status_approval'] = 'menunggu_persetujuan';
        }

        $realisasi = RealisasiPekerjaan::create($validated);

        if ($isPembuat) {
            // Rekalkulasi progres pekerjaan secara otomatis
            $pekerjaan->recalculateProgresFromRealisasi();
            $pekerjaan->refresh();

            $pesan = 'Laporan progres ' . $pekerjaan->kode_tampilan . ' berhasil dicatat dan disetujui (+' . number_format($nilaiKontribusi, 1) . '%). Total progres akumulasi saat ini: ' . number_format($pekerjaan->progres, 1) . '%.';
        } else {
            $pembuatNama = $pekerjaan->pembuat ? $pekerjaan->pembuat->name : 'Katim/Koordinator';
            $pesan = 'Laporan progres (+' . number_format($nilaiKontribusi, 1) . '%) berhasil diajukan! Menunggu persetujuan dari ' . $pembuatNama . ' (Pemberi Tugas) sebelum progres ditambahkan ke capaian pekerjaan.';
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

        $isPembuat = ($pekerjaan->pembuat_id === $user->id);
        $isKatimPokja = ($user->isKatim() && $pekerjaan->pokja && $pekerjaan->pokja->katim_id === $user->id);
        $isKoorUnit = (($user->isKorlayanan() || $user->isKorjafung()) && $pekerjaan->pokja_id === $user->pokja_id);

        if (!$isPembuat && !$isKatimPokja && !$isKoorUnit && !$user->isAdmin()) {
            abort(403, 'Hanya Katim / Koordinator pembuat tugas yang berwenang menyetujui laporan progres ini.');
        }

        $realisasi->update([
            'status_approval' => 'disetujui',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'catatan_approval' => $request->input('catatan'),
        ]);

        // Rekalkulasi progres pekerjaan
        $pekerjaan->recalculateProgresFromRealisasi();
        $pekerjaan->refresh();

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'approval_progres_disetujui',
            'status_sebelumnya' => 'menunggu_persetujuan',
            'status_baru' => 'disetujui',
            'catatan' => 'Laporan progres +' . number_format($realisasi->progres_capaian, 1) . '% oleh ' . $realisasi->user->name . ' disetujui.',
        ]);

        $pesan = 'Laporan progres dari ' . $realisasi->user->name . ' (+' . number_format($realisasi->progres_capaian, 1) . '%) berhasil disetujui! Progres pekerjaan kini ' . number_format($pekerjaan->progres, 1) . '%.';
        if ($pekerjaan->progres >= 100) {
            $pesan .= ' Pekerjaan telah tuntas 100% dan dinyatakan Selesai!';
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

        $isPembuat = ($pekerjaan->pembuat_id === $user->id);
        $isKatimPokja = ($user->isKatim() && $pekerjaan->pokja && $pekerjaan->pokja->katim_id === $user->id);
        $isKoorUnit = (($user->isKorlayanan() || $user->isKorjafung()) && $pekerjaan->pokja_id === $user->pokja_id);

        if (!$isPembuat && !$isKatimPokja && !$isKoorUnit && !$user->isAdmin()) {
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

        // Rekalkulasi progres pekerjaan
        $pekerjaan->recalculateProgresFromRealisasi();
        $pekerjaan->refresh();

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'approval_progres_ditolak',
            'status_sebelumnya' => 'menunggu_persetujuan',
            'status_baru' => 'ditolak',
            'catatan' => 'Laporan progres oleh ' . $realisasi->user->name . ' ditolak: ' . $validated['catatan'],
        ]);

        return redirect()->back()->with('info', 'Laporan progres dari ' . $realisasi->user->name . ' ditolak dengan catatan perbaikan.');
    }

    /**
     * Pusat Persetujuan & Verifikasi Pekerjaan (PRD FR04 & FR07)
     */
    public function persetujuanIndex(Request $request): View
    {
        $user = Auth::user();

        // Base Query untuk filter lingkup kerja / role
        $baseQuery = Pekerjaan::query();

        if ($user->isAdmin() || $user->isKabal() || $user->isKasubag()) {
            // Pimpinan Balai, Kasubag TU, dan Administrator memiliki hak supervisi seluruh unit balai
            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isKatim()) {
            // Katim berwenang menyetujui:
            // 1. Seluruh pekerjaan dalam Pokja yang dipimpinnya
            // 2. Pekerjaan yang dia buat/tugaskan langsung (pembuat_id)
            // 3. Subpekerjaan turunan dari tugas induk milik Katim (parent.pembuat_id atau parent.penanggung_jawab_id)
            $katimPokjaIds = Pokja::where('katim_id', $user->id)->pluck('id')->toArray();
            if ($user->pokja_id && !in_array($user->pokja_id, $katimPokjaIds)) {
                $katimPokjaIds[] = $user->pokja_id;
            }

            $baseQuery->where(function ($q) use ($user, $katimPokjaIds) {
                if (!empty($katimPokjaIds)) {
                    $q->whereIn('pokja_id', $katimPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                  ->orWhere('penanggung_jawab_id', $user->id)
                  ->orWhereHas('parent', function ($p) use ($user, $katimPokjaIds) {
                      $p->where('pembuat_id', $user->id)
                        ->orWhere('penanggung_jawab_id', $user->id);
                      if (!empty($katimPokjaIds)) {
                          $p->orWhereIn('pokja_id', $katimPokjaIds);
                      }
                  });
            });

            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isKorlayanan()) {
            // Koordinator Bidang/Layanan berwenang menyetujui:
            // 1. Seluruh pekerjaan lingkup Subbag Umum / unit layanannya
            // 2. Pekerjaan yang dibuat atau ditugaskannya (pembuat_id)
            // 3. Subpekerjaan turunan dari tugas induk milik Koordinator
            $subbagId = Pokja::where('kode_pokja', 'SUBBAG-UMUM')->value('id');
            $korPokjaIds = array_filter([$user->pokja_id, $subbagId]);

            $baseQuery->where(function ($q) use ($user, $korPokjaIds) {
                if (!empty($korPokjaIds)) {
                    $q->whereIn('pokja_id', $korPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                  ->orWhere('penanggung_jawab_id', $user->id)
                  ->orWhereHas('parent', function ($p) use ($user, $korPokjaIds) {
                      $p->where('pembuat_id', $user->id)
                        ->orWhere('penanggung_jawab_id', $user->id);
                      if (!empty($korPokjaIds)) {
                          $p->orWhereIn('pokja_id', $korPokjaIds);
                      }
                  });
            });

            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isKorjafung()) {
            // Koordinator Jabatan Fungsional berwenang atas tim fungsional (TIM-01 s.d. TIM-05) dan tugas yang dibuatnya
            $jfPokjaIds = Pokja::where('kode_pokja', 'LIKE', 'TIM%')->pluck('id')->toArray();
            $baseQuery->where(function ($q) use ($user, $jfPokjaIds) {
                if (!empty($jfPokjaIds)) {
                    $q->whereIn('pokja_id', $jfPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                  ->orWhere('penanggung_jawab_id', $user->id)
                  ->orWhereHas('parent', function ($p) use ($user) {
                      $p->where('pembuat_id', $user->id)
                        ->orWhere('penanggung_jawab_id', $user->id);
                  });
            });

            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isPegawai()) {
            // Pegawai biasa: memantau pengajuan miliknya atau yang ditugaskan kepadanya
            $baseQuery->where(function ($q) use ($user) {
                $q->where('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id);
            });
        }

        // Pencarian Teks
        if ($request->filled('q')) {
            $qText = $request->q;
            $baseQuery->where(function ($q) use ($qText) {
                $q->where('judul', 'like', "%{$qText}%")
                    ->orWhere('kode_tampilan', 'like', "%{$qText}%")
                    ->orWhere('nomor_st', 'like', "%{$qText}%")
                    ->orWhereHas('penanggungJawab', fn ($pj) => $pj->where('name', 'like', "%{$qText}%"));
            });
        }

        // 1. Antrean Persetujuan Laporan Progres Pegawai (Alur Baru: Katim/Koor Pembuat Tugas yang Approve)
        $progresQuery = RealisasiPekerjaan::with(['pekerjaan.pembuat', 'pekerjaan.pokja', 'pekerjaan.penanggungJawab', 'user'])
            ->where('status_approval', 'menunggu_persetujuan');

        if ($user->isAdmin() || $user->isKabal() || $user->isKasubag()) {
            if ($request->filled('pokja_id')) {
                $progresQuery->whereHas('pekerjaan', fn ($q) => $q->where('pokja_id', $request->pokja_id));
            }
        } elseif ($user->isKatim()) {
            $katimPokjaIds = Pokja::where('katim_id', $user->id)->pluck('id')->toArray();
            if ($user->pokja_id && !in_array($user->pokja_id, $katimPokjaIds)) {
                $katimPokjaIds[] = $user->pokja_id;
            }
            $progresQuery->whereHas('pekerjaan', function ($q) use ($user, $katimPokjaIds) {
                $q->where('pembuat_id', $user->id);
                if (!empty($katimPokjaIds)) {
                    $q->orWhereIn('pokja_id', $katimPokjaIds);
                }
            });
        } elseif ($user->isKorlayanan() || $user->isKorjafung()) {
            $progresQuery->whereHas('pekerjaan', function ($q) use ($user) {
                $q->where('pembuat_id', $user->id);
                if ($user->pokja_id) {
                    $q->orWhere('pokja_id', $user->pokja_id);
                }
            });
        } else {
            // Pegawai biasa: memantau progres yang diajukan oleh dirinya
            $progresQuery->where('user_id', $user->id);
        }

        if ($request->filled('q')) {
            $qText = $request->q;
            $progresQuery->where(function ($q) use ($qText) {
                $q->where('deskripsi_progres', 'like', "%{$qText}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$qText}%"))
                  ->orWhereHas('pekerjaan', fn ($p) => $p->where('judul', 'like', "%{$qText}%")->orWhere('kode_tampilan', 'like', "%{$qText}%"));
            });
        }

        $antreanProgres = $progresQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'page_progres');
        $countProgresPending = (clone $progresQuery)->count();

        // 2. Antrean Persetujuan Struktur Rincian (Draf / Pengajuan Tugas)
        $antreanRincian = (clone $baseQuery)
            ->whereIn('status_persetujuan', ['diajukan', 'perlu_perbaikan'])
            ->with(['parent', 'pembuat', 'penanggungJawab', 'pokja'])
            ->orderByRaw("FIELD(status_persetujuan, 'diajukan', 'perlu_perbaikan')")
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'page_rincian');

        // 3. Antrean Hasil Akhir (Legacy / Pimpinan)
        $antreanHasil = (clone $baseQuery)
            ->where(function ($q) {
                $q->where('status_pelaksanaan', 'hasil_diajukan')
                    ->orWhere(function ($sub) {
                        $sub->where('progres', '>=', 100)
                            ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
                    });
            })
            ->with(['penanggungJawab', 'pokja', 'realisasi' => fn ($r) => $r->latest()->with('user')])
            ->orderBy('updated_at', 'desc')
            ->paginate(15, ['*'], 'page_hasil');

        // 4. Riwayat Keputusan Persetujuan & Verifikasi
        $riwayatQuery = RiwayatPersetujuanPekerjaan::with(['pekerjaan.pokja', 'user'])
            ->latest();

        if ($user->isAdmin() || $user->isKabal() || $user->isKasubag()) {
            if ($request->filled('pokja_id')) {
                $riwayatQuery->whereHas('pekerjaan', fn ($q) => $q->where('pokja_id', $request->pokja_id));
            }
        } elseif ($user->isKatim()) {
            $katimPokjaIds = Pokja::where('katim_id', $user->id)->pluck('id')->toArray();
            if ($user->pokja_id && !in_array($user->pokja_id, $katimPokjaIds)) {
                $katimPokjaIds[] = $user->pokja_id;
            }
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user, $katimPokjaIds) {
                if (!empty($katimPokjaIds)) {
                    $q->whereIn('pokja_id', $katimPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                  ->orWhere('penanggung_jawab_id', $user->id)
                  ->orWhereHas('parent', fn ($p) => $p->where('pembuat_id', $user->id)->orWhere('penanggung_jawab_id', $user->id));
            });
        } elseif ($user->isKorlayanan()) {
            $subbagId = Pokja::where('kode_pokja', 'SUBBAG-UMUM')->value('id');
            $korPokjaIds = array_filter([$user->pokja_id, $subbagId]);
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user, $korPokjaIds) {
                if (!empty($korPokjaIds)) {
                    $q->whereIn('pokja_id', $korPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                  ->orWhere('penanggung_jawab_id', $user->id)
                  ->orWhereHas('parent', fn ($p) => $p->where('pembuat_id', $user->id)->orWhere('penanggung_jawab_id', $user->id));
            });
        } elseif ($user->isKorjafung()) {
            $jfPokjaIds = Pokja::where('kode_pokja', 'LIKE', 'TIM%')->pluck('id')->toArray();
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user, $jfPokjaIds) {
                if (!empty($jfPokjaIds)) {
                    $q->whereIn('pokja_id', $jfPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                  ->orWhere('penanggung_jawab_id', $user->id)
                  ->orWhereHas('parent', fn ($p) => $p->where('pembuat_id', $user->id)->orWhere('penanggung_jawab_id', $user->id));
            });
        } elseif ($user->isPegawai()) {
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user) {
                $q->where('pembuat_id', $user->id)
                  ->orWhere('penanggung_jawab_id', $user->id)
                  ->orWhereHas('pelaksana', fn ($sub) => $sub->where('users.id', $user->id));
            });
        }

        $riwayatPersetujuan = $riwayatQuery->paginate(15, ['*'], 'page_riwayat');

        // Metrik Statistik Cepat
        $countRincianPending = (clone $baseQuery)->where('status_persetujuan', 'diajukan')->count();
        $countRincianRevisi = (clone $baseQuery)->where('status_persetujuan', 'perlu_perbaikan')->count();
        $countHasilPending = (clone $baseQuery)->where(function ($q) {
            $q->where('status_pelaksanaan', 'hasil_diajukan')
                ->orWhere(function ($sub) {
                    $sub->where('progres', '>=', 100)
                        ->where('status_pelaksanaan', '!=', 'selesai_terverifikasi');
                });
        })->count();
        $countSelesaiTerverifikasi = (clone $baseQuery)->where('status_pelaksanaan', 'selesai_terverifikasi')->count();

        $pokjas = Pokja::orderBy('kode_pokja')->get();
        $activeTab = $request->get('tab', 'progres');
        $canReview = $user->canReviewPekerjaan();

        return view('pekerjaan.persetujuan', compact(
            'antreanProgres',
            'countProgresPending',
            'antreanRincian',
            'antreanHasil',
            'riwayatPersetujuan',
            'countRincianPending',
            'countRincianRevisi',
            'countHasilPending',
            'countSelesaiTerverifikasi',
            'pokjas',
            'activeTab',
            'canReview'
        ));
    }

    /**
     * Persetujuan / Permintaan Perbaikan Struktur Rincian oleh Katim (PRD FR04)
     */
    public function reviewRincian(Request $request, Pekerjaan $pekerjaan): RedirectResponse
    {
        $user = Auth::user();
        $isPemberiTugas = ($pekerjaan->pembuat_id === $user->id) 
            || ($pekerjaan->parent && ($pekerjaan->parent->pembuat_id === $user->id || $pekerjaan->parent->penanggung_jawab_id === $user->id));
        
        if (! $user->canReviewPekerjaan() && ! $isPemberiTugas) {
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
            $pesan = 'Rincian pekerjaan ' . $pekerjaan->kode_tampilan . ' berhasil disetujui. Penugasan kini aktif.';
        } elseif ($validated['aksi'] === 'minta_perbaikan') {
            $pekerjaan->status_persetujuan = 'perlu_perbaikan';
            $pesan = 'Rincian pekerjaan ' . $pekerjaan->kode_tampilan . ' dikembalikan untuk diperbaiki oleh pelaksana.';
        } else {
            $pekerjaan->status_persetujuan = 'dibatalkan';
            $pesan = 'Rincian pekerjaan ' . $pekerjaan->kode_tampilan . ' telah dibatalkan.';
        }

        $pekerjaan->catatan_persetujuan = $validated['catatan'] ?? null;
        $pekerjaan->save();

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'review_rincian_' . $validated['aksi'],
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
            $pesan = 'Hasil pekerjaan ' . $pekerjaan->kode_tampilan . ' telah diterima dan dinyatakan Selesai Terverifikasi.';
        } else {
            $pekerjaan->status_pelaksanaan = 'perlu_perbaikan_hasil';
            $pesan = 'Hasil pekerjaan ' . $pekerjaan->kode_tampilan . ' dikembalikan untuk diperbaiki oleh pelaksana.';
        }

        $pekerjaan->catatan_hasil = $validated['catatan'] ?? null;
        $pekerjaan->save();

        RiwayatPersetujuanPekerjaan::create([
            'pekerjaan_id' => $pekerjaan->id,
            'user_id' => $user->id,
            'jenis_aksi' => 'review_hasil_' . $validated['aksi'],
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
