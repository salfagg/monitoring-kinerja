@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru | TANJIDOR')
@section('page-title', 'Tambah Pengguna Baru')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
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
