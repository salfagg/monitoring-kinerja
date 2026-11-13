@extends('layouts.app')

@section('title', 'Log Harian Kinerja Pegawai')
@section('page-title', 'Log Harian')

@section('styles')
<style>
    /* Header Toolbar */
    .log-header-bar {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .log-period-nav {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .log-period-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--bg-hover, #f8fafc);
        color: var(--text-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }

    .log-period-btn:hover {
        background: var(--primary-light);
        color: var(--primary);
        border-color: var(--primary);
        transform: translateY(-1px);
    }

    .log-period-title {
        font-size: 19px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .log-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* KPI Summary Row */
    .log-kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .log-kpi-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px 18px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s;
    }

    .log-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-card);
    }

    .log-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .log-kpi-info h4 {
        font-size: 20px;
        font-weight: 800;
        margin: 0;
        line-height: 1.1;
        color: var(--text-dark);
    }

    .log-kpi-info p {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        margin: 3px 0 0 0;
    }

    /* Calendar Container */
    .calendar-container {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: 28px;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
    }

    .weekday-header {
        padding: 12px 10px;
        text-align: center;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .weekday-header.weekend {
        color: #ef4444;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: var(--border);
    }

    .calendar-cell {
        background: #ffffff;
        min-height: 120px;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: all 0.18s;
        position: relative;
    }

    .calendar-cell:hover {
        background: #f8fafc;
        z-index: 2;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }

    .calendar-cell.other-month {
        background: #fbfcfd;
        opacity: 0.45;
        cursor: default;
    }

    .calendar-cell.is-today {
        background: rgba(10, 111, 181, 0.03);
    }

    .calendar-cell.is-today .cell-day-number {
        background: var(--primary);
        color: white;
        border-radius: 8px;
        padding: 2px 7px;
        font-weight: 800;
    }

    .calendar-cell.has-logs {
        background: #ffffff;
    }

    .calendar-cell.has-logs:hover {
        background: #f0fdf4;
    }

    .cell-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .cell-day-number {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dark);
    }

    .cell-log-count {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary);
    }

    .cell-log-count.count-approved {
        background: #ecfdf5;
        color: #059669;
    }

    .cell-logs-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        overflow: hidden;
        flex: 1;
    }

    .mini-log-item {
        font-size: 11px;
        padding: 3px 6px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-left: 3px solid #94a3b8;
        line-height: 1.3;
        transition: transform 0.1s;
    }

    .mini-log-item:hover {
        transform: translateX(2px);
    }

    .mini-log-item.status-disetujui_katim,
    .mini-log-item.status-disetujui_kabal,
    .mini-log-item.status-disetujui_pokja {
        background: #ecfdf5;
        color: #065f46;
        border-left-color: #10b981;
    }

    .mini-log-item.status-diajukan {
        background: #eff6ff;
        color: #1e40af;
        border-left-color: #3b82f6;
    }

    .mini-log-item.status-draft {
        background: #f8fafc;
        color: #475569;
        border-left-color: #cbd5e1;
    }

    .mini-log-item.status-revisi_pokja,
    .mini-log-item.status-revisi_katim {
        background: #fef2f2;
        color: #991b1b;
        border-left-color: #ef4444;
    }

    .cell-add-hint {
        opacity: 0;
        font-size: 10px;
        color: var(--primary);
        font-weight: 700;
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 3px;
        padding-top: 4px;
        transition: opacity 0.15s;
    }

    .calendar-cell:hover .cell-add-hint {
        opacity: 1;
    }

    /* Modal Backdrop & Card */
    .log-modal-backdrop {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(4px) !important;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 16px;
    }

    .log-modal-backdrop.is-open {
        display: flex;
    }

    .log-modal-card {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 35px -5px rgba(0, 0, 0, 0.25);
        animation: logModalIn 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
        border: 1px solid var(--border);
    }

    @keyframes logModalIn {
        from { transform: scale(0.92) translateY(15px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }

    .log-modal-head {
        padding: 16px 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(to right, rgba(10, 111, 181, 0.04), transparent);
    }

    .log-modal-head h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .log-modal-body {
        padding: 20px 24px;
        overflow-y: auto;
        flex: 1;
    }

    .log-modal-foot {
        padding: 12px 22px;
        border-top: 1px solid var(--border);
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }

    /* Day Detail Card in Modal */
    .day-log-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.15s;
    }

    .day-log-card:hover {
        border-color: rgba(10, 111, 181, 0.3);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Form Styles in Modal */
    .form-group-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        font-size: 13px;
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #ffffff;
        outline: none;
        transition: border 0.15s;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(10, 111, 181, 0.12);
    }

    /* Tab switcher for Bukti Dukung */
    .bukti-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }

    .bukti-tab-btn {
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid var(--border);
        background: #f8fafc;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s;
    }

    .bukti-tab-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .calendar-cell {
            min-height: 80px;
            padding: 4px 6px;
        }
        .mini-log-item {
            font-size: 9.5px;
            padding: 2px 4px;
        }
        .form-group-grid {
            grid-template-columns: 1fr;
        }
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

    @if(isset($errors) && $errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; color: #991b1b; font-size: 13px;">
            <div style="font-weight: 700; margin-bottom: 4px;">Harap perbaiki kesalahan berikut:</div>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. Header Toolbar: Periode Kalender, Pilihan Pegawai, & Tombol Aksi -->
    <div class="log-header-bar">
        <div class="log-period-nav">
            @php
                $prevMonth = $month == 1 ? 12 : $month - 1;
                $prevYear = $month == 1 ? $year - 1 : $year;
                $nextMonth = $month == 12 ? 1 : $month + 1;
                $nextYear = $month == 12 ? $year + 1 : $year;
            @endphp
            <a href="{{ route('log-harian.index', array_merge(request()->all(), ['bulan' => $prevMonth, 'tahun' => $prevYear])) }}" class="log-period-btn" title="Bulan Sebelumnya">
                <i class="ph-bold ph-caret-left"></i>
            </a>

            <div class="log-period-title">
                <i class="ph-duotone ph-calendar" style="color: var(--primary);"></i>
                <span>{{ $namaBulanIndo[$month] }} {{ $year }}</span>
            </div>

            <a href="{{ route('log-harian.index', array_merge(request()->all(), ['bulan' => $nextMonth, 'tahun' => $nextYear])) }}" class="log-period-btn" title="Bulan Berikutnya">
                <i class="ph-bold ph-caret-right"></i>
            </a>

            <!-- Quick Month/Year Dropdown Form -->
            <form action="{{ route('log-harian.index') }}" method="GET" style="display: inline-flex; align-items: center; gap: 6px; margin-left: 8px;">
                @if(request()->has('user_id'))
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                @endif
                <select name="bulan" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 6px 8px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none; cursor: pointer;">
                    @foreach($namaBulanIndo as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ $month == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
                <select name="tahun" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 6px 8px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none; cursor: pointer;">
                    @for($y = 2024; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <div class="log-actions">
            <!-- Filter Pegawai untuk Katim / Pimpinan / Admin -->
            @if($canManage && $pegawaiList->isNotEmpty())
                <form action="{{ route('log-harian.index') }}" method="GET" style="display: flex; align-items: center; gap: 6px;">
                    <input type="hidden" name="bulan" value="{{ $month }}">
                    <input type="hidden" name="tahun" value="{{ $year }}">
                    <div style="position: relative;">
                        <i class="ph-bold ph-user" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--text-muted); pointer-events: none;"></i>
                        <select name="user_id" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 7px 10px 7px 28px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none; max-width: 210px; cursor: pointer;">
                            @foreach($pegawaiList as $p)
                                <option value="{{ $p->id }}" {{ $targetUser->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->pokja?->kode_pokja ?? 'BGTK' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

            <!-- Rekap & Tarik Data Bulanan Button -->
            <a href="{{ route('log-harian.rekap', ['user_id' => $targetUser->id, 'bulan' => $month, 'tahun' => $year]) }}" class="btn-secondary" style="font-size: 12.5px; padding: 7px 14px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; background: #ffffff; border: 1px solid var(--border); color: var(--text-dark);">
                <i class="ph-bold ph-printer" style="font-size: 15px; color: var(--primary);"></i>
                <span>Rekap & Tarik Laporan</span>
            </a>

            <!-- Tombol Tambah Log Baru -->
            @if(Auth::id() === $targetUser->id || Auth::user()->isAdmin())
                <button type="button" class="btn-primary" onclick="openCreateLogModal('{{ \Carbon\Carbon::today()->format('Y-m-d') }}')" style="font-size: 12.5px; padding: 7px 14px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
                    <i class="ph-bold ph-plus-circle" style="font-size: 16px;"></i>
                    <span>Catat Log Harian</span>
                </button>
            @endif
        </div>
    </div>

    <!-- 2. Ringkasan Eksekutif KPI Bulanan Pegawai -->
    <div class="log-kpi-row">
        <div class="log-kpi-card">
            <div class="log-kpi-icon" style="background: #e8f2fb; color: #0a6fb5;">
                <i class="ph-duotone ph-calendar-check"></i>
            </div>
            <div class="log-kpi-info">
                <h4>{{ $totalHariAktif }} Hari</h4>
                <p>Hari Kerja Terisi</p>
            </div>
        </div>

        <div class="log-kpi-card">
            <div class="log-kpi-icon" style="background: #e3f6ee; color: #0c8a5d;">
                <i class="ph-duotone ph-notepad"></i>
            </div>
            <div class="log-kpi-info">
                <h4>{{ $totalLogs }}</h4>
                <p>Total Log Kegiatan</p>
            </div>
        </div>

        <div class="log-kpi-card">
            <div class="log-kpi-icon" style="background: #fff3d9; color: #b87400;">
                <i class="ph-duotone ph-clock"></i>
            </div>
            <div class="log-kpi-info">
                <h4>{{ $totalJamKerja }} Jam</h4>
                <p>Total Waktu Kerja</p>
            </div>
        </div>

        <div class="log-kpi-card">
            <div class="log-kpi-icon" style="background: #efe8fa; color: #6f45bd;">
                <i class="ph-duotone ph-seal-check"></i>
            </div>
            <div class="log-kpi-info">
                <h4>{{ $countDisetujui }} / {{ $totalLogs }}</h4>
                <p>Disetujui &bull; {{ $countDiajukan }} Diajukan</p>
            </div>
        </div>
    </div>

    <!-- Jika ada draft log bulan ini, tampilkan banner pengajuan batch -->
    @if(Auth::id() === $targetUser->id && $countDraft > 0)
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 18px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="ph-bold ph-info" style="font-size: 20px; color: #d97706;"></i>
                <span style="font-size: 13px; font-weight: 600; color: #92400e;">
                    Anda memiliki <strong>{{ $countDraft }} catatan log</strong> yang masih berstatus Draf untuk bulan {{ $namaBulanIndo[$month] }} {{ $year }}.
                </span>
            </div>
            <form action="{{ route('log-harian.ajukan-bulan') }}" method="POST" onsubmit="return confirm('Ajukan seluruh {{ $countDraft }} draf log harian bulan ini untuk diverifikasi?')">
                @csrf
                <input type="hidden" name="bulan" value="{{ $month }}">
                <input type="hidden" name="tahun" value="{{ $year }}">
                <button type="submit" style="padding: 6px 14px; background: #d97706; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="ph-bold ph-paper-plane-tilt"></i>
                    <span>Ajukan Semua Draf Bulan Ini</span>
                </button>
            </form>
        </div>
    @endif

    <!-- 3. Grid Kalender Bulanan Interaktif -->
    <div class="calendar-container">
        <!-- Nama Hari Header -->
        <div class="calendar-weekdays">
            <div class="weekday-header">SEN</div>
            <div class="weekday-header">SEL</div>
            <div class="weekday-header">RAB</div>
            <div class="weekday-header">KAM</div>
            <div class="weekday-header">JUM</div>
            <div class="weekday-header weekend">SAB</div>
            <div class="weekday-header weekend">MIN</div>
        </div>

        <!-- Tanggal Sel-Sel Kalender -->
        <div class="calendar-grid">
            <!-- Filler Bulan Sebelumnya -->
            @foreach($prevMonthDays as $prev)
                <div class="calendar-cell other-month">
                    <div class="cell-top">
                        <span class="cell-day-number">{{ $prev['day'] }}</span>
                    </div>
                </div>
            @endforeach

            <!-- Tanggal Bulan Berjalan -->
            @foreach($currentMonthDays as $item)
                @php
                    $dDate = $item['date'];
                    $dLogs = $item['logs'];
                    $dCount = $dLogs->count();
                    $hasLogs = $dCount > 0;
                    $allApproved = $hasLogs && $dLogs->every(fn($l) => in_array($l->status, ['disetujui_katim', 'disetujui_kabal', 'disetujui_pokja']));
                @endphp

                <div class="calendar-cell {{ $item['isToday'] ? 'is-today' : '' }} {{ $hasLogs ? 'has-logs' : '' }}"
                     onclick="openDayDetailModal('{{ $dDate }}')"
                     title="Klik untuk melihat / menambah log tanggal {{ \Carbon\Carbon::parse($dDate)->format('d/m/Y') }}">
                    
                    <div class="cell-top">
                        <span class="cell-day-number">{{ $item['day'] }}</span>
                        @if($hasLogs)
                            <span class="cell-log-count {{ $allApproved ? 'count-approved' : '' }}">
                                {{ $dCount }} Log
                            </span>
                        @endif
                    </div>

                    <div class="cell-logs-list">
                        @foreach($dLogs->take(3) as $log)
                            <div class="mini-log-item status-{{ $log->status }}" title="{{ $log->uraian_kegiatan }}">
                                @if($log->jam_mulai)
                                    <strong style="font-family: monospace;">{{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }}</strong>
                                @endif
                                <span>{{ Str::limit($log->uraian_kegiatan, 22) }}</span>
                            </div>
                        @endforeach
                        @if($dCount > 3)
                            <div style="font-size: 9.5px; color: var(--text-muted); font-weight: 700; text-align: center; margin-top: 2px;">
                                +{{ $dCount - 3 }} kegiatan lainnya
                            </div>
                        @endif
                    </div>

                    <div class="cell-add-hint">
                        <i class="ph-bold ph-plus"></i>
                        <span>Detail &bull; Tambah</span>
                    </div>
                </div>
            @endforeach

            <!-- Filler Bulan Berikutnya -->
            @foreach($nextMonthDays as $next)
                <div class="calendar-cell other-month">
                    <div class="cell-top">
                        <span class="cell-day-number">{{ $next['day'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- =========================================================================
         4. MODAL DETAIL LOG HARIAN PER TANGGAL (Terbuka saat klik tanggal kalender)
         ========================================================================= -->
    <div id="dayDetailModal" class="log-modal-backdrop" onclick="closeDayDetailModalOnBackdrop(event)">
        <div class="log-modal-card" onclick="event.stopPropagation()">
            <div class="log-modal-head">
                <h3>
                    <i class="ph-duotone ph-calendar-check" style="color: var(--primary);"></i>
                    <span id="modalDateTitle">Aktivitas Harian</span>
                </h3>
                <button type="button" onclick="closeDayDetailModal()" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer; padding: 4px;">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>

            <div class="log-modal-body" id="modalDayLogsContainer">
                <!-- Konten dinamis di-render via Javascript dari data cache -->
            </div>

            <div class="log-modal-foot">
                @if(Auth::id() === $targetUser->id || Auth::user()->isAdmin())
                    <button type="button" class="btn-primary" id="btnTambahOnSelectedDate" onclick="handleTambahFromModal()" style="font-size: 12.5px; padding: 7px 14px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
                        <i class="ph-bold ph-plus-circle"></i>
                        <span>+ Tambah Log di Tanggal Ini</span>
                    </button>
                @endif
                <button type="button" class="btn-secondary" onclick="closeDayDetailModal()" style="font-size: 12.5px; padding: 7px 14px; border-radius: 8px; border: 1px solid var(--border); background: white; cursor: pointer; font-weight: 700;">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         5. MODAL FORM TAMBAH LOG HARIAN BARU
         ========================================================================= -->
    <div id="createLogModal" class="log-modal-backdrop" onclick="closeCreateLogModalOnBackdrop(event)">
        <div class="log-modal-card" style="max-width: 620px;" onclick="event.stopPropagation()">
            <form action="{{ route('log-harian.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="log-modal-head">
                    <h3>
                        <i class="ph-duotone ph-notepad" style="color: var(--primary);"></i>
                        <span>Catat Log Kinerja Harian</span>
                    </h3>
                    <button type="button" onclick="closeCreateLogModal()" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer; padding: 4px;">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <div class="log-modal-body">
                    <!-- Tanggal & Jam Kegiatan -->
                    <div class="form-group-grid">
                        <div class="form-group" style="grid-column: span 2;">
                            <label><i class="ph-bold ph-calendar"></i> Tanggal Kegiatan <span style="color: red;">*</span></label>
                            <input type="date" name="tanggal_kegiatan" id="inputTanggalKegiatan" class="form-control" required value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label><i class="ph-bold ph-clock"></i> Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" placeholder="08:00">
                        </div>
                        <div class="form-group">
                            <label><i class="ph-bold ph-clock-afternoon"></i> Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" placeholder="16:00">
                        </div>
                    </div>

                    <!-- Uraian Aktivitas -->
                    <div class="form-group">
                        <label><i class="ph-bold ph-text-align-left"></i> Uraian Kegiatan / Aktivitas <span style="color: red;">*</span></label>
                        <textarea name="uraian_kegiatan" class="form-control" rows="3" required placeholder="Tuliskan detail pekerjaan atau aktivitas yang Anda kerjakan pada hari tersebut..."></textarea>
                    </div>

                    <!-- Output & Capaian -->
                    <div class="form-group-grid">
                        <div class="form-group">
                            <label><i class="ph-bold ph-package"></i> Output / Hasil Kegiatan <span style="color: red;">*</span></label>
                            <input type="text" name="output_kegiatan" class="form-control" required placeholder="Contoh: Draf Modul Pelatihan Bab 2">
                        </div>
                        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div>
                                <label>Volume <span style="color: red;">*</span></label>
                                <input type="number" step="0.1" name="volume_capaian" class="form-control" required value="1">
                            </div>
                            <div>
                                <label>Satuan <span style="color: red;">*</span></label>
                                <input type="text" name="satuan" class="form-control" required value="Dokumen" list="satuanList">
                                <datalist id="satuanList">
                                    <option value="Dokumen">
                                    <option value="Laporan">
                                    <option value="Kegiatan">
                                    <option value="Berkas">
                                    <option value="Peserta">
                                    <option value="Modul">
                                    <option value="Paket">
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <!-- Rujukan Pekerjaan (Opsional) -->
                    <div class="form-group">
                        <label><i class="ph-bold ph-briefcase"></i> Tautkan ke Tugas / Pekerjaan (Opsional)</label>
                        <select name="pekerjaan_id" class="form-control">
                            <option value="">-- Tanpa Rujukan Pekerjaan Tertentu --</option>
                            @foreach($activePekerjaan as $pkj)
                                <option value="{{ $pkj->id }}">
                                    [{{ $pkj->kode_tampilan }}] {{ Str::limit($pkj->judul, 60) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Rujukan Sasaran Kinerja / SKP (Opsional) -->
                    <div class="form-group">
                        <label><i class="ph-bold ph-target"></i> Rujukan Rencana Hasil Kerja SKP (Opsional)</label>
                        <select name="skp_tahunan_id" class="form-control">
                            <option value="">-- Tanpa Rujukan SKP Tertentu --</option>
                            @foreach($skpList as $skp)
                                <option value="{{ $skp->id }}">
                                    [{{ $skp->kode_rhk }}] {{ Str::limit($skp->rencana_hasil_kerja, 65) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bukti Dukung (Lampiran) -->
                    <div class="form-group">
                        <label><i class="ph-bold ph-paperclip"></i> Bukti Dukung / Lampiran (Opsional)</label>
                        <div class="bukti-tabs">
                            <button type="button" class="bukti-tab-btn active" id="tabBtnFile" onclick="switchBuktiTab('file')">
                                <i class="ph-bold ph-file-arrow-up"></i> Upload File
                            </button>
                            <button type="button" class="bukti-tab-btn" id="tabBtnLink" onclick="switchBuktiTab('link')">
                                <i class="ph-bold ph-link"></i> Tautan Link (GDrive / Dokumen)
                            </button>
                            <button type="button" class="bukti-tab-btn" id="tabBtnNone" onclick="switchBuktiTab('none')">
                                <i class="ph-bold ph-x"></i> Tidak Ada
                            </button>
                        </div>
                        <input type="hidden" name="tipe_bukti" id="inputTipeBukti" value="file">

                        <!-- Field File Upload -->
                        <div id="containerBuktiFile">
                            <input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx">
                            <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">
                                Format: PDF, JPG, PNG, DOCX, XLSX (Maks. 10MB)
                            </small>
                        </div>

                        <!-- Field Link Dokumen -->
                        <div id="containerBuktiLink" style="display: none;">
                            <input type="url" name="link_bukti" class="form-control" placeholder="https://drive.google.com/file/d/...">
                            <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">
                                Masukkan tautan link Google Drive atau penyimpanan dokumen cloud Anda
                            </small>
                        </div>

                        <div style="margin-top: 8px;">
                            <input type="text" name="nama_bukti" class="form-control" placeholder="Keterangan nama dokumen bukti (opsional)">
                        </div>
                    </div>
                </div>

                <div class="log-modal-foot">
                    <button type="submit" name="aksi_status" value="draft" style="padding: 8px 16px; border: 1px solid var(--border); background: white; border-radius: 8px; font-size: 12.5px; font-weight: 700; color: #475569; cursor: pointer;">
                        Simpan Draf Saja
                    </button>
                    <button type="submit" name="aksi_status" value="diajukan" class="btn-primary" style="padding: 8px 18px; border: none; border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="ph-bold ph-paper-plane-tilt"></i>
                        <span>Simpan &amp; Ajukan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================================================
         6. MODAL VERIFIKASI / PERSETUJUAN KATIM
         ========================================================================= -->
    <div id="approveLogModal" class="log-modal-backdrop" onclick="closeApproveModalOnBackdrop(event)">
        <div class="log-modal-card" style="max-width: 480px;" onclick="event.stopPropagation()">
            <form id="formApproveLog" method="POST">
                @csrf
                <div class="log-modal-head">
                    <h3>
                        <i class="ph-duotone ph-seal-check" style="color: #10b981;"></i>
                        <span>Persetujuan Log Harian</span>
                    </h3>
                    <button type="button" onclick="closeApproveModal()" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>
                <div class="log-modal-body">
                    <p style="font-size: 13px; color: var(--text-dark); margin-top: 0;" id="approveTargetInfo"></p>

                    <div class="form-group">
                        <label>Nilai Kualitas (0 - 100)</label>
                        <input type="number" step="0.5" name="nilai_kualitas" class="form-control" value="100" min="0" max="100">
                    </div>

                    <div class="form-group">
                        <label>Catatan / Feedback Verifikator (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan hasil verifikasi..."></textarea>
                    </div>
                </div>
                <div class="log-modal-foot">
                    <button type="button" onclick="closeApproveModal()" class="btn-secondary" style="padding: 7px 14px; font-size: 12px;">Batal</button>
                    <button type="submit" class="btn-primary" style="padding: 7px 16px; font-size: 12px; background: #10b981; border: none; color: white; font-weight: 700; border-radius: 8px;">
                        Setujui Log Harian
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Data cache seluruh logs bulan ini dari server untuk interaksi instan tanpa delay
    const MONTH_LOGS = @json($logs);
    const CURRENT_USER_ID = {{ Auth::id() }};
    const IS_ADMIN = {{ Auth::user()->isAdmin() ? 'true' : 'false' }};
    const CAN_MANAGE = {{ $canManage ? 'true' : 'false' }};

    let selectedDateState = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}';

    // Format Tanggal Indonesia
    function formatTanggalIndo(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        return d.toLocaleDateString('id-ID', options);
    }

    // ========== DAY DETAIL MODAL ==========
    function openDayDetailModal(dateStr) {
        selectedDateState = dateStr;
        const titleEl = document.getElementById('modalDateTitle');
        if (titleEl) {
            titleEl.textContent = 'Aktivitas: ' + formatTanggalIndo(dateStr);
        }

        const container = document.getElementById('modalDayLogsContainer');
        container.innerHTML = '';

        // Filter log untuk tanggal yang diklik
        const dayLogs = MONTH_LOGS.filter(l => l.tanggal_kegiatan && l.tanggal_kegiatan.startsWith(dateStr));

        if (dayLogs.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 36px 16px; color: var(--text-muted);">
                    <i class="ph-duotone ph-calendar-blank" style="font-size: 44px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                    <h4 style="font-size: 15px; font-weight: 700; color: var(--text-dark); margin: 0 0 4px 0;">Belum Ada Catatan Log</h4>
                    <p style="font-size: 12.5px; margin: 0;">Tidak ada aktivitas harian yang tercatat pada tanggal ini.</p>
                </div>
            `;
        } else {
            dayLogs.forEach((log, index) => {
                const timeText = (log.jam_mulai && log.jam_selesai) 
                    ? `${log.jam_mulai.substring(0,5)} - ${log.jam_selesai.substring(0,5)}`
                    : (log.jam_mulai ? `Mulai ${log.jam_mulai.substring(0,5)}` : 'Waktu Fleksibel');

                let statusBadge = '';
                if (log.status === 'disetujui_katim' || log.status === 'disetujui_kabal' || log.status === 'disetujui_pokja') {
                    statusBadge = '<span style="background: #ecfdf5; color: #059669; font-size: 10.5px; font-weight: 800; padding: 2px 8px; border-radius: 12px; border: 1px solid #a7f3d0;"><i class="ph-bold ph-check"></i> Disetujui</span>';
                } else if (log.status === 'diajukan') {
                    statusBadge = '<span style="background: #eff6ff; color: #1d4ed8; font-size: 10.5px; font-weight: 800; padding: 2px 8px; border-radius: 12px; border: 1px solid #bfdbfe;"><i class="ph-bold ph-hourglass"></i> Menunggu Verifikasi</span>';
                } else if (log.status.includes('revisi')) {
                    statusBadge = '<span style="background: #fef2f2; color: #b91c1c; font-size: 10.5px; font-weight: 800; padding: 2px 8px; border-radius: 12px; border: 1px solid #fecaca;"><i class="ph-bold ph-warning"></i> Perlu Revisi</span>';
                } else {
                    statusBadge = '<span style="background: #f1f5f9; color: #475569; font-size: 10.5px; font-weight: 800; padding: 2px 8px; border-radius: 12px; border: 1px solid #e2e8f0;">Draf</span>';
                }

                // Tautan Pekerjaan / SKP
                let refBadge = '';
                if (log.pekerjaan) {
                    refBadge += `<div style="font-size: 11px; color: var(--primary); font-weight: 700; margin-top: 4px;"><i class="ph-bold ph-briefcase"></i> Tugas: [${log.pekerjaan.kode_tampilan || 'TUGAS'}] ${log.pekerjaan.judul}</div>`;
                }
                if (log.skp_tahunan) {
                    refBadge += `<div style="font-size: 11px; color: #6f45bd; font-weight: 700; margin-top: 2px;"><i class="ph-bold ph-target"></i> SKP: ${log.skp_tahunan.rencana_hasil_kerja}</div>`;
                }

                // Bukti Dukung
                let buktiHtml = '';
                if (log.bukti_dukung && log.bukti_dukung.length > 0) {
                    buktiHtml = '<div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border); display: flex; gap: 8px; flex-wrap: wrap;">';
                    log.bukti_dukung.forEach(b => {
                        if (b.tipe === 'file') {
                            buktiHtml += `<a href="/storage/${b.file_path}" target="_blank" style="font-size: 11px; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 3px 8px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"><i class="ph-bold ph-file-text"></i> ${b.nama_dokumen}</a>`;
                        } else {
                            buktiHtml += `<a href="${b.link_url}" target="_blank" style="font-size: 11px; font-weight: 700; color: #059669; background: #ecfdf5; padding: 3px 8px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"><i class="ph-bold ph-arrow-square-out"></i> ${b.nama_dokumen || 'Tautan Dokumen'}</a>`;
                        }
                    });
                    buktiHtml += '</div>';
                }

                // Tombol aksi per baris
                let actionsHtml = '';
                const canEditThis = (log.user_id === CURRENT_USER_ID || IS_ADMIN) && log.status !== 'disetujui_katim' && log.status !== 'disetujui_kabal';
                
                if (canEditThis) {
                    actionsHtml += `
                        <a href="/log-harian/${log.id}/edit" style="font-size: 11.5px; font-weight: 700; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                            <i class="ph-bold ph-pencil-simple"></i> Edit
                        </a>
                        <form action="/log-harian/${log.id}" method="POST" onsubmit="return confirm('Hapus catatan log ini?')" style="display: inline;">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" style="background: none; border: none; font-size: 11.5px; font-weight: 700; color: #ef4444; cursor: pointer; display: inline-flex; align-items: center; gap: 3px;">
                                <i class="ph-bold ph-trash"></i> Hapus
                            </button>
                        </form>
                    `;
                    if (log.status === 'draft') {
                        actionsHtml += `
                            <form action="/log-harian/${log.id}/ajukan" method="POST" style="display: inline;">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                <button type="submit" style="background: #eff6ff; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 700; color: #1d4ed8; padding: 2px 7px; border-radius: 6px; cursor: pointer;">
                                    Ajukan Verifikasi
                                </button>
                            </form>
                        `;
                    }
                }

                // Katim Approval Button
                if (CAN_MANAGE && (log.status === 'diajukan' || log.status === 'disetujui_pokja')) {
                    actionsHtml += `
                        <button type="button" onclick="openApproveModal(${log.id}, '${escapeHtml(log.uraian_kegiatan)}')" style="background: #ecfdf5; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 800; color: #047857; padding: 3px 8px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="ph-bold ph-seal-check"></i> Beri Persetujuan
                        </button>
                    `;
                }

                // Catatan Verifikator
                let catatanHtml = '';
                if (log.catatan_katim || log.catatan_pokja) {
                    catatanHtml = `
                        <div style="background: #f8fafc; border-left: 3px solid #f59e0b; padding: 6px 10px; border-radius: 4px; font-size: 11.5px; color: #78350f; margin-top: 8px;">
                            <strong>Catatan Katim/Pokja:</strong> ${escapeHtml(log.catatan_katim || log.catatan_pokja)}
                        </div>
                    `;
                }

                container.innerHTML += `
                    <div class="day-log-card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 6px;">
                            <span style="font-family: monospace; font-size: 11.5px; font-weight: 800; color: var(--text-dark); background: #f8fafc; border: 1px solid var(--border); padding: 2px 6px; border-radius: 4px;">
                                <i class="ph-bold ph-clock"></i> ${timeText}
                            </span>
                            ${statusBadge}
                        </div>

                        <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; line-height: 1.4; margin-bottom: 4px;">
                            ${escapeHtml(log.uraian_kegiatan)}
                        </div>

                        <div style="font-size: 12px; color: var(--text-muted);">
                            Output: <strong>${escapeHtml(log.output_kegiatan)}</strong> &bull; Volume: <strong>${log.volume_capaian} ${escapeHtml(log.satuan)}</strong>
                        </div>

                        ${refBadge}
                        ${buktiHtml}
                        ${catatanHtml}

                        <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
                            ${actionsHtml}
                        </div>
                    </div>
                `;
            });
        }

        const modal = document.getElementById('dayDetailModal');
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDayDetailModal() {
        const modal = document.getElementById('dayDetailModal');
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function closeDayDetailModalOnBackdrop(e) {
        if (e.target.id === 'dayDetailModal') {
            closeDayDetailModal();
        }
    }

    function handleTambahFromModal() {
        closeDayDetailModal();
        openCreateLogModal(selectedDateState);
    }

    // ========== CREATE LOG MODAL ==========
    function openCreateLogModal(dateStr) {
        const inputDate = document.getElementById('inputTanggalKegiatan');
        if (inputDate && dateStr) {
            inputDate.value = dateStr;
        }
        const modal = document.getElementById('createLogModal');
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCreateLogModal() {
        const modal = document.getElementById('createLogModal');
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function closeCreateLogModalOnBackdrop(e) {
        if (e.target.id === 'createLogModal') {
            closeCreateLogModal();
        }
    }

    function switchBuktiTab(type) {
        document.querySelectorAll('.bukti-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('inputTipeBukti').value = type;

        const fileContainer = document.getElementById('containerBuktiFile');
        const linkContainer = document.getElementById('containerBuktiLink');

        if (type === 'file') {
            document.getElementById('tabBtnFile').classList.add('active');
            fileContainer.style.display = 'block';
            linkContainer.style.display = 'none';
        } else if (type === 'link') {
            document.getElementById('tabBtnLink').classList.add('active');
            fileContainer.style.display = 'none';
            linkContainer.style.display = 'block';
        } else {
            document.getElementById('tabBtnNone').classList.add('active');
            fileContainer.style.display = 'none';
            linkContainer.style.display = 'none';
        }
    }

    // ========== APPROVE MODAL ==========
    function openApproveModal(logId, title) {
        const form = document.getElementById('formApproveLog');
        form.action = `/log-harian/${logId}/approve`;
        document.getElementById('approveTargetInfo').textContent = `Menyetujui kegiatan: "${title}"`;
        const modal = document.getElementById('approveLogModal');
        if (modal) {
            modal.classList.add('is-open');
        }
    }

    function closeApproveModal() {
        const modal = document.getElementById('approveLogModal');
        if (modal) {
            modal.classList.remove('is-open');
        }
    }

    function closeApproveModalOnBackdrop(e) {
        if (e.target.id === 'approveLogModal') {
            closeApproveModal();
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Global ESC Listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDayDetailModal();
            closeCreateLogModal();
            closeApproveModal();
        }
    });
</script>
@endpush

