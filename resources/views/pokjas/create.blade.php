@extends('layouts.app')

@section('title', 'Tambah Pokja Baru | TANJIDOR')
@section('page-title', 'Tambah Kelompok Kerja (Pokja)')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endsection

@section('content')

    <!-- Navigation Breadcrumb -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('pokjas.index') }}" style="color: var(--primary); font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="ph-bold ph-arrow-left"></i>
            <span>Kembali ke Master Pokja</span>
        </a>
    </div>

    <div class="form-layout-grid">
        <!-- Form Isian Pokja -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon-box">
                    <i class="ph-bold ph-users-three"></i>
                </div>
                <div>
                    <h2>Tambah Kelompok Kerja (Pokja) Baru</h2>
                    <p>Daftarkan unit Pokja dan tentukan Katim penanggung jawab kegiatan</p>
                </div>
            </div>

            <form action="{{ route('pokjas.store') }}" method="POST" id="pokjaForm">
                @csrf

                <!-- Nama Pokja -->
                <div class="form-group">
                    <label class="form-label" for="nama_pokja">
                        <i class="ph-bold ph-folder-notch-open"></i>
                        <span>Nama Kelompok Kerja (Pokja)</span>
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" 
                               name="nama_pokja" 
                               id="nama_pokja" 
                               class="form-input" 
                               value="{{ old('nama_pokja') }}" 
                               placeholder="Contoh: Tim Kerja 1: Pemetaan & Evaluasi" 
                               required
                               autocomplete="off">
                        <i class="ph-bold ph-buildings prefix-icon"></i>
                    </div>
                    @error('nama_pokja') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Kode Pokja -->
                <div class="form-group">
                    <label class="form-label" for="kode_pokja">
                        <i class="ph-bold ph-hash"></i>
                        <span>Kode Pokja (Unik)</span>
                        <span class="required">*</span>
                        <span class="form-label-hint">Huruf kapital</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" 
                               name="kode_pokja" 
                               id="kode_pokja" 
                               class="form-input" 
                               value="{{ old('kode_pokja') }}" 
                               placeholder="Contoh: TIM-01 atau POKJA-ULT" 
                               style="text-transform: uppercase;" 
                               required
                               autocomplete="off">
                        <i class="ph-bold ph-tag prefix-icon"></i>
                    </div>
                    @error('kode_pokja') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Kategori / Tipe Unit -->
                <div class="form-group">
                    <label class="form-label" for="tipe">
                        <i class="ph-bold ph-tree-structure"></i>
                        <span>Kategori Unit Kerja</span>
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <select name="tipe" id="tipe" class="form-select" required>
                            <option value="tim_kerja" {{ old('tipe') == 'tim_kerja' ? 'selected' : '' }}>
                                ðŸ›ï¸ Tim Kerja Teknis / Tata Usaha Balai
                            </option>
                            <option value="subbag_pilar" {{ old('tipe') == 'subbag_pilar' ? 'selected' : '' }}>
                                âš™ï¸ Pilar Operasional Subbagian Umum
                            </option>
                            <option value="kelompok_kerja" {{ old('tipe') == 'kelompok_kerja' ? 'selected' : '' }}>
                                âš¡ Satuan Tugas Ad Hoc (Kelompok Kerja Penugasan RBI)
                            </option>
                        </select>
                        <i class="ph-bold ph-squares-four prefix-icon"></i>
                    </div>
                    @error('tipe') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Penunjukan Katim / Kasubag -->
                <div class="form-group">
                    <label class="form-label" for="katim_id">
                        <i class="ph-bold ph-user-circle-gear"></i>
                        <span>Tunjuk Ketua Tim (Katim) / Kepala Subbagian</span>
                        <span class="form-label-hint">Dapat diubah nanti</span>
                    </label>
                    <div class="input-wrapper">
                        <select name="katim_id" id="katim_id" class="form-select">
                            <option value="">-- Belum Menunjuk Katim / Kasubag --</option>
                            @foreach($potentialKatims as $user)
                                <option value="{{ $user->id }}" 
                                        data-name="{{ $user->name }}" 
                                        data-role="{{ $user->role }}"
                                        {{ old('katim_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} (NIP: {{ $user->nip ?? '-' }}) â€” {{ $user->jabatan ?? $user->role }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ph-bold ph-user prefix-icon"></i>
                    </div>
                    @error('katim_id') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Deskripsi & Ruang Lingkup -->
                <div class="form-group">
                    <label class="form-label" for="deskripsi">
                        <i class="ph-bold ph-text-align-left"></i>
                        <span>Deskripsi & Ruang Lingkup Pokja</span>
                    </label>
                    <div class="input-wrapper">
                        <textarea name="deskripsi" 
                                  id="deskripsi" 
                                  class="form-textarea" 
                                  placeholder="Tuliskan fokus bidang tugas, tema kerja, atau ruang lingkup operasional Pokja ini...">{{ old('deskripsi') }}</textarea>
                        <i class="ph-bold ph-note-pencil prefix-icon"></i>
                    </div>
                    @error('deskripsi') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="ph-bold ph-check"></i>
                        <span>Simpan Pokja Baru</span>
                    </button>
                    <a href="{{ route('pokjas.index') }}" class="btn-cancel">
                        <i class="ph-bold ph-x"></i>
                        <span>Batal</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Live Preview Card -->
        <div>
            <div class="preview-card-sticky">
                <div class="preview-header-tag">
                    <span class="preview-title-badge">
                        <span class="live-pulse"></span>
                        Pratinjau Kartu Pokja
                    </span>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600;">Realtime View</span>
                </div>

                <div class="preview-pokja-box">
                    <span class="preview-badge" id="previewBadge">KODE-POKJA</span>
                    <div class="preview-nama" id="previewNama">Nama Pokja Akan Tampil Di Sini</div>
                    <div class="preview-desc" id="previewDesc">Deskripsi dan ruang lingkup pokja akan langsung diperbarui saat Anda mengetik...</div>

                    <div class="preview-meta-box">
                        <div class="preview-meta-row">
                            <span style="color: #64748b;">
                                <i class="ph-bold ph-user-circle"></i> 
                                <span id="previewKatimLabel">Ketua Tim (Katim):</span>
                            </span>
                            <strong id="previewKatim" style="color: #0f172a;">Belum Ditunjuk</strong>
                        </div>
                        <div class="preview-meta-row">
                            <span style="color: #64748b;"><i class="ph-bold ph-users-three"></i> Total Anggota:</span>
                            <strong style="color: var(--primary);">0 Pegawai (Baru)</strong>
                        </div>
                    </div>
                </div>

                <div class="tips-box">
                    <strong><i class="ph-bold ph-lightbulb"></i> Panduan Pengisian:</strong>
                    Kode Pokja sebaiknya singkat dan konsisten (misal <code>TIM-01</code>, <code>TIM-02</code>, <code>SUBBAG-UMUM</code>). Pegawai nantinya dapat didistribusikan ke dalam Pokja ini melalui menu Kelola Pegawai.
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputNama = document.getElementById('nama_pokja');
        const inputKode = document.getElementById('kode_pokja');
        const inputDesc = document.getElementById('deskripsi');
        const selectKatim = document.getElementById('katim_id');

        const previewNama = document.getElementById('previewNama');
        const previewBadge = document.getElementById('previewBadge');
        const previewDesc = document.getElementById('previewDesc');
        const previewKatim = document.getElementById('previewKatim');
        const previewKatimLabel = document.getElementById('previewKatimLabel');

        function updatePreview() {
            // Update Nama
            if (inputNama.value.trim() !== '') {
                previewNama.textContent = inputNama.value;
            } else {
                previewNama.textContent = 'Nama Pokja Akan Tampil Di Sini';
            }

            // Update Kode (auto-caps)
            if (inputKode.value.trim() !== '') {
                previewBadge.textContent = inputKode.value.toUpperCase();
            } else {
                previewBadge.textContent = 'KODE-POKJA';
            }

            // Update Deskripsi
            if (inputDesc.value.trim() !== '') {
                previewDesc.textContent = inputDesc.value;
            } else {
                previewDesc.textContent = 'Deskripsi dan ruang lingkup pokja akan langsung diperbarui saat Anda mengetik...';
            }

            // Update Katim / Kasubag
            const selectedOpt = selectKatim.options[selectKatim.selectedIndex];
            const isSubbag = inputKode.value.trim().toUpperCase() === 'SUBBAG-UMUM' || (selectedOpt && selectedOpt.getAttribute('data-role') === 'kasubag');
            if (previewKatimLabel) {
                previewKatimLabel.textContent = isSubbag ? 'Kepala Subbagian (Kasubag):' : 'Ketua Tim (Katim):';
            }

            if (selectKatim.value !== '' && selectedOpt) {
                previewKatim.textContent = selectedOpt.getAttribute('data-name') || selectedOpt.text.split('(')[0].trim();
            } else {
                previewKatim.textContent = 'Belum Ditunjuk';
            }
        }

        inputNama.addEventListener('input', updatePreview);
        inputKode.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            updatePreview();
        });
        inputDesc.addEventListener('input', updatePreview);
        selectKatim.addEventListener('change', updatePreview);

        // Initial preview sync
        updatePreview();
    });
</script>
@endsection
