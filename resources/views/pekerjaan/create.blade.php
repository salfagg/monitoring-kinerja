@extends('layouts.app')

@section('title', 'Buat & Tugaskan Pekerjaan Baru | SIMON-K')
@section('page-title', 'Buat & Tugaskan Pekerjaan Baru')

@section('styles')
<style>
    /* Layout Container & Grid */
    .form-page-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 28px;
        align-items: start;
        margin-bottom: 50px;
    }

    @media (max-width: 1024px) {
        .form-page-layout {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    /* Form Section Card */
    .form-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        padding: 28px 32px;
        margin-bottom: 24px;
        transition: var(--transition);
        position: relative;
    }

    .form-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
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
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        flex-shrink: 0;
    }

    .form-card-header h3 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .form-card-header p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 2px 0 0 0;
    }

    /* Grids inside form */
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    @media (max-width: 640px) {
        .form-card {
            padding: 20px 18px;
        }
        .form-grid-2 {
            grid-template-columns: 1fr;
            gap: 14px;
        }
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-label-hint {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Input Wrappers with Icons */
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 18px;
        pointer-events: none;
        transition: var(--transition);
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 11px 14px 11px 42px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 13.5px;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: var(--transition);
        font-family: inherit;
    }

    .form-textarea {
        padding: 12px 14px;
        line-height: 1.5;
        resize: vertical;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .form-input.is-invalid, .form-select.is-invalid, .form-textarea.is-invalid {
        border-color: #ef4444;
        background-color: #fef2f2;
    }

    .error-feedback {
        font-size: 12px;
        color: #dc2626;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Dropzone Upload ST */
    .dropzone-container {
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius-md);
        padding: 22px 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
    }

    .dropzone-container:hover, .dropzone-container.drag-over {
        border-color: var(--primary);
        background: #f0f7ff;
    }

    .dropzone-icon {
        font-size: 32px;
        color: #3b82f6;
        margin-bottom: 6px;
    }

    .dropzone-text {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
    }

    .dropzone-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .dropzone-file-info {
        display: none;
        background: #ffffff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 10px;
        align-items: center;
        justify-content: space-between;
        text-align: left;
    }

    /* Live Summary Preview Sidebar */
    .preview-card-sticky {
        position: sticky;
        top: 90px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .preview-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        padding: 18px 20px;
    }

    .preview-badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .preview-title {
        font-size: 14.5px;
        font-weight: 800;
        line-height: 1.4;
        margin: 0;
        color: #f8fafc;
        word-break: break-word;
    }

    .preview-body {
        padding: 18px 20px;
    }

    .preview-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 9px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 12.5px;
        gap: 10px;
    }

    .preview-item:last-child {
        border-bottom: none;
    }

    .preview-label {
        color: #64748b;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .preview-val {
        color: #0f172a;
        font-weight: 700;
        text-align: right;
        word-break: break-word;
    }

    .form-checklist {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
    }

    .checklist-title {
        font-size: 11.5px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
    }

    .progress-micro {
        height: 6px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-micro-fill {
        height: 100%;
        background: #10b981;
        transition: width 0.3s ease;
    }

    /* Actions Bar */
    .form-actions-desktop {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .btn-submit-main {
        background: var(--primary);
        color: #ffffff;
        padding: 12px 28px;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .btn-submit-main:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.32);
    }

    .btn-cancel-main {
        background: #ffffff;
        color: #475569;
        padding: 12px 22px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--border);
        transition: var(--transition);
    }

    .btn-cancel-main:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Mobile Floating Bar */
    .mobile-floating-bar {
        display: none;
    }

    @media (max-width: 768px) {
        .form-actions-desktop {
            display: none;
        }

        .mobile-floating-bar {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 12px 18px;
            border-top: 1px solid var(--border);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
            z-index: 99;
            gap: 10px;
        }

        .mobile-floating-bar .btn-submit-main {
            flex: 1;
            justify-content: center;
            padding: 13px;
        }

        .mobile-floating-bar .btn-cancel-main {
            padding: 13px 18px;
        }

        body {
            padding-bottom: 70px;
        }
    }

    /* Multi-Assignee Interactive Selector Styles */
    .pelaksana-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        background: #f8fafc;
        cursor: pointer;
        transition: var(--transition);
        user-select: none;
        position: relative;
    }

    .pelaksana-card:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .pelaksana-card.selected {
        background: #f0fdf4;
        border-color: #86efac;
    }

    .pelaksana-card.is-pic {
        background: #eff6ff;
        border-color: #93c5fd;
    }

    .pelaksana-checkbox {
        width: 17px;
        height: 17px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    .pelaksana-avatar {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #334155;
        font-weight: 800;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pelaksana-card.selected .pelaksana-avatar {
        background: #16a34a;
        color: #ffffff;
    }

    .pelaksana-card.is-pic .pelaksana-avatar {
        background: var(--primary);
        color: #ffffff;
    }

    .pelaksana-meta {
        overflow: hidden;
        flex: 1;
    }

    .pelaksana-name {
        font-size: 12.5px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pelaksana-sub {
        font-size: 11px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pic-tag {
        font-size: 10px;
        font-weight: 800;
        background: #2563eb;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        letter-spacing: 0.03em;
    }

    .pelaksana-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .pelaksana-chip.is-pic-chip {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1e40af;
        font-weight: 700;
    }

    .pelaksana-chip .btn-remove-chip {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        font-size: 14px;
        line-height: 1;
    }

    .pelaksana-chip .btn-remove-chip:hover {
        color: #ef4444;
    }

    /* Kategori / Sumber Penugasan Card */
    .sumber-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .sumber-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .sumber-card.active {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow: 0 4px 16px rgba(2, 132, 199, 0.12);
    }
    .sumber-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .sumber-check-badge {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .sumber-card.active .sumber-check-badge {
        background: #0284c7;
        color: #ffffff;
    }
</style>
@endsection

@section('content')

<form id="formPekerjaan" action="{{ route('pekerjaan.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-page-layout">
        
        <!-- ==================== KOLOM KIRI: FORMULIR INPUT ==================== -->
        <div class="form-main-content">
            
            <!-- SECTION KHUSUS: Kategori & Sumber Penugasan (Manual vs SKP) -->
            <div class="form-card" style="border: 2px solid #bae6fd; background: linear-gradient(180deg, #f8fafc 0%, #f0f9ff 100%);">
                <div class="form-card-header" style="border-bottom-color: #e2e8f0;">
                    <div class="card-step-badge" style="background: #0284c7; color: #ffffff;">
                        <i class="ph-bold ph-git-fork"></i>
                    </div>
                    <div>
                        <h3>Kategori & Sumber Penugasan</h3>
                        <p>Pilih apakah pekerjaan ini merupakan tugas mandiri/manual atau ditautkan langsung ke target SKP pegawai.</p>
                    </div>
                </div>

                <!-- Pilihan 2 Card Sumber Penugasan -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px;">
                    <!-- Option 1: Manual -->
                    <label class="sumber-card {{ old('sumber_pekerjaan', 'manual') === 'manual' ? 'active' : '' }}" id="cardSumberManual" onclick="switchSumberPekerjaan('manual')">
                        <input type="radio" name="sumber_pekerjaan" value="manual" id="radioManual" {{ old('sumber_pekerjaan', 'manual') === 'manual' ? 'checked' : '' }} style="display: none;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div class="sumber-icon-box" style="background: #e2e8f0; color: #334155;">
                                <i class="ph-bold ph-pencil-line"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 13.5px; color: #0f172a; margin-bottom: 2px;">
                                    Pekerjaan Mandiri / Manual
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; line-height: 1.4;">
                                    Penugasan umum, kegiatan lapangan, atau pekerjaan insidental yang diisi bebas.
                                </div>
                            </div>
                        </div>
                        <div class="sumber-check-badge">
                            <i class="ph-bold ph-check"></i>
                        </div>
                    </label>

                    <!-- Option 2: Merujuk ke SKP -->
                    <label class="sumber-card {{ old('sumber_pekerjaan') === 'skp' ? 'active' : '' }}" id="cardSumberSkp" onclick="switchSumberPekerjaan('skp')">
                        <input type="radio" name="sumber_pekerjaan" value="skp" id="radioSkp" {{ old('sumber_pekerjaan') === 'skp' ? 'checked' : '' }} style="display: none;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div class="sumber-icon-box" style="background: #e0f2fe; color: #0284c7;">
                                <i class="ph-bold ph-target"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 13.5px; color: #0284c7; margin-bottom: 2px; display: flex; align-items: center; gap: 6px;">
                                    <span>Merujuk ke SKP Pegawai</span>
                                    <span style="font-size: 9.5px; background: #0284c7; color: #fff; padding: 1px 6px; border-radius: 99px; font-weight: 800;">TERHUBUNG</span>
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; line-height: 1.4;">
                                    Terhubung ke Sasaran Kinerja Pegawai (RHK). Judul, target & satuan otomatis terisi!
                                </div>
                            </div>
                        </div>
                        <div class="sumber-check-badge">
                            <i class="ph-bold ph-check"></i>
                        </div>
                    </label>
                </div>

                <!-- Panel Pemilihan SKP (Hanya Tampil Jika Rujukan SKP Dipilih) -->
                <div id="panelRujukanSkp" style="{{ old('sumber_pekerjaan') === 'skp' ? 'display: block;' : 'display: none;' }} background: #ffffff; border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 18px; margin-top: 14px; box-shadow: 0 4px 16px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="font-weight: 800; font-size: 13px; color: #0369a1; display: flex; align-items: center; gap: 6px;">
                            <i class="ph-bold ph-crosshair"></i> Pilih Butir Rencana Kinerja SKP (RHK)
                        </div>
                        <span style="font-size: 11px; color: #0284c7; background: #f0f9ff; padding: 2px 8px; border-radius: 99px; font-weight: 700;">
                            ✨ Auto-Fill Form
                        </span>
                    </div>

                    <div class="form-group" style="margin-bottom: 8px;">
                        <div class="input-wrapper">
                            <i class="ph-bold ph-list-star input-icon" style="color: #0284c7;"></i>
                            <select id="skp_tahunan_id" name="skp_tahunan_id" class="form-select @error('skp_tahunan_id') is-invalid @enderror" onchange="handleSkpSelection(this)">
                                <option value="">-- Pilih Rencana Hasil Kerja (SKP Pegawai) --</option>
                                @foreach($skpList as $skp)
                                    <option value="{{ $skp->id }}" 
                                            data-rhk="{{ $skp->rencana_kinerja }}"
                                            data-target="{{ (float)($skp->target_max ?: ($skp->target_min ?: 1)) }}"
                                            data-satuan="{{ $skp->satuan }}"
                                            data-user-id="{{ $skp->user_id }}"
                                            data-user-name="{{ $skp->user?->name ?? 'Pegawai' }}"
                                            data-tahun="{{ $skp->tahun }}"
                                            data-iki="{{ $skp->indikator_kinerja }}"
                                            {{ old('skp_tahunan_id') == $skp->id ? 'selected' : '' }}>
                                        [{{ $skp->tahun }}] {{ $skp->user?->name ?? 'Pegawai' }} — {{ Str::limit($skp->rencana_kinerja, 75) }} ({{ (float)$skp->target_max }} {{ $skp->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('skp_tahunan_id')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Info Alert SKP Terpilih -->
                    <div id="skpSelectedInfo" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; margin-top: 10px;">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <i class="ph-bold ph-check-circle" style="color: #16a34a; font-size: 18px; margin-top: 1px;"></i>
                            <div style="font-size: 12px; color: #166534; line-height: 1.45;">
                                <div><strong>RHK Terpilih:</strong> <span id="skpRhkText">-</span></div>
                                <div style="margin-top: 3px; font-size: 11px; color: #15803d;">
                                    Pegawai Pemilik: <strong id="skpOwnerText">-</strong> • Target: <strong id="skpTargetText">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: Identitas & Tim Kerja -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-step-badge">1</div>
                    <div>
                        <h3>Identitas & Tim Kerja</h3>
                        <p>Tentukan kode/nomor pekerjaan, tim kerja naungan (Pokja), dan nama tugas resmi.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="kode_tampilan" class="form-label">
                            <span>Kode / No. Pekerjaan <span class="required">*</span></span>
                            <span class="form-label-hint">Misal: PK-01, 1, atau 02</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-list-numbers input-icon"></i>
                            <input type="text" 
                                   id="kode_tampilan" 
                                   name="kode_tampilan" 
                                   class="form-input @error('kode_tampilan') is-invalid @enderror" 
                                   value="{{ old('kode_tampilan', $suggestedKode) }}" 
                                   placeholder="Misal: 1 atau PK-01" 
                                   required
                                   oninput="updateLivePreview()">
                        </div>
                        @error('kode_tampilan')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pokja_id" class="form-label">
                            <span>Tim Kerja (Pokja)</span>
                            <span class="form-label-hint">Pilih Pokja penanggung jawab</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-buildings input-icon"></i>
                            <select id="pokja_id" name="pokja_id" class="form-select @error('pokja_id') is-invalid @enderror" onchange="updateLivePreview()">
                                <option value="">-- Pilih Tim Kerja / Pokja --</option>
                                @foreach($pokjas as $p)
                                    <option value="{{ $p->id }}" {{ (old('pokja_id', Auth::user()->pokja_id) == $p->id) ? 'selected' : '' }} data-name="{{ $p->nama_pokja }}">
                                        {{ $p->kode_pokja }} - {{ $p->nama_pokja }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('pokja_id')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="judul" class="form-label">
                        <span>Nama / Judul Pekerjaan <span class="required">*</span></span>
                        <span id="charCount" class="form-label-hint">0 / 255 karakter</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="ph-bold ph-briefcase input-icon"></i>
                        <input type="text" 
                               id="judul" 
                               name="judul" 
                               maxlength="255"
                               class="form-input @error('judul') is-invalid @enderror" 
                               value="{{ old('judul') }}" 
                               placeholder="Contoh: Pelaksanaan Workshop Peningkatan Mutu Guru Penggerak Tahap I" 
                               required
                               oninput="updateLivePreview()">
                    </div>
                    @error('judul')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SECTION 2: Dasar Penugasan & Surat Tugas (ST) -->
            <div class="form-card" style="background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);">
                <div class="form-card-header">
                    <div class="card-step-badge" style="background: #eff6ff; color: #2563eb;">2</div>
                    <div>
                        <h3>Dasar Penugasan & Surat Tugas (ST)</h3>
                        <p>Tautkan penugasan ini ke Surat Tugas resmi yang diterbitkan untuk pegawai pelaksana.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="nomor_st" class="form-label">
                            <span>Nomor Surat Tugas (ST)</span>
                            <span class="form-label-hint">Register BGTK</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-file-text input-icon"></i>
                            <input type="text" 
                                   id="nomor_st" 
                                   name="nomor_st" 
                                   class="form-input @error('nomor_st') is-invalid @enderror" 
                                   value="{{ old('nomor_st') }}" 
                                   placeholder="Contoh: 0451/B7.3/KP.02.00/2026"
                                   oninput="updateLivePreview()">
                        </div>
                        @error('nomor_st')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_st" class="form-label">
                            <span>Tanggal Surat Tugas (ST)</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-calendar-blank input-icon"></i>
                            <input type="date" 
                                   id="tanggal_st" 
                                   name="tanggal_st" 
                                   class="form-input @error('tanggal_st') is-invalid @enderror" 
                                   value="{{ old('tanggal_st') }}"
                                   onchange="updateLivePreview()">
                        </div>
                        @error('tanggal_st')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Custom Interactive File Dropzone -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Berkas Dokumen ST (PDF / Gambar / Docx)</span>
                        <span class="form-label-hint">Maksimal 10 MB</span>
                    </label>

                    <input type="file" 
                           id="file_st" 
                           name="file_st" 
                           accept=".pdf,.png,.jpg,.jpeg,.docx" 
                           style="display: none;" 
                           onchange="handleFileSelect(this)">

                    <div id="dropzoneBox" class="dropzone-container" onclick="document.getElementById('file_st').click()">
                        <div class="dropzone-icon">
                            <i class="ph-bold ph-cloud-arrow-up"></i>
                        </div>
                        <div class="dropzone-text">Pilih Berkas atau Seret ke Sini</div>
                        <div class="dropzone-sub">Mendukung format PDF, PNG, JPG, JPEG, atau DOCX</div>

                        <div id="dropzoneFileInfo" class="dropzone-file-info" onclick="event.stopPropagation()">
                            <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                <i class="ph-bold ph-file-pdf" style="color: #ef4444; font-size: 24px;"></i>
                                <div style="overflow: hidden;">
                                    <div id="fileNameDisplay" style="font-weight: 700; color: #0f172a; font-size: 13px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">-</div>
                                    <div id="fileSizeDisplay" style="font-size: 11px; color: #64748b;">-</div>
                                </div>
                            </div>
                            <button type="button" onclick="removeSelectedFile()" style="background: none; border: none; color: #ef4444; font-weight: 700; cursor: pointer; font-size: 13px;">
                                Ganti
                            </button>
                        </div>
                    </div>
                    @error('file_st')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SECTION 3: Sasaran Target Output & Kriteria -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-step-badge" style="background: #f0fdf4; color: #16a34a;">3</div>
                    <div>
                        <h3>Target Output & Kriteria Hasil</h3>
                        <p>Kuantifikasi sasaran kuantitas dan kriteria penerimaan hasil akhir pekerjaan.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="target" class="form-label">
                            <span>Target Kuantitas <span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-target input-icon"></i>
                            <input type="number" 
                                   id="target" 
                                   name="target" 
                                   step="0.01" 
                                   min="0.01"
                                   class="form-input @error('target') is-invalid @enderror" 
                                   value="{{ old('target', 1) }}" 
                                   required
                                   oninput="updateLivePreview()">
                        </div>
                        @error('target')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="satuan" class="form-label">
                            <span>Satuan Ukuran <span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-tag input-icon"></i>
                            <input type="text" 
                                   id="satuan" 
                                   name="satuan" 
                                   class="form-input @error('satuan') is-invalid @enderror" 
                                   value="{{ old('satuan', 'Laporan') }}" 
                                   placeholder="Dokumen, Kegiatan, Paket" 
                                   required
                                   oninput="updateLivePreview()">
                        </div>
                        @error('satuan')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" style="margin-top: 18px;">
                    <label for="hasil_diharapkan" class="form-label">
                        <span>Hasil / Output yang Diharapkan <span class="required">*</span></span>
                        <span class="form-label-hint">Kriteria selesai saat verifikasi akhir</span>
                    </label>
                    <textarea id="hasil_diharapkan" 
                              name="hasil_diharapkan" 
                              class="form-textarea @error('hasil_diharapkan') is-invalid @enderror" 
                              rows="2"
                              placeholder="Misal: Laporan pelaksanaan kegiatan terverifikasi, daftar hadir peserta, dan materi dokumentasi lengkap..." 
                              required
                              oninput="updateLivePreview()">{{ old('hasil_diharapkan') }}</textarea>
                    @error('hasil_diharapkan')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">
                        <span>Deskripsi Lingkup Kerja</span>
                        <span class="form-label-hint">Latar belakang & rincian teknis pelaksanaan</span>
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              class="form-textarea @error('deskripsi') is-invalid @enderror" 
                              rows="2"
                              placeholder="Jelaskan ruang lingkup, arahan, dan langkah kerja..."
                              oninput="updateLivePreview()">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SECTION 4: Penugasan Pelaksana & Tenggat Waktu -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-step-badge" style="background: #faf5ff; color: #9333ea;">4</div>
                    <div>
                        <h3>Penugasan Pelaksana & Tenggat Waktu</h3>
                        <p>Tentukan Koordinator / PIC Utama serta pilih satu atau lebih pegawai untuk menjalankan tugas bersama.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    @php
                        $myPokjaId = Auth::user()->pokja_id;
                        $teamUsers = $users->filter(fn($u) => $myPokjaId && $u->pokja_id == $myPokjaId);
                        $otherUsers = $users->reject(fn($u) => $myPokjaId && $u->pokja_id == $myPokjaId);
                        $defaultPicId = old('penanggung_jawab_id', Auth::id());
                    @endphp
                    <div class="form-group">
                        <label for="penanggung_jawab_id" class="form-label">
                            <span>Koordinator / PIC Utama <span class="required">*</span></span>
                            <span class="form-label-hint">Ketua pelaksana penugasan</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-user-check input-icon"></i>
                            <select id="penanggung_jawab_id" 
                                    name="penanggung_jawab_id" 
                                    class="form-select @error('penanggung_jawab_id') is-invalid @enderror" 
                                    required
                                    onchange="handlePicChange()">
                                <option value="">-- Pilih PIC Utama --</option>
                                @if($teamUsers->isNotEmpty())
                                    <optgroup label="⭐ Anggota Tim Kerja / Pokja Anda">
                                        @foreach($teamUsers as $u)
                                            <option value="{{ $u->id }}" {{ $defaultPicId == $u->id ? 'selected' : '' }} data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}">
                                                {{ $u->name }} ({{ $u->nip ?? 'Pegawai' }}) - {{ $u->role_label }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                <optgroup label="👥 Seluruh Pegawai BGTK">
                                    @foreach($otherUsers as $u)
                                        <option value="{{ $u->id }}" {{ $defaultPicId == $u->id ? 'selected' : '' }} data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}">
                                            {{ $u->name }} ({{ $u->nip ?? 'Pegawai' }}) - {{ $u->pokja?->nama_pokja ?? 'BGTK' }} ({{ $u->role_label }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        @error('penanggung_jawab_id')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tenggat" class="form-label">
                            <span>Tenggat Waktu Selesai <span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-calendar-check input-icon"></i>
                            <input type="date" 
                                   id="tenggat" 
                                   name="tenggat" 
                                   class="form-input @error('tenggat') is-invalid @enderror" 
                                   value="{{ old('tenggat', date('Y-12-31')) }}" 
                                   required
                                   onchange="updateLivePreview()">
                        </div>
                        @error('tenggat')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Tim Pelaksana Tambahan (Multi-Select) -->
                <div class="form-group" style="margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <label class="form-label" style="margin-bottom: 0;">
                            <span>Anggota Tim Pelaksana (Multi-Select)</span>
                            <span class="form-label-hint">Pilih anggota tim yang akan berkontribusi melaporkan progres tugas</span>
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span id="pelaksanaCountBadge" style="background: #eff6ff; color: #1d4ed8; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; border: 1px solid #bfdbfe;">
                                1 Pegawai Terpilih (PIC)
                            </span>
                            @if($teamUsers->isNotEmpty())
                                <button type="button" class="btn-action-outline" style="font-size: 11.5px; padding: 4px 10px; border-radius: 6px;" onclick="selectAllPokja()">
                                    <i class="ph-bold ph-users-three"></i> + Semua Pokja Saya
                                </button>
                            @endif
                            <button type="button" class="btn-action-outline" style="font-size: 11.5px; padding: 4px 10px; border-radius: 6px; color: #dc2626; border-color: #fecaca;" onclick="resetAnggotaTambahan()">
                                <i class="ph-bold ph-x"></i> Reset Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Selected Assignees Chips Container -->
                    <div id="selectedPelaksanaChips" style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; min-height: 38px; padding: 6px 10px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: var(--radius-md); align-items: center;">
                        <!-- Chips rendered via JS -->
                    </div>

                    <!-- Search Input Filter -->
                    <div class="input-wrapper" style="margin-bottom: 10px;">
                        <i class="ph-bold ph-magnifying-glass input-icon"></i>
                        <input type="text" 
                               id="searchPelaksana" 
                               class="form-input" 
                               placeholder="Cari nama pegawai, NIP, atau tim kerja untuk ditugaskan..." 
                               oninput="filterPelaksanaList(this.value)">
                    </div>

                    <!-- Scrollable Assignee Cards Grid -->
                    <div class="pelaksana-selection-container" style="max-height: 270px; overflow-y: auto; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 12px; background: #ffffff;">
                        @if($teamUsers->isNotEmpty())
                            <div class="pelaksana-group-title" style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; letter-spacing: 0.04em; margin-bottom: 8px; padding: 2px 4px;">
                                ⭐ Anggota Tim Kerja / Pokja Anda
                            </div>
                            <div class="pelaksana-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px; margin-bottom: 16px;">
                                @foreach($teamUsers as $u)
                                    <label class="pelaksana-card" id="pelaksana-card-{{ $u->id }}" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}" data-is-my-pokja="1">
                                        <input type="checkbox" 
                                               name="pelaksana_ids[]" 
                                               value="{{ $u->id }}" 
                                               class="pelaksana-checkbox" 
                                               id="pelaksana_chk_{{ $u->id }}"
                                               {{ (is_array(old('pelaksana_ids')) && in_array($u->id, old('pelaksana_ids'))) || $defaultPicId == $u->id ? 'checked' : '' }}
                                               onchange="syncPelaksanaSelection()">
                                        <div class="pelaksana-avatar">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                                        <div class="pelaksana-meta">
                                            <div class="pelaksana-name">{{ $u->name }}</div>
                                            <div class="pelaksana-sub">{{ $u->role_label }} &bull; {{ $u->nip ?? 'NIP -' }}</div>
                                        </div>
                                        <span class="pic-tag" id="pic_tag_{{ $u->id }}" style="display: none;">PIC UTAMA</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="pelaksana-group-title" style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.04em; margin-bottom: 8px; padding: 2px 4px;">
                            👥 Seluruh Pegawai BGTK
                        </div>
                        <div class="pelaksana-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px;">
                            @foreach($otherUsers as $u)
                                <label class="pelaksana-card" id="pelaksana-card-{{ $u->id }}" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}" data-is-my-pokja="0">
                                    <input type="checkbox" 
                                           name="pelaksana_ids[]" 
                                           value="{{ $u->id }}" 
                                           class="pelaksana-checkbox" 
                                           id="pelaksana_chk_{{ $u->id }}"
                                           {{ (is_array(old('pelaksana_ids')) && in_array($u->id, old('pelaksana_ids'))) || $defaultPicId == $u->id ? 'checked' : '' }}
                                           onchange="syncPelaksanaSelection()">
                                        <div class="pelaksana-avatar">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                                        <div class="pelaksana-meta">
                                            <div class="pelaksana-name">{{ $u->name }}</div>
                                            <div class="pelaksana-sub">{{ $u->pokja?->nama_pokja ?? 'BGTK' }} &bull; {{ $u->role_label }}</div>
                                        </div>
                                        <span class="pic-tag" id="pic_tag_{{ $u->id }}" style="display: none;">PIC UTAMA</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status_persetujuan" class="form-label">
                        <span>Status Awal Pekerjaan <span class="required">*</span></span>
                    </label>
                    <div class="input-wrapper">
                        <i class="ph-bold ph-shield-check input-icon"></i>
                        <select id="status_persetujuan" name="status_persetujuan" class="form-select @error('status_persetujuan') is-invalid @enderror" required onchange="updateLivePreview()">
                            <option value="disetujui" {{ old('status_persetujuan', 'disetujui') == 'disetujui' ? 'selected' : '' }}>
                                Disetujui Langsung (Pekerjaan Langsung Aktif untuk Pelaksana)
                            </option>
                            <option value="draft" {{ old('status_persetujuan') == 'draft' ? 'selected' : '' }}>
                                Simpan Sebagai Draf (Belum aktif)
                            </option>
                        </select>
                    </div>
                    @error('status_persetujuan')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tombol Aksi Desktop -->
            <div class="form-actions-desktop">
                <a href="{{ route('pekerjaan.index') }}" class="btn-cancel-main">
                    <i class="ph-bold ph-arrow-left"></i>
                    <span>Batal</span>
                </a>
                <button type="submit" class="btn-submit-main">
                    <i class="ph-bold ph-check"></i>
                    <span>Simpan & Terbitkan Pekerjaan</span>
                </button>
            </div>

        </div>

        <!-- ==================== KOLOM KANAN: LIVE SUMMARY PREVIEW ==================== -->
        <div class="form-preview-sidebar">
            <div class="preview-card-sticky">
                <div class="preview-header">
                    <div style="display: flex; gap: 6px; align-items: center; margin-bottom: 8px; flex-wrap: wrap;">
                        <div class="preview-badge-status" id="previewBadgeStatus">
                            <i class="ph-bold ph-check-circle"></i> <span id="previewStatusText">Disetujui Langsung</span>
                        </div>
                        <div class="preview-badge-status" id="previewSumberBadge" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); font-size: 11px;">
                            <i class="ph-bold ph-pencil-line" id="previewSumberIcon"></i> <span id="previewSumberText">Manual</span>
                        </div>
                    </div>
                    <div style="font-family: monospace; font-size: 13px; font-weight: 800; color: #93c5fd; margin-bottom: 4px;" id="previewKode">
                        {{ $suggestedKode }}
                    </div>
                    <h4 class="preview-title" id="previewJudul">
                        Nama pekerjaan akan tampil di sini...
                    </h4>
                </div>

                <div class="preview-body">
                    <div class="preview-item" id="previewSkpRow" style="display: none; background: #f0f9ff; margin: -10px -18px 12px -18px; padding: 10px 18px; border-bottom: 1px solid #e0f2fe;">
                        <span class="preview-label" style="color: #0369a1;"><i class="ph-bold ph-target"></i> SKP Terkait</span>
                        <span class="preview-val" id="previewSkpVal" style="font-size: 11.5px; color: #0284c7; font-weight: 700; text-align: right; max-width: 60%;">-</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-buildings"></i> Tim Kerja</span>
                        <span class="preview-val" id="previewPokja">-</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-user"></i> PIC Utama</span>
                        <span class="preview-val" id="previewPic">{{ Auth::user()->name }}</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-users"></i> Tim Pelaksana</span>
                        <span class="preview-val" id="previewAnggotaCount">1 Orang</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-target"></i> Target</span>
                        <span class="preview-val" id="previewTarget">1 Laporan</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-calendar"></i> Tenggat</span>
                        <span class="preview-val" id="previewTenggat">-</span>
                    </div>

                    <div class="preview-item" id="previewStRow" style="display: none;">
                        <span class="preview-label"><i class="ph-bold ph-file-text"></i> Dasar ST</span>
                        <span class="preview-val" style="font-family: monospace; color: #1e40af;" id="previewStVal">-</span>
                    </div>

                    <!-- Checklist Kelengkapan Form -->
                    <div class="form-checklist">
                        <div class="checklist-title">
                            <span>Kelengkapan Data</span>
                            <span id="completenessPercent">0%</span>
                        </div>
                        <div class="progress-micro">
                            <div id="completenessBar" class="progress-micro-fill" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Mobile Floating Action Bar -->
    <div class="mobile-floating-bar">
        <a href="{{ route('pekerjaan.index') }}" class="btn-cancel-main">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <button type="submit" class="btn-submit-main">
            <i class="ph-bold ph-check"></i>
            <span>Simpan Pekerjaan</span>
        </button>
    </div>
</form>

@endsection

@section('scripts')
<script>
    // ==========================================
    // Switch Sumber Penugasan (Manual vs SKP)
    // ==========================================
    function switchSumberPekerjaan(sumber) {
        const cardManual = document.getElementById('cardSumberManual');
        const cardSkp = document.getElementById('cardSumberSkp');
        const radioManual = document.getElementById('radioManual');
        const radioSkp = document.getElementById('radioSkp');
        const panelSkp = document.getElementById('panelRujukanSkp');
        const previewSumberText = document.getElementById('previewSumberText');
        const previewSumberIcon = document.getElementById('previewSumberIcon');
        const previewSkpRow = document.getElementById('previewSkpRow');

        if (sumber === 'skp') {
            radioSkp.checked = true;
            cardSkp.classList.add('active');
            cardManual.classList.remove('active');
            panelSkp.style.display = 'block';

            if (previewSumberText) previewSumberText.textContent = 'Rujukan SKP';
            if (previewSumberIcon) previewSumberIcon.className = 'ph-bold ph-target';
            
            // Check if SKP already selected
            const skpSelect = document.getElementById('skp_tahunan_id');
            if (skpSelect && skpSelect.value) {
                handleSkpSelection(skpSelect);
            }
        } else {
            radioManual.checked = true;
            cardManual.classList.add('active');
            cardSkp.classList.remove('active');
            panelSkp.style.display = 'none';

            if (previewSumberText) previewSumberText.textContent = 'Manual';
            if (previewSumberIcon) previewSumberIcon.className = 'ph-bold ph-pencil-line';
            if (previewSkpRow) previewSkpRow.style.display = 'none';
        }

        updateLivePreview();
    }

    // Handler Saat Pengguna Memilih Butir SKP
    function handleSkpSelection(selectEl) {
        const val = selectEl.value;
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const previewSkpRow = document.getElementById('previewSkpRow');
        const previewSkpVal = document.getElementById('previewSkpVal');
        const skpInfo = document.getElementById('skpSelectedInfo');

        if (!val || !selectedOption) {
            if (previewSkpRow) previewSkpRow.style.display = 'none';
            if (skpInfo) skpInfo.style.display = 'none';
            return;
        }

        const rhk = selectedOption.getAttribute('data-rhk') || '';
        const target = selectedOption.getAttribute('data-target') || '1';
        const satuan = selectedOption.getAttribute('data-satuan') || 'Laporan';
        const userId = selectedOption.getAttribute('data-user-id') || '';
        const userName = selectedOption.getAttribute('data-user-name') || '-';
        const iki = selectedOption.getAttribute('data-iki') || '';

        // Auto-fill input fields
        const inputJudul = document.getElementById('judul');
        if (inputJudul) {
            inputJudul.value = rhk;
        }

        const inputTarget = document.getElementById('target');
        if (inputTarget) {
            inputTarget.value = target;
        }

        const inputSatuan = document.getElementById('satuan');
        if (inputSatuan) {
            inputSatuan.value = satuan;
        }

        const inputHasil = document.getElementById('hasil_diharapkan');
        if (inputHasil && (!inputHasil.value || inputHasil.value.trim() === '')) {
            inputHasil.value = iki ? `Tercapainya indikator: ${iki}` : `Terselesaikannya target: ${rhk}`;
        }

        // Auto-select PIC Utama if matches owner of SKP
        if (userId) {
            const picSelect = document.getElementById('penanggung_jawab_id');
            if (picSelect) {
                const picOption = picSelect.querySelector(`option[value="${userId}"]`);
                if (picOption) {
                    picSelect.value = userId;
                    handlePicChange();
                }
            }
        }

        // Show Info Box
        if (skpInfo) {
            document.getElementById('skpRhkText').textContent = rhk;
            document.getElementById('skpOwnerText').textContent = userName;
            document.getElementById('skpTargetText').textContent = `${target} ${satuan}`;
            skpInfo.style.display = 'block';
        }

        // Update preview
        if (previewSkpRow && previewSkpVal) {
            previewSkpVal.textContent = rhk.length > 50 ? rhk.substring(0, 48) + '...' : rhk;
            previewSkpRow.style.display = 'flex';
        }

        updateLivePreview();
    }

    // Live Summary Preview Updates
    function updateLivePreview() {
        const kode = document.getElementById('kode_tampilan')?.value || '-';
        const judul = document.getElementById('judul')?.value || 'Nama pekerjaan akan tampil di sini...';
        const target = document.getElementById('target')?.value || '0';
        const satuan = document.getElementById('satuan')?.value || '';
        const tenggat = document.getElementById('tenggat')?.value || '-';
        const nomorSt = document.getElementById('nomor_st')?.value || '';
        
        const pokjaSelect = document.getElementById('pokja_id');
        const pokjaText = pokjaSelect && pokjaSelect.selectedIndex > 0 ? pokjaSelect.options[pokjaSelect.selectedIndex].getAttribute('data-name') || pokjaSelect.options[pokjaSelect.selectedIndex].text : 'BGTK DKI Jakarta';

        const statusSelect = document.getElementById('status_persetujuan');
        const statusText = statusSelect ? statusSelect.options[statusSelect.selectedIndex].text : 'Disetujui Langsung';

        // Update elements
        document.getElementById('previewKode').textContent = kode;
        document.getElementById('previewJudul').textContent = judul;
        document.getElementById('previewPokja').textContent = pokjaText;
        document.getElementById('previewTarget').textContent = `${target} ${satuan}`;
        document.getElementById('previewStatusText').textContent = statusText.split('(')[0].trim();
        
        if (tenggat !== '-') {
            const dateObj = new Date(tenggat);
            if (!isNaN(dateObj.getTime())) {
                document.getElementById('previewTenggat').textContent = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            }
        }

        // ST row toggle
        const stRow = document.getElementById('previewStRow');
        if (nomorSt.trim() !== '') {
            stRow.style.display = 'flex';
            document.getElementById('previewStVal').textContent = nomorSt;
        } else {
            stRow.style.display = 'none';
        }

        // Char count for judul
        const charCount = document.getElementById('judul')?.value.length || 0;
        document.getElementById('charCount').textContent = `${charCount} / 255 karakter`;

        // Calculate completeness
        calculateCompleteness();
    }

    // Hitung Kelengkapan Pengisian Form
    function calculateCompleteness() {
        const fields = [
            document.getElementById('kode_tampilan')?.value.trim(),
            document.getElementById('judul')?.value.trim(),
            document.getElementById('target')?.value.trim(),
            document.getElementById('satuan')?.value.trim(),
            document.getElementById('hasil_diharapkan')?.value.trim(),
            document.getElementById('penanggung_jawab_id')?.value.trim(),
            document.getElementById('tenggat')?.value.trim()
        ];

        let filled = 0;
        fields.forEach(f => { if (f && f !== '') filled++; });
        
        const percent = Math.round((filled / fields.length) * 100);
        document.getElementById('completenessPercent').textContent = `${percent}%`;
        document.getElementById('completenessBar').style.width = `${percent}%`;
    }

    // ==========================================
    // Multi-Assignee Interactive Selector Logic
    // ==========================================
    function handlePicChange() {
        const picSelect = document.getElementById('penanggung_jawab_id');
        const picId = picSelect.value;
        const selectedOption = picSelect.options[picSelect.selectedIndex];
        const picName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.text : '-';

        document.getElementById('previewPic').textContent = picName;

        // Auto check PIC card & mark as PIC tag
        if (picId) {
            const picChk = document.getElementById(`pelaksana_chk_${picId}`);
            if (picChk) {
                picChk.checked = true;
            }
        }

        syncPelaksanaSelection();
    }

    function syncPelaksanaSelection() {
        const picId = document.getElementById('penanggung_jawab_id')?.value;
        const allCards = document.querySelectorAll('.pelaksana-card');
        const chipsContainer = document.getElementById('selectedPelaksanaChips');
        chipsContainer.innerHTML = '';

        let count = 0;

        allCards.forEach(card => {
            const id = card.getAttribute('data-id');
            const name = card.getAttribute('data-name');
            const chk = document.getElementById(`pelaksana_chk_${id}`);
            const tag = document.getElementById(`pic_tag_${id}`);

            if (chk && chk.checked) {
                count++;
                card.classList.add('selected');

                const isPic = (id === picId);
                if (isPic) {
                    card.classList.add('is-pic');
                    if (tag) tag.style.display = 'inline-block';
                } else {
                    card.classList.remove('is-pic');
                    if (tag) tag.style.display = 'none';
                }

                // Add chip
                const chip = document.createElement('div');
                chip.className = `pelaksana-chip ${isPic ? 'is-pic-chip' : ''}`;
                chip.innerHTML = `
                    <span>${isPic ? '⭐ ' : ''}${name}</span>
                    ${!isPic ? `<button type="button" class="btn-remove-chip" onclick="uncheckPelaksana('${id}')" title="Hapus dari penugasan">&times;</button>` : ''}
                `;
                chipsContainer.appendChild(chip);

            } else {
                card.classList.remove('selected');
                card.classList.remove('is-pic');
                if (tag) tag.style.display = 'none';
            }
        });

        if (count === 0) {
            chipsContainer.innerHTML = '<span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">Belum ada anggota yang dipilih. Centang daftar pegawai di bawah.</span>';
        }

        document.getElementById('pelaksanaCountBadge').textContent = `${count} Pegawai Terpilih`;
        document.getElementById('previewAnggotaCount').textContent = `${count} Orang`;
    }

    function uncheckPelaksana(id) {
        const chk = document.getElementById(`pelaksana_chk_${id}`);
        if (chk) {
            chk.checked = false;
            syncPelaksanaSelection();
        }
    }

    function selectAllPokja() {
        document.querySelectorAll('.pelaksana-card[data-is-my-pokja="1"]').forEach(card => {
            const id = card.getAttribute('data-id');
            const chk = document.getElementById(`pelaksana_chk_${id}`);
            if (chk) chk.checked = true;
        });
        syncPelaksanaSelection();
    }

    function resetAnggotaTambahan() {
        const picId = document.getElementById('penanggung_jawab_id')?.value;
        document.querySelectorAll('.pelaksana-checkbox').forEach(chk => {
            if (chk.value !== picId) {
                chk.checked = false;
            }
        });
        syncPelaksanaSelection();
    }

    function filterPelaksanaList(keyword) {
        const q = keyword.toLowerCase().trim();
        document.querySelectorAll('.pelaksana-card').forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // ==========================================
    // File Dropzone ST Handler
    // ==========================================
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('fileNameDisplay').textContent = file.name;
            document.getElementById('fileSizeDisplay').textContent = formatBytes(file.size);
            document.getElementById('dropzoneFileInfo').style.display = 'flex';
        }
    }

    function removeSelectedFile() {
        const fileInput = document.getElementById('file_st');
        fileInput.value = '';
        document.getElementById('dropzoneFileInfo').style.display = 'none';
    }

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Init on Page Load
    document.addEventListener('DOMContentLoaded', () => {
        handlePicChange();
        const initialSumber = document.querySelector('input[name="sumber_pekerjaan"]:checked')?.value || 'manual';
        switchSumberPekerjaan(initialSumber);
        updateLivePreview();
    });
</script>
@endsection
