@extends('layouts.app')

@section('title', "Pekerjaan {$pekerjaan->kode_tampilan}: {$pekerjaan->judul} | SIMON-K")
@section('page-title', "Rincian Pekerjaan: {$pekerjaan->kode_tampilan}")

@section('styles')
<style>
    /* Breadcrumb Chain */
    .hierarchy-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 13px;
        margin-bottom: 20px;
        background: #ffffff;
        padding: 12px 18px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .hierarchy-breadcrumb a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .hierarchy-breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb-sep {
        color: #94a3b8;
    }

    .breadcrumb-current {
        font-weight: 700;
        color: #0f172a;
    }

    /* Main Job Detail Card */
    .detail-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 28px 32px;
        margin-bottom: 24px;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .code-badge-large {
        font-family: monospace;
        background: #0f172a;
        color: white;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 0.05em;
        display: inline-block;
        margin-bottom: 8px;
    }

    .job-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .meta-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
    }

    .meta-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .meta-value {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }

    /* Progress Banner */
    .progress-banner {
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 18px 24px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .progress-bar-lg {
        flex: 1;
        min-width: 200px;
        height: 12px;
        background: #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 20px;
        transition: width 0.4s ease;
    }

    /* Badges */
    .badge-pill {
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .status-disetujui { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .status-diajukan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .status-draft { background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; }
    .status-perlu_perbaikan { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .exec-selesai_terverifikasi { background: #f0fdf4; color: #166534; font-weight: 800; border: 1px solid #86efac; }
    .exec-hasil_diajukan { background: #fdf4ff; color: #86198f; font-weight: 700; border: 1px solid #f0abfc; }
    .exec-dikerjakan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .exec-belum_dimulai { background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; }
    .exec-perlu_perbaikan_hasil { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

    /* Tables */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    .data-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-align: left;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 12px;
        text-transform: uppercase;
    }

    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .data-table tr:hover td {
        background: #fafbfc;
    }

    /* Modal Styling */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(2px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 600px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: modalFadeIn 0.2s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.97); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .modal-body {
        padding: 24px;
        max-height: calc(85vh - 120px);
        overflow-y: auto;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border);
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-action-primary {
        background: var(--primary);
        color: white;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-action-primary:hover {
        background: var(--primary-hover);
    }

    .btn-action-outline {
        background: white;
        color: #334155;
        border: 1px solid var(--border);
        padding: 9px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-action-outline:hover {
        background: #f8fafc;
    }

    @media (max-width: 640px) {
        .detail-card {
            padding: 18px 16px;
        }
        .job-title {
            font-size: 18px;
        }
        .meta-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .modal-card {
            max-height: 92vh;
            border-radius: 14px;
        }
        .modal-header, .modal-footer {
            padding: 14px 16px;
        }
        .modal-body {
            padding: 16px;
        }
    }
</style>
@endsection

@section('content')

    <!-- 1. Navigasi Kembali -->
    <div class="hierarchy-breadcrumb">
        <a href="{{ route('pekerjaan.index') }}">
            <i class="ph-bold ph-arrow-left"></i> Kembali ke Daftar Pekerjaan
        </a>
        <span class="breadcrumb-sep">&rsaquo;</span>
        <span class="breadcrumb-current">{{ $pekerjaan->judul }}</span>
    </div>

    <!-- 2. Detail Utama Pekerjaan -->
    <div class="detail-card">
        <div class="detail-header">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                    <span class="code-badge-large">{{ $pekerjaan->kode_tampilan }}</span>
                    @if($pekerjaan->isFromSkp())
                        <span style="font-size: 11px; background: #e0f2fe; color: #0284c7; padding: 3px 10px; border-radius: 99px; font-weight: 800; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="ph-bold ph-target"></i> Rujukan SKP Pegawai
                        </span>
                    @else
                        <span style="font-size: 11px; background: #f8fafc; color: #64748b; padding: 3px 10px; border-radius: 99px; font-weight: 700; border: 1px solid #e2e8f0;">
                            Pekerjaan Mandiri / Manual
                        </span>
                    @endif
                </div>
                <h2 class="job-title">{{ $pekerjaan->judul }}</h2>
                <div style="font-size: 13.5px; color: var(--text-muted); margin-top: 6px; display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                    <span><i class="ph-bold ph-buildings"></i> {{ $pekerjaan->pokja?->nama_pokja ?? 'BGTK DKI Jakarta' }}</span>
                    <span><i class="ph-bold ph-user-circle"></i> Pemberi Tugas: {{ $pekerjaan->pembuat->name }} ({{ $pekerjaan->pembuat->role_label }})</span>
                    <span><i class="ph-bold ph-clock"></i> Ditugaskan: {{ $pekerjaan->created_at->format('d M Y') }}</span>
                </div>
            </div>

            <!-- Action Buttons Area -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                @if($canUpdateProgres && $pekerjaan->status_pelaksanaan !== 'selesai_terverifikasi')
                    <button type="button" class="btn-action-primary" style="background: #10b981;" onclick="openProgressModalInShow(false)">
                        <i class="ph-bold ph-pencil-simple-line"></i>
                        <span>Update Progres & Bukti</span>
                    </button>

                    @if($pekerjaan->status_pelaksanaan !== 'hasil_diajukan')
                        <button type="button" class="btn-action-primary" style="background: #8b5cf6;" onclick="openProgressModalInShow(true)">
                            <i class="ph-bold ph-seal-check"></i>
                            <span>Selesaikan Tugas (100%)</span>
                        </button>
                    @endif
                @endif

                @if($canEdit)
                    <a href="{{ route('pekerjaan.edit', $pekerjaan) }}" class="btn-action-outline" style="border-color: #f59e0b; color: #b45309;" title="Edit rincian data pekerjaan">
                        <i class="ph-bold ph-pencil-simple"></i>
                        <span>Edit</span>
                    </a>
                @endif

                @if($canDelete)
                    <button type="button" class="btn-action-outline" style="border-color: #fca5a5; color: #dc2626;" onclick="openModal('modalHapusPekerjaan')" title="Hapus pekerjaan ini">
                        <i class="ph-bold ph-trash"></i>
                        <span>Hapus</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Banner Informasi Keterkaitan SKP (Jika Merujuk SKP) -->
        @if($pekerjaan->isFromSkp() && $pekerjaan->skpTahunan)
            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1.5px solid #bae6fd; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 14px; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.05);">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #0284c7; color: white; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                    <i class="ph-bold ph-target"></i>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #0369a1; letter-spacing: 0.05em;">
                            Tautan Sasaran Kinerja Pegawai (SKP Tahunan {{ $pekerjaan->skpTahunan->tahun }})
                        </span>
                        <span style="font-size: 10.5px; background: #0284c7; color: white; padding: 2px 7px; border-radius: 4px; font-weight: 700;">
                            RHK Resmi
                        </span>
                    </div>
                    <div style="font-size: 14.5px; font-weight: 800; color: #0c4a6e; margin-top: 4px; line-height: 1.4;">
                        {{ $pekerjaan->skpTahunan->rencana_kinerja }}
                    </div>
                    <div style="font-size: 12px; color: #0284c7; margin-top: 6px; display: flex; gap: 14px; flex-wrap: wrap;">
                        <span>Pegawai Pemilik: <strong>{{ $pekerjaan->skpTahunan->user?->name ?? '-' }}</strong></span>
                        <span>Target SKP: <strong>{{ (float)$pekerjaan->skpTahunan->target_max }} {{ $pekerjaan->skpTahunan->satuan }}</strong></span>
                        @if($pekerjaan->skpTahunan->indikator_kinerja)
                            <span>Indikator (IKI): <em>{{ Str::limit($pekerjaan->skpTahunan->indikator_kinerja, 60) }}</em></span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Dasar Penugasan & Surat Tugas (ST) Banner -->
        @if($pekerjaan->nomor_st || $pekerjaan->pembuat_id !== $pekerjaan->penanggung_jawab_id)
            <div style="background: linear-gradient(135deg, #f0f7ff 0%, #f8fafc 100%); border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                        <i class="ph-bold ph-file-text"></i>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; letter-spacing: 0.05em;">
                            Dasar Penugasan Resmi (Surat Tugas)
                        </div>
                        <div style="font-size: 15px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                            @if($pekerjaan->nomor_st)
                                Nomor ST: <span style="font-family: monospace; background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 4px;">{{ $pekerjaan->nomor_st }}</span>
                            @else
                                <span style="color: #64748b; font-weight: 600;">Penugasan Langsung dari Tim Kerja</span>
                            @endif
                        </div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px; display: flex; gap: 16px; flex-wrap: wrap;">
                            @if($pekerjaan->tanggal_st)
                                <span><i class="ph-bold ph-calendar"></i> Tanggal ST: <strong>{{ $pekerjaan->tanggal_st->format('d M Y') }}</strong></span>
                            @endif
                            <span><i class="ph-bold ph-user-check"></i> Pemberi Tugas: <strong>{{ $pekerjaan->pembuat->name }}</strong> ({{ $pekerjaan->pembuat->role_label }})</span>
                            <span><i class="ph-bold ph-user"></i> PIC Utama: <strong>{{ $pekerjaan->penanggungJawab->name }}</strong></span>
                            @if($pekerjaan->pelaksana->count() > 1)
                                <span><i class="ph-bold ph-users-three"></i> Total Tim: <strong>{{ $pekerjaan->pelaksana->count() }} Orang</strong></span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($pekerjaan->file_st)
                    <a href="{{ Storage::url($pekerjaan->file_st) }}" target="_blank" class="btn-action-primary" style="background: #2563eb;">
                        <i class="ph-bold ph-download-simple"></i>
                        <span>Unduh Berkas ST</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-box">
                <div class="meta-label"><i class="ph-bold ph-user"></i> Penanggung Jawab (PIC)</div>
                <div class="meta-value">{{ $pekerjaan->penanggungJawab->name }}</div>
                <div style="font-size: 11.5px; color: #64748b;">
                    {{ $pekerjaan->penanggungJawab->role_label }} &bull; NIP: {{ $pekerjaan->penanggungJawab->nip ?? '-' }}
                </div>
                @if($pekerjaan->pelaksana->count() > 1)
                    <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #cbd5e1; font-size: 11px; color: #2563eb; font-weight: 700;">
                        <i class="ph-bold ph-users-three"></i> +{{ $pekerjaan->pelaksana->count() - 1 }} Anggota Tim Terlibat
                    </div>
                @endif
            </div>

            <div class="meta-box">
                <div class="meta-label"><i class="ph-bold ph-target"></i> Target & Satuan</div>
                <div class="meta-value">{{ number_format($pekerjaan->target, 0) }} {{ $pekerjaan->satuan }}</div>
            </div>

            <div class="meta-box">
                <div class="meta-label"><i class="ph-bold ph-scales"></i> Bobot Terhadap Induk</div>
                <div class="meta-value">{{ number_format($pekerjaan->bobot, 1) }}%</div>
            </div>

            <div class="meta-box">
                <div class="meta-label"><i class="ph-bold ph-calendar"></i> Tenggat Waktu</div>
                <div class="meta-value" style="color: {{ $pekerjaan->isTerlambat() ? '#dc2626' : '#1e293b' }};">
                    {{ $pekerjaan->tenggat->format('d M Y') }}
                    @if($pekerjaan->isTerlambat())
                        <span style="font-size: 11px; background: #fee2e2; color: #dc2626; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Terlambat</span>
                    @endif
                </div>
            </div>

            <div class="meta-box">
                <div class="meta-label"><i class="ph-bold ph-file-check"></i> Status Persetujuan Struktur</div>
                <div>
                    <span class="badge-pill status-{{ $pekerjaan->status_persetujuan }}">
                        {{ str_replace('_', ' ', $pekerjaan->status_persetujuan) }}
                    </span>
                </div>
            </div>

            <div class="meta-box">
                <div class="meta-label"><i class="ph-bold ph-activity"></i> Status Pelaksanaan</div>
                <div>
                    <span class="badge-pill exec-{{ $pekerjaan->status_pelaksanaan }}">
                        {{ str_replace('_', ' ', $pekerjaan->status_pelaksanaan) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Progress Banner -->
        <div class="progress-banner">
            <div>
                <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                    Capaian Progres Realisasi
                    @if($pekerjaan->children->isNotEmpty())
                        <span style="color: var(--primary); font-weight: 600;">(Dihitung Otomatis Dari Bobot Subpekerjaan)</span>
                    @else
                        <span style="color: #10b981; font-weight: 600;">(Diperbarui Oleh PIC)</span>
                    @endif
                </div>
                <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                    {{ number_format($pekerjaan->progres, 1) }}%
                </div>
            </div>

            <div class="progress-bar-lg">
                <div class="progress-bar-fill {{ $pekerjaan->progres >= 100 ? 'bg-progres-green' : ($pekerjaan->progres >= 50 ? 'bg-progres-blue' : 'bg-progres-amber') }}" 
                     style="width: {{ $pekerjaan->progres }}%;"></div>
            </div>
        </div>

        <!-- Deskripsi & Hasil yang Diharapkan (PRD) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 24px;">
            <div style="background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px;">
                <h4 style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <i class="ph-bold ph-text-align-left"></i> Ruang Lingkup & Deskripsi
                </h4>
                <p style="font-size: 13.5px; color: #475569; line-height: 1.6; margin: 0;">
                    {{ $pekerjaan->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                </p>
            </div>

            <div style="background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px;">
                <h4 style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <i class="ph-bold ph-check-square-offset"></i> Hasil / Output yang Diharapkan
                </h4>
                <p style="font-size: 13.5px; color: #475569; line-height: 1.6; margin: 0;">
                    {{ $pekerjaan->hasil_diharapkan }}
                </p>
            </div>
        </div>

        <!-- Tim Pelaksana Tugas Resmi (ST) -->
        @if($pekerjaan->pelaksana->isNotEmpty())
            <div style="margin-top: 20px; background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                    <h4 style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="ph-bold ph-users-three" style="color: #2563eb;"></i> Tim Pelaksana Tugas Resmi ({{ $pekerjaan->pelaksana->count() }} Pegawai)
                    </h4>
                    <span style="font-size: 11.5px; color: #64748b;">
                        Diberikan wewenang melaksanakan & melaporkan capaian realisasi
                    </span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px;">
                    @foreach($pekerjaan->pelaksana as $pelaksana)
                        @php
                            $isPicUtama = ((int)$pelaksana->id === (int)$pekerjaan->penanggung_jawab_id) || ($pelaksana->pivot && $pelaksana->pivot->peran === 'pic_utama');
                        @endphp
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 8px; border: 1px solid {{ $isPicUtama ? '#bfdbfe' : '#e2e8f0' }}; background: {{ $isPicUtama ? '#eff6ff' : '#ffffff' }};">
                            <div style="width: 36px; height: 36px; border-radius: 999px; background: {{ $isPicUtama ? '#2563eb' : '#e2e8f0' }}; color: {{ $isPicUtama ? '#ffffff' : '#334155' }}; font-weight: 800; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                {{ strtoupper(substr($pelaksana->name, 0, 2)) }}
                            </div>
                            <div style="overflow: hidden; flex: 1;">
                                <div style="font-size: 13px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $pelaksana->name }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $pelaksana->role_label }} &bull; {{ $pelaksana->nip ?? 'Pegawai' }}
                                </div>
                            </div>
                            @if($isPicUtama)
                                <span style="font-size: 10px; font-weight: 800; background: #dbeafe; color: #1e40af; padding: 2px 7px; border-radius: 4px; white-space: nowrap;">
                                    PIC UTAMA
                                </span>
                            @else
                                <span style="font-size: 10px; font-weight: 700; background: #f1f5f9; color: #475569; padding: 2px 7px; border-radius: 4px; white-space: nowrap;">
                                    ANGGOTA
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- 3. Log Realisasi Progres & Bukti Dukung -->
    <div class="detail-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a;">
                    Riwayat Realisasi & Bukti Capaian ({{ $pekerjaan->realisasi->count() }})
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted);">
                    Catatan log kontribusi progres akumulatif pekerjaan oleh pelaksana
                </p>
            </div>

            @if($canUpdateProgres && $pekerjaan->status_pelaksanaan !== 'selesai_terverifikasi')
                <button type="button" class="btn-action-primary" style="background: #10b981;" onclick="openModal('modalUpdateProgres')">
                    <i class="ph-bold ph-plus"></i> Tambah Progres
                </button>
            @endif
        </div>

        @if($pekerjaan->realisasi->isNotEmpty())
            @php
                $runningTotal = 0;
                $realisasiList = $pekerjaan->realisasi->sortBy('created_at')->map(function($item) use (&$runningTotal) {
                    if ($item->status_approval === 'disetujui') {
                        $runningTotal += (float) $item->progres_capaian;
                        $item->cumulative_progres = min(100, $runningTotal);
                    } else {
                        $item->cumulative_progres = null;
                    }
                    return $item;
                })->sortByDesc('created_at');

                $isPemberiTugas = ($pekerjaan->pembuat_id === Auth::id())
                    || (Auth::user()->isKatim() && $pekerjaan->pokja && $pekerjaan->pokja->katim_id === Auth::id())
                    || Auth::user()->isAdmin();
            @endphp
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelapor</th>
                            <th>Capaian Progres</th>
                            <th>Status Validasi</th>
                            <th>Uraian Progres</th>
                            <th>Kendala & Solusi</th>
                            <th>Bukti Dukung</th>
                            @if($isPemberiTugas)
                                <th style="text-align: right;">Aksi Approval</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($realisasiList as $r)
                            <tr>
                                <td style="white-space: nowrap; font-weight: 600;">
                                    {{ $r->tanggal->format('d M Y') }}
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 28px; height: 28px; border-radius: 999px; background: #e2e8f0; color: #334155; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($r->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1e293b; font-size: 13px;">{{ $r->user->name }}</div>
                                            <div style="font-size: 11px; color: #64748b;">{{ $r->user->role_label }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 3px;">
                                        <span style="font-size: 12.5px; font-weight: 800; color: {{ $r->status_approval === 'disetujui' ? '#047857' : ($r->status_approval === 'ditolak' ? '#dc2626' : '#d97706') }}; background: {{ $r->status_approval === 'disetujui' ? '#ecfdf5' : ($r->status_approval === 'ditolak' ? '#fef2f2' : '#fffbeb') }}; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 3px; border: 1px solid {{ $r->status_approval === 'disetujui' ? 'rgba(16, 185, 129, 0.25)' : ($r->status_approval === 'ditolak' ? 'rgba(239, 68, 68, 0.25)' : 'rgba(245, 158, 11, 0.25)') }};">
                                            <i class="ph-bold ph-plus" style="font-size: 10px;"></i>{{ number_format($r->progres_capaian, 1) }}%
                                        </span>
                                        @if($r->cumulative_progres !== null)
                                            <span style="font-size: 11px; color: #64748b; font-weight: 600;">
                                                Akumulasi: <strong style="color: #2563eb;">{{ number_format($r->cumulative_progres, 1) }}%</strong>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($r->status_approval === 'disetujui')
                                        <span class="badge-pill" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="ph-bold ph-check-circle"></i> Disetujui
                                        </span>
                                        @if($r->approver)
                                            <div style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                                                Oleh: <strong>{{ $r->approver->name }}</strong>
                                            </div>
                                        @endif
                                    @elseif($r->status_approval === 'ditolak')
                                        <span class="badge-pill" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="ph-bold ph-x-circle"></i> Ditolak
                                        </span>
                                        @if($r->catatan_approval)
                                            <div style="font-size: 11px; color: #dc2626; margin-top: 3px; max-width: 200px;">
                                                <em>"{{ $r->catatan_approval }}"</em>
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge-pill" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="ph-bold ph-clock"></i> Menunggu Persetujuan
                                        </span>
                                        <div style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                                            Pemberi tugas: <strong>{{ $pekerjaan->pembuat->name }}</strong>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $r->deskripsi_progres }}</td>
                                <td>
                                    @if($r->kendala)
                                        <div style="color: #dc2626; font-size: 12px; margin-bottom: 2px;">
                                            <strong>Kendala:</strong> {{ $r->kendala }}
                                        </div>
                                    @endif
                                    @if($r->tindak_lanjut)
                                        <div style="color: #047857; font-size: 12px;">
                                            <strong>Solusi:</strong> {{ $r->tindak_lanjut }}
                                        </div>
                                    @endif
                                    @if(!$r->kendala && !$r->tindak_lanjut)
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($r->file_bukti)
                                        <a href="{{ asset('storage/' . $r->file_bukti) }}" target="_blank" class="btn-action-outline" style="padding: 4px 8px; font-size: 11.5px;">
                                            <i class="ph-bold ph-download-simple"></i> Unduh File
                                        </a>
                                    @endif
                                    @if($r->link_bukti)
                                        <a href="{{ $r->link_bukti }}" target="_blank" class="btn-action-outline" style="padding: 4px 8px; font-size: 11.5px; margin-top: 4px;">
                                            <i class="ph-bold ph-arrow-square-out"></i> Tautan
                                        </a>
                                    @endif
                                    @if(!$r->file_bukti && !$r->link_bukti)
                                        <span style="color: #94a3b8; font-size: 12px;">Tanpa berkas</span>
                                    @endif
                                </td>
                                @if($isPemberiTugas)
                                    <td style="text-align: right; white-space: nowrap;">
                                        @if($r->status_approval === 'menunggu_persetujuan')
                                            <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                                <form action="{{ route('realisasi.approve', $r) }}" method="POST" onsubmit="return confirm('Setujui laporan progres ini? Capaian +{{ $r->progres_capaian }}% akan langsung diakumulasikan ke pekerjaan.')">
                                                    @csrf
                                                    <button type="submit" class="btn-action-primary" style="padding: 5px 10px; font-size: 11.5px; background: #10b981;" title="Setujui penambahan progres">
                                                        <i class="ph-bold ph-check"></i> Setujui
                                                    </button>
                                                </form>
                                                <button type="button" class="btn-action-outline" style="padding: 5px 10px; font-size: 11.5px; color: #dc2626; border-color: #fca5a5;" onclick="openModalTolakProgres('{{ route('realisasi.reject', $r) }}')" title="Tolak dengan catatan">
                                                    <i class="ph-bold ph-x"></i> Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Selesai divalidasi</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 24px; text-align: center; color: #94a3b8; font-size: 13.5px;">
                Belum ada log realisasi yang dilaporkan untuk pekerjaan ini.
            </div>
        @endif
    </div>

    <!-- 5. Riwayat Keputusan & Persetujuan (Audit Trail PRD Bagian 10.4) -->
    <div class="detail-card">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
            Riwayat Keputusan & Audit Trail ({{ $pekerjaan->riwayatPersetujuan->count() }})
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 18px;">
            Jejak digital persetujuan rincian dan verifikasi hasil akhir pekerjaan
        </p>

        @if($pekerjaan->riwayatPersetujuan->isNotEmpty())
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pemeriksa</th>
                            <th>Aksi</th>
                            <th>Perubahan Status</th>
                            <th>Catatan Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pekerjaan->riwayatPersetujuan->sortByDesc('created_at') as $rw)
                            <tr>
                                <td style="white-space: nowrap; font-size: 12px;">{{ $rw->created_at->format('d M Y H:i') }}</td>
                                <td style="font-weight: 700;">{{ $rw->user->name }}</td>
                                <td>
                                    <span style="font-family: monospace; font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">
                                        {{ $rw->jenis_aksi }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: #64748b;">{{ $rw->status_sebelumnya ?? 'Awal' }}</span>
                                    &rarr;
                                    <strong style="font-size: 12.5px; color: #0f172a;">{{ $rw->status_baru }}</strong>
                                </td>
                                <td>{{ $rw->catatan ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 16px; text-align: center; color: #94a3b8; font-size: 13px;">
                Belum ada jejak audit persetujuan tercatat.
            </div>
        @endif
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Modal 1: Update Progres & Bukti (Pelaksana) -->
    @if($canUpdateProgres)
        <div id="modalUpdateProgres" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'modalUpdateProgres')">
            <div class="modal-card">
                <form action="{{ route('pekerjaan.update-progres', $pekerjaan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <div class="modal-title">
                            <i class="ph-bold ph-pencil-simple-line"></i> Laporkan Realisasi & Progres
                        </div>
                        <button type="button" onclick="closeModal('modalUpdateProgres')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
                    </div>

                    <div class="modal-body">
                        @php
                            $currentProg = (float) $pekerjaan->progres;
                            $remainingProg = max(0, round(100 - $currentProg, 1));
                        @endphp

                        <!-- Banner Status Progres Tim Saat Ini -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 18px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                    <i class="ph-bold ph-chart-line-up" style="color: #2563eb;"></i> Progres Tim Saat Ini:
                                </span>
                                <span style="font-size: 15px; font-weight: 800; color: #2563eb; font-family: monospace;">
                                    {{ number_format($currentProg, 1) }}%
                                </span>
                            </div>
                            <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin-bottom: 8px;">
                                <div style="width: {{ $currentProg }}%; height: 100%; background: linear-gradient(90deg, #3b82f6, #10b981); border-radius: 999px;"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: #64748b;">
                                <span>Sisa menuju tuntas: <strong style="color: #0f172a;">{{ number_format($remainingProg, 1) }}%</strong></span>
                                <span>Target: 100% (Kolaboratif)</span>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Tanggal Pelaporan <span class="required">*</span></label>
                            <input type="date" name="tanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label class="form-label" style="margin-bottom: 0;">Tambahan Progres Anda (+%) <span class="required">*</span></label>
                                @if($remainingProg > 0)
                                    <button type="button" onclick="setProgresToShowRemaining()" style="background: none; border: none; color: #2563eb; font-size: 11.5px; font-weight: 700; cursor: pointer; text-decoration: underline;">
                                        ⚡ Selesaikan Sisa ({{ number_format($remainingProg, 1) }}%)
                                    </button>
                                @endif
                            </div>
                            <input type="number" id="inputProgresShow" name="progres_capaian" class="form-input" min="0.1" max="{{ $remainingProg }}" step="0.1" placeholder="Misal: 20 (artinya menambah +20% ke progres saat ini)" required>

                            <!-- Quick Preset Pills -->
                            @if($remainingProg > 0)
                                <div style="display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; align-items: center;">
                                    <span style="font-size: 11px; color: #64748b; font-weight: 600;">Pilih Cepat:</span>
                                    @foreach([5, 10, 15, 20, 25, 50] as $preset)
                                        @if($preset <= $remainingProg)
                                            <button type="button" onclick="setPresetToShow({{ $preset }})" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 8px; font-size: 11.5px; font-weight: 700; color: #334155; cursor: pointer;">
                                                +{{ $preset }}%
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <!-- Live Calculation Box -->
                            <div id="liveCalcBoxShow" style="margin-top: 10px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; color: #1e40af;">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                                    <div>
                                        <span>Saat Ini: <strong>{{ number_format($currentProg, 1) }}%</strong></span>
                                        <span style="margin: 0 4px;">+</span>
                                        <span>Tambahan: <strong id="previewAdditionShow" style="color: #059669;">+0.0%</strong></span>
                                    </div>
                                    <div style="font-size: 13px;">
                                        <span>Total Akumulasi: <strong id="previewTotalShow" style="font-size: 15px; color: #2563eb;">{{ number_format($currentProg, 1) }}%</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div id="noticeSelesaiShow" style="display: none; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 8px; padding: 10px 14px; margin-top: 10px; font-size: 12px; color: #7e22ce;">
                                <i class="ph-bold ph-seal-check"></i> <strong>Akumulasi Mencapai 100%!</strong> Pekerjaan akan otomatis diajukan untuk Verifikasi Hasil Akhir oleh Katim/Pimpinan.
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Uraian Kemajuan / Realisasi <span class="required">*</span></label>
                            <textarea id="deskripsiProgresShow" name="deskripsi_progres" class="form-textarea" rows="3" placeholder="Jelaskan kontribusi atau kemajuan pekerjaan yang telah diselesaikan..." required></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Kendala (Bila ada)</label>
                            <textarea name="kendala" class="form-textarea" rows="2" placeholder="Tuliskan kendala teknis atau lapangan jika ditemui..."></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Rencana Tindak Lanjut / Solusi</label>
                            <textarea name="tindak_lanjut" class="form-textarea" rows="2" placeholder="Langkah mitigasi atau aksi berikutnya..."></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Unggah Berkas Bukti Dukung (PDF/Gambar/Zip)</label>
                            <input type="file" name="file_bukti" class="form-input" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx,.zip">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Atau Tautan Drive / Cloud Bukti</label>
                            <input type="url" name="link_bukti" class="form-input" placeholder="https://drive.google.com/...">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-action-outline" onclick="closeModal('modalUpdateProgres')">Batal</button>
                        <button type="submit" id="btnSubmitShowProgres" class="btn-action-primary" style="background: #10b981;">Simpan Realisasi</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Tolak Laporan Progres (Pembuat Tugas / Katim) -->
    <div id="modalTolakProgres" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'modalTolakProgres')">
        <div class="modal-card">
            <form id="formTolakProgres" method="POST">
                @csrf
                <div class="modal-header">
                    <div class="modal-title" style="color: #dc2626;">
                        <i class="ph-bold ph-x-circle"></i> Tolak Laporan Progres Pegawai
                    </div>
                    <button type="button" onclick="closeModal('modalTolakProgres')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
                </div>

                <div class="modal-body">
                    <p style="font-size: 13px; color: #475569; margin-bottom: 14px;">
                        Tuliskan catatan evaluasi atau alasan penolakan agar pegawai pelaksana dapat memperbaiki laporan progresnya:
                    </p>

                    <div class="form-group">
                        <label class="form-label">Catatan / Alasan Penolakan <span class="required">*</span></label>
                        <textarea name="catatan" class="form-textarea" rows="3" placeholder="Contoh: Bukti dukung laporan belum lengkap, mohon lampirkan daftar hadir peserta..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action-outline" onclick="closeModal('modalTolakProgres')">Batal</button>
                    <button type="submit" class="btn-action-primary" style="background: #dc2626;">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Pekerjaan -->
    @if($canDelete)
        <div id="modalHapusPekerjaan" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'modalHapusPekerjaan')">
            <div class="modal-card" style="max-width: 480px;">
                <form action="{{ route('pekerjaan.destroy', $pekerjaan) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header" style="background: #fef2f2; border-bottom: 1px solid #fee2e2;">
                        <div class="modal-title" style="color: #991b1b;">
                            <i class="ph-bold ph-warning-octagon" style="color: #dc2626; font-size: 20px;"></i>
                            Konfirmasi Hapus Pekerjaan
                        </div>
                        <button type="button" onclick="closeModal('modalHapusPekerjaan')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
                    </div>

                    <div class="modal-body" style="padding: 22px 24px;">
                        @if($pekerjaan->children->isNotEmpty())
                            <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px;">
                                <div style="color: #9f1239; font-weight: 700; font-size: 13px;">
                                    <i class="ph-bold ph-prohibit"></i> Tidak Dapat Dihapus Langsung
                                </div>
                                <div style="font-size: 12.5px; color: #881337; margin-top: 4px;">
                                    Pekerjaan ini memiliki <strong>{{ $pekerjaan->children->count() }} subpekerjaan</strong> di bawahnya. Anda harus menghapus atau memindahkan subpekerjaan terlebih dahulu.
                                </div>
                            </div>
                        @else
                            <p style="font-size: 13.5px; color: #334155; line-height: 1.5; margin: 0 0 14px 0;">
                                Apakah Anda yakin ingin menghapus pekerjaan berikut?
                            </p>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                                <div style="font-family: monospace; font-size: 12px; font-weight: 700; color: #2563eb;">
                                    {{ $pekerjaan->kode_tampilan }}
                                </div>
                                <div style="font-weight: 800; color: #0f172a; font-size: 14px; margin-top: 2px;">
                                    {{ $pekerjaan->judul }}
                                </div>
                            </div>
                            <p style="font-size: 12px; color: #ef4444; margin: 0;">
                                ⚠️ Tindakan ini akan menghapus data penugasan, riwayat persetujuan, dan berkas terkait secara permanen dan tidak dapat dibatalkan.
                            </p>
                        @endif
                    </div>

                    <div class="modal-footer" style="padding: 14px 24px; background: #f8fafc;">
                        <button type="button" class="btn-action-outline" onclick="closeModal('modalHapusPekerjaan')">
                            Batal
                        </button>
                        @if($pekerjaan->children->isEmpty())
                            <button type="submit" class="btn-action-primary" style="background: #dc2626;">
                                <i class="ph-bold ph-trash"></i>
                                Ya, Hapus Sekarang
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function closeModalOnBackdrop(e, id) {
        if (e.target.id === id) {
            closeModal(id);
        }
    }

    function openModalTolakProgres(actionUrl) {
        const form = document.getElementById('formTolakProgres');
        if (form) {
            form.action = actionUrl;
        }
        openModal('modalTolakProgres');
    }

    const baseProgresShow = {{ (float) $pekerjaan->progres }};
    const maxRemainingShow = {{ max(0, round(100 - (float) $pekerjaan->progres, 1)) }};

    function updateLiveCalcShow(additionVal) {
        const val = parseFloat(additionVal) || 0;
        const total = Math.min(100, Math.round((baseProgresShow + val) * 10) / 10);
        
        const previewAddition = document.getElementById('previewAdditionShow');
        const previewTotal = document.getElementById('previewTotalShow');
        const noticeSelesai = document.getElementById('noticeSelesaiShow');
        const submitBtn = document.getElementById('btnSubmitShowProgres');

        if (previewAddition) previewAddition.textContent = `+${val.toFixed(1)}%`;
        if (previewTotal) previewTotal.textContent = `${total.toFixed(1)}%`;

        if (total >= 100) {
            if (noticeSelesai) noticeSelesai.style.display = 'block';
            if (submitBtn) {
                submitBtn.textContent = 'Selesaikan & Ajukan ke Katim';
                submitBtn.style.background = '#8b5cf6';
            }
        } else {
            if (noticeSelesai) noticeSelesai.style.display = 'none';
            if (submitBtn) {
                submitBtn.textContent = val > 0 ? `Simpan Realisasi (+${val.toFixed(1)}%)` : 'Simpan Realisasi';
                submitBtn.style.background = '#10b981';
            }
        }
    }

    function setPresetToShow(preset) {
        const input = document.getElementById('inputProgresShow');
        if (input) {
            input.value = preset;
            updateLiveCalcShow(preset);
        }
    }

    function setProgresToShowRemaining() {
        const input = document.getElementById('inputProgresShow');
        if (input) {
            input.value = maxRemainingShow;
            updateLiveCalcShow(maxRemainingShow);
        }
    }

    function openProgressModalInShow(isCompleting) {
        const input = document.getElementById('inputProgresShow');
        const desc = document.getElementById('deskripsiProgresShow');

        if (isCompleting) {
            if (input) input.value = maxRemainingShow;
            if (desc && !desc.value) {
                desc.value = 'Telah menyelesaikan seluruh sisa target penugasan bersama tim sesuai spesifikasi.';
            }
            updateLiveCalcShow(maxRemainingShow);
        } else {
            if (input && !input.value) {
                input.value = '';
            }
            updateLiveCalcShow(input ? input.value : 0);
        }

        if (input) {
            input.oninput = function() {
                updateLiveCalcShow(this.value);
            };
        }

        openModal('modalUpdateProgres');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('inputProgresShow');
        if (input) {
            input.addEventListener('input', function() {
                updateLiveCalcShow(this.value);
            });
        }
    });
</script>
@endsection
