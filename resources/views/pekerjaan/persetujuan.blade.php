@extends('layouts.app')

@section('title', 'Pusat Persetujuan Laporan Progres | SIMON-K BGTK')
@section('page-title', 'Pusat Persetujuan Progres')

@section('styles')
<style>
    /* Hero Header */
    .approval-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-lg);
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.15);
    }

    .approval-header::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .header-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
    }

    .header-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #93c5fd;
        margin-bottom: 10px;
    }

    .approval-header h2 {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 6px;
        letter-spacing: -0.01em;
    }

    .approval-header p {
        font-size: 13.5px;
        color: #cbd5e1;
        line-height: 1.5;
        margin: 0;
    }

    /* KPI Summary Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
        cursor: pointer;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .kpi-amber { background: #fffbeb; color: #d97706; }
    .kpi-blue { background: #eff6ff; color: #2563eb; }
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

    /* Tab Navigation */
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

    .tab-badge-amber { background: #fffbeb; color: #b45309; }
    .tab-badge-gray { background: #f1f5f9; color: #475569; }

    /* Filter Bar inside Tab */
    .filter-bar {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: #ffffff;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 240px;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
    }

    .search-input {
        width: 100%;
        padding: 9px 12px 9px 36px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        background: #f8fafc;
        outline: none;
        transition: var(--transition);
    }

    .search-input:focus {
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .filter-select {
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        background: #f8fafc;
        outline: none;
        min-width: 180px;
        cursor: pointer;
    }

    /* Tables */
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
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #1e293b;
    }

    .data-table tr:hover td {
        background: #fbfdff;
    }

    .btn-action-primary {
        background: #10b981;
        color: white;
        padding: 6px 12px;
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
        background: #059669;
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
        cursor: pointer;
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

    /* Modal Styling */
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
        max-width: 520px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    }

    .modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
</style>
@endsection

@section('content')

    <!-- Flash Alerts -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-check-circle" style="font-size: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-info" style="font-size: 20px;"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- 1. Header Banner -->
    <div class="approval-header">
        <div class="header-content">
            <div class="header-tag">
                <i class="ph-bold ph-seal-check"></i> Validasi Progres oleh Katim / Koordinator Pemberi Tugas
            </div>
            <h2>Pusat Persetujuan Laporan Progres Pegawai</h2>
            <p>
                Setiap laporan progres dari pegawai pelaksana harus disetujui terlebih dahulu oleh <strong>Katim atau Koordinator pembuat tugas</strong>. Setelah disetujui, capaian persentase progres akan otomatis diakumulasikan ke pekerjaan hingga 100%.
            </p>
        </div>
    </div>

    <!-- 2. KPI Summary Cards -->
    <div class="kpi-grid">
        <a href="{{ route('persetujuan.index', ['tab' => 'progres']) }}" class="kpi-card">
            <div class="kpi-icon kpi-amber">
                <i class="ph-duotone ph-clock"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countProgresPending }}</h4>
                <p>Menunggu Persetujuan Progres</p>
            </div>
        </a>

        <a href="{{ route('pekerjaan.index') }}" class="kpi-card">
            <div class="kpi-icon kpi-green">
                <i class="ph-duotone ph-check-circle"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countSelesaiTerverifikasi }}</h4>
                <p>Pekerjaan Tuntas (100%)</p>
            </div>
        </a>

        <a href="{{ route('persetujuan.index', ['tab' => 'riwayat']) }}" class="kpi-card">
            <div class="kpi-icon kpi-blue">
                <i class="ph-duotone ph-clock-counter-clockwise"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $riwayatPersetujuan->total() }}</h4>
                <p>Total Log Keputusan</p>
            </div>
        </a>
    </div>

    <!-- 3. Tabs Navigation & Content -->
    <div class="tabs-wrapper">
        <div class="tabs-header">
            <a href="{{ route('persetujuan.index', array_merge(request()->query(), ['tab' => 'progres'])) }}" 
               class="tab-btn {{ $activeTab === 'progres' ? 'active' : '' }}">
                <i class="ph-bold ph-hourglass-high"></i>
                <span>Antrean Laporan Progres Pegawai</span>
                <span class="tab-badge tab-badge-amber">{{ $countProgresPending }}</span>
            </a>

            <a href="{{ route('persetujuan.index', array_merge(request()->query(), ['tab' => 'riwayat'])) }}" 
               class="tab-btn {{ $activeTab === 'riwayat' ? 'active' : '' }}">
                <i class="ph-bold ph-clock-counter-clockwise"></i>
                <span>Arsip Jejak Audit Keputusan</span>
                <span class="tab-badge tab-badge-gray">{{ $riwayatPersetujuan->total() }}</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('persetujuan.index') }}" class="filter-bar">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="search-box">
                <i class="ph-bold ph-magnifying-glass"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pegawai, uraian progres, atau judul pekerjaan..." class="search-input">
            </div>

            @if(Auth::user()->isAdmin() || Auth::user()->isKabal() || Auth::user()->isKasubag())
                <select name="pokja_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Tim Kerja (Pokja)</option>
                    @foreach($pokjas as $p)
                        <option value="{{ $p->id }}" {{ request('pokja_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_pokja }}
                        </option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="btn-action-outline">
                <i class="ph-bold ph-funnel"></i> Filter
            </button>
            @if(request()->hasAny(['q', 'pokja_id']))
                <a href="{{ route('persetujuan.index', ['tab' => $activeTab]) }}" class="btn-action-outline" style="color: #ef4444;">
                    <i class="ph-bold ph-x"></i> Reset
                </a>
            @endif
        </form>

        <!-- TAB 1: Antrean Laporan Progres Pegawai -->
        @if($activeTab === 'progres')
            @if($antreanProgres->isNotEmpty())
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal Lapor</th>
                                <th>Pegawai Pelapor</th>
                                <th>Pekerjaan & Tim</th>
                                <th>Pemberi Tugas</th>
                                <th>Tambahan Progres</th>
                                <th>Uraian Progres</th>
                                <th>Bukti Dukung</th>
                                <th style="text-align: right;">Aksi Approval</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($antreanProgres as $item)
                                @php
                                    $pekerjaan = $item->pekerjaan;
                                    $isPembuat = ($pekerjaan && $pekerjaan->pembuat_id === Auth::id())
                                        || (Auth::user()->isKatim() && $pekerjaan && $pekerjaan->pokja && $pekerjaan->pokja->katim_id === Auth::id())
                                        || Auth::user()->isAdmin();
                                @endphp
                                <tr>
                                    <td style="white-space: nowrap; font-weight: 600;">
                                        {{ $item->tanggal->format('d M Y') }}
                                        <div style="font-size: 11px; color: #94a3b8;">{{ $item->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 30px; height: 30px; border-radius: 999px; background: #eff6ff; color: #2563eb; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; border: 1px solid #bfdbfe;">
                                                {{ strtoupper(substr($item->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #0f172a;">{{ $item->user->name }}</div>
                                                <div style="font-size: 11px; color: #64748b;">{{ $item->user->nip ?? 'Pegawai' }} &bull; {{ $item->user->role_label }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($pekerjaan)
                                            <a href="{{ route('pekerjaan.show', $pekerjaan) }}" style="font-weight: 700; color: #1e40af; text-decoration: none;" class="hover-underline">
                                                <span style="font-family: monospace; font-size: 11.5px; background: #e0e7ff; color: #3730a3; padding: 1px 6px; border-radius: 4px; margin-right: 4px;">
                                                    {{ $pekerjaan->kode_tampilan }}
                                                </span>
                                                {{ $pekerjaan->judul }}
                                            </a>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                                <i class="ph-bold ph-buildings"></i> {{ $pekerjaan->pokja?->nama_pokja ?? 'BGTK DKI' }}
                                                &bull; Progres Saat Ini: <strong>{{ number_format($pekerjaan->progres, 1) }}%</strong>
                                            </div>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 12.5px;">
                                            {{ $pekerjaan->pembuat->name ?? '-' }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">
                                            {{ $pekerjaan->pembuat->role_label ?? 'Pemberi Tugas' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 2px;">
                                            <span style="font-size: 13px; font-weight: 800; color: #d97706; background: #fffbeb; padding: 2px 8px; border-radius: 6px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 2px;">
                                                <i class="ph-bold ph-plus" style="font-size: 10px;"></i>{{ number_format($item->progres_capaian, 1) }}%
                                            </span>
                                            @if($pekerjaan)
                                                <span style="font-size: 11px; color: #64748b;">
                                                    Menjadi: <strong style="color: #10b981;">{{ min(100, number_format($pekerjaan->progres + $item->progres_capaian, 1)) }}%</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="max-width: 250px;">
                                        <div style="font-size: 12.5px; color: #1e293b; line-height: 1.4;">
                                            {{ $item->deskripsi_progres }}
                                        </div>
                                        @if($item->kendala)
                                            <div style="font-size: 11.5px; color: #dc2626; margin-top: 4px;">
                                                <strong>Kendala:</strong> {{ $item->kendala }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->file_bukti)
                                            <a href="{{ asset('storage/' . $item->file_bukti) }}" target="_blank" class="btn-action-outline" style="padding: 4px 8px; font-size: 11.5px;">
                                                <i class="ph-bold ph-download-simple"></i> File Bukti
                                            </a>
                                        @endif
                                        @if($item->link_bukti)
                                            <a href="{{ $item->link_bukti }}" target="_blank" class="btn-action-outline" style="padding: 4px 8px; font-size: 11.5px; margin-top: 4px;">
                                                <i class="ph-bold ph-arrow-square-out"></i> Tautan
                                            </a>
                                        @endif
                                        @if(!$item->file_bukti && !$item->link_bukti)
                                            <span style="color: #94a3b8; font-size: 12px;">Tanpa berkas</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        @if($isPembuat)
                                            <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                                <form action="{{ route('realisasi.approve', $item) }}" method="POST" onsubmit="return confirm('Setujui laporan progres ini? Capaian +{{ $item->progres_capaian }}% akan langsung diakumulasikan ke pekerjaan.')">
                                                    @csrf
                                                    <button type="submit" class="btn-action-primary" title="Setujui laporan progres">
                                                        <i class="ph-bold ph-check"></i> Setujui
                                                    </button>
                                                </form>
                                                <button type="button" class="btn-action-outline" style="color: #dc2626; border-color: #fca5a5;" onclick="openModalTolakProgres('{{ route('realisasi.reject', $item) }}')" title="Tolak dengan catatan perbaikan">
                                                    <i class="ph-bold ph-x"></i> Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">
                                                Menunggu Approval Pembuat Tugas
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 16px 20px;">
                    {{ $antreanProgres->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="ph-duotone ph-check-circle" style="color: #10b981;"></i>
                    </div>
                    <h4>Tidak Ada Antrean Laporan Progres</h4>
                    <p>Semua laporan progres pekerjaan dari pegawai pelaksana telah disetujui atau belum ada pengajuan baru.</p>
                </div>
            @endif
        @endif

        <!-- TAB 2: Arsip Jejak Audit Keputusan -->
        @if($activeTab === 'riwayat')
            @if($riwayatPersetujuan->isNotEmpty())
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pemeriksa</th>
                                <th>Tugas / Pekerjaan</th>
                                <th>Jenis Keputusan</th>
                                <th>Perubahan Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatPersetujuan as $item)
                                <tr>
                                    <td style="white-space: nowrap; font-size: 12px;">{{ $item->created_at->format('d M Y H:i') }}</td>
                                    <td style="font-weight: 700;">{{ $item->user->name }}</td>
                                    <td>
                                        @if($item->pekerjaan)
                                            <a href="{{ route('pekerjaan.show', $item->pekerjaan) }}" style="font-weight: 600; color: #1e40af; text-decoration: none;">
                                                {{ $item->pekerjaan->kode_tampilan }} - {{ $item->pekerjaan->judul }}
                                            </a>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-family: monospace; font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                            {{ str_replace('_', ' ', $item->jenis_aksi) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 12px; color: #64748b;">{{ $item->status_sebelumnya ?? 'Awal' }}</span>
                                        &rarr;
                                        <strong style="font-size: 12.5px; color: #0f172a;">{{ $item->status_baru }}</strong>
                                    </td>
                                    <td>{{ $item->catatan ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 16px 20px;">
                    {{ $riwayatPersetujuan->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="ph-duotone ph-clock-counter-clockwise"></i>
                    </div>
                    <h4>Belum Ada Riwayat Keputusan</h4>
                    <p>Catatan audit trail keputusan persetujuan akan otomatis tercatat di sini.</p>
                </div>
            @endif
        @endif

    </div>

    <!-- Modal Tolak Laporan Progres -->
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
                        Tuliskan catatan evaluasi atau alasan penolakan agar pegawai pelaksana dapat memperbaiki laporannya:
                    </p>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Catatan / Alasan Penolakan <span class="required" style="color: #ef4444;">*</span></label>
                        <textarea name="catatan" class="form-textarea" rows="3" placeholder="Contoh: Bukti dukung laporan belum lengkap, mohon lampirkan daftar hadir peserta..." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none;"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action-outline" onclick="closeModal('modalTolakProgres')">Batal</button>
                    <button type="submit" class="btn-action-primary" style="background: #dc2626;">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>

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
</script>
@endsection
