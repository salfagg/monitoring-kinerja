@extends('layouts.app')

@section('title', 'Kelola Pokja & Pilar Subbag Umum | SIMON-K')
@section('page-title', 'Kelola Master Pokja & Struktur Unit Kerja')

@section('styles')
<style>
    /* Page Header */
    .pokja-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .pokja-page-header h2 {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-dark, #0f172a);
        margin: 0 0 4px 0;
    }

    .pokja-page-header p {
        font-size: 13px;
        color: var(--text-muted, #64748b);
        margin: 0;
    }

    /* KPI Summary Cards Strip */
    .pokja-kpi-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .pokja-kpi-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 14px 18px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s ease;
    }

    .pokja-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-card);
    }

    .pokja-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .pokja-kpi-content h4 {
        font-size: 20px;
        font-weight: 800;
        margin: 0;
        line-height: 1.1;
        color: var(--text-dark, #0f172a);
    }

    .pokja-kpi-content p {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted, #64748b);
        margin: 2px 0 0 0;
    }

    /* Toolbar: Tabs & Live Search */
    .pokja-toolbar {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 12px 18px;
        margin-bottom: 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: var(--shadow-sm);
    }

    .pokja-tabs-wrapper {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .pokja-tab-btn {
        background: var(--bg-hover, #f1f5f9);
        color: var(--text-muted, #64748b);
        border: 1px solid var(--border);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.18s ease;
        white-space: nowrap;
    }

    .pokja-tab-btn:hover {
        background: #e2e8f0;
        color: var(--primary);
    }

    .pokja-tab-btn.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(10, 111, 181, 0.28);
    }

    .pokja-tab-count {
        background: rgba(0, 0, 0, 0.08);
        padding: 1px 6px;
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 800;
    }

    .pokja-tab-btn.active .pokja-tab-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    .pokja-search-box {
        position: relative;
        min-width: 250px;
    }

    .pokja-search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: var(--text-muted, #94a3b8);
        pointer-events: none;
    }

    .pokja-search-input {
        width: 100%;
        padding: 7px 12px 7px 34px;
        font-size: 12.5px;
        border: 1px solid var(--border);
        border-radius: 20px;
        background: #f8fafc;
        outline: none;
        transition: all 0.18s ease;
    }

    .pokja-search-input:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(10, 111, 181, 0.12);
    }

    /* Subbag Pilar Info Banner */
    .pilar-banner {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.08), rgba(59, 130, 246, 0.04));
        border: 1px solid rgba(14, 165, 233, 0.22);
        border-radius: var(--radius-lg);
        padding: 14px 18px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .pilar-banner-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .pilar-banner h4 {
        font-size: 13.5px;
        font-weight: 800;
        color: #0369a1;
        margin: 0 0 2px 0;
    }

    .pilar-banner p {
        font-size: 12px;
        color: #475569;
        margin: 0;
    }

    /* Pokja Grid & Cards */
    .pokja-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (max-width: 640px) {
        .pokja-grid {
            grid-template-columns: 1fr;
        }
    }

    .pokja-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
    }

    .pokja-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        border-color: rgba(10, 111, 181, 0.35);
    }

    /* Top Accent Line based on type */
    .pokja-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #94a3b8;
    }

    .type-timker::before  { background: #2563eb; }
    .type-subbag::before  { background: #6366f1; }
    .type-pilar::before   { background: #0284c7; }
    .type-adhoc::before   { background: #059669; }

    /* Card Header */
    .pokja-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        gap: 8px;
    }

    .pokja-code-badge {
        font-family: monospace;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.04em;
        padding: 3px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #334155;
    }

    .type-timker .pokja-code-badge { background: #eff6ff; color: #1d4ed8; }
    .type-subbag .pokja-code-badge { background: #eef2ff; color: #4338ca; }
    .type-pilar  .pokja-code-badge { background: #f0f9ff; color: #0369a1; }
    .type-adhoc  .pokja-code-badge { background: #ecfdf5; color: #047857; }

    .pokja-type-pill {
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f8fafc;
        color: #64748b;
        border: 1px solid var(--border);
    }

    .type-timker .pokja-type-pill { background: rgba(37,99,235,0.08); color: #1d4ed8; border-color: rgba(37,99,235,0.2); }
    .type-subbag .pokja-type-pill { background: rgba(99,102,241,0.08); color: #4338ca; border-color: rgba(99,102,241,0.2); }
    .type-pilar  .pokja-type-pill { background: rgba(2,132,199,0.08);  color: #0369a1; border-color: rgba(2,132,199,0.2); }
    .type-adhoc  .pokja-type-pill { background: rgba(5,150,105,0.08);  color: #047857; border-color: rgba(5,150,105,0.2); }

    /* Card Titles */
    .pokja-card-title {
        font-size: 15.5px;
        font-weight: 800;
        color: var(--text-dark, #0f172a);
        line-height: 1.35;
        margin: 0 0 6px 0;
    }

    .pokja-card-desc {
        font-size: 12.5px;
        color: var(--text-muted, #64748b);
        line-height: 1.5;
        margin: 0 0 14px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Subbag Sub-pillars tags */
    .subbag-pilar-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 14px;
    }

    .subbag-pilar-chip {
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* Leader Profile Card */
    .pokja-leader-box {
        background: var(--bg-hover, #f8fafc);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 9px 12px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .leader-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        color: #ffffff;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #64748b;
    }

    .type-timker .leader-avatar { background: #2563eb; }
    .type-subbag .leader-avatar { background: #6366f1; }
    .type-pilar  .leader-avatar { background: #0284c7; }
    .type-adhoc  .leader-avatar { background: #059669; }

    .leader-info {
        min-width: 0;
        flex: 1;
    }

    .leader-role-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-muted, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        line-height: 1;
        margin-bottom: 2px;
    }

    .leader-name {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-dark, #0f172a);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Team & Progress Snapshot Strip */
    .pokja-team-snapshot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-top: 10px;
        border-top: 1px dashed var(--border);
        font-size: 11.5px;
        color: var(--text-muted, #64748b);
    }

    .avatar-stack {
        display: flex;
        align-items: center;
    }

    .avatar-stack-item {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #cbd5e1;
        color: #1e293b;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        margin-left: -6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .avatar-stack-item:first-child {
        margin-left: 0;
    }

    .avatar-stack-more {
        font-size: 10px;
        font-weight: 800;
        color: var(--primary);
        margin-left: 6px;
    }

    .pokja-task-count {
        font-family: monospace;
        font-size: 11px;
        font-weight: 800;
        color: #059669;
        background: #ecfdf5;
        padding: 2px 7px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    /* Card Footer Actions */
    .pokja-card-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        border-top: 1px solid var(--border);
        padding-top: 12px;
        margin-top: auto;
    }

    .btn-card-detail {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--primary);
        background: rgba(10, 111, 181, 0.08);
        border: 1px solid rgba(10, 111, 181, 0.2);
        border-radius: 7px;
        padding: 6px 11px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }

    .btn-card-detail:hover {
        background: var(--primary);
        color: #ffffff;
    }

    .btn-card-personil {
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        background: var(--bg-hover, #f8fafc);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 6px 11px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }

    .btn-card-personil:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-action-icon {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text-muted, #64748b);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-action-icon:hover {
        background: #f8fafc;
        color: var(--text-dark, #0f172a);
    }

    .btn-action-icon.btn-edit:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .btn-action-icon.btn-delete:hover {
        background: #fef2f2;
        color: #ef4444;
        border-color: #fecaca;
    }

    /* =========================================================
       MODAL STYLES (DETAIL & MANAGE MEMBERS)
       ========================================================= */
    .modal-backdrop {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 16px;
    }

    .modal-backdrop.is-open {
        display: flex;
    }

    .modal-card {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 620px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 35px -5px rgba(0, 0, 0, 0.25);
        animation: modalScale 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
        border: 1px solid var(--border);
    }

    @keyframes modalScale {
        from { transform: scale(0.93) translateY(12px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }

    .modal-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(to right, rgba(10, 111, 181, 0.04), transparent);
    }

    .modal-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: var(--text-dark, #0f172a);
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #ffffff;
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.15s;
    }

    .modal-close:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fca5a5;
    }

    .modal-body {
        padding: 20px 22px;
        overflow-y: auto;
        flex: 1;
    }

    .modal-footer {
        padding: 12px 22px;
        border-top: 1px solid var(--border);
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }

    /* Detail Modal Specific Styling */
    .detail-leader-banner {
        background: var(--bg-hover, #f8fafc);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
    }

    .detail-leader-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        font-size: 18px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .detail-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }

    .detail-stat-box {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 14px;
        text-align: center;
    }

    .detail-stat-box strong {
        display: block;
        font-size: 18px;
        font-weight: 900;
        color: var(--primary);
    }

    .detail-stat-box span {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted, #64748b);
    }

    .detail-member-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #ffffff;
        margin-bottom: 6px;
    }

    .detail-member-row.is-leader {
        background: rgba(37, 99, 235, 0.04);
        border-color: rgba(37, 99, 235, 0.3);
    }

    /* Checklist Selection in Manage Members Modal */
    .user-search-input {
        width: 100%;
        padding: 9px 12px 9px 34px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 12px;
        background: #ffffff;
        outline: none;
    }

    .user-search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(10, 111, 181, 0.12);
    }

    .user-checklist-container {
        display: flex;
        flex-direction: column;
        gap: 6px;
        max-height: 360px;
        overflow-y: auto;
    }

    .user-check-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #ffffff;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .user-check-item:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .user-check-item.leader-item {
        background: rgba(37, 99, 235, 0.04);
        border-color: rgba(37, 99, 235, 0.25);
    }

    .user-check-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .pokja-kpi-card,
    [data-theme="dark"] .pokja-toolbar,
    [data-theme="dark"] .pokja-card,
    [data-theme="dark"] .modal-card,
    [data-theme="dark"] .detail-member-row,
    [data-theme="dark"] .user-check-item {
        background: #151e32 !important;
        border-color: #223049 !important;
    }

    [data-theme="dark"] .pokja-card-title,
    [data-theme="dark"] .modal-header h3,
    [data-theme="dark"] .leader-name {
        color: #f8fafc !important;
    }

    [data-theme="dark"] .pokja-leader-box,
    [data-theme="dark"] .detail-leader-banner,
    [data-theme="dark"] .modal-footer {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .pokja-search-input,
    [data-theme="dark"] .user-search-input {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    [data-theme="dark"] .pokja-tab-btn {
        background: #151e32;
        color: #94a3b8;
        border-color: #223049;
    }

    [data-theme="dark"] .pokja-tab-btn.active {
        background: #2563eb !important;
        color: #ffffff !important;
    }

    [data-theme="dark"] .btn-action-icon,
    [data-theme="dark"] .modal-close {
        background: #1e293b;
        border-color: #334155;
        color: #cbd5e1;
    }
</style>
@endsection

@section('content')

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: #065f46; font-size: 13.5px; font-weight: 600;">
            <i class="ph-bold ph-check-circle" style="font-size: 20px; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Page Header -->
    <div class="pokja-page-header">
        <div>
            <h2>Master Kelompok Kerja (Pokja) &amp; Pilar Subbag Umum</h2>
            <p>Kelola struktur tim kerja teknis, 6 pilar operasional tata usaha, satuan tugas ad hoc RBI, dan pembagian personil.</p>
        </div>
        <a href="{{ route('pokjas.create') }}" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; padding: 9px 18px; border-radius: 8px;">
            <i class="ph-bold ph-plus-circle" style="font-size: 16px;"></i>
            <span>Tambah Pokja / Pilar Baru</span>
        </a>
    </div>

    @php
        $timKerjaCount = $pokjas->where('tipe', 'tim_kerja')->count();
        $pilarSubbagCount = $pokjas->where('tipe', 'subbag_pilar')->count();
        $kelompokKerjaCount = $pokjas->where('tipe', 'kelompok_kerja')->count();
        $totalPersonilTerdaftar = $pokjas->pluck('anggota')->flatten()->unique('id')->count();
    @endphp

    <!-- 2. KPI Summary Cards Strip -->
    <div class="pokja-kpi-strip">
        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #e8f2fb; color: #0a6fb5;">
                <i class="ph-duotone ph-tree-structure"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $pokjas->count() }}</h4>
                <p>Total Unit &amp; Pilar</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="ph-duotone ph-buildings"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $timKerjaCount }}</h4>
                <p>Tim Kerja &amp; TU Balai</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #f0f9ff; color: #0284c7;">
                <i class="ph-duotone ph-gear-six"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $pilarSubbagCount }}</h4>
                <p>6 Pilar Subbag Umum</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #ecfdf5; color: #059669;">
                <i class="ph-duotone ph-lightning"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $kelompokKerjaCount }}</h4>
                <p>Satgas Ad Hoc (Pokja RBI)</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #efe8fa; color: #6f45bd;">
                <i class="ph-duotone ph-users-three"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $totalPersonilTerdaftar }}</h4>
                <p>Pegawai Ditugaskan</p>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar: Filter Tabs & Live Instant Search -->
    <div class="pokja-toolbar">
        <div class="pokja-tabs-wrapper">
            <button type="button" class="pokja-tab-btn active" onclick="switchPokjaTab('all', this)">
                <i class="ph-bold ph-squares-four"></i>
                <span>Semua Unit &amp; Pilar</span>
                <span class="pokja-tab-count">{{ $pokjas->count() }}</span>
            </button>
            <button type="button" class="pokja-tab-btn" onclick="switchPokjaTab('tim_kerja', this)">
                <i class="ph-bold ph-buildings"></i>
                <span>Tim Kerja &amp; TU Balai</span>
                <span class="pokja-tab-count">{{ $timKerjaCount }}</span>
            </button>
            <button type="button" class="pokja-tab-btn" onclick="switchPokjaTab('subbag_pilar', this)">
                <i class="ph-bold ph-gear-six"></i>
                <span>6 Pilar Operasional Subbag Umum</span>
                <span class="pokja-tab-count">{{ $pilarSubbagCount }}</span>
            </button>
            <button type="button" class="pokja-tab-btn" onclick="switchPokjaTab('kelompok_kerja', this)">
                <i class="ph-bold ph-lightning"></i>
                <span>Satgas Ad Hoc (Pokja RBI)</span>
                <span class="pokja-tab-count">{{ $kelompokKerjaCount }}</span>
            </button>
        </div>

        <!-- Live Search Input -->
        <div class="pokja-search-box">
            <i class="ph-bold ph-magnifying-glass"></i>
            <input type="text" id="pokjaLiveSearch" class="pokja-search-input" placeholder="Cari nama unit, kode, atau katim..." oninput="handlePokjaLiveSearch(this.value)">
        </div>
    </div>

    <!-- 4. Subbag Pilar Info Banner (Tampil jika tab Semua atau Subbag aktif) -->
    <div class="pilar-banner" id="pilarBanner">
        <div class="pilar-banner-icon">
            <i class="ph-bold ph-gear-fine"></i>
        </div>
        <div>
            <h4>6 Pilar Operasional Subbagian Umum (Tata Usaha &amp; Operasional)</h4>
            <p>Ruang lingkup kerja harian yang menjaga stabilitas, efisiensi administrasi, BMN, OSDM, keuangan, dan tata kelola BGTK Provinsi DKI Jakarta.</p>
        </div>
    </div>

    <!-- 5. Pokja Cards Grid -->
    <div class="pokja-grid" id="pokjaCardsGrid">
        @forelse($pokjas as $pokja)
            @php
                $isSubbag = ($pokja->kode_pokja === 'SUBBAG-UMUM');
                $isPilar = ($pokja->tipe === 'subbag_pilar');
                $isAdhoc = ($pokja->tipe === 'kelompok_kerja');

                $themeClass = $isSubbag ? 'type-subbag' : ($isPilar ? 'type-pilar' : ($isAdhoc ? 'type-adhoc' : 'type-timker'));
                $typeBadgeText = $isSubbag ? 'TU Balai' : ($isPilar ? 'Pilar Subbag' : ($isAdhoc ? 'Satgas RBI' : 'Tim Kerja'));
                $leaderRoleText = $isSubbag ? 'Kepala Subbagian (Kasubag)' : ($isPilar ? 'Koordinator / PIC Pilar' : ($isAdhoc ? 'Ketua Satgas' : 'Ketua Tim (Katim)'));
                $leaderName = $pokja->katim?->name ?? 'Belum Ditunjuk';

                $totalTugas = $pokja->pekerjaan_count ?? ($pokja->pekerjaan ? $pokja->pekerjaan->count() : 0);
                $avgProgres = ($totalTugas > 0 && $pokja->pekerjaan) ? round($pokja->pekerjaan->avg('progres'), 0) : 0;
            @endphp

            <div class="pokja-card {{ $themeClass }}" 
                 data-tipe="{{ $pokja->tipe ?? 'tim_kerja' }}"
                 data-search="{{ strtolower($pokja->nama_pokja . ' ' . $pokja->kode_pokja . ' ' . $leaderName) }}">
                
                <div>
                    <!-- Header Card: Kode & Type Badge -->
                    <div class="pokja-card-head">
                        <span class="pokja-code-badge">{{ $pokja->kode_pokja }}</span>
                        <span class="pokja-type-pill">
                            <i class="ph-bold {{ $isSubbag ? 'ph-shield-check' : ($isPilar ? 'ph-gear' : ($isAdhoc ? 'ph-sparkle' : 'ph-briefcase')) }}"></i>
                            <span>{{ $typeBadgeText }}</span>
                        </span>
                    </div>

                    <!-- Judul & Deskripsi -->
                    <h3 class="pokja-card-title">{{ $pokja->nama_pokja }}</h3>
                    <p class="pokja-card-desc">{{ $pokja->deskripsi ?? 'Belum ada deskripsi spesifik untuk ruang lingkup unit kerja ini.' }}</p>

                    <!-- Khusus Subbag Umum: Preview 6 Pilar Naungan -->
                    @if($isSubbag)
                        <div class="subbag-pilar-chips">
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-coins"></i> Keuangan</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-users"></i> OSDM</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-envelope"></i> Persuratan</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-desktop"></i> Sistem Info</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-archive"></i> BMN &amp; RT</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-shield"></i> K3</span>
                        </div>
                    @endif
                </div>

                <div>
                    <!-- Penanggung Jawab (Katim / Kasubag / PIC) Profile Box -->
                    <div class="pokja-leader-box">
                        <div class="leader-avatar">
                            {{ strtoupper(substr($leaderName, 0, 1)) }}
                        </div>
                        <div class="leader-info">
                            <div class="leader-role-label">{{ $leaderRoleText }}</div>
                            <div class="leader-name" title="{{ $leaderName }}">{{ $leaderName }}</div>
                        </div>
                    </div>

                    <!-- Team Snapshot & Tugas Count Strip -->
                    <div class="pokja-team-snapshot">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="avatar-stack">
                                @forelse($pokja->anggota->take(3) as $m)
                                    <div class="avatar-stack-item" title="{{ $m->name }}">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                @empty
                                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Kosong</span>
                                @endforelse
                            </div>
                            <span style="font-size: 11px; font-weight: 700; color: #475569;">
                                {{ $pokja->anggota->count() }} Pegawai
                            </span>
                        </div>

                        <div>
                            @if($totalTugas > 0)
                                <span class="pokja-task-count" title="Tugas aktif dan progres rata-rata unit">
                                    <i class="ph-bold ph-check"></i> {{ $totalTugas }} Tugas &bull; {{ $avgProgres }}%
                                </span>
                            @else
                                <span style="font-size: 11px; color: #94a3b8;">Belum ada tugas</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    <div class="pokja-card-foot">
                        <div style="display: flex; gap: 6px;">
                            <!-- Tombol Lihat Detail Proper -->
                            <button type="button" class="btn-card-detail" onclick="openDetailModal('{{ $pokja->id }}')" title="Buka Detail Lengkap Unit Kerja Ini">
                                <i class="ph-bold ph-info"></i>
                                <span>Detail</span>
                            </button>

                            <!-- Tombol Kelola Personil -->
                            <button type="button" class="btn-card-personil" onclick="openManageMembersModal('{{ $pokja->id }}')" title="Atur Personil Pegawai">
                                <i class="ph-bold ph-user-plus"></i>
                                <span>Personil</span>
                            </button>
                        </div>

                        <!-- Edit & Delete Icons -->
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('pokjas.edit', $pokja) }}" class="btn-action-icon btn-edit" title="Ubah Identitas Pokja">
                                <i class="ph-bold ph-pencil-simple"></i>
                            </a>

                            <form action="{{ route('pokjas.destroy', $pokja) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit / kelompok kerja &quot;{{ addslashes($pokja->nama_pokja) }}&quot;? Seluruh keanggotaan personil di unit ini akan dilepaskan secara aman.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-icon btn-delete" title="Hapus Pokja / Unit">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =========================================================================
                 MODAL 1: DETAIL LENGKAP POKJA (Sangat Proper, Elegan & Rapi)
                 ========================================================================= -->
            <div id="detailModal_{{ $pokja->id }}" class="modal-backdrop" onclick="closeDetailModalOnBackdrop(event, '{{ $pokja->id }}')">
                <div class="modal-card" style="max-width: 650px;" onclick="event.stopPropagation()">
                    <div class="modal-header">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="pokja-code-badge">{{ $pokja->kode_pokja }}</span>
                            <span class="pokja-type-pill">{{ $typeBadgeText }}</span>
                        </div>
                        <button type="button" class="modal-close" onclick="closeDetailModal('{{ $pokja->id }}')" title="Tutup">
                            <i class="ph-bold ph-x"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Judul & Deskripsi Unit -->
                        <div style="margin-bottom: 16px;">
                            <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">{{ $pokja->nama_pokja }}</h2>
                            <p style="font-size: 13px; color: #475569; line-height: 1.6; margin: 0;">
                                {{ $pokja->deskripsi ?? 'Belum ada deskripsi ruang lingkup yang dicatat untuk unit ini.' }}
                            </p>
                        </div>

                        <!-- Banner Penanggung Jawab -->
                        <div class="detail-leader-banner">
                            <div class="detail-leader-avatar">
                                {{ strtoupper(substr($leaderName, 0, 1)) }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">
                                    {{ $leaderRoleText }}
                                </span>
                                <strong style="font-size: 14.5px; color: #0f172a; display: block;">{{ $leaderName }}</strong>
                                <span style="font-size: 11.5px; color: #64748b;">
                                    NIP: {{ $pokja->katim?->nip ?? '-' }} &bull; {{ $pokja->katim?->jabatan ?? 'Pegawai' }}
                                </span>
                            </div>
                        </div>

                        <!-- 3-Col KPI Stats Unit -->
                        <div class="detail-stats-grid">
                            <div class="detail-stat-box">
                                <strong>{{ $pokja->anggota->count() }}</strong>
                                <span>Personil Anggota</span>
                            </div>
                            <div class="detail-stat-box">
                                <strong style="color: #059669;">{{ $totalTugas }}</strong>
                                <span>Tugas / Pekerjaan</span>
                            </div>
                            <div class="detail-stat-box">
                                <strong style="color: #d97706;">{{ $avgProgres }}%</strong>
                                <span>Rata-rata Capaian</span>
                            </div>
                        </div>

                        <!-- Daftar Anggota & Personil Lengkap -->
                        <div style="margin-top: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 12px; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <i class="ph-bold ph-users-three"></i> Daftar Seluruh Anggota ({{ $pokja->anggota->count() }} Orang)
                                </span>
                                <button type="button" onclick="closeDetailModal('{{ $pokja->id }}'); openManageMembersModal('{{ $pokja->id }}');" style="font-size: 11.5px; font-weight: 700; color: var(--primary); background: none; border: none; cursor: pointer;">
                                    <i class="ph-bold ph-pencil-simple"></i> Kelola Anggota
                                </button>
                            </div>

                            <div style="max-height: 240px; overflow-y: auto; padding-right: 4px;">
                                @forelse($pokja->anggota as $anggota)
                                    @php
                                        $isLeaderPerson = ($anggota->pivot->peran ?? '') === 'ketua' || $anggota->id === $pokja->katim_id;
                                    @endphp
                                    <div class="detail-member-row {{ $isLeaderPerson ? 'is-leader' : '' }}">
                                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                            <div style="width: 30px; height: 30px; border-radius: 50%; background: {{ $isLeaderPerson ? '#2563eb' : '#64748b' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; flex-shrink: 0;">
                                                {{ strtoupper(substr($anggota->name, 0, 1)) }}
                                            </div>
                                            <div style="min-width: 0;">
                                                <div style="font-size: 12.5px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $anggota->name }}
                                                </div>
                                                <div style="font-size: 11px; color: #64748b;">
                                                    NIP: {{ $anggota->nip ?? '-' }} &bull; {{ $anggota->jabatan ?? $anggota->role }}
                                                </div>
                                            </div>
                                        </div>
                                        <div style="flex-shrink: 0; margin-left: 8px;">
                                            @if($isLeaderPerson)
                                                <span style="font-size: 10.5px; font-weight: 700; padding: 2px 7px; border-radius: 4px; background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                                                    ⭐ {{ $leaderRoleText }}
                                                </span>
                                            @else
                                                <span style="font-size: 10.5px; font-weight: 600; padding: 2px 7px; border-radius: 4px; background: #f1f5f9; color: #475569;">
                                                    Anggota
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div style="text-align: center; padding: 20px; font-size: 12px; color: var(--text-muted); background: #f8fafc; border-radius: 8px; border: 1px dashed var(--border);">
                                        Belum ada personil yang terdaftar pada unit ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if($totalTugas > 0)
                            <a href="{{ route('pekerjaan.index', ['pokja_id' => $pokja->id]) }}" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none; margin-right: auto; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="ph-bold ph-list-bullets"></i>
                                Lihat Daftar Pekerjaan Unit Ini &rarr;
                            </a>
                        @endif
                        <a href="{{ route('pokjas.edit', $pokja) }}" class="btn-card-personil" style="text-decoration: none; font-size: 12px; padding: 7px 14px;">
                            <i class="ph-bold ph-pencil-simple"></i> Ubah Pokja
                        </a>
                        <button type="button" class="btn-primary" onclick="closeDetailModal('{{ $pokja->id }}')" style="font-size: 12px; padding: 7px 16px; border-radius: 8px;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <!-- =========================================================================
                 MODAL 2: KELOLA PERSONIL & ANGGOTA (Fleksibel Checklist BGTK)
                 ========================================================================= -->
            <div id="manageMembersModal_{{ $pokja->id }}" class="modal-backdrop" onclick="closeManageMembersModalOnBackdrop(event, '{{ $pokja->id }}')">
                <div class="modal-card" style="max-width: 600px;" onclick="event.stopPropagation()">
                    <form action="{{ route('pokjas.sync-anggota', $pokja) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <div>
                                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 2px 0;">
                                    Kelola Personil: {{ $pokja->nama_pokja }}
                                </h3>
                                <span style="font-size: 11.5px; color: #64748b;">
                                    Pilih pegawai dari seluruh staf BGTK (Fleksibel Multi-Kelompok)
                                </span>
                            </div>
                            <button type="button" class="modal-close" onclick="closeManageMembersModal('{{ $pokja->id }}')">
                                <i class="ph-bold ph-x"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <!-- Live search personil input -->
                            <div style="position: relative;">
                                <i class="ph-bold ph-magnifying-glass" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                                <input type="text" 
                                       class="user-search-input" 
                                       placeholder="Cari nama pegawai atau NIP staf..." 
                                       oninput="filterUserList(this, 'userList_{{ $pokja->id }}')">
                            </div>

                            @php
                                $existingMemberIds = $pokja->anggota->pluck('id')->toArray();
                            @endphp

                            <div class="user-checklist-container" id="userList_{{ $pokja->id }}">
                                @foreach($allUsers as $u)
                                    @php
                                        $isChecked = in_array($u->id, $existingMemberIds);
                                        $isLeader = ($u->id == $pokja->katim_id);
                                    @endphp
                                    <label class="user-check-item {{ $isLeader ? 'leader-item' : '' }}" data-search="{{ strtolower($u->name . ' ' . $u->nip) }}">
                                        <input type="checkbox" 
                                               name="anggota_ids[]" 
                                               value="{{ $u->id }}" 
                                               {{ $isChecked || $isLeader ? 'checked' : '' }}
                                               {{ $isLeader ? 'disabled' : '' }}>
                                        @if($isLeader)
                                            <input type="hidden" name="anggota_ids[]" value="{{ $u->id }}">
                                        @endif
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-size: 12.5px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                                                <span>{{ $u->name }}</span>
                                                @if($isLeader)
                                                    <span style="font-size: 10px; color: #2563eb; background: #eff6ff; padding: 1px 6px; border-radius: 4px; font-weight: 800;">
                                                        ⭐ {{ $isPilar ? 'PIC Pilar' : 'Ketua' }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                                NIP: {{ $u->nip ?? '-' }} &bull; {{ $u->jabatan ?? $u->role }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-card-personil" onclick="closeManageMembersModal('{{ $pokja->id }}')" style="font-size: 12px; padding: 7px 14px;">
                                Batal
                            </button>
                            <button type="submit" class="btn-primary" style="font-size: 12px; padding: 7px 16px; border-radius: 8px;">
                                <i class="ph-bold ph-floppy-disk"></i>
                                <span>Simpan Perubahan Personil</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="ph-duotone ph-tree-structure" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
                <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">Belum Ada Pokja / Unit</h4>
                <p style="font-size: 13px; margin: 0;">Silakan tambahkan pokja atau unit kerja baru menggunakan tombol di atas.</p>
            </div>
        @endforelse
    </div>

    <!-- Empty State saat filter search tidak ada hasil -->
    <div id="pokjaSearchEmptyState" style="display: none; text-align: center; padding: 40px; color: var(--text-muted); background: white; border: 1px dashed var(--border); border-radius: 12px; margin-bottom: 30px;">
        <i class="ph-duotone ph-magnifying-glass" style="font-size: 40px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
        <h4 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">Tidak Ada Unit Kerja yang Cocok</h4>
        <p style="font-size: 12.5px; margin: 0;">Coba gunakan kata kunci pencarian nama atau kode pokja yang berbeda.</p>
    </div>

@endsection

@section('scripts')
<script>
    let currentTabType = 'all';

    // Tab Filter Functionality
    function switchPokjaTab(type, btn) {
        currentTabType = type;
        document.querySelectorAll('.pokja-tab-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const banner = document.getElementById('pilarBanner');
        if (banner) {
            banner.style.display = (type === 'subbag_pilar' || type === 'all') ? 'flex' : 'none';
        }

        applyPokjaFilters();
    }

    // Live Instant Search
    function handlePokjaLiveSearch(query) {
        applyPokjaFilters();
    }

    function applyPokjaFilters() {
        const searchInput = document.getElementById('pokjaLiveSearch');
        const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const cards = document.querySelectorAll('.pokja-card');
        const emptyState = document.getElementById('pokjaSearchEmptyState');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardType = card.getAttribute('data-tipe');
            const searchKey = card.getAttribute('data-search') || '';

            const matchTab = (currentTabType === 'all') || (cardType === currentTabType);
            const matchQuery = !q || searchKey.includes(q);

            if (matchTab && matchQuery) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Modal Detail Pokja
    function openDetailModal(pokjaId) {
        const modal = document.getElementById('detailModal_' + pokjaId);
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDetailModal(pokjaId) {
        const modal = document.getElementById('detailModal_' + pokjaId);
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function closeDetailModalOnBackdrop(e, pokjaId) {
        if (e.target.id === 'detailModal_' + pokjaId) {
            closeDetailModal(pokjaId);
        }
    }

    // Modal Manage Members
    function openManageMembersModal(pokjaId) {
        const modal = document.getElementById('manageMembersModal_' + pokjaId);
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeManageMembersModal(pokjaId) {
        const modal = document.getElementById('manageMembersModal_' + pokjaId);
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function closeManageMembersModalOnBackdrop(e, pokjaId) {
        if (e.target.id === 'manageMembersModal_' + pokjaId) {
            closeManageMembersModal(pokjaId);
        }
    }

    // Live search filter in manage members modal
    function filterUserList(input, containerId) {
        const query = input.value.toLowerCase().trim();
        const container = document.getElementById(containerId);
        if (!container) return;

        const items = container.querySelectorAll('.user-check-item');
        items.forEach(item => {
            const searchKey = item.getAttribute('data-search') || '';
            if (searchKey.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Escape Key Listener to close any open modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.is-open').forEach(m => {
                m.classList.remove('is-open');
            });
            document.body.style.overflow = '';
        }
    });
</script>
@endsection
