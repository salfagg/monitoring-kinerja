@extends('layouts.app')

@section('title', 'Tugas & Pekerjaan Saya | SIMON-K BGTK')
@section('page-title', 'Tugas & Pekerjaan Saya')

@section('styles')
<style>
    /* Hero Banner */
    .hero-box {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: var(--radius-lg);
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.12);
    }

    .hero-box h2 {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .hero-box p {
        font-size: 13.5px;
        color: #94a3b8;
        max-width: 750px;
        line-height: 1.5;
        margin: 0;
    }

    /* KPI Summary Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        text-decoration: none;
        color: inherit;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }

    .kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .kpi-blue { background: #eff6ff; color: #2563eb; }
    .kpi-orange { background: #fff7ed; color: #ea580c; }
    .kpi-purple { background: #faf5ff; color: #9333ea; }
    .kpi-green { background: #f0fdf4; color: #16a34a; }

    .kpi-info h4 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .kpi-info p {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
    }

    /* Tabs Component */
    .tabs-wrapper {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .tabs-header {
        display: flex;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        overflow-x: auto;
    }

    .tab-btn {
        padding: 16px 22px;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-bottom: 3px solid transparent;
        transition: var(--transition);
        white-space: nowrap;
    }

    .tab-btn:hover {
        color: var(--primary);
        background: rgba(37, 99, 235, 0.03);
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background: #ffffff;
    }

    .tab-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 999px;
        line-height: 1;
    }

    .tab-badge-blue { background: #eff6ff; color: #1d4ed8; }
    .tab-badge-orange { background: #fff7ed; color: #c2410c; }
    .tab-badge-purple { background: #faf5ff; color: #7e22ce; }
    .tab-badge-green { background: #ecfdf5; color: #047857; }

    /* Filter Bar */
    .filter-bar {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 250px;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .search-input {
        width: 100%;
        padding: 9px 12px 9px 36px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        background: #f8fafc;
        outline: none;
    }

    .search-input:focus {
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* Data Table */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .data-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-align: left;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .data-table tr:hover td {
        background: #fafbfc;
    }

    .code-badge {
        font-family: 'SF Mono', Consolas, Menlo, monospace;
        font-weight: 700;
        font-size: 11.5px;
        background: #f1f5f9;
        color: #0f172a;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        display: inline-block;
    }

    .badge-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .status-disetujui { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .status-diajukan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .status-perlu_perbaikan { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .exec-dikerjakan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; }
    .exec-belum_dimulai { background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; }
    .exec-hasil_diajukan { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; font-weight: 800; }
    .exec-selesai_terverifikasi { background: #f0fdf4; color: #166534; border: 1px solid #86efac; font-weight: 800; }
    .exec-perlu_perbaikan_hasil { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

    /* Action Buttons */
    .btn-action-primary {
        background: var(--primary);
        color: white;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition);
        text-decoration: none;
    }

    .btn-action-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-action-green {
        background: #10b981;
        color: white;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition);
        text-decoration: none;
    }

    .btn-action-green:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-action-purple {
        background: #8b5cf6;
        color: white;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition);
        text-decoration: none;
    }

    .btn-action-purple:hover {
        background: #7c3aed;
        transform: translateY(-1px);
    }

    .btn-action-outline {
        background: white;
        color: #475569;
        border: 1px solid var(--border);
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: var(--transition);
    }

    .btn-action-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #64748b;
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #94a3b8;
        margin: 0 auto 16px;
    }

    .empty-state h4 {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
    }

    .empty-state p {
        font-size: 13px;
        margin: 0;
        max-width: 400px;
        margin-inline: auto;
    }

    /* Modal Overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-card {
        background: #ffffff;
        border-radius: 12px;
        width: 100%;
        max-width: 580px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: modalFadeIn 0.2s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-body {
        padding: 20px 24px;
        max-height: 75vh;
        overflow-y: auto;
    }

    .modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .form-input, .form-textarea {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: var(--transition);
        font-family: inherit;
    }

    .form-input:focus, .form-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    @media (max-width: 640px) {
        .modal-card {
            border-radius: 14px;
            max-height: 92vh;
        }
        .modal-header, .modal-footer {
            padding: 14px 18px;
        }
        .modal-body {
            padding: 16px 18px;
        }
    }
</style>
@endsection

@section('content')

    <!-- Flash Messages -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-check-circle" style="font-size: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-warning-circle" style="font-size: 20px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. Hero Banner -->
    <div class="hero-box">
        <h2>Tugas & Pekerjaan Saya</h2>
        <p>
            Kelola dan laporkan kemajuan pekerjaan yang Anda ajukan atau ditugaskan kepada Anda. Catat realisasi berkala, lampirkan bukti dukung, atau selesaikan penugasan 100% untuk diajukan ke Katim.
        </p>
    </div>

    <!-- 2. KPI Summary Cards -->
    <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
        <a href="{{ route('pekerjaan.saya', ['tab' => 'berjalan']) }}" class="kpi-card">
            <div class="kpi-icon kpi-blue">
                <i class="ph-duotone ph-rocket-launch"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countBerjalan }}</h4>
                <p>Tugas Aktif & Berjalan</p>
            </div>
        </a>

        <a href="{{ route('pekerjaan.saya', ['tab' => 'selesai']) }}" class="kpi-card">
            <div class="kpi-icon kpi-green">
                <i class="ph-duotone ph-check-circle"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countSelesai }}</h4>
                <p>Pekerjaan Selesai Tuntas (100%)</p>
            </div>
        </a>
    </div>

    <!-- 3. Tabs Navigation & Content -->
    <div class="tabs-wrapper">
        <div class="tabs-header">
            <a href="{{ route('pekerjaan.saya', ['tab' => 'berjalan']) }}" 
               class="tab-btn {{ $activeTab === 'berjalan' ? 'active' : '' }}">
                <i class="ph-bold ph-lightning"></i>
                <span>Tugas Berjalan & Aktif</span>
                <span class="tab-badge tab-badge-blue">{{ $countBerjalan }}</span>
            </a>

            <a href="{{ route('pekerjaan.saya', ['tab' => 'selesai']) }}" 
               class="tab-btn {{ $activeTab === 'selesai' ? 'active' : '' }}">
                <i class="ph-bold ph-check-fat"></i>
                <span>Pekerjaan Selesai (100%)</span>
                <span class="tab-badge tab-badge-green">{{ $countSelesai }}</span>
            </a>
        </div>

        <!-- Filter Search Bar -->
        <form method="GET" action="{{ route('pekerjaan.saya') }}" class="filter-bar">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="search-box">
                <i class="ph-bold ph-magnifying-glass"></i>
                <input type="text" name="q" class="search-input" value="{{ request('q') }}" placeholder="Cari kode atau nama tugas Anda...">
            </div>

            <button type="submit" class="btn-action-primary" style="padding: 9px 16px;">
                <i class="ph-bold ph-funnel"></i> Cari
            </button>

            @if(request()->filled('q'))
                <a href="{{ route('pekerjaan.saya', ['tab' => $activeTab]) }}" class="btn-action-outline" style="padding: 8px 12px;">
                    <i class="ph-bold ph-arrows-counter-clockwise"></i> Reset
                </a>
            @endif
        </form>

        <!-- Tasks Table -->
        @if($pekerjaanList->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="ph-duotone ph-list-checks"></i>
                </div>
                <h4>Tidak Ada Pekerjaan pada Kategori Ini</h4>
                <p>
                    @if($activeTab === 'berjalan')
                        Belum ada tugas aktif yang sedang berjalan. Rincian subpekerjaan Anda perlu disetujui oleh Katim terlebih dahulu.
                    @else
                        Tidak ditemukan penugasan yang sesuai dengan filter saat ini.
                    @endif
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pekerjaan & Induk</th>
                            <th>Target & Satuan</th>
                            <th>Bobot</th>
                            <th>Tenggat Waktu</th>
                            <th>Capaian Progres</th>
                            <th>Status</th>
                            <th style="text-align: right;">Aksi Pelaporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pekerjaanList as $item)
                            <tr>
                                <td>
                                    <span class="code-badge">{{ $item->kode_tampilan }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('pekerjaan.show', $item) }}" style="font-weight: 700; color: #0f172a; text-decoration: none;" class="hover-underline">
                                        {{ $item->judul }}
                                    </a>
                                    @if($item->parent)
                                        <div style="font-size: 11.5px; color: #64748b; margin-top: 3px;">
                                            Induk: <a href="{{ route('pekerjaan.show', $item->parent) }}" style="color: #475569; text-decoration: none;">{{ $item->parent->kode_tampilan }} - {{ Str::limit($item->parent->judul, 35) }}</a>
                                        </div>
                                    @endif
                                    <div style="display: flex; gap: 8px; align-items: center; margin-top: 5px; flex-wrap: wrap;">
                                        @php
                                            $isUserPic = ((int)$item->penanggung_jawab_id === (int)Auth::id());
                                        @endphp
                                        @if($isUserPic)
                                            <span style="font-size: 10.5px; font-weight: 800; background: #faf5ff; color: #7e22ce; padding: 2px 7px; border-radius: 4px; border: 1px solid #e9d5ff; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-star"></i> PIC Utama
                                            </span>
                                        @else
                                            <span style="font-size: 10.5px; font-weight: 700; background: #f0fdf4; color: #166534; padding: 2px 7px; border-radius: 4px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-user-check"></i> Anggota Pelaksana
                                            </span>
                                        @endif
                                        @if($item->pelaksana && $item->pelaksana->count() > 1)
                                            <span style="font-size: 11px; color: #475569; display: inline-flex; align-items: center; gap: 4px;" title="Total {{ $item->pelaksana->count() }} orang pelaksana">
                                                <i class="ph-bold ph-users-three"></i> Tim ({{ $item->pelaksana->count() }} Pegawai)
                                            </span>
                                        @endif
                                        @if($item->nomor_st)
                                            <span style="font-size: 11px; font-weight: 700; background: #eff6ff; color: #1d4ed8; padding: 2px 7px; border-radius: 4px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-file-text"></i> ST: {{ $item->nomor_st }}
                                            </span>
                                        @endif
                                        @if($item->pembuat && $item->pembuat_id !== Auth::id())
                                            <span style="font-size: 11px; color: #64748b; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-user-check"></i> Pemberi Tugas: <strong>{{ $item->pembuat->name }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ number_format($item->target, 0) }}</strong> {{ $item->satuan }}
                                </td>
                                <td>
                                    <strong>{{ number_format($item->bobot, 1) }}%</strong>
                                </td>
                                <td>
                                    <span style="color: {{ $item->isTerlambat() ? '#dc2626' : 'inherit' }}; font-weight: {{ $item->isTerlambat() ? '700' : 'normal' }};">
                                        {{ $item->tenggat ? $item->tenggat->format('d M Y') : '-' }}
                                    </span>
                                    @if($item->isTerlambat())
                                        <div style="font-size: 10.5px; color: #dc2626; font-weight: 700;">Lewat Tenggat</div>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 70px; height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden;">
                                            <div style="height: 100%; width: {{ $item->progres }}%; background: {{ $item->progres >= 100 ? '#10b981' : '#2563eb' }}; border-radius: 10px;"></div>
                                        </div>
                                        <span style="font-weight: 800; font-size: 12px; color: {{ $item->progres >= 100 ? '#047857' : '#1d4ed8' }};">
                                            {{ number_format($item->progres, 0) }}%
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($item->status_persetujuan !== 'disetujui')
                                        <span class="badge-pill status-{{ $item->status_persetujuan }}">
                                            {{ str_replace('_', ' ', $item->status_persetujuan) }}
                                        </span>
                                    @else
                                        <span class="badge-pill exec-{{ $item->status_pelaksanaan }}">
                                            {{ str_replace('_', ' ', $item->status_pelaksanaan) }}
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    @if($item->isDisetujui() && $item->status_pelaksanaan !== 'selesai_terverifikasi')
                                        <!-- Tombol Lapor Progres Berkala -->
                                        <button type="button" 
                                                class="btn-action-green" 
                                                onclick="openProgressModal({{ $item->id }}, '{{ addslashes($item->kode_tampilan) }}', '{{ addslashes($item->judul) }}', {{ $item->progres }}, false)">
                                            <i class="ph-bold ph-pencil-simple-line"></i> Lapor Progres
                                        </button>

                                        <!-- Tombol Selesaikan Pekerjaan (100%) -->
                                        @if($item->status_pelaksanaan !== 'hasil_diajukan')
                                            <button type="button" 
                                                    class="btn-action-purple" 
                                                    onclick="openProgressModal({{ $item->id }}, '{{ addslashes($item->kode_tampilan) }}', '{{ addslashes($item->judul) }}', 100, true)">
                                                <i class="ph-bold ph-seal-check"></i> Selesaikan (100%)
                                            </button>
                                        @endif
                                    @endif

                                    @if($item->pembuat_id === Auth::id() || $item->penanggung_jawab_id === Auth::id() || Auth::user()->canManagePekerjaan())
                                        <a href="{{ route('pekerjaan.edit', $item) }}" class="btn-action-outline" style="border-color: #fcd34d; color: #b45309;" title="Edit data pekerjaan">
                                            <i class="ph-bold ph-pencil-simple"></i> Edit
                                        </a>
                                    @endif

                                    <a href="{{ route('pekerjaan.show', $item) }}" class="btn-action-outline">
                                        <i class="ph-bold ph-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9;">
                {{ $pekerjaanList->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- ==================== MODAL LAPOR PROGRES & SELESAIKAN ==================== -->
    <div id="modalUpdateProgresUniversal" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'modalUpdateProgresUniversal')">
        <div class="modal-card">
            <form id="formUpdateProgres" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <div class="modal-title" id="modalProgressHeading">
                        <i class="ph-bold ph-pencil-simple-line"></i> Laporkan Realisasi & Progres
                    </div>
                    <button type="button" onclick="closeModal('modalUpdateProgresUniversal')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
                </div>

                <div class="modal-body">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                        <div style="font-size: 11.5px; color: #64748b; font-weight: 700; text-transform: uppercase;">Penugasan:</div>
                        <div style="font-weight: 800; color: #0f172a; margin-top: 2px;" id="progressTaskTitle">-</div>
                    </div>

                    <!-- Banner Status Progres Tim Saat Ini -->
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 12px; font-weight: 700; color: #1e40af; text-transform: uppercase;">
                                <i class="ph-bold ph-chart-line-up"></i> Progres Tim Saat Ini:
                            </span>
                            <span style="font-size: 15px; font-weight: 800; color: #1d4ed8; font-family: monospace;" id="displayCurrentProg">0.0%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: rgba(59, 130, 246, 0.2); border-radius: 999px; overflow: hidden; margin-bottom: 6px;">
                            <div id="barCurrentProg" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3b82f6, #10b981); border-radius: 999px; transition: width 0.3s ease;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: #475569;">
                            <span>Sisa menuju tuntas: <strong id="displayRemainingProg" style="color: #0f172a;">100.0%</strong></span>
                            <span>Target: 100% (Kolaboratif)</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Tanggal Pelaporan <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="tanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label class="form-label" style="margin-bottom: 0;">Tambahan Progres (+%) <span style="color: #ef4444;">*</span></label>
                                <button type="button" onclick="setProgresToRemaining()" style="background: none; border: none; color: #2563eb; font-size: 11.5px; font-weight: 700; cursor: pointer; text-decoration: underline;">
                                    ⚡ Selesaikan Sisa
                                </button>
                            </div>
                            <input type="number" id="inputProgresCapaian" name="progres_capaian" class="form-input" min="0.1" max="100" step="0.1" placeholder="Misal: 20 (+20%)" required>
                        </div>
                    </div>

                    <!-- Preset Quick Add Buttons -->
                    <div id="presetButtonsWrap" style="display: flex; gap: 6px; margin-bottom: 14px; flex-wrap: wrap; align-items: center;">
                        <span style="font-size: 11px; color: #64748b; font-weight: 600;">Pilih Cepat:</span>
                        <button type="button" onclick="setPresetToSaya(5)" class="preset-pill">+5%</button>
                        <button type="button" onclick="setPresetToSaya(10)" class="preset-pill">+10%</button>
                        <button type="button" onclick="setPresetToSaya(15)" class="preset-pill">+15%</button>
                        <button type="button" onclick="setPresetToSaya(20)" class="preset-pill">+20%</button>
                        <button type="button" onclick="setPresetToSaya(25)" class="preset-pill">+25%</button>
                    </div>

                    <!-- Live Calculation Preview -->
                    <div id="liveCalcBoxSaya" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 12.5px; color: #334155;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                            <div>
                                <span>Saat Ini: <strong id="previewCalcCurrent">0.0%</strong></span>
                                <span style="margin: 0 4px;">+</span>
                                <span>Tambahan: <strong id="previewCalcAddition" style="color: #059669;">+0.0%</strong></span>
                            </div>
                            <div style="font-size: 13px;">
                                <span>Total Akumulasi: <strong id="previewCalcTotal" style="font-size: 15px; color: #2563eb;">0.0%</strong></span>
                            </div>
                        </div>
                    </div>

                    <div id="noticeSelesai" style="display: none; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 12px; color: #7e22ce;">
                        <i class="ph-bold ph-seal-check"></i> <strong>Akumulasi Mencapai 100%!</strong> Status akan otomatis beralih ke <strong>Hasil Diajukan</strong> untuk diverifikasi & disahkan oleh Katim.
                    </div>

                    <div class="form-group">
                        <label class="form-label">Uraian Realisasi / Hasil Kemajuan <span style="color: #ef4444;">*</span></label>
                        <textarea id="inputDeskripsiProgres" name="deskripsi_progres" class="form-textarea" rows="3" placeholder="Jelaskan kontribusi atau kemajuan tugas yang telah diselesaikan pada laporan ini..." required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Kendala (Bila Ada)</label>
                            <textarea name="kendala" class="form-textarea" rows="2" placeholder="Kendala teknis/lapangan..."></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Rencana Tindak Lanjut</label>
                            <textarea name="tindak_lanjut" class="form-textarea" rows="2" placeholder="Langkah berikutnya..."></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Unggah Berkas Bukti (PDF, DOCX, ZIP, Gambar)</label>
                        <input type="file" name="file_bukti" class="form-input" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx,.zip">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Atau Tautan Bukti (Google Drive / Cloud Link)</label>
                        <input type="url" name="link_bukti" class="form-input" placeholder="https://drive.google.com/...">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action-outline" onclick="closeModal('modalUpdateProgresUniversal')">Batal</button>
                    <button type="submit" id="btnSubmitProgress" class="btn-action-green">Simpan Realisasi</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<style>
    .preset-pill {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 11.5px;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
    }
    .preset-pill:hover {
        background: #e2e8f0;
        border-color: #94a3b8;
    }
</style>
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'flex';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'none';
    }

    function closeModalOnBackdrop(e, id) {
        if (e.target.id === id) closeModal(id);
    }

    let currentTaskBaseProg = 0;
    let currentTaskMaxRemaining = 100;

    function updateLiveCalcSaya(additionVal) {
        const val = parseFloat(additionVal) || 0;
        const total = Math.min(100, Math.round((currentTaskBaseProg + val) * 10) / 10);

        document.getElementById('previewCalcCurrent').textContent = `${currentTaskBaseProg.toFixed(1)}%`;
        document.getElementById('previewCalcAddition').textContent = `+${val.toFixed(1)}%`;
        document.getElementById('previewCalcTotal').textContent = `${total.toFixed(1)}%`;

        const notice = document.getElementById('noticeSelesai');
        const submitBtn = document.getElementById('btnSubmitProgress');

        if (total >= 100) {
            notice.style.display = 'block';
            submitBtn.textContent = 'Selesaikan & Ajukan ke Katim';
            submitBtn.className = 'btn-action-purple';
        } else {
            notice.style.display = 'none';
            submitBtn.textContent = val > 0 ? `Simpan Realisasi (+${val.toFixed(1)}%)` : 'Simpan Realisasi';
            submitBtn.className = 'btn-action-green';
        }
    }

    function setPresetToSaya(preset) {
        const input = document.getElementById('inputProgresCapaian');
        const safeVal = Math.min(preset, currentTaskMaxRemaining);
        input.value = safeVal;
        updateLiveCalcSaya(safeVal);
    }

    function setProgresToRemaining() {
        const input = document.getElementById('inputProgresCapaian');
        input.value = currentTaskMaxRemaining;
        updateLiveCalcSaya(currentTaskMaxRemaining);
    }

    function openProgressModal(id, kode, judul, currentProgres, isCompleting) {
        const form = document.getElementById('formUpdateProgres');
        form.action = `/pekerjaan/${id}/progres`;
        document.getElementById('progressTaskTitle').textContent = `[${kode}] ${judul}`;

        currentTaskBaseProg = parseFloat(currentProgres) || 0;
        currentTaskMaxRemaining = Math.max(0, Math.round((100 - currentTaskBaseProg) * 10) / 10);

        document.getElementById('displayCurrentProg').textContent = `${currentTaskBaseProg.toFixed(1)}%`;
        document.getElementById('barCurrentProg').style.width = `${currentTaskBaseProg}%`;
        document.getElementById('displayRemainingProg').textContent = `${currentTaskMaxRemaining.toFixed(1)}%`;

        const inputProgres = document.getElementById('inputProgresCapaian');
        inputProgres.max = currentTaskMaxRemaining;

        const heading = document.getElementById('modalProgressHeading');
        const descInput = document.getElementById('inputDeskripsiProgres');

        if (isCompleting) {
            heading.innerHTML = '<i class="ph-bold ph-seal-check"></i> Selesaikan Pekerjaan & Ajukan Hasil';
            inputProgres.value = currentTaskMaxRemaining;
            if (!descInput.value) {
                descInput.value = 'Telah menyelesaikan seluruh sisa target penugasan bersama tim sesuai spesifikasi.';
            }
            updateLiveCalcSaya(currentTaskMaxRemaining);
        } else {
            heading.innerHTML = '<i class="ph-bold ph-pencil-simple-line"></i> Laporkan Realisasi & Progres';
            inputProgres.value = '';
            updateLiveCalcSaya(0);
        }

        inputProgres.oninput = function() {
            updateLiveCalcSaya(this.value);
        };

        openModal('modalUpdateProgresUniversal');
    }

    function setProgresTo100() {
        setProgresToRemaining();
    }
</script>
@endsection
