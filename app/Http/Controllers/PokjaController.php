<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PokjaController extends Controller
{
    public function index(): View
    {
        $pokjas = Pokja::with([
                'katim',
                'anggota',
                'pekerjaan' => fn($q) => $q->select('id', 'pokja_id', 'progres', 'status_pelaksanaan')
            ])
            ->withCount(['anggota', 'pekerjaan'])
            ->orderBy('id')
            ->get();

        $allUsers = User::orderBy('name')->get();

        return view('pokjas.index', compact('pokjas', 'allUsers'));
    }

    public function create(): View
    {
        $potentialKatims = User::whereIn('role', ['katim_korjafung', 'tim_pokja', 'pegawai', 'kasubag', 'korlayanan'])
            ->orderBy('name')
            ->get();
        return view('pokjas.create', compact('potentialKatims'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pokja' => ['required', 'string', 'max:255'],
            'kode_pokja' => ['required', 'string', 'max:50', 'unique:pokjas,kode_pokja'],
            'tipe' => ['required', 'string', 'in:tim_kerja,kelompok_kerja,subbag_pilar'],
            'deskripsi' => ['nullable', 'string'],
            'katim_id' => ['nullable', 'exists:users,id'],
        ]);

        $pokja = Pokja::create($validated);

        if (!empty($validated['katim_id'])) {
            $selectedUser = User::find($validated['katim_id']);
            if ($selectedUser) {
                $updateData = ['pokja_id' => $pokja->id];
                if (!in_array($selectedUser->role, ['kasubag', 'kabal', 'admin'])) {
                    $updateData['role'] = 'katim_korjafung';
                }
                $selectedUser->update($updateData);

                // Masukkan ketua ke pivot
                $pokja->anggota()->syncWithoutDetaching([
                    $selectedUser->id => ['peran' => 'ketua']
                ]);
            }
        }

        return redirect()->route('pokjas.index')
            ->with('success', "Pokja {$pokja->nama_pokja} berhasil dibuat.");
    }

    public function edit(Pokja $pokja): View
    {
        $potentialKatims = User::whereIn('role', ['katim_korjafung', 'tim_pokja', 'pegawai', 'kasubag', 'korlayanan'])
            ->orderBy('name')
            ->get();
        return view('pokjas.edit', compact('pokja', 'potentialKatims'));
    }

    public function update(Request $request, Pokja $pokja): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pokja' => ['required', 'string', 'max:255'],
            'kode_pokja' => ['required', 'string', 'max:50', Rule::unique('pokjas', 'kode_pokja')->ignore($pokja->id)],
            'tipe' => ['required', 'string', 'in:tim_kerja,kelompok_kerja,subbag_pilar'],
            'deskripsi' => ['nullable', 'string'],
            'katim_id' => ['nullable', 'exists:users,id'],
        ]);

        $pokja->update($validated);

        if (!empty($validated['katim_id'])) {
            $selectedUser = User::find($validated['katim_id']);
            if ($selectedUser) {
                $updateData = ['pokja_id' => $pokja->id];
                if (!in_array($selectedUser->role, ['kasubag', 'kabal', 'admin'])) {
                    $updateData['role'] = 'katim_korjafung';
                }
                $selectedUser->update($updateData);

                // Masukkan ketua ke pivot
                $pokja->anggota()->syncWithoutDetaching([
                    $selectedUser->id => ['peran' => 'ketua']
                ]);
            }
        }

        return redirect()->route('pokjas.index')
            ->with('success', "Pokja {$pokja->nama_pokja} berhasil diperbarui.");
    }

    public function syncAnggota(Request $request, Pokja $pokja): RedirectResponse
    {
        $validated = $request->validate([
            'anggota_ids' => ['nullable', 'array'],
            'anggota_ids.*' => ['exists:users,id'],
        ]);

        $anggotaIds = $validated['anggota_ids'] ?? [];
        $syncData = [];

        // Tetap masukkan ketua ke pivot jika ada
        if ($pokja->katim_id) {
            $syncData[$pokja->katim_id] = ['peran' => 'ketua'];
        }

        foreach ($anggotaIds as $id) {
            if ($id != $pokja->katim_id) {
                $syncData[$id] = ['peran' => 'anggota'];
            }
        }

        $pokja->anggota()->sync($syncData);

        return back()->with('success', "Keanggotaan untuk {$pokja->nama_pokja} berhasil diperbarui ({$pokja->anggota()->count()} pegawai).");
    }

    public function destroy(Pokja $pokja): RedirectResponse
    {
        // 1. Lepaskan relasi pekerjaan jika ada agar integritas data pekerjaan tetap terjaga
        if ($pokja->pekerjaan()->count() > 0) {
            $pokja->pekerjaan()->update(['pokja_id' => null]);
        }

        // 2. Lepaskan keanggotaan di tabel pivot pokja_user
        $pokja->anggota()->detach();

        // 3. Lepaskan pokja_id utama di tabel users jika ada
        User::where('pokja_id', $pokja->id)->update(['pokja_id' => null]);

        $nama = $pokja->nama_pokja;
        $pokja->delete();

        return redirect()->route('pokjas.index')
            ->with('success', "Unit / Kelompok Kerja \"{$nama}\" berhasil dihapus.");
    }
}
