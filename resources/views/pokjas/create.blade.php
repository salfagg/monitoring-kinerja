@extends('layouts.app')

@section('title', 'Tambah Pokja Baru | SIMON-K')
@section('page-title', 'Tambah Kelompok Kerja (Pokja)')

@section('styles')
<style>
    .form-layout-grid {
        display: grid;
        grid-template-columns: 1.25fr 0.95fr;
        gap: 24px;
        align-items: start;
        margin-top: 14px;
    }

    @media (max-width: 992px) {
        .form-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Form Container */
    .form-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 28px;
    }

    .form-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--border);
    }

    .header-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(59, 130, 246, 0.2));
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .form-card-header h2 {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 3px;
    }

    .form-card-header p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }

    /* Form Group & Controls */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 7px;
    }

    .form-label i {
        font-size: 16px;
        color: var(--primary);
    }

    .form-label .required {
        color: #ef4444;
    }

    .form-label-hint {
        font-size: 11.5px;
        font-weight: 500;
        color: #94a3b8;
        margin-left: auto;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i.prefix-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #94a3b8;
        pointer-events: none;
        transition: var(--transition);
    }

    .input-wrapper textarea ~ i.prefix-icon {
        top: 18px;
        transform: none;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 14px 12px 42px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 13.5px;
        background: #f8fafc;
        color: var(--text-dark);
        outline: none;
        transition: var(--transition);
        font-family: inherit;
    }

    .form-textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.5;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
    }

    .form-input:focus ~ .prefix-icon,
    .form-select:focus ~ .prefix-icon,
    .form-textarea:focus ~ .prefix-icon {
        color: var(--primary);
    }

    .error-feedback {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Actions Bar */
    .form-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .btn-submit {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        padding: 11px 24px;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 13.5px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        transition: var(--transition);
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
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
        gap: 6px;
        transition: var(--transition);
    }

    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* Sticky Live Preview Card */
    .preview-card-sticky {
        position: sticky;
        top: 24px;
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 24px;
    }

    .preview-header-tag {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px dashed var(--border);
    }

    .preview-title-badge {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .live-pulse {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        animation: pulse 1.8s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Card Preview Representation */
    .preview-pokja-box {
        background: #f8fafc;
        border-radius: 12px;
        border: 1.5px dashed #cbd5e1;
        padding: 20px;
        transition: var(--transition);
    }

    .preview-badge {
        display: inline-block;
        background: #eff6ff;
        color: var(--primary);
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #bfdbfe;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 10px;
    }

    .preview-nama {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        line-height: 1.35;
        word-break: break-word;
    }

    .preview-desc {
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 16px;
        word-break: break-word;
    }

    .preview-meta-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 12px;
    }

    .preview-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .tips-box {
        margin-top: 18px;
        padding: 14px;
        background: rgba(37, 99, 235, 0.05);
        border: 1px solid rgba(37, 99, 235, 0.15);
        border-radius: 10px;
        font-size: 12px;
        color: #1e40af;
        line-height: 1.5;
    }

    .tips-box strong {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
        font-size: 12.5px;
    }

    /* Dark Mode Styling */
    [data-theme="dark"] .form-card,
    [data-theme="dark"] .preview-card-sticky {
        background: #151e32 !important;
        border-color: #223049 !important;
    }

    [data-theme="dark"] .form-card-header,
    [data-theme="dark"] .preview-header-tag,
    [data-theme="dark"] .form-actions {
        border-color: #223049 !important;
    }

    [data-theme="dark"] .form-card-header h2,
    [data-theme="dark"] .preview-nama {
        color: #f8fafc !important;
    }

    [data-theme="dark"] .form-label {
        color: #e2e8f0 !important;
    }

    [data-theme="dark"] .form-input,
    [data-theme="dark"] .form-select,
    [data-theme="dark"] .form-textarea {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    [data-theme="dark"] .form-input:focus,
    [data-theme="dark"] .form-select:focus,
    [data-theme="dark"] .form-textarea:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3.5px rgba(59, 130, 246, 0.2) !important;
    }

    [data-theme="dark"] .btn-cancel {
        background: #1e293b !important;
        color: #cbd5e1 !important;
    }

    [data-theme="dark"] .btn-cancel:hover {
        background: #27354f !important;
        color: #ffffff !important;
    }

    [data-theme="dark"] .preview-pokja-box {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .preview-badge {
        background: rgba(37, 99, 235, 0.2) !important;
        border-color: #2563eb !important;
        color: #93c5fd !important;
    }

    [data-theme="dark"] .preview-desc {
        color: #94a3b8 !important;
    }

    [data-theme="dark"] .preview-meta-box {
        background: #151e32 !important;
        border-color: #223049 !important;
        color: #cbd5e1;
    }

    [data-theme="dark"] .tips-box {
        background: rgba(37, 99, 235, 0.1) !important;
        border-color: rgba(37, 99, 235, 0.3) !important;
        color: #93c5fd !important;
    }
</style>
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
                                🏛️ Tim Kerja Teknis / Tata Usaha Balai
                            </option>
                            <option value="subbag_pilar" {{ old('tipe') == 'subbag_pilar' ? 'selected' : '' }}>
                                ⚙️ Pilar Operasional Subbagian Umum
                            </option>
                            <option value="kelompok_kerja" {{ old('tipe') == 'kelompok_kerja' ? 'selected' : '' }}>
                                ⚡ Satuan Tugas Ad Hoc (Kelompok Kerja Penugasan RBI)
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
                                    {{ $user->name }} (NIP: {{ $user->nip ?? '-' }}) — {{ $user->jabatan ?? $user->role }}
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
