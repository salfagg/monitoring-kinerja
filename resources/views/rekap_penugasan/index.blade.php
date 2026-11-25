@extends('layouts.app')

@section('title', 'Rekap Penugasan & Surat Tugas | TANJIDOR BGTK')
@section('page-title', 'Rekap Penugasan & Surat Tugas')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rekap-penugasan.css') }}">
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
    @php
        $baseFilterParams = request()->except(['filter_tugas', 'expand', 'page']);
        $activeFilterTugas = $filters['filter_tugas'] ?? 'all';
        $isExpandedAll = request()->boolean('expand');
    @endphp
    <div class="kpi-row">
        <a href="{{ route('rekap-penugasan.index', array_merge($baseFilterParams, ['filter_tugas' => 'all'])) }}"
           class="kpi-stat-card is-clickable {{ $activeFilterTugas === 'all' && !$isExpandedAll ? 'is-active' : '' }}"
           title="Klik untuk menampilkan seluruh personel">
            <div class="kpi-icon-wrap" style="background: rgba(11, 74, 130, 0.1); color: var(--navy);">
                <i class="ph-bold ph-users"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_pegawai'] }}</div>
                <div class="kpi-label">Total Personel Terdata</div>
            </div>
        </a>

        <a href="{{ route('rekap-penugasan.index', array_merge($baseFilterParams, ['filter_tugas' => 'ada_tugas'])) }}"
           class="kpi-stat-card is-clickable {{ $activeFilterTugas === 'ada_tugas' && !$isExpandedAll ? 'is-active' : '' }}"
           title="Klik untuk memfilter pegawai yang memiliki penugasan">
            <div class="kpi-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="ph-bold ph-user-check"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_pegawai_bertugas'] }}</div>
                <div class="kpi-label">Pegawai Memiliki Penugasan</div>
            </div>
        </a>

        <a href="{{ route('rekap-penugasan.index', array_merge($baseFilterParams, ['filter_tugas' => 'ada_st'])) }}"
           class="kpi-stat-card is-clickable {{ $activeFilterTugas === 'ada_st' ? 'is-active' : '' }}"
           title="Klik untuk memfilter pegawai yang memiliki Surat Tugas (ST)">
            <div class="kpi-icon-wrap" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="ph-bold ph-file-text"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_st_terbit'] }}</div>
                <div class="kpi-label">Surat Tugas (ST) Terbit</div>
            </div>
        </a>

        <a href="{{ route('rekap-penugasan.index', array_merge($baseFilterParams, ['filter_tugas' => 'ada_tugas', 'expand' => 1])) }}"
           class="kpi-stat-card is-clickable {{ $activeFilterTugas === 'ada_tugas' && $isExpandedAll ? 'is-active' : '' }}"
           title="Klik untuk menampilkan dan membuka seluruh rincian penugasan">
            <div class="kpi-icon-wrap" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                <i class="ph-bold ph-briefcase"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['total_penugasan_balai'] }}</div>
                <div class="kpi-label">Total Penugasan Balai</div>
            </div>
        </a>

        <a href="{{ route('rekap-penugasan.index', array_merge($baseFilterParams, ['filter_tugas' => 'tanpa_tugas'])) }}"
           class="kpi-stat-card is-clickable {{ $activeFilterTugas === 'tanpa_tugas' ? 'is-active' : '' }}"
           title="Klik untuk memfilter personel yang belum memiliki penugasan">
            <div class="kpi-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="ph-bold ph-gauge"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $kpi['rata_beban'] }}</div>
                <div class="kpi-label">Rata-rata Tugas / Orang</div>
            </div>
        </a>
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
                        <option value="ada_st" {{ $filters['filter_tugas'] === 'ada_st' ? 'selected' : '' }}>Hanya yang Memiliki Surat Tugas (ST)</option>
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
                                                <div class="mini-progress-fill" style="width: {{ min(100, $item['tingkat_ketuntasan']) }}%; background: #10b981;"></div>
                                            </div>
                                            <span style="font-size: 11px; font-weight: 700; color: #047857;">
                                                {{ round($item['tingkat_ketuntasan'], 0) }}% Tuntas
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
                                                    <th style="text-align: center; width: 110px;">Laporan Masuk</th>
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
                                                        <div style="font-weight: 700; font-size: 11.5px; color: #1e293b; display: inline-flex; align-items: center; gap: 4px;">
                                                            <i class="ph-bold ph-newspaper-clipping" style="color: #64748b;"></i>
                                                            <span>{{ $tgs['laporan_count'] ?? 0 }} Laporan</span>
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

    @if(request()->boolean('expand'))
    document.addEventListener('DOMContentLoaded', function() {
        expandAllDetails();
    });
    @endif
</script>
@endsection
