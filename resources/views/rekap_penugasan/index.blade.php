@extends('layouts.app')

@section('title', 'Rekap Penugasan & Surat Tugas | TANJIDOR BGTK')
@section('page-title', 'Rekap Penugasan & Surat Tugas')

@section('styles')
<style>
    /* Hero Header */
    .rekap-hero {
        background: linear-gradient(135deg, #0b4a82 0%, #083a68 50%, #062646 100%);
        border-radius: var(--radius-lg);
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 8px 24px rgba(11, 74, 130, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.12);
        position: relative;
        overflow: hidden;
    }

    .rekap-hero::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(34, 184, 232, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .rekap-hero-info h1 {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rekap-hero-info p {
        font-size: 13.5px;
        color: rgba(255, 255, 255, 0.88);
        max-width: 680px;
        line-height: 1.5;
        margin: 0;
    }

    .btn-export-excel {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        transition: var(--transition);
        white-space: nowrap;
        cursor: pointer;
    }

    .btn-export-excel:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
        color: #ffffff;
    }

    /* KPI Cards */
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .kpi-stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .kpi-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-card);
        border-color: var(--primary);
    }

    .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .kpi-val {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1.1;
    }

    .kpi-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 4px;
    }

    /* Filter Toolbar */
    .filter-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: var(--shadow-sm);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr auto;
        gap: 12px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .filter-input, .filter-select {
        height: 40px;
        padding: 0 14px;
        border-radius: 9px;
        border: 1px solid var(--border-strong);
        background: var(--bg-sub);
        color: var(--text-dark);
        font-size: 13px;
        transition: var(--transition);
        outline: none;
        width: 100%;
    }

    .filter-input:focus, .filter-select:focus {
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .btn-filter-submit {
        background: var(--navy);
        color: #ffffff;
        border: none;
        height: 40px;
        padding: 0 18px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-filter-submit:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-filter-reset {
        background: var(--bg-sub);
        color: var(--text-muted);
        border: 1px solid var(--border-strong);
        height: 40px;
        padding: 0 14px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-filter-reset:hover {
        background: #e2e8f0;
        color: var(--text-dark);
    }

    /* Main Table Container */
    .table-container {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .table-header-bar {
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border);
        background: var(--bg-sub);
    }

    .table-header-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .table-custom thead th {
        background: var(--bg-sub);
        color: var(--text-dark);
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 2px solid var(--border);
        white-space: nowrap;
    }

    .table-custom tbody tr.user-row {
        border-bottom: 1px solid var(--border);
        transition: background-color 0.15s ease;
    }

    .table-custom tbody tr.user-row:hover {
        background-color: var(--bg-sub);
    }

    .table-custom tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: var(--text-body);
    }

    /* User Profile Pill */
    .user-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
        color: #ffffff;
        font-weight: 800;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(11, 74, 130, 0.2);
    }

    .user-name-text {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 13.5px;
        line-height: 1.3;
    }

    .user-meta-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 2px;
    }

    .badge-pns {
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        font-size: 10.5px;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #bae6fd;
    }

    .badge-tad {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 10.5px;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    /* Stat Badges */
    .badge-st-count {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 800;
        font-size: 12.5px;
        padding: 4px 11px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-tugas-count {
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        font-weight: 800;
        font-size: 13px;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .role-split-badge {
        font-size: 11.5px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .role-split-badge span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .status-summary-box {
        font-size: 11.5px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .mini-progress-track {
        width: 100px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 2px;
    }

    .mini-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #059669);
        border-radius: 4px;
    }

    /* Accordion Trigger Button */
    .btn-toggle-detail {
        background: var(--bg-sub);
        border: 1px solid var(--border-strong);
        color: var(--primary);
        font-weight: 700;
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
    }

    .btn-toggle-detail:hover {
        background: var(--primary-light);
        border-color: var(--primary);
    }

    .btn-toggle-detail.active {
        background: var(--navy);
        color: #ffffff;
        border-color: var(--navy);
    }

    .btn-toggle-detail.active i {
        transform: rotate(180deg);
    }

    /* Nested Detail Row */
    .detail-drawer-row {
        background: #f8fafc;
        display: none;
    }

    .detail-drawer-row.show {
        display: table-row;
    }

    .detail-drawer-content {
        padding: 16px 20px 20px 20px;
        border-left: 4px solid var(--primary);
        background: #ffffff;
        margin: 10px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border);
    }

    .detail-header-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-header-info h4 {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--navy);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Sub-Table Rincian Tugas */
    .sub-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .sub-table thead th {
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        padding: 8px 12px;
        text-align: left;
        border-bottom: 1px solid #cbd5e1;
    }

    .sub-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .sub-table tbody tr:hover {
        background: #f8fafc;
    }

    .badge-role-pj {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-role-anggota {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 4px;
    }

    .badge-st-num {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-no-st {
        color: #94a3b8;
        font-style: italic;
        font-size: 11px;
    }

    .badge-selesai {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-verifikasi {
        background: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-dikerjakan {
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-revisi {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-belum {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }

    /* Empty state */
    .empty-state-rekap {
        text-align: center;
        padding: 48px 20px;
        color: var(--text-muted);
    }

    .empty-state-rekap i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    @media (max-width: 992px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
        .rekap-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid" style="padding: 0 4px;">

    <!-- ================= HERO HEADER ================= -->
    <div class="rekap-hero">
        <div class="rekap-hero-info">
            <h1>
                <i class="ph-bold ph-table"></i>
                Rekap Penugasan &amp; Distribusi Surat Tugas (ST)
            </h1>
            <p>
                Monitoring beban kerja per personel, jumlah Surat Tugas resmi yang diemban, dan rincian pekerjaan aktif maupun selesai di lingkungan BGTK Provinsi DKI Jakarta.
            </p>
        </div>

        <div>
            <a href="{{ route('rekap-penugasan.export-excel', request()->all()) }}" class="btn-export-excel" title="Unduh seluruh data rekap dan rincian penugasan ke format file Excel (.xlsx)">
                <i class="ph-bold ph-file-xls"></i>
                <span>Tarik ke Excel (.xlsx)</span>
            </a>
        </div>
    </div>

    <!-- ================= KPI METRICS ROW ================= -->
    <div class="kpi-row">
        <div class="kpi-stat-card">
            <div class="kpi-icon-wrap" style="background: rgba(11, 74, 130, 0.1); color: var(--navy);">
                <i class="ph-bold ph-users"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_pegawai'] }}</div>
                <div class="kpi-label">Total Personel Terdata</div>
            </div>
        </div>

        <div class="kpi-stat-card">
            <div class="kpi-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="ph-bold ph-user-check"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_pegawai_bertugas'] }}</div>
                <div class="kpi-label">Pegawai Memiliki Penugasan</div>
            </div>
        </div>

        <div class="kpi-stat-card">
            <div class="kpi-icon-wrap" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="ph-bold ph-file-text"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_st_terbit'] }}</div>
                <div class="kpi-label">Surat Tugas (ST) Terbit</div>
            </div>
        </div>

        <div class="kpi-stat-card">
            <div class="kpi-icon-wrap" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                <i class="ph-bold ph-briefcase"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_penugasan_balai'] }}</div>
                <div class="kpi-label">Total Penugasan Balai</div>
            </div>
        </div>

        <div class="kpi-stat-card">
            <div class="kpi-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="ph-bold ph-gauge"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['rata_beban'] }}</div>
                <div class="kpi-label">Rata-rata Tugas / Orang</div>
            </div>
        </div>
    </div>

    <!-- ================= FILTER TOOLBAR ================= -->
    <div class="filter-card">
        <form method="GET" action="{{ route('rekap-penugasan.index') }}">
            <div class="filter-grid">
                <!-- Search Input -->
                <div class="filter-group">
                    <label class="filter-label" for="q">
                        <i class="ph-bold ph-magnifying-glass"></i>
                        Pencarian Pegawai / No. ST / Judul
                    </label>
                    <input type="text" name="q" id="q" class="filter-input"
                        placeholder="Ketik nama, NIP, jabatan, judul tugas, atau no ST..."
                        value="{{ $filters['q'] }}">
                </div>

                <!-- Filter Pokja -->
                <div class="filter-group">
                    <label class="filter-label" for="pokja_id">
                        <i class="ph-bold ph-tree-structure"></i>
                        Unit Kerja / Pokja
                    </label>
                    <select name="pokja_id" id="pokja_id" class="filter-select">
                        <option value="">-- Semua Unit &amp; Pokja --</option>
                        @foreach($pokjas as $pj)
                            <option value="{{ $pj->id }}" {{ (string)$filters['pokja_id'] === (string)$pj->id ? 'selected' : '' }}>
                                [{{ $pj->kode_pokja }}] {{ $pj->nama_pokja }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Kepegawaian -->
                <div class="filter-group">
                    <label class="filter-label" for="status_pegawai">
                        <i class="ph-bold ph-identification-badge"></i>
                        Status Kepegawaian
                    </label>
                    <select name="status_pegawai" id="status_pegawai" class="filter-select">
                        <option value="">-- Semua Status --</option>
                        <option value="pns" {{ $filters['status_pegawai'] === 'pns' ? 'selected' : '' }}>PNS</option>
                        <option value="tad" {{ $filters['status_pegawai'] === 'tad' ? 'selected' : '' }}>Tenaga Alih Daya</option>
                    </select>
                </div>

                <!-- Filter Status Penugasan -->
                <div class="filter-group">
                    <label class="filter-label" for="filter_tugas">
                        <i class="ph-bold ph-check-square-offset"></i>
                        Filter Beban Kerja
                    </label>
                    <select name="filter_tugas" id="filter_tugas" class="filter-select">
                        <option value="all" {{ $filters['filter_tugas'] === 'all' ? 'selected' : '' }}>Semua Personel</option>
                        <option value="ada_tugas" {{ $filters['filter_tugas'] === 'ada_tugas' ? 'selected' : '' }}>Hanya yang Memiliki Tugas</option>
                        <option value="tanpa_tugas" {{ $filters['filter_tugas'] === 'tanpa_tugas' ? 'selected' : '' }}>Belum Ada Penugasan</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-filter-submit" title="Terapkan Filter">
                        <i class="ph-bold ph-funnel"></i>
                        <span>Filter</span>
                    </button>
                    @if(!empty($filters['q']) || !empty($filters['pokja_id']) || !empty($filters['status_pegawai']) || $filters['filter_tugas'] !== 'all')
                        <a href="{{ route('rekap-penugasan.index') }}" class="btn-filter-reset" title="Reset Semua Filter">
                            <i class="ph-bold ph-arrow-counter-clockwise"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ================= MAIN REKAP TABLE ================= -->
    <div class="table-container">
        <div class="table-header-bar">
            <div class="table-header-title">
                <i class="ph-bold ph-users-three" style="color: var(--primary);"></i>
                <span>Daftar Personel &amp; Rekapitulasi Beban Penugasan ({{ count($pegawaiList) }} Pegawai)</span>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn-toggle-detail" onclick="expandAllDetails()" id="btnExpandAll" title="Buka rincian tugas seluruh pegawai sekaligus">
                    <i class="ph-bold ph-arrows-out-line-vertical"></i>
                    <span>Buka Semua Rincian</span>
                </button>
                <button type="button" class="btn-toggle-detail" onclick="collapseAllDetails()" id="btnCollapseAll" style="display: none;" title="Tutup seluruh rincian tugas">
                    <i class="ph-bold ph-arrows-in-line-vertical"></i>
                    <span>Tutup Semua Rincian</span>
                </button>
            </div>
        </div>

        @if(count($pegawaiList) > 0)
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 48px; text-align: center;">No</th>
                        <th>Pegawai &amp; Status</th>
                        <th>Jabatan &amp; Unit Kerja</th>
                        <th style="text-align: center;">Surat Tugas (ST)</th>
                        <th style="text-align: center;">Total Tugas</th>
                        <th>Peran (PIC / Anggota)</th>
                        <th>Capaian &amp; Progres</th>
                        <th style="text-align: center; width: 140px;">Rincian Tugas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawaiList as $index => $item)
                        @php
                            $isPns = (!empty($item['nip']) && $item['nip'] !== '-' && strtolower($item['jabatan']) !== 'tenaga alih daya');
                            $hasTasks = $item['total_tugas'] > 0;
                            $rowId = 'detailRow_' . $item['id'];
                            $btnId = 'btnToggle_' . $item['id'];
                        @endphp
                        <tr class="user-row" id="userRow_{{ $item['id'] }}">
                            <!-- No -->
                            <td style="text-align: center; font-weight: 700; color: var(--text-muted);">
                                {{ $loop->iteration }}
                            </td>

                            <!-- Profil Pegawai -->
                            <td>
                                <div class="user-profile-cell">
                                    <div class="user-avatar-circle">
                                        {{ strtoupper(substr($item['name'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="user-name-text">{{ $item['name'] }}</div>
                                        <div class="user-meta-sub">
                                            @if($isPns)
                                                <span class="badge-pns">PNS</span>
                                                <span>NIP: {{ $item['nip'] }}</span>
                                            @else
                                                <span class="badge-tad">Tenaga Alih Daya</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Jabatan & Pokja -->
                            <td>
                                <div style="font-weight: 600; color: var(--text-dark); font-size: 13px;">
                                    {{ $item['jabatan'] ?? '-' }}
                                </div>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                    <i class="ph-bold ph-shield"></i>
                                    {{ $item['pokja_nama'] }}
                                </div>
                            </td>

                            <!-- Jumlah ST -->
                            <td style="text-align: center;">
                                @if($item['total_st'] > 0)
                                    <span class="badge-st-count" title="{{ $item['total_st'] }} Surat Tugas resmi diterbitkan">
                                        <i class="ph-bold ph-file-text"></i>
                                        {{ $item['total_st'] }} ST
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 12px;">-</span>
                                @endif
                            </td>

                            <!-- Total Tugas -->
                            <td style="text-align: center;">
                                @if($hasTasks)
                                    <span class="badge-tugas-count" title="{{ $item['total_tugas'] }} penugasan pekerjaan">
                                        <i class="ph-bold ph-briefcase"></i>
                                        {{ $item['total_tugas'] }} Tugas
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 12px; font-style: italic;">0 tugas</span>
                                @endif
                            </td>

                            <!-- Breakdown Peran -->
                            <td>
                                <div class="role-split-badge">
                                    <span style="color: #b45309; font-weight: 700;">
                                        <i class="ph-bold ph-user-circle"></i> PIC: {{ $item['total_pj'] }}
                                    </span>
                                    <span style="color: #475569;">
                                        <i class="ph-bold ph-users"></i> Anggota: {{ $item['total_anggota'] }}
                                    </span>
                                </div>
                            </td>

                            <!-- Capaian & Progres -->
                            <td>
                                <div class="status-summary-box">
                                    <div>
                                        <span style="color: #15803d; font-weight: 700;">{{ $item['tugas_selesai'] }} Selesai</span>
                                        <span style="color: #94a3b8;">/</span>
                                        <span style="color: #0284c7; font-weight: 600;">{{ $item['tugas_berjalan'] }} Berjalan</span>
                                    </div>
                                    @if($hasTasks)
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <div class="mini-progress-track">
                                                <div class="mini-progress-fill" style="width: {{ min(100, $item['avg_progres']) }}%;"></div>
                                            </div>
                                            <span style="font-size: 11px; font-weight: 700; color: var(--text-dark);">
                                                {{ round($item['avg_progres'], 1) }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi Toggle Accordion -->
                            <td style="text-align: center;">
                                <button type="button" class="btn-toggle-detail" id="{{ $btnId }}"
                                    onclick="toggleUserDetail('{{ $item['id'] }}')"
                                    title="Tampilkan / sembunyikan rincian ST dan pekerjaan">
                                    <span>Rincian ({{ $item['total_tugas'] }})</span>
                                    <i class="ph-bold ph-caret-down"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Sub-Drawer Accordion Baris Rincian Tugas -->
                        <tr class="detail-drawer-row" id="{{ $rowId }}">
                            <td colspan="8" style="padding: 0;">
                                <div class="detail-drawer-content">
                                    <div class="detail-header-info">
                                        <h4>
                                            <i class="ph-bold ph-list-dashes"></i>
                                            Daftar Surat Tugas &amp; Pekerjaan: {{ $item['name'] }}
                                        </h4>
                                        <div style="font-size: 12px; color: var(--text-muted);">
                                            Total: <strong>{{ $item['total_tugas'] }} Pekerjaan</strong> |
                                            ST Unik: <strong>{{ $item['total_st'] }} Surat Tugas</strong>
                                        </div>
                                    </div>

                                    @if($hasTasks)
                                    <div class="table-responsive">
                                        <table class="sub-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 36px; text-align: center;">No</th>
                                                    <th style="width: 170px;">Surat Tugas (ST)</th>
                                                    <th>Pekerjaan / Tugas</th>
                                                    <th>Unit / Pokja</th>
                                                    <th style="text-align: center;">Peran</th>
                                                    <th style="text-align: center;">Tenggat</th>
                                                    <th style="text-align: center; width: 100px;">Progres</th>
                                                    <th style="text-align: center;">Status</th>
                                                    <th style="text-align: center; width: 80px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item['daftar_tugas'] as $tIndex => $tgs)
                                                <tr>
                                                    <td style="text-align: center; color: #94a3b8; font-weight: 600;">
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td>
                                                        @if(!empty($tgs['nomor_st']))
                                                            <div class="badge-st-num">
                                                                <i class="ph-bold ph-file-text"></i>
                                                                {{ $tgs['nomor_st'] }}
                                                            </div>
                                                            @if(!empty($tgs['tanggal_st']))
                                                                <div style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                                                                    Tgl: {{ \Carbon\Carbon::parse($tgs['tanggal_st'])->format('d M Y') }}
                                                                </div>
                                                            @endif
                                                        @else
                                                            <span class="badge-no-st">Penugasan Langsung</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div style="font-weight: 700; color: var(--text-dark); font-size: 12.5px;">
                                                            {{ $tgs['judul'] }}
                                                        </div>
                                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                                            Kode: <strong style="color: var(--primary);">{{ $tgs['kode_tampilan'] }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span style="font-weight: 600; color: #334155;">{{ $tgs['pokja_nama'] }}</span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        @if($tgs['is_pj'])
                                                            <span class="badge-role-pj">
                                                                <i class="ph-bold ph-shield-star"></i> PIC / PJ
                                                            </span>
                                                        @else
                                                            <span class="badge-role-anggota">
                                                                Anggota Tim
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center; font-size: 11.5px; color: #475569;">
                                                        {{ !empty($tgs['tenggat']) ? \Carbon\Carbon::parse($tgs['tenggat'])->format('d/m/Y') : '-' }}
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                                            <div class="mini-progress-track" style="width: 55px;">
                                                                <div class="mini-progress-fill" style="width: {{ min(100, $tgs['progres']) }}%;"></div>
                                                            </div>
                                                            <span style="font-weight: 700; font-size: 11px;">{{ round($tgs['progres']) }}%</span>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="{{ $tgs['status_badge_class'] }}">
                                                            {{ $tgs['status_label'] }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <a href="{{ route('pekerjaan.show', $tgs['id']) }}" class="btn-toggle-detail"
                                                            style="padding: 3px 8px; font-size: 11px; text-decoration: none;"
                                                            title="Buka rincian lengkap tugas di Daftar Pekerjaan">
                                                            <i class="ph-bold ph-arrow-square-out"></i>
                                                            <span>Buka</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div style="text-align: center; padding: 18px; color: #94a3b8; font-size: 12.5px; font-style: italic;">
                                        <i class="ph-bold ph-info" style="font-size: 18px; vertical-align: middle; margin-right: 4px;"></i>
                                        Pegawai ini belum memiliki penugasan atau Surat Tugas (ST) aktif saat ini.
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state-rekap">
            <i class="ph-bold ph-users"></i>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Tidak Ada Data Pegawai Ditemukan</h3>
            <p style="font-size: 13px; margin: 0;">Silakan sesuaikan kata kunci pencarian atau ubah filter unit kerja yang Anda gunakan.</p>
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    function toggleUserDetail(userId) {
        const row = document.getElementById('detailRow_' + userId);
        const btn = document.getElementById('btnToggle_' + userId);
        if (!row || !btn) return;

        if (row.classList.contains('show')) {
            row.classList.remove('show');
            btn.classList.remove('active');
        } else {
            row.classList.add('show');
            btn.classList.add('active');
        }
    }

    function expandAllDetails() {
        document.querySelectorAll('.detail-drawer-row').forEach(row => row.classList.add('show'));
        document.querySelectorAll('.btn-toggle-detail').forEach(btn => btn.classList.add('active'));
        document.getElementById('btnExpandAll').style.display = 'none';
        document.getElementById('btnCollapseAll').style.display = 'inline-flex';
    }

    function collapseAllDetails() {
        document.querySelectorAll('.detail-drawer-row').forEach(row => row.classList.remove('show'));
        document.querySelectorAll('.btn-toggle-detail').forEach(btn => btn.classList.remove('active'));
        document.getElementById('btnExpandAll').style.display = 'inline-flex';
        document.getElementById('btnCollapseAll').style.display = 'none';
    }
</script>
@endsection
