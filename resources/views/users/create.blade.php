@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru | SIMON-K')
@section('page-title', 'Tambah Pengguna Baru')

@section('styles')
<style>
    .form-container {
        max-width: 800px;
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 32px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .form-label .required {
        color: #ef4444;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 13.5px;
        background: #f8fafc;
        color: var(--text-dark);
        outline: none;
        transition: var(--transition);
    }

    .form-input:focus, .form-select:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .error-feedback {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .btn-submit {
        background: var(--primary);
        color: #ffffff;
        padding: 11px 22px;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 13.5px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .btn-submit:hover {
        background: var(--primary-hover);
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        padding: 11px 20px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 13.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: var(--transition);
    }

    .btn-cancel:hover {
        background: #e2e8f0;
    }

    @media (max-width: 700px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

    <div style="margin-bottom: 24px;">
        <a href="{{ route('users.index') }}" style="color: var(--primary); font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
            <i class="ph-bold ph-arrow-left"></i> Kembali ke Daftar Pengguna
        </a>
    </div>

    <div class="form-container">
        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Form Tambah Pengguna Baru</h2>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 28px;">Lengkapi data diri dan role akun pegawai yang akan didaftarkan</p>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap & Gelar <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Pd., M.M." required>
                    @error('name') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nip">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" id="nip" class="form-input" value="{{ old('nip') }}" placeholder="18 digit angka NIP">
                    @error('nip') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Resmi <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" placeholder="pegawai@bgtk.go.id" required>
                    @error('email') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi Awal <span class="required">*</span></label>
                    <input type="password" name="password" id="password" class="form-input" placeholder="Minimal 6 karakter" required>
                    @error('password') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Role Sistem <span class="required">*</span></label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="kabal" {{ old('role') == 'kabal' ? 'selected' : '' }}>1. Kepala Balai (Pantau seluruh pekerjaan, arahan, & approval hasil)</option>
                        <option value="kasubag" {{ old('role') == 'kasubag' ? 'selected' : '' }}>2. Kepala Subbagian Umum (Pantau seluruh pekerjaan & koordinasi bidang/layanan)</option>
                        <option value="korjafung" {{ old('role') == 'korjafung' ? 'selected' : '' }}>3. Koordinator Jabatan Fungsional (Koordinasi & evaluasi penugasan JF)</option>
                        <option value="katim" {{ old('role') == 'katim' ? 'selected' : '' }}>4. Ketua Tim Kerja / Ketua Pokja (Kelola pekerjaan tim, bagi tugas, persetujuan & verifikasi)</option>
                        <option value="korlayanan" {{ old('role') == 'korlayanan' ? 'selected' : '' }}>5. Koordinator Bidang / Layanan (Kelola pekerjaan bidang/layanan & verifikasi)</option>
                        <option value="pegawai" {{ old('role', 'pegawai') == 'pegawai' ? 'selected' : '' }}>6. Pegawai (Pelaksana tugas, ajukan subpekerjaan, update progres & bukti)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>7. Administrator (Kelola akun, role, struktur organisasi & sistem)</option>
                    </select>
                    @error('role') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="pokja_id">Penempatan Pokja</label>
                    <select name="pokja_id" id="pokja_id" class="form-select">
                        <option value="">-- Tidak Terikat Pokja (cth: Kabal, Kasubag, Admin) --</option>
                        @foreach($pokjas as $p)
                            <option value="{{ $p->id }}" {{ old('pokja_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->kode_pokja }} - {{ $p->nama_pokja }}
                            </option>
                        @endforeach
                    </select>
                    @error('pokja_id') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="jabatan">Jabatan Fungsional / Struktural</label>
                    <input type="text" name="jabatan" id="jabatan" class="form-input" value="{{ old('jabatan') }}" placeholder="Contoh: Widyaprada Ahli Madya">
                    @error('jabatan') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nomor_hp">Nomor Handphone / WhatsApp</label>
                    <input type="text" name="nomor_hp" id="nomor_hp" class="form-input" value="{{ old('nomor_hp') }}" placeholder="08xxxxxxxxxx">
                    @error('nomor_hp') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="ph-bold ph-check"></i>
                    <span>Simpan Pengguna</span>
                </button>
                <a href="{{ route('users.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

@endsection
