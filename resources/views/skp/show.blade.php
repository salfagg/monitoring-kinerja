@extends('layouts.app')

@section('title', 'Rincian Sasaran Kinerja (SKP) | SIMON-K')
@section('page-title', 'Rincian Sasaran Kinerja Pegawai')

@section('styles')
<style>
    .detail-container {
        max-width: 960px;
        margin: 0 auto 50px auto;
    }

    .card-detail {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 28px 32px;
        margin-bottom: 24px;
    }

    .badge-status-skp {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 99px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-disetujui { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-diajukan { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
    .status-draft { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .status-revisi { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .info-item .info-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .info-item .info-val {
        font-size: 14.5px;
        font-weight: 800;
        color: #0f172a;
    }

    .btn-action-main {
        padding: 9px 18px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .data-table th {
        background: #f8fafc;
        padding: 12px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border);
    }

    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .progress-bar-wrap {
        width: 80px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
        margin-right: 8px;
        display: inline-block;
    }

    .progress-bar-fill {
        height: 100%;
        background: #10b981;
        border-radius: 99px;
    }
</style>
@endsection

@section('content')

<div class="detail-container">

    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div>
            <a href="{{ route('skp.index') }}" style="font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                <i class="ph-bold ph-arrow-left"></i> Kembali ke Daftar SKP
            </a>
            <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 6px;">
                Rincian Sasaran Kinerja Pegawai (SKP)
            </h2>
        </div>

        <div style="display: flex; gap: 8px;">
            @if($isManager && $skp->status === 'diajukan')
                <form action="{{ route('skp.approve', $skp) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-action-main" style="background: #10b981; color: white;">
                        <i class="ph-bold ph-check"></i> Setujui SKP
                    </button>
                </form>
            @endif

            <a href="{{ route('skp.edit', $skp) }}" class="btn-action-main" style="background: #f1f5f9; color: #334155; border: 1px solid var(--border);">
                <i class="ph-bold ph-pencil-simple"></i> Edit
            </a>

            @if(Auth::user()->canManagePekerjaan())
                <a href="{{ route('pekerjaan.create') }}" class="btn-action-main" style="background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%); color: white;">
                    <i class="ph-bold ph-plus-circle"></i> Buat Pekerjaan dari SKP
                </a>
            @endif
        </div>
    </div>

    <!-- 1. KARTU UTAMA SKP -->
    <div class="card-detail">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; border-radius: 99px; background: #0284c7; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex-shrink: 0;">
                    {{ strtoupper(substr($skp->user?->name ?? 'P', 0, 2)) }}
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">
                        {{ $skp->user?->name ?? '-' }}
                    </h3>
                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">
                        NIP: {{ $skp->user?->nip ?? '-' }} • Unit: <strong>{{ $skp->user?->pokja?->nama_pokja ?? 'BGTK DKI Jakarta' }}</strong>
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="badge-status-skp status-{{ $skp->status }}">
                    @if($skp->status === 'disetujui')
                        <i class="ph-bold ph-seal-check"></i>
                    @elseif($skp->status === 'diajukan')
                        <i class="ph-bold ph-clock"></i>
                    @else
                        <i class="ph-bold ph-pencil"></i>
                    @endif
                    Status: {{ ucfirst($skp->status) }}
                </span>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #0284c7; letter-spacing: 0.05em; margin-bottom: 6px;">
                Rencana Hasil Kerja (RHK) Resmi
            </div>
            <div style="font-size: 16px; font-weight: 800; color: #0c4a6e; line-height: 1.5; background: #f0f9ff; padding: 16px 20px; border-radius: 10px; border: 1px solid #bae6fd;">
                {{ $skp->rencana_kinerja }}
            </div>
        </div>

        @if($skp->indikator_kinerja)
            <div style="margin-top: 16px;">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 4px;">
                    Indikator Kinerja Individu (IKI)
                </div>
                <div style="font-size: 13.5px; color: #334155; line-height: 1.5; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    {{ $skp->indikator_kinerja }}
                </div>
            </div>
        @endif

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Tahun Periode</div>
                <div class="info-val">Tahun {{ $skp->tahun }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Aspek Penilaian</div>
                <div class="info-val">{{ $skp->aspek }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Target Sasaran</div>
                <div class="info-val" style="color: #0284c7;">
                    {{ (float)$skp->target_max }} {{ $skp->satuan }}
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Pekerjaan Terkait</div>
                <div class="info-val">{{ $skp->pekerjaan->count() }} Tugas Terhubung</div>
            </div>
        </div>
    </div>

    <!-- 2. DAFTAR PEKERJAAN YANG MERUJUK KE SKP INI -->
    <div class="card-detail">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">
                    Pekerjaan & Penugasan yang Ditautkan
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin: 2px 0 0 0;">
                    Daftar pekerjaan aktif balai yang menjadikan butir SKP ini sebagai dasar rujukan target.
                </p>
            </div>
            <span style="font-size: 12px; background: #e0f2fe; color: #0284c7; padding: 3px 10px; border-radius: 99px; font-weight: 800;">
                {{ $skp->pekerjaan->count() }} Tugas
            </span>
        </div>

        @if($skp->pekerjaan->isNotEmpty())
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Judul Pekerjaan</th>
                            <th>PIC Utama</th>
                            <th>Tenggat</th>
                            <th>Progres</th>
                            <th>Status Pelaksanaan</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($skp->pekerjaan as $p)
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-size: 11.5px; font-weight: 800; background: #f1f5f9; padding: 2px 7px; border-radius: 4px;">
                                        {{ $p->kode_tampilan }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pekerjaan.show', $p) }}" style="font-weight: 700; color: #0f172a; text-decoration: none;">
                                        {{ $p->judul }}
                                    </a>
                                </td>
                                <td>
                                    <span style="font-size: 12.5px; font-weight: 600;">{{ $p->penanggungJawab->name }}</span>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: var(--text-muted);">{{ $p->tenggat->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <div class="progress-bar-wrap">
                                            <div class="progress-bar-fill" style="width: {{ $p->progres }}%;"></div>
                                        </div>
                                        <span style="font-size: 11.5px; font-family: monospace; font-weight: 700;">{{ (float)$p->progres }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 11px; padding: 2px 7px; border-radius: 4px; font-weight: 700; background: #f1f5f9; color: #475569;">
                                        {{ ucwords(str_replace('_', ' ', $p->status_pelaksanaan)) }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('pekerjaan.show', $p) }}" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none;">
                                        Buka &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 32px 20px; background: #f8fafc; border-radius: 10px; border: 1px dashed #cbd5e1;">
                <div style="font-size: 32px; color: #94a3b8; margin-bottom: 8px;">
                    <i class="ph-duotone ph-briefcase"></i>
                </div>
                <div style="font-size: 13.5px; font-weight: 700; color: #334155;">Belum Ada Pekerjaan yang Ditautkan</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Saat membuat penugasan baru di menu Pekerjaan, pilih opsi <em>"Merujuk ke SKP Pegawai"</em> dan pilih butir SKP ini.
                </div>
            </div>
        @endif
    </div>

</div>

@endsection

