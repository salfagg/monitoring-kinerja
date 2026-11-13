<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Models\SkpTahunan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SkpController extends Controller
{
    /**
     * Tampilan Daftar SKP (Pegawai: miliknya sendiri, Katim/Pimpinan: semua/tim kerja)
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isManager = $user->canManagePekerjaan();

        $query = SkpTahunan::with(['user.pokja', 'pekerjaan', 'targetBulanan']);

        // Lingkup data
        if (! $isManager) {
            // Pegawai biasa: hanya melihat SKP miliknya sendiri
            $query->where('user_id', $user->id);
        } else {
            // Katim / Korjafung / Korlayanan: anggota pokja sendiri + miliknya
            if (! ($user->isKabal() || $user->isKasubag() || $user->isAdmin())) {
                $query->where(function ($q) use ($user) {
                    if ($user->pokja_id) {
                        $q->whereHas('user', fn($u) => $u->where('pokja_id', $user->pokja_id));
                    }
                    $q->orWhere('user_id', $user->id);
                });
            }
        }

        // Filter Tahun
        $tahun = $request->input('tahun', date('Y'));
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $query->where('tahun', $request->tahun);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Pegawai (khusus manager)
        if ($isManager && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Pokja (khusus pimpinan/admin)
        if ($isManager && $request->filled('pokja_id')) {
            $query->whereHas('user', fn($u) => $u->where('pokja_id', $request->pokja_id));
        }

        // Pencarian teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rencana_kinerja', 'like', "%{$search}%")
                  ->orWhere('indikator_kinerja', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $skpList = $query->orderByDesc('tahun')->latest()->paginate(15);

        // Metrik
        $baseMetrik = SkpTahunan::query();
        if (! $isManager) {
            $baseMetrik->where('user_id', $user->id);
        } elseif (! ($user->isKabal() || $user->isKasubag() || $user->isAdmin())) {
            $baseMetrik->where(function ($q) use ($user) {
                if ($user->pokja_id) {
                    $q->whereHas('user', fn($u) => $u->where('pokja_id', $user->pokja_id));
                }
                $q->orWhere('user_id', $user->id);
            });
        }

        $totalSkp = (clone $baseMetrik)->count();
        $skpDisetujui = (clone $baseMetrik)->where('status', 'disetujui')->count();
        $skpDiajukan = (clone $baseMetrik)->where('status', 'diajukan')->count();
        $skpDraft = (clone $baseMetrik)->whereIn('status', ['draft', 'revisi'])->count();

        // Data dropdown untuk filter
        $users = $isManager ? User::with('pokja')->orderBy('name')->get() : collect([$user]);
        $pokjas = Pokja::orderBy('kode_pokja')->get();

        return view('skp.index', compact(
            'skpList',
            'isManager',
            'totalSkp',
            'skpDisetujui',
            'skpDiajukan',
            'skpDraft',
            'users',
            'pokjas',
            'tahun'
        ));
    }

    /**
     * Form Tambah SKP
     */
    public function create(): View
    {
        $user = Auth::user();
        $isManager = $user->canManagePekerjaan();

        if ($user->isKabal() || $user->isKasubag() || $user->isAdmin()) {
            $users = User::with('pokja')->orderBy('name')->get();
        } elseif ($user->isKatim() || $user->isKorlayanan() || $user->isKorjafung()) {
            $users = User::where('pokja_id', $user->pokja_id)->orWhere('id', $user->id)->orderBy('name')->get();
        } else {
            $users = collect([$user]);
        }

        return view('skp.create', compact('users', 'isManager'));
    }

    /**
     * Simpan SKP Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isManager = $user->canManagePekerjaan();

        $validated = $request->validate([
            'user_id' => [$isManager ? 'required' : 'nullable', 'exists:users,id'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2035'],
            'rencana_kinerja' => ['required', 'string', 'max:1000'],
            'aspek' => ['required', 'string', 'in:Kuantitas,Kualitas,Waktu'],
            'indikator_kinerja' => ['required', 'string', 'max:1000'],
            'target_min' => ['nullable', 'numeric', 'min:0'],
            'target_max' => ['required', 'numeric', 'min:0.01'],
            'satuan' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:draft,diajukan,disetujui,revisi'],
        ]);

        if (! $isManager) {
            $validated['user_id'] = $user->id;
            // Pegawai biasa menyetor draf atau mengajukan
            if (! in_array($validated['status'], ['draft', 'diajukan'])) {
                $validated['status'] = 'diajukan';
            }
        }

        $validated['target_min'] = $validated['target_min'] ?? 0;

        $skp = SkpTahunan::create($validated);

        return redirect()->route('skp.index')
            ->with('success', "Sasaran Kinerja Pegawai (SKP) '{$skp->rencana_kinerja}' berhasil ditambahkan.");
    }

    /**
     * Rincian SKP
     */
    public function show(SkpTahunan $skp): View
    {
        $user = Auth::user();
        $isManager = $user->canManagePekerjaan();

        if (! $isManager && $skp->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang melihat SKP pegawai lain.');
        }

        $skp->load(['user.pokja', 'pekerjaan.penanggungJawab', 'targetBulanan']);

        return view('skp.show', compact('skp', 'isManager'));
    }

    /**
     * Form Edit SKP
     */
    public function edit(SkpTahunan $skp): View
    {
        $user = Auth::user();
        $isManager = $user->canManagePekerjaan();

        if (! $isManager && $skp->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang mengedit SKP pegawai lain.');
        }

        if ($user->isKabal() || $user->isKasubag() || $user->isAdmin()) {
            $users = User::with('pokja')->orderBy('name')->get();
        } elseif ($user->isKatim() || $user->isKorlayanan() || $user->isKorjafung()) {
            $users = User::where('pokja_id', $user->pokja_id)->orWhere('id', $user->id)->orderBy('name')->get();
        } else {
            $users = collect([$skp->user]);
        }

        return view('skp.edit', compact('skp', 'users', 'isManager'));
    }

    /**
     * Update SKP
     */
    public function update(Request $request, SkpTahunan $skp): RedirectResponse
    {
        $user = Auth::user();
        $isManager = $user->canManagePekerjaan();

        if (! $isManager && $skp->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang memperbarui SKP ini.');
        }

        $validated = $request->validate([
            'user_id' => [$isManager ? 'required' : 'nullable', 'exists:users,id'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2035'],
            'rencana_kinerja' => ['required', 'string', 'max:1000'],
            'aspek' => ['required', 'string', 'in:Kuantitas,Kualitas,Waktu'],
            'indikator_kinerja' => ['required', 'string', 'max:1000'],
            'target_min' => ['nullable', 'numeric', 'min:0'],
            'target_max' => ['required', 'numeric', 'min:0.01'],
            'satuan' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:draft,diajukan,disetujui,revisi'],
        ]);

        if (! $isManager) {
            $validated['user_id'] = $skp->user_id;
            if ($skp->status === 'disetujui' && $validated['status'] === 'disetujui') {
                // Biarkan disetujui jika tidak diubah
            } elseif (! in_array($validated['status'], ['draft', 'diajukan'])) {
                $validated['status'] = 'diajukan';
            }
        }

        $validated['target_min'] = $validated['target_min'] ?? 0;

        $skp->update($validated);

        return redirect()->route('skp.index')
            ->with('success', "Sasaran Kinerja Pegawai (SKP) '{$skp->rencana_kinerja}' berhasil diperbarui.");
    }

    /**
     * Hapus SKP
     */
    public function destroy(SkpTahunan $skp): RedirectResponse
    {
        $user = Auth::user();
        $isManager = $user->canManagePekerjaan();

        if (! $isManager && $skp->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang menghapus SKP ini.');
        }

        // Cek apakah ada pekerjaan yang merujuk ke SKP ini
        if ($skp->pekerjaan()->count() > 0) {
            return redirect()->back()
                ->with('error', "SKP ini tidak dapat dihapus karena sudah dirujuk oleh {$skp->pekerjaan()->count()} pekerjaan aktif. Ubah rujukan pekerjaan terlebih dahulu.");
        }

        $skp->delete();

        return redirect()->route('skp.index')
            ->with('success', "SKP berhasil dihapus.");
    }

    /**
     * Setujui SKP Cepat (Approval oleh Katim / Pimpinan)
     */
    public function approve(SkpTahunan $skp): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManagePekerjaan()) {
            abort(403, 'Hanya Katim atau Pimpinan yang dapat menyetujui SKP.');
        }

        $skp->update(['status' => 'disetujui']);

        return redirect()->back()
            ->with('success', "SKP Pegawai '{$skp->user?->name}' berhasil disetujui.");
    }
}

