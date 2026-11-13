@extends('layouts.app')

@section('title', 'Edit Sasaran Kinerja (SKP) | SIMON-K')
@section('page-title', 'Edit Sasaran Kinerja')

@section('styles')
<style>
    .form-container {
        max-width: 820px;
        margin: 0 auto 50px auto;
    }

    .form-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 28px 32px;
        margin-bottom: 24px;
    }

    .form-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-step-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #fef3c7;
        color: #b45309;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }

    .form-card-header h3 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .form-card-header p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 2px 0 0 0;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    @media (max-width: 640px) {
        .form-card { padding: 20px 18px; }
        .form-grid-2 { grid-template-columns: 1fr; gap: 14px; }
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 7px;
    }

    .form-label .required { color: #ef4444; margin-left: 2px; }
    .form-label-hint { font-size: 11.5px; font-weight: 500; color: var(--text-muted); }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        font-size: 18px;
        color: #94a3b8;
        pointer-events: none;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 10px 14px 10px 42px;
        border: 1.5px solid #cbd5e1;
        border-radius: 9px;
        font-size: 13px;
        font-family: inherit;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        transition: var(--transition);
    }

    .form-textarea {
        padding: 12px 14px;
        min-height: 90px;
        resize: vertical;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }

    .form-input.is-invalid, .form-select.is-invalid, .form-textarea.is-invalid {
        border-color: #ef4444;
        background: #fff8f8;
    }

    .error-feedback {
        font-size: 11.5px;
        color: #ef4444;
        font-weight: 600;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-cancel {
        padding: 10px 20px;
        border-radius: 9px;
        border: 1px solid var(--border);
        background: #ffffff;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .btn-submit {
        padding: 10px 24px;
        border-radius: 9px;
        background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
        color: white;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(11, 74, 130, 0.22);
        transition: var(--transition);
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, var(--navy-deep) 0%, var(--primary-hover) 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(11, 74, 130, 0.28);
    }
</style>
@endsection

@section('content')

<div class="form-container">

    <div style="margin-bottom: 20px;">
        <a href="{{ route('skp.index') }}" style="font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="ph-bold ph-arrow-left"></i> Kembali ke Daftar SKP
        </a>
        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 6px;">
            Perbarui Sasaran Kinerja Pegawai (SKP)
        </h2>
        <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
            Edit butir Rencana Hasil Kerja (RHK) atau perbarui target capaian sasaran.
        </p>
    </div>

    <form action="{{ route('skp.update', $skp) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- CARD 1: Identitas Pegawai & Tahun Sasaran -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="card-step-badge">1</div>
                <div>
                    <h3>Pegawai Pemilik & Tahun Sasaran</h3>
                    <p>Tentukan pegawai pemilik SKP dan tahun anggaran pelaksanaan.</p>
                </div>
            </div>

            <div class="form-grid-2">
                <!-- Field Pegawai -->
                <div class="form-group">
                    <label for="user_id" class="form-label">
                        <span>Pegawai Pemilik SKP <span class="required">*</span></span>
                    </label>
                    @if($isManager)
                        <div class="input-wrapper">
                            <i class="ph-bold ph-user input-icon"></i>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id', $skp->user_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->nip ?? 'Pegawai' }}) - {{ $u->pokja?->nama_pokja ?? 'BGTK' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="user_id" value="{{ $skp->user_id }}">
                        <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 9px; padding: 10px 14px; display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 99px; background: #0284c7; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px;">
                                {{ strtoupper(substr($skp->user?->name ?? 'P', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 13px; color: #0f172a;">{{ $skp->user?->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $skp->user?->nip ?? 'Pegawai BGTK' }} • {{ $skp->user?->pokja?->nama_pokja ?? 'BGTK' }}</div>
                            </div>
                        </div>
                    @endif
                    @error('user_id')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Field Tahun -->
                <div class="form-group">
                    <label for="tahun" class="form-label">
                        <span>Tahun Periode SKP <span class="required">*</span></span>
                    </label>
                    <div class="input-wrapper">
                        <i class="ph-bold ph-calendar input-icon"></i>
                        <select id="tahun" name="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                            @for($y = date('Y') + 1; $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ old('tahun', $skp->tahun) == $y ? 'selected' : '' }}>
                                    Tahun {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    @error('tahun')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Field Aspek SKP -->
            <div class="form-group">
                <label for="aspek" class="form-label">
                    <span>Aspek Penilaian SKP <span class="required">*</span></span>
                    <span class="form-label-hint">Standar PermenPANRB</span>
                </label>
                <div class="input-wrapper">
                    <i class="ph-bold ph-chart-polar input-icon"></i>
                    <select id="aspek" name="aspek" class="form-select @error('aspek') is-invalid @enderror" required>
                        <option value="Kuantitas" {{ old('aspek', $skp->aspek) == 'Kuantitas' ? 'selected' : '' }}>Kuantitas (Jumlah dokumen, kegiatan, laporan)</option>
                        <option value="Kualitas" {{ old('aspek', $skp->aspek) == 'Kualitas' ? 'selected' : '' }}>Kualitas (Tingkat kesesuaian mutu / standar)</option>
                        <option value="Waktu" {{ old('aspek', $skp->aspek) == 'Waktu' ? 'selected' : '' }}>Waktu (Ketepatan waktu pelaksanaan)</option>
                    </select>
                </div>
                @error('aspek')
                    <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- CARD 2: Rencana Kinerja (RHK) & Indikator -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="card-step-badge">2</div>
                <div>
                    <h3>Rencana Hasil Kerja (RHK) & Indikator</h3>
                    <p>Uraian butir kinerja utama dan indikator capaian individu (IKI).</p>
                </div>
            </div>

            <!-- Field RHK -->
            <div class="form-group">
                <label for="rencana_kinerja" class="form-label">
                    <span>Rencana Hasil Kerja (RHK) <span class="required">*</span></span>
                    <span class="form-label-hint">Uraian tugas resmi SKP</span>
                </label>
                <textarea id="rencana_kinerja" 
                          name="rencana_kinerja" 
                          class="form-textarea @error('rencana_kinerja') is-invalid @enderror" 
                          rows="3" 
                          placeholder="Contoh: Terlaksananya fasilitasi dan penyusunan modul peningkatan kompetensi guru penggerak wilayah binaan..." 
                          required>{{ old('rencana_kinerja', $skp->rencana_kinerja) }}</textarea>
                @error('rencana_kinerja')
                    <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <!-- Field IKI -->
            <div class="form-group">
                <label for="indikator_kinerja" class="form-label">
                    <span>Indikator Kinerja Individu (IKI) <span class="required">*</span></span>
                    <span class="form-label-hint">Ukuran keberhasilan</span>
                </label>
                <textarea id="indikator_kinerja" 
                          name="indikator_kinerja" 
                          class="form-textarea @error('indikator_kinerja') is-invalid @enderror" 
                          rows="2" 
                          placeholder="Contoh: Jumlah modul pelatihan dan laporan pendampingan yang diselesaikan dan terverifikasi..." 
                          required>{{ old('indikator_kinerja', $skp->indikator_kinerja) }}</textarea>
                @error('indikator_kinerja')
                    <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid-2">
                <!-- Target Kuantitas (Maksimum) -->
                <div class="form-group">
                    <label for="target_max" class="form-label">
                        <span>Target Sasaran <span class="required">*</span></span>
                    </label>
                    <div class="input-wrapper">
                        <i class="ph-bold ph-target input-icon"></i>
                        <input type="number" 
                               id="target_max" 
                               name="target_max" 
                               step="0.01" 
                               min="0.01" 
                               class="form-input @error('target_max') is-invalid @enderror" 
                               value="{{ old('target_max', (float)$skp->target_max) }}" 
                               required>
                    </div>
                    @error('target_max')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Satuan Ukuran -->
                <div class="form-group">
                    <label for="satuan" class="form-label">
                        <span>Satuan Ukuran <span class="required">*</span></span>
                        <span class="form-label-hint">Misal: Dokumen, Laporan, Kegiatan</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="ph-bold ph-tag input-icon"></i>
                        <input type="text" 
                               id="satuan" 
                               name="satuan" 
                               class="form-input @error('satuan') is-invalid @enderror" 
                               value="{{ old('satuan', $skp->satuan) }}" 
                               placeholder="Dokumen, Modul, Kegiatan, Paket" 
                               required>
                    </div>
                    @error('satuan')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Status SKP -->
            <div class="form-group">
                <label for="status" class="form-label">
                    <span>Status SKP <span class="required">*</span></span>
                </label>
                <div class="input-wrapper">
                    <i class="ph-bold ph-seal-check input-icon"></i>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @if($isManager)
                            <option value="disetujui" {{ old('status', $skp->status) == 'disetujui' ? 'selected' : '' }}>
                                Disetujui (Aktif & Siap dijadikan rujukan pekerjaan)
                            </option>
                            <option value="diajukan" {{ old('status', $skp->status) == 'diajukan' ? 'selected' : '' }}>
                                Diajukan (Menunggu verifikasi lanjutan)
                            </option>
                            <option value="revisi" {{ old('status', $skp->status) == 'revisi' ? 'selected' : '' }}>
                                Revisi (Perlu perbaikan oleh pegawai)
                            </option>
                            <option value="draft" {{ old('status', $skp->status) == 'draft' ? 'selected' : '' }}>
                                Draf (Belum aktif)
                            </option>
                        @else
                            <option value="{{ $skp->status }}" selected>
                                Status saat ini: {{ ucfirst($skp->status) }}
                            </option>
                            <option value="diajukan">
                                Ajukan Ulang ke Katim / Pimpinan
                            </option>
                            <option value="draft">
                                Simpan Sebagai Draf
                            </option>
                        @endif
                    </select>
                </div>
                @error('status')
                    <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="form-actions">
            <a href="{{ route('skp.index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-submit">
                <i class="ph-bold ph-check"></i>
                <span>Simpan Perubahan SKP</span>
            </button>
        </div>
    </form>

</div>

@endsection

