<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('pokja');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'pimpinan_katim') {
                $query->whereIn('role', ['kabal', 'kasubag', 'korjafung', 'katim', 'korlayanan']);
            } else {
                $query->where('role', $role);
            }
        }

        if ($request->filled('pokja_id')) {
            $query->where('pokja_id', $request->pokja_id);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $pokjas = Pokja::orderBy('kode_pokja')->get();

        $totalUsers = User::count();
        $totalPimpinanKatim = User::whereIn('role', ['kabal', 'kasubag', 'korjafung', 'katim', 'korlayanan'])->count();
        $totalPegawai = User::where('role', 'pegawai')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        return view('users.index', compact('users', 'pokjas', 'totalUsers', 'totalPimpinanKatim', 'totalPegawai', 'totalAdmin'));
    }

    public function create(): View
    {
        $pokjas = Pokja::orderBy('nama_pokja')->get();
        return view('users.create', compact('pokjas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:users,nip'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(6)],
            'role' => ['required', Rule::in(['kabal', 'kasubag', 'korjafung', 'katim', 'korlayanan', 'pegawai', 'admin', 'katim_korjafung', 'tim_pokja'])],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'pokja_id' => ['nullable', 'exists:pokjas,id'],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $pokjas = Pokja::orderBy('nama_pokja')->get();
        return view('users.edit', compact('user', 'pokjas'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nip')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(6)],
            'role' => ['required', Rule::in(['kabal', 'kasubag', 'korjafung', 'katim', 'korlayanan', 'pegawai', 'admin', 'katim_korjafung', 'tim_pokja'])],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'pokja_id' => ['nullable', 'exists:pokjas,id'],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$name} berhasil dihapus dari sistem.");
    }
}
