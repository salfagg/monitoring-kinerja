@extends('layouts.app')

@section('title', 'Edit Sasaran Kinerja (SKP) | TANJIDOR')
@section('page-title', 'Edit Sasaran Kinerja')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
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
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $skp->user?->nip ?? 'Pegawai BGTK' }} â€¢ {{ $skp->user?->pokja?->nama_pokja ?? 'BGTK' }}</div>
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

