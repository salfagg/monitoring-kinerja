@extends('layouts.app')

@section('title', 'Ubah Pengguna | TANJIDOR')
@section('page-title', 'Ubah Data Pengguna')

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
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Ubah Data: {{ $user->name }}</h2>
                <p style="font-size: 13px; color: var(--text-muted);">Perbarui profil, penempatan pokja, atau hak akses akun pengguna</p>
            </div>
            <span class="user-role-badge role-{{ $user->role }}">
                {{ str_replace('_', ' ', $user->role) }}
            </span>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap & Gelar <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nip">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" id="nip" class="form-input" value="{{ old('nip', $user->nip) }}">
                    @error('nip') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Resmi <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Ganti Kata Sandi (Opsional)</label>
                    <input type="password" name="password" id="password" class="form-input" placeholder="Biarkan kosong jika tidak diubah">
                    @error('password') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Role Sistem <span class="required">*</span></label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="kabal" {{ old('role', $user->role) == 'kabal' ? 'selected' : '' }}>1. Kepala Balai (Pantau seluruh pekerjaan, arahan, & approval hasil)</option>
                        <option value="kasubag" {{ old('role', $user->role) == 'kasubag' ? 'selected' : '' }}>2. Kepala Subbagian Umum (Pantau seluruh pekerjaan & koordinasi bidang/layanan)</option>
                        <option value="korjafung" {{ old('role', $user->role) == 'korjafung' ? 'selected' : '' }}>3. Koordinator Jabatan Fungsional (Koordinasi & evaluasi penugasan JF)</option>
                        <option value="katim" {{ in_array(old('role', $user->role), ['katim', 'katim_korjafung']) ? 'selected' : '' }}>4. Ketua Tim Kerja / Ketua Pokja (Kelola pekerjaan tim, bagi tugas, persetujuan & verifikasi)</option>
                        <option value="korlayanan" {{ in_array(old('role', $user->role), ['korlayanan', 'koordinator_layanan']) ? 'selected' : '' }}>5. Koordinator Bidang / Layanan (Kelola pekerjaan bidang/layanan & verifikasi)</option>
                        <option value="pegawai" {{ in_array(old('role', $user->role), ['pegawai', 'tim_pokja']) ? 'selected' : '' }}>6. Pegawai (Pelaksana tugas, ajukan subpekerjaan, update progres & bukti)</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>7. Administrator (Kelola akun, role, struktur organisasi & sistem)</option>
                    </select>
                    @error('role') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="pokja_id">Penempatan Pokja</label>
                    <select name="pokja_id" id="pokja_id" class="form-select">
                        <option value="">-- Tidak Terikat Pokja --</option>
                        @foreach($pokjas as $p)
                            <option value="{{ $p->id }}" {{ old('pokja_id', $user->pokja_id) == $p->id ? 'selected' : '' }}>
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
                    <input type="text" name="jabatan" id="jabatan" class="form-input" value="{{ old('jabatan', $user->jabatan) }}">
                    @error('jabatan') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nomor_hp">Nomor Handphone / WhatsApp</label>
                    <input type="text" name="nomor_hp" id="nomor_hp" class="form-input" value="{{ old('nomor_hp', $user->nomor_hp) }}">
                    @error('nomor_hp') <div class="error-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="ph-bold ph-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
                <a href="{{ route('users.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

@endsection
