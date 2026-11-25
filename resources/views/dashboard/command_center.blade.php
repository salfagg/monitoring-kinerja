<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoring Balai | TANJIDOR BGTK DKI Jakarta</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    <script>
        // Terapkan tema sebelum render agar tidak berkedip
        try {
            const t = localStorage.getItem('tanjidor_theme') || localStorage.getItem('bgtkTheme');
            if (t) document.documentElement.setAttribute('data-theme', t);
        } catch (e) { }

        // Global SweetAlert Helpers untuk Command Center
        window.tanjidorAlert = function (title, text = '', type = 'info') {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (typeof Swal !== 'undefined') {
                return Swal.fire({
                    title: title,
                    text: text,
                    icon: type,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#0b4a82',
                    background: isDark ? '#0f172a' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#0f172a',
                    customClass: { popup: 'cmd-swal-modal' }
                });
            }
            alert(title + (text ? ': ' + text : ''));
        };
    </script>

    <link rel="stylesheet" href="{{ asset('css/tanjidor-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/command-center.css') }}">
</head>

<body class="cmd-page">

    <!-- Top Navigation Progress Bar -->
    <div id="tanjidorTopProgress" aria-hidden="true"></div>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

    <!-- Sidebar Menu Utama -->
    @include('layouts.partials.sidebar')

    <div class="cmd-main-wrapper">

    @php
        $pct = fn($v) => $totalPekerjaan > 0 ? round(($v / $totalPekerjaan) * 100) : 0;
        $ringOffset = round(326.73 * (1 - min(100, max(0, $tingkatKetuntasan)) / 100), 2);
        $medal = fn($i) => $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#' . ($i + 1)));
    @endphp

    <!-- ================= HEADER COMMAND CENTER ================= -->
    <header class="top-command-bar">
        <div class="brand-section">
            <button type="button" class="btn-control btn-icon cmd-mobile-menu-btn" onclick="toggleMobileSidebar()"
                aria-label="Buka Menu Navigasi" title="Menu Navigasi">
                <i class="ph-bold ph-list"></i>
            </button>
            <div class="brand-badge"><i class="ph-bold ph-shield-check"></i></div>
            <div>
                <div class="brand-title">
                    BGTK PROVINSI DKI JAKARTA
                    <span class="live-tag tag-brand">TANJIDOR</span>
                    <span class="live-tag">LIVE</span>
                </div>
                <div class="brand-sub">TANJIDOR &bull; Tata Kelola Kinerja Digital dan Monitoring</div>
            </div>
        </div>

        <div class="live-clock-section">
            <div class="clock-time" id="liveTimeDisplay">00:00:00 WIB</div>
            <div class="clock-date" id="liveDateDisplay">-</div>
        </div>

        <div class="command-controls">
            <button type="button" class="btn-control active" id="btnToggleDiagram" onclick="toggleDiagramBar()"
                title="Sembunyikan / tampilkan diagram">
                <i class="ph-bold ph-chart-donut"></i>
                <span id="diagramToggleLabel">Diagram: Aktif</span>
            </button>

            <button type="button" class="btn-control active" id="btnAutoRefresh" onclick="toggleAutoRefresh()"
                title="Perbarui data otomatis (tombol R)">
                <i class="ph-bold ph-arrows-clockwise"></i>
                <span id="refreshLabel">Segarkan: 30d</span>
            </button>

            <button type="button" class="btn-control" id="btnAutoScroll" onclick="toggleAutoScroll()"
                title="Gulir otomatis layar TV (tombol S)">
                <i class="ph-bold ph-arrows-down-up"></i>
                <span id="scrollLabel">Gulir TV: Mati</span>
            </button>

            <button type="button" class="btn-control" onclick="toggleFullscreen()" title="Layar penuh (tombol F)">
                <i class="ph-bold ph-corners-out"></i>
                <span id="fsLabel">Layar Penuh</span>
            </button>

            <button type="button" class="btn-control btn-icon" onclick="toggleTheme()"
                title="Ganti tema terang / gelap (tombol T)">
                <i class="ph-bold ph-moon-stars" id="themeIcon"></i>
            </button>

            <a href="{{ route('dashboard') }}" class="btn-control" title="Kembali ke dashboard utama">
                <i class="ph-bold ph-arrow-square-out"></i>
                <span>Portal</span>
            </a>
        </div>
    </header>

    <main class="command-container">

        <!-- ================= HERO + KPI ================= -->
        <div class="overview-grid">

            <section class="hero-card">
                <div class="hero-glow"></div>
                <div class="hero-ring">
                    <svg viewBox="0 0 120 120" aria-hidden="true">
                        <defs>
                            <linearGradient id="ringGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#22d3ee" />
                                <stop offset="100%" stop-color="#34d399" />
                            </linearGradient>
                        </defs>
                        <circle class="ring-bg" cx="60" cy="60" r="52" />
                        <circle class="ring-fg" cx="60" cy="60" r="52" style="--off: {{ $ringOffset }}" />
                    </svg>
                    <div class="ring-center">
                        <span class="ring-num"><span class="count-up" data-count="{{ $tingkatKetuntasan }}"
                                data-decimals="0">{{ round($tingkatKetuntasan) }}</span><small>%</small></span>
                        <span class="ring-lbl">Tuntas</span>
                    </div>
                </div>
                <div class="hero-text">
                    <span class="hero-eyebrow"><i class="ph-bold ph-sparkle"></i> Ringkasan Kinerja Balai</span>
                    <h2>{{ $selesaiTerverifikasi }} dari {{ $totalPekerjaan }} tugas telah tuntas</h2>
                    <p>
                        @if($lewatTenggat > 0)
                            Ada <b>{{ $lewatTenggat }} tugas</b> melewati tenggat. Prioritaskan tindak lanjut bersama Katim
                            terkait.
                        @else
                            Seluruh tugas berjalan tepat waktu. Pertahankan ritme pelaporan tim.
                        @endif
                    </p>
                    <div class="hero-chips">
                        <span class="chip chip-ok" title="Persentase tugas yang tidak melewati tenggat"><i
                                class="ph-bold ph-timer"></i> Tepat waktu {{ $onTimeRate }}%</span>
                        <span class="chip"><i class="ph-bold ph-paper-plane-tilt"></i> {{ $totalLaporanBalai }}
                            laporan</span>
                        <span class="chip"><i class="ph-bold ph-file-text"></i> {{ $totalKegiatanSt }} kegiatan
                            ST</span>
                    </div>
                </div>
            </section>

            <div class="giant-kpi-grid">
                <div class="kpi-box kpi-blue">
                    <span class="kpi-title">Total Tugas</span>
                    <div class="kpi-icon-wrap"><i class="ph-duotone ph-tree-structure"></i></div>
                    <div class="kpi-number"><span class="count-up"
                            data-count="{{ $totalPekerjaan }}">{{ number_format($totalPekerjaan) }}</span></div>
                    <div class="kpi-subtext"><i class="ph-bold ph-stack"></i> Seluruh penugasan balai</div>
                    <div class="kpi-bar"><i style="width: 100%"></i></div>
                </div>

                <div class="kpi-box kpi-cyan">
                    <span class="kpi-title">Sedang Dikerjakan</span>
                    <div class="kpi-icon-wrap"><i class="ph-duotone ph-rocket-launch"></i></div>
                    <div class="kpi-number"><span class="count-up"
                            data-count="{{ $pekerjaanBerjalan }}">{{ number_format($pekerjaanBerjalan) }}</span></div>
                    <div class="kpi-subtext"><i class="ph-bold ph-spinner"></i> {{ $pct($pekerjaanBerjalan) }}% dari
                        total</div>
                    <div class="kpi-bar"><i style="width: {{ $pct($pekerjaanBerjalan) }}%"></i></div>
                </div>

                <div class="kpi-box kpi-purple">
                    <span class="kpi-title">Menunggu Verifikasi</span>
                    <div class="kpi-icon-wrap"><i class="ph-duotone ph-seal-check"></i></div>
                    <div class="kpi-number"><span class="count-up"
                            data-count="{{ $menungguVerifikasi }}">{{ number_format($menungguVerifikasi) }}</span></div>
                    <div class="kpi-subtext"><i class="ph-bold ph-clock"></i> Menunggu tinjauan Katim</div>
                    <div class="kpi-bar"><i style="width: {{ $pct($menungguVerifikasi) }}%"></i></div>
                </div>

                <div class="kpi-box kpi-green">
                    <span class="kpi-title">Selesai</span>
                    <div class="kpi-icon-wrap"><i class="ph-duotone ph-check-fat"></i></div>
                    <div class="kpi-number"><span class="count-up"
                            data-count="{{ $selesaiTerverifikasi }}">{{ number_format($selesaiTerverifikasi) }}</span>
                    </div>
                    <div class="kpi-subtext"><i class="ph-bold ph-check"></i> Hasil terverifikasi</div>
                    <div class="kpi-bar"><i style="width: {{ $pct($selesaiTerverifikasi) }}%"></i></div>
                </div>

                <div class="kpi-box kpi-amber">
                    <span class="kpi-title">Laporan Masuk</span>
                    <div class="kpi-icon-wrap"><i class="ph-duotone ph-paper-plane-tilt"></i></div>
                    <div class="kpi-number"><span class="count-up"
                            data-count="{{ $totalLaporanBalai }}">{{ number_format($totalLaporanBalai) }}</span></div>
                    <div class="kpi-subtext"><i class="ph-bold ph-newspaper-clipping"></i> Progres dihimpun balai</div>
                    <div class="kpi-bar"><i style="width: 100%"></i></div>
                </div>

                <div class="kpi-box {{ $lewatTenggat > 0 ? 'kpi-red is-alert' : 'kpi-green' }}">
                    <span class="kpi-title">Terlambat</span>
                    <div class="kpi-icon-wrap">
                        <i class="ph-duotone {{ $lewatTenggat > 0 ? 'ph-warning-octagon' : 'ph-check-circle' }}"></i>
                    </div>
                    <div class="kpi-number"><span class="count-up"
                            data-count="{{ $lewatTenggat }}">{{ number_format($lewatTenggat) }}</span></div>
                    <div class="kpi-subtext">
                        @if($lewatTenggat > 0)
                            <i class="ph-bold ph-warning"></i> Perlu perhatian khusus
                        @else
                            <i class="ph-bold ph-check"></i> 100% tepat waktu
                        @endif
                    </div>
                    <div class="kpi-bar"><i style="width: {{ $pct($lewatTenggat) }}%"></i></div>
                </div>
            </div>
        </div>

        <!-- ================= DIAGRAM ANALITIK ================= -->
        <div class="diagram-section-wrapper" id="diagramSectionWrapper">
            <div class="diagram-header-bar">
                <div class="diagram-title">
                    <span class="title-ico"><i class="ph-bold ph-chart-donut"></i></span>
                    <span>Diagram Analitik Kinerja Real-Time Balai</span>
                </div>
                <div class="diagram-actions">
                    <span class="diagram-hint">Klik status, bar, atau nama untuk menyaring &amp; membuka detail</span>
                    <button type="button" class="diagram-btn-toggle" onclick="toggleDiagramBar()">
                        <i class="ph-bold ph-caret-up" id="diagramToggleIcon"></i> <span
                            id="diagramBtnText">Tutup</span>
                    </button>
                </div>
            </div>

            <div class="diagram-content-grid" id="diagramGridContainer">

                <!-- Donut Status -->
                <div class="tile diagram-donut-box">
                    <div class="tile-head"><span><i class="ph-bold ph-chart-donut"></i> Komposisi Status</span></div>
                    <div class="donut-body">
                        <div class="donut-canvas-wrap">
                            <canvas id="cmdDonutStatus"></canvas>
                            <div class="donut-center-text">
                                <span class="donut-num" id="donutNum">{{ $totalPekerjaan }}</span>
                                <span class="donut-lbl">Total Tugas</span>
                            </div>
                        </div>
                        <div class="donut-legend-mini" id="donutLegend">
                            <div class="legend-pill" onclick="filterTableByStatusClick('selesai')"
                                title="Filter tugas selesai">
                                <span class="legend-left"><span class="dot-status dot-selesai"></span>Selesai</span>
                                <strong>{{ $statusCounts['selesai'] ?? 0 }}</strong>
                            </div>
                            <div class="legend-pill" onclick="filterTableByStatusClick('berjalan')"
                                title="Filter tugas dikerjakan">
                                <span class="legend-left"><span
                                        class="dot-status dot-dikerjakan"></span>Dikerjakan</span>
                                <strong>{{ $statusCounts['dikerjakan'] ?? 0 }}</strong>
                            </div>
                            <div class="legend-pill" onclick="filterTableByStatusClick('menunggu_verifikasi')"
                                title="Filter menunggu verifikasi">
                                <span class="legend-left"><span
                                        class="dot-status dot-verifikasi"></span>Verifikasi</span>
                                <strong>{{ $statusCounts['menunggu_verifikasi'] ?? 0 }}</strong>
                            </div>
                            <div class="legend-pill static">
                                <span class="legend-left"><span class="dot-status dot-belum"></span>Belum Dimulai</span>
                                <strong>{{ $statusCounts['belum_dimulai'] ?? 0 }}</strong>
                            </div>
                            <div class="legend-pill pill-late" onclick="filterTableByStatusClick('terlambat')"
                                title="Filter tugas terlambat">
                                <span class="legend-left"><span class="dot-status dot-late"></span>Terlambat</span>
                                <strong>{{ $lewatTenggat }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tren Laporan -->
                <div class="tile diagram-trend-box">
                    <div class="tile-head">
                        <span><i class="ph-bold ph-trend-up"></i> Tren Laporan Masuk</span>
                        <span class="tile-sub" id="trendSummary">14 hari terakhir</span>
                    </div>
                    <div class="chart-canvas-wrap"><canvas id="cmdTrend"></canvas></div>
                </div>

                <!-- Bar Chart -->
                <div class="tile diagram-bar-box">
                    <div class="tile-head">
                        <div class="tile-title-stack">
                            <span id="chartModeTitle"><i class="ph-bold ph-chart-bar"></i> Ketuntasan per Tim
                                Kerja</span>
                            <span id="chartModeSub" class="tile-sub accent">(Balai:
                                {{ $selesaiTerverifikasi }}/{{ $totalPekerjaan }} Tuntas)</span>
                        </div>
                        <div class="cmd-chart-tabs">
                            <button type="button" class="btn-cmd-chart-tab active" id="btnChartModePokja"
                                onclick="switchChartMode('pokja')" title="Ketuntasan per Tim Kerja">
                                <i class="ph-bold ph-buildings"></i> Tim
                            </button>
                            <button type="button" class="btn-cmd-chart-tab" id="btnChartModeSt"
                                onclick="switchChartMode('st')" title="Pegawai paling sering ikut Surat Tugas (ST)">
                                <i class="ph-bold ph-file-text"></i> ST
                            </button>
                            <button type="button" class="btn-cmd-chart-tab" id="btnChartModeLaporan"
                                onclick="switchChartMode('laporan')" title="Pegawai paling aktif melapor">
                                <i class="ph-bold ph-paper-plane-tilt"></i> Lapor
                            </button>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap"><canvas id="cmdBarPokja"></canvas></div>
                </div>

                <!-- Leaderboard -->
                <div class="tile diagram-leaderboard-box">
                    <div class="leaderboard-tabs-header">
                        <button type="button" class="btn-lead-tab active" id="tabLeadSt"
                            onclick="switchLeaderboardTab('st')">
                            <i class="ph-bold ph-medal"></i> Top Penugasan
                        </button>
                        <button type="button" class="btn-lead-tab" id="tabLeadReport"
                            onclick="switchLeaderboardTab('report')">
                            <i class="ph-bold ph-fire"></i> Top Aktifitas
                        </button>
                    </div>

                    <div class="lead-list-panel" id="panelLeadSt">
                        @forelse($topStPersonnel as $idx => $st)
                            <div class="lead-item" onclick="openPersonModalByUserId({{ $st->id }})"
                                title="Klik untuk melihat detail penugasan {{ $st->name }}">
                                <div class="lead-rank">{{ $medal($idx) }}</div>
                                <div class="lead-info">
                                    <div class="lead-name" title="{{ $st->name }}">{{ $st->name }}</div>
                                    <div class="lead-sub">{{ $st->pokja_kode }} • {{ $st->total_semua_tugas }} Total Tugas
                                    </div>
                                </div>
                                <div class="lead-score score-st"
                                    title="{{ $st->st_count }} kegiatan berbasis Surat Tugas (ST)">
                                    <i class="ph-bold ph-file-text"></i> {{ $st->st_count }} ST
                                </div>
                            </div>
                        @empty
                            <div class="lead-empty">Belum ada penugasan ST</div>
                        @endforelse
                    </div>

                    <div class="lead-list-panel" id="panelLeadReport" style="display: none;">
                        @forelse($topReportPersonnel as $idx => $rp)
                            <div class="lead-item" onclick="openPersonModalByUserId({{ $rp->id }})"
                                title="Klik untuk melihat riwayat laporan {{ $rp->name }}">
                                <div class="lead-rank">{{ $medal($idx) }}</div>
                                <div class="lead-info">
                                    <div class="lead-name" title="{{ $rp->name }}">{{ $rp->name }}</div>
                                    <div class="lead-sub">{{ $rp->pokja_kode }} • {{ $rp->total_semua_tugas }} Total Tugas
                                    </div>
                                </div>
                                <div class="lead-score score-rep" title="{{ $rp->laporan_count }} laporan progres diajukan">
                                    <i class="ph-bold ph-paper-plane-tilt"></i> {{ $rp->laporan_count }} Lapor
                                </div>
                            </div>
                        @empty
                            <div class="lead-empty">Belum ada laporan progres</div>
                        @endforelse
                    </div>

                    <div class="lead-footer-stats">
                        <span title="Total pekerjaan yang memiliki nomor Surat Tugas resmi"><i
                                class="ph-bold ph-file-text"></i>
                            <strong>{{ $totalKegiatanSt }}</strong> Kegiatan ST</span>
                        <span>•</span>
                        <span title="Total laporan progres yang telah dihimpun balai"><i
                                class="ph-bold ph-paper-plane-tilt"></i>
                            <strong>{{ $totalLaporanBalai }}</strong> Laporan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= BODY ================= -->
        <div class="command-body-grid">

            <!-- Kolom kiri -->
            <div class="left-col">
                <div class="panel-card side-card">

                    <div class="side-switch" role="tablist">
                        <button type="button" class="side-tab active" id="sideTabPokja"
                            onclick="switchSidePane('pokja')">
                            <i class="ph-bold ph-buildings"></i> Tim
                            <span class="side-count">{{ $pokjas->count() }}</span>
                        </button>
                        <button type="button" class="side-tab" id="sideTabStaf" onclick="switchSidePane('staf')">
                            <i class="ph-bold ph-users-three"></i> Personil
                            <span class="side-count">{{ $topAssignees->count() }}</span>
                        </button>
                        <button type="button" class="side-tab" id="sideTabRadar" onclick="switchSidePane('radar')">
                            <i class="ph-bold ph-radar"></i> Radar
                            <span
                                class="side-count {{ count($upcomingDeadlines) > 0 ? 'count-warn' : '' }}">{{ count($upcomingDeadlines) }}</span>
                        </button>
                    </div>

                    <!-- Panel: Capaian Tim Kerja -->
                    <div class="side-pane" id="panePokja">
                        <div class="scroll-area pokja-cards-container" id="pokjaScrollContainer">
                            <div class="pokja-card-grid">
                                <a href="{{ route('monitoring.balai') }}"
                                    class="pokja-card-item pokja-item-all pokja-item {{ !request('pokja_id') ? 'active' : '' }}">
                                    <div class="pcard-all-left">
                                        <div class="pcard-all-icon"><i class="ph-bold ph-squares-four"></i></div>
                                        <div>
                                            <div class="pcard-all-title">Semua Unit Kerja &amp; Tim</div>
                                            <div class="pcard-all-sub">Monitoring terpadu seluruh balai</div>
                                        </div>
                                    </div>
                                    <span class="pcard-all-badge">{{ $totalPekerjaan }} tugas</span>
                                </a>

                                @foreach($pokjas as $p)
                                    @php 
                                        $pPct = $p->total_tugas > 0 ? round(($p->tugas_selesai / $p->total_tugas) * 100, 1) : 0; 
                                        $isFullDone = ($p->total_tugas > 0 && $p->tugas_selesai == $p->total_tugas);
                                    @endphp
                                    <a href="{{ route('monitoring.balai', array_merge(request()->except(['pokja_id']), ['pokja_id' => $p->id])) }}"
                                        class="pokja-card-item pokja-item {{ $p->total_tugas == 0 ? 'is-empty' : '' }} {{ request('pokja_id') == $p->id ? 'active' : '' }}"
                                        title="{{ $p->nama_pokja }} (Katim: {{ $p->katim_name }})">
                                        
                                        <div class="pcard-header">
                                            <div class="pcard-title-wrap">
                                                <span class="pcard-badge-code">{{ $p->kode_pokja }}</span>
                                                <span class="pcard-name-clean">{{ $p->nama_pokja }}</span>
                                            </div>
                                            <span class="pcard-ratio-badge {{ $p->total_tugas > 0 ? 'ratio-has-tasks' : 'ratio-empty' }}">
                                                {{ $p->total_tugas > 0 ? $p->tugas_selesai . '/' . $p->total_tugas : '0 tugas' }}
                                            </span>
                                        </div>

                                        <div class="pcard-katim-row">
                                            <i class="ph-bold ph-user-circle"></i>
                                            <span>Katim: <strong>{{ $p->katim_name }}</strong></span>
                                        </div>

                                        <div class="pcard-progress-box">
                                            <div class="pcard-progress-track">
                                                <div class="pcard-progress-bar {{ $isFullDone ? 'bar-complete' : '' }}" style="width: {{ $pPct }}%;"></div>
                                            </div>
                                            <div class="pcard-progress-foot">
                                                <span class="pcard-pct-label">{{ $p->total_tugas > 0 ? round($pPct, 0) . '% tuntas' : 'Belum ada tugas' }}</span>
                                                @if($p->tugas_terlambat > 0)
                                                    <span class="pcard-late-chip" title="{{ $p->tugas_terlambat }} tugas terlambat">
                                                        <i class="ph-bold ph-warning"></i> {{ $p->tugas_terlambat }} terlambat
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Panel: Beban Kerja Personil -->
                    <div class="side-pane" id="paneStaf" hidden>
                        
                        <!-- Toolbar Personil Modern & Rapi -->
                        <div class="assignee-toolbar-box">
                            <div class="assignee-search-wrap">
                                <i class="ph-bold ph-magnifying-glass"></i>
                                <input type="text" id="assigneeSearchInput" placeholder="Cari personil, NIP, atau unit kerja..."
                                    class="assignee-search-input" oninput="filterAssigneeRows(this.value)">
                            </div>

                            <div class="assignee-controls-bar">
                                <div class="assignee-tabs-mini">
                                    <button type="button" class="btn-assignee-tab active"
                                        onclick="switchAssigneeTab('all', this)">
                                        Semua <span class="tab-chip-count">{{ $topAssignees->count() }}</span>
                                    </button>
                                    <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('pegawai', this)">
                                        Pelaksana <span class="tab-chip-count">{{ $topAssignees->filter(fn($u) => in_array($u->role, ['pegawai', 'tim_pokja']))->count() }}</span>
                                    </button>
                                    <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('katim', this)">
                                        Katim &amp; Koor <span class="tab-chip-count">{{ $topAssignees->filter(fn($u) => in_array($u->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']))->count() }}</span>
                                    </button>
                                </div>

                                <div class="assignee-sort-strip">
                                    <span class="sort-lbl"><i class="ph-bold ph-arrows-down-up"></i> Urut:</span>
                                    <div class="sort-pills">
                                        <button type="button" class="btn-sort-pill active" id="sortBtnActive"
                                            onclick="sortAssigneeList('active', this)"
                                            title="Urutkan dari beban tugas berjalan">Beban</button>
                                        <button type="button" class="btn-sort-pill" id="sortBtnSt"
                                            onclick="sortAssigneeList('st', this)" title="Paling banyak ikut Surat Tugas">Sering ST</button>
                                        <button type="button" class="btn-sort-pill" id="sortBtnReport"
                                            onclick="sortAssigneeList('report', this)" title="Paling aktif lapor progres">Aktif Lapor</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="scroll-area assignee-cards-container" id="assigneeListContainer">
                            @foreach($topAssignees as $staf)
                                @php
                                    $isKatimOrKoor = in_array($staf->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']);
                                    $roleCategory = $isKatimOrKoor ? 'katim' : 'pegawai';
                                    $totalActiveVal = $staf->total_active ?? ($staf->pic_count + $staf->member_count);
                                    $stCountVal = $staf->st_count ?? 0;
                                    $repCountVal = $staf->laporan_count ?? 0;

                                    if ($staf->role === 'kepala_balai' || str_contains(strtolower($staf->jabatan ?? ''), 'kepala balai')) {
                                        $avatarGrad = 'grad-pimpinan';
                                        $rolePillStyle = 'badge-pimpinan';
                                    } elseif ($isKatimOrKoor) {
                                        $avatarGrad = 'grad-katim';
                                        $rolePillStyle = 'badge-katim';
                                    } elseif ($staf->role === 'admin') {
                                        $avatarGrad = 'grad-admin';
                                        $rolePillStyle = 'badge-admin';
                                    } else {
                                        $avatarGrad = 'grad-staff';
                                        $rolePillStyle = 'badge-staff';
                                    }
                                @endphp
                                <div class="assignee-card-item assignee-item" data-role-category="{{ $roleCategory }}"
                                    data-name="{{ strtolower($staf->name) }}" data-fullname="{{ $staf->name }}"
                                    data-active-count="{{ $totalActiveVal }}" data-st-count="{{ $stCountVal }}"
                                    data-rep-count="{{ $repCountVal }}" data-staf="{{ json_encode($staf) }}"
                                    onclick="openPersonModal(this)"
                                    title="Klik untuk melihat rincian penugasan & laporan {{ $staf->name }}">
                                    
                                    <div class="acard-header">
                                        <div class="acard-avatar {{ $avatarGrad }}">{{ strtoupper(substr($staf->name, 0, 2)) }}</div>
                                        <div class="acard-main">
                                            <div class="acard-name-row">
                                                <span class="acard-name" title="{{ $staf->name }}">{{ $staf->name }}</span>
                                            </div>
                                            <div class="acard-sub">
                                                <span class="acard-unit-code">{{ $staf->pokja_kode ?? 'BGTK' }}</span>
                                                <span class="acard-role-pill {{ $rolePillStyle }}">{{ $staf->role_label }}</span>
                                            </div>
                                        </div>
                                        <div class="acard-workload-wrap">
                                            <span class="acard-workload-pill {{ $totalActiveVal > 0 ? 'workload-busy' : 'workload-idle' }}">
                                                {{ $totalActiveVal }} tugas
                                            </span>
                                            <button type="button"
                                                onclick="event.stopPropagation(); filterTableByAssignee('{{ addslashes($staf->name) }}')"
                                                class="btn-filter-icon"
                                                title="Saring tabel utama berdasarkan {{ $staf->name }}">
                                                <i class="ph-bold ph-funnel"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="acard-metrics-row">
                                        @if($stCountVal > 0)
                                            <span class="acard-metric-chip chip-st"
                                                title="{{ $stCountVal }} kegiatan berbasis Surat Tugas (ST)">
                                                <i class="ph-bold ph-file-text"></i> <b>{{ $stCountVal }}</b> ST
                                            </span>
                                        @endif
                                        @if($repCountVal > 0)
                                            <span class="acard-metric-chip chip-rep"
                                                title="{{ $repCountVal }} laporan progres diajukan">
                                                <i class="ph-bold ph-paper-plane-tilt"></i> <b>{{ $repCountVal }}</b> Lapor
                                            </span>
                                        @endif
                                        @if($stCountVal == 0 && $repCountVal == 0)
                                            <span class="acard-metric-chip chip-idle">
                                                <i class="ph-bold ph-check"></i> Siap Tugas
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="assignee-footer">
                            <i class="ph-bold ph-cursor-click"></i> Klik kartu personil untuk melihat detail tugas &amp; laporan
                        </div>
                    </div>

                    <!-- Panel: Radar (tenggat + aktivitas) -->
                    <div class="side-pane" id="paneRadar" hidden>
                        <div class="scroll-area">
                            <div class="radar-title"><span><i class="ph-bold ph-alarm"></i> Tenggat 7 Hari ke
                                    Depan</span><em>{{ count($upcomingDeadlines) }}</em></div>
                            @forelse($upcomingDeadlines as $d)
                                <div class="radar-item" onclick="openJobModalById({{ $d['id'] }})"
                                    title="{{ $d['judul'] }}">
                                    <div
                                        class="radar-days {{ $d['sisa_hari'] <= 1 ? 'urgent' : ($d['sisa_hari'] <= 3 ? 'soon' : '') }}">
                                        <b>{{ $d['sisa_hari'] === 0 ? 'Hari' : 'H-' . $d['sisa_hari'] }}</b>
                                        <span>{{ $d['sisa_hari'] === 0 ? 'ini' : 'hari' }}</span>
                                    </div>
                                    <div class="radar-info">
                                        <div class="radar-name">{{ $d['judul'] }}</div>
                                        <div class="radar-meta"><span class="task-code">{{ $d['kode'] }}</span>
                                            {{ $d['pokja'] }} • {{ $d['pic'] }} • {{ $d['tenggat'] }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="lead-empty">Tidak ada tenggat dalam 7 hari ke depan 🎉</div>
                            @endforelse

                            <div class="radar-title"><span><i class="ph-bold ph-lightning"></i> Aktivitas
                                    Terbaru</span><em>{{ count($recentActivity) }}</em></div>
                            <div class="feed-list">
                                @forelse($recentActivity as $a)
                                    <div class="feed-item" onclick="openJobModalById({{ $a['pekerjaan_id'] }})">
                                        <div
                                            class="feed-dot {{ $a['status_approval'] === 'disetujui' ? 'ok' : ($a['status_approval'] === 'ditolak' ? 'bad' : 'wait') }}">
                                        </div>
                                        <div class="feed-body">
                                            <div class="feed-line"><b>{{ $a['pelapor'] }}</b>
                                                {{ $a['is_penyelesaian'] ? 'menyelesaikan' : 'melaporkan progres' }}
                                                <span class="task-code">{{ $a['kode'] }}</span>
                                            </div>
                                            <div class="feed-sub">{{ $a['judul'] }}</div>
                                            <div class="feed-time"><i class="ph-bold ph-clock"></i> {{ $a['waktu'] }} •
                                                {{ $a['pokja'] }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="lead-empty">Belum ada aktivitas laporan</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Kolom kanan: tabel / kartu -->
            <div class="panel-card right-col" id="streamPanel">

                <div class="stream-controls">
                    <div class="stream-tabs">
                        <a href="{{ route('monitoring.balai', request()->except(['status'])) }}"
                            class="tab-btn {{ !request('status') ? 'active' : '' }}">
                            Semua <span class="num">{{ $totalPekerjaan }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'berjalan'])) }}"
                            class="tab-btn {{ request('status') === 'berjalan' ? 'active' : '' }}">
                            Dikerjakan <span class="num">{{ $pekerjaanBerjalan }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'menunggu_verifikasi'])) }}"
                            class="tab-btn {{ request('status') === 'menunggu_verifikasi' ? 'active' : '' }}">
                            Verifikasi <span class="num">{{ $menungguVerifikasi }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'terlambat'])) }}"
                            class="tab-btn {{ $lewatTenggat > 0 ? 'tab-danger' : '' }} {{ request('status') === 'terlambat' ? 'active' : '' }}">
                            Terlambat <span class="num">{{ $lewatTenggat }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'selesai'])) }}"
                            class="tab-btn {{ request('status') === 'selesai' ? 'active' : '' }}">
                            Selesai <span class="num">{{ $selesaiTerverifikasi }}</span>
                        </a>
                    </div>

                    <form method="GET" action="{{ route('monitoring.balai') }}" class="stream-form">
                        @if(request('pokja_id')) <input type="hidden" name="pokja_id" value="{{ request('pokja_id') }}">
                        @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

                        <div class="stream-search">
                            <i class="ph-bold ph-magnifying-glass"></i>
                            <input type="text" name="q" id="searchFilterInput" value="{{ request('q') }}"
                                placeholder="Cari tugas, kode, atau PIC...  ( / )"
                                onkeyup="clientFilterTable(this.value)">
                        </div>

                        @if(request()->hasAny(['pokja_id', 'status', 'q']))
                            <a href="{{ route('monitoring.balai') }}" class="btn-reset" title="Hapus semua filter">
                                <i class="ph-bold ph-x"></i> Hapus filter
                            </a>
                        @endif

                        <div class="view-switch" title="Ganti tampilan (tombol V)">
                            <button type="button" class="btn-view active" id="btnViewTable"
                                onclick="setView('table')"><i class="ph-bold ph-table"></i></button>
                            <button type="button" class="btn-view" id="btnViewCards" onclick="setView('cards')"><i
                                    class="ph-bold ph-squares-four"></i></button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive" id="tableScrollContainer">
                    <table class="command-table" id="commandTable">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Kode</th>
                                <th>Nama Tugas</th>
                                <th style="width: 85px;">Unit</th>
                                <th style="width: 180px;">Penanggung Jawab</th>
                                <th style="width: 115px;">Tenggat</th>
                                <th style="width: 130px;">Laporan Masuk</th>
                                <th style="width: 140px;">Status</th>
                                <th style="width: 85px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pekerjaanList as $job)
                                @php
                                    $isTerlambat = $job->isTerlambat();
                                    $sisaHari = (int) \Carbon\Carbon::today()->diffInDays($job->tenggat, false);
                                    $rowLate = $isTerlambat && $job->status_pelaksanaan !== 'selesai_terverifikasi';
                                    $picName = $job->penanggungJawab?->name ?? 'PJ';

                                    $statusLabel = match ($job->status_pelaksanaan) {
                                        'selesai_terverifikasi' => 'Selesai',
                                        'hasil_diajukan' => 'Menunggu Verifikasi',
                                        'dikerjakan' => 'Dikerjakan',
                                        'perlu_perbaikan_hasil' => 'Perlu Revisi',
                                        default => 'Belum Dimulai'
                                    };
                                    $statusClass = match ($job->status_pelaksanaan) {
                                        'selesai_terverifikasi' => 'st-selesai',
                                        'hasil_diajukan' => 'st-verifikasi',
                                        'dikerjakan' => 'st-berjalan',
                                        'perlu_perbaikan_hasil' => 'st-terlambat',
                                        default => 'st-belum'
                                    };

                                    $payload = [
                                        'id' => $job->id,
                                        'kode' => $job->kode_tampilan,
                                        'judul' => $job->judul,
                                        'deskripsi' => $job->deskripsi ?? 'Belum ada deskripsi rinci untuk pekerjaan ini.',
                                        'hasil_diharapkan' => $job->hasil_diharapkan ?? 'Tercapainya output kegiatan sesuai sasaran.',
                                        'target' => number_format($job->target, 0) . ' ' . $job->satuan,
                                        'laporan_acc_count' => $job->realisasi->where('status_approval', 'disetujui')->count(),
                                        'tenggat' => $job->tenggat ? $job->tenggat->format('d/m/Y') : '-',
                                        'is_terlambat' => $isTerlambat,
                                        'sisa_hari' => $sisaHari,
                                        'nomor_st' => $job->nomor_st,
                                        'status_raw' => $job->status_pelaksanaan,
                                        'status_label' => $statusLabel,
                                        'status_class' => $statusClass,
                                        'pokja' => $job->pokja ? $job->pokja->kode_pokja . ' - ' . $job->pokja->nama_pokja : 'BGTK DKI Jakarta',
                                        'pokja_code' => $job->pokja?->kode_pokja ?? 'BGTK',
                                        'parent' => $job->parent ? $job->parent->kode_tampilan . ' - ' . $job->parent->judul : null,
                                        'pj_nama' => $job->penanggungJawab?->name ?? 'Belum Ditugaskan',
                                        'pj_nip' => $job->penanggungJawab?->nip ? 'NIP: ' . $job->penanggungJawab->nip : 'Tenaga Alih Daya / Non-PNS',
                                        'pj_jabatan' => $job->penanggungJawab?->jabatan ?? 'Pegawai',
                                        'pj_initial' => strtoupper(substr($picName, 0, 2)),
                                        'pelaksana' => $job->pelaksana->map(fn($p) => [
                                            'name' => $p->name,
                                            'jabatan' => $p->jabatan ?? 'Pegawai',
                                            'initial' => strtoupper(substr($p->name, 0, 2)),
                                        ])->toArray(),
                                        'url_show' => route('pekerjaan.show', $job),
                                        'last_update' => $job->realisasi->first() ? $job->realisasi->first()->created_at->format('d/m/Y H:i') : null,
                                        'last_note' => $job->realisasi->first()?->catatan ?? null,
                                        'laporan_list' => $job->realisasi->map(fn($r) => [
                                            'id' => $r->id,
                                            'pelapor' => $r->user?->name ?? 'Pegawai Pelaksana',
                                            'pelapor_initial' => strtoupper(substr($r->user?->name ?? 'P', 0, 2)),
                                            'tanggal' => $r->tanggal ? \Carbon\Carbon::parse($r->tanggal)->format('d M Y') : $r->created_at->format('d M Y'),
                                            'jenis_laporan' => $r->jenis_laporan ?? 'kemajuan',
                                            'is_penyelesaian' => $r->isPenyelesaian(),
                                            'status_approval' => $r->status_approval,
                                            'status_label' => match ($r->status_approval) {
                                                'disetujui' => 'Disetujui',
                                                'ditolak' => 'Perlu Perbaikan',
                                                default => 'Menunggu Persetujuan'
                                            },
                                            'deskripsi' => $r->deskripsi_progres,
                                            'kendala' => $r->kendala,
                                            'tindak_lanjut' => $r->tindak_lanjut,
                                            'catatan_penolakan' => $r->catatan_penolakan,
                                            'file_bukti' => $r->file_bukti ? asset('storage/' . $r->file_bukti) : null,
                                            'file_name' => $r->file_bukti ? basename($r->file_bukti) : null,
                                            'link_bukti' => $r->link_bukti,
                                        ])->values()->toArray(),
                                    ];
                                @endphp
                                <tr class="table-row-item {{ $rowLate ? 'row-late' : '' }}"
                                    data-job="{{ json_encode($payload) }}"
                                    title="Klik untuk melihat rincian tugas {{ $job->kode_tampilan }}">
                                    <td><span class="task-code">{{ $job->kode_tampilan }}</span></td>

                                    <td>
                                        <div class="task-title-cell">
                                            <span class="task-title-text" title="{{ $job->judul }}">{{ $job->judul }}</span>
                                            @if($job->nomor_st)
                                                <span class="st-badge-mini" title="Surat Tugas Nomor: {{ $job->nomor_st }}"><i
                                                        class="ph-bold ph-file-text"></i> ST</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td><span class="task-pokja-badge">{{ $job->pokja?->kode_pokja ?? 'BGTK' }}</span></td>

                                    <td>
                                        <div class="pic-cell">
                                            <div class="pic-avatar-mini">{{ strtoupper(substr($picName, 0, 2)) }}</div>
                                            <div class="pic-name-wrap">
                                                <div class="pic-name-clean"
                                                    title="{{ $job->penanggungJawab->name ?? '-' }}">
                                                    {{ $job->penanggungJawab->name ?? 'Belum ditugaskan' }}
                                                </div>
                                            </div>
                                            @if($job->pelaksana && $job->pelaksana->count() > 1)
                                                <span class="pic-team-badge"
                                                    title="Tim {{ $job->pelaksana->count() }} orang">+{{ $job->pelaksana->count() - 1 }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="due-date-clean {{ $rowLate ? 'is-late' : '' }}">
                                            @if($rowLate) <span class="late-dot" title="Terlambat"></span> @endif
                                            {{ $job->tenggat ? $job->tenggat->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="reports-cell">
                                            <i class="ph-bold ph-newspaper-clipping"></i>
                                            <span>{{ $job->realisasi->where('status_approval', 'disetujui')->count() }}
                                                Laporan</span>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="status-badge-cmd {{ $statusClass }}">
                                            @if($job->status_pelaksanaan === 'selesai_terverifikasi')
                                                <i class="ph-bold ph-check-circle"></i>
                                            @elseif($job->status_pelaksanaan === 'hasil_diajukan')
                                                <i class="ph-bold ph-clock"></i>
                                            @elseif($job->status_pelaksanaan === 'dikerjakan')
                                                <i class="ph-bold ph-spinner"></i>
                                            @elseif($job->status_pelaksanaan === 'perlu_perbaikan_hasil')
                                                <i class="ph-bold ph-warning"></i>
                                            @else
                                                <i class="ph-bold ph-circle-dashed"></i>
                                            @endif
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    <td style="text-align: center;">
                                        <button type="button" class="btn-detail-cmd"><i class="ph-bold ph-eye"></i>
                                            Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="ph-duotone ph-tray"></i>
                                        Tidak ada tugas yang sesuai dengan filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Tampilan kartu (diisi via JavaScript dari baris tabel) -->
                    <div class="card-grid" id="cardGrid"></div>
                </div>

                <div class="table-footer">
                    <span><i class="ph-bold ph-list-checks"></i>Menampilkan <b id="rowShown">0</b> dari <b
                            id="rowTotal">0</b> tugas</span>
                    <span class="kbd-hint"><i class="ph-bold ph-keyboard"></i> <kbd>F</kbd> layar penuh <kbd>S</kbd>
                        gulir <kbd>R</kbd> segarkan <kbd>T</kbd> tema <kbd>V</kbd> tampilan <kbd>/</kbd> cari</span>
                </div>

            </div>
        </div>
    </main>
    </div><!-- /.cmd-main-wrapper -->

    <!-- Data chart (dibaca JS; ikut di-refresh otomatis) -->
    @php
        $chartPayload = [
            'status' => [
                (int) ($statusCounts['selesai'] ?? 0),
                (int) ($statusCounts['dikerjakan'] ?? 0),
                (int) ($statusCounts['menunggu_verifikasi'] ?? 0),
                (int) ($statusCounts['belum_dimulai'] ?? 0),
            ],
            'totalPekerjaan' => $totalPekerjaan,
            'selesai' => $selesaiTerverifikasi,
            'totalSt' => $totalKegiatanSt,
            'totalLaporan' => $totalLaporanBalai,
            'pokja' => collect($pokjaChartData)->values()->all(),
            'st' => collect($stChartData)->values()->all(),
            'report' => collect($reportChartData)->values()->all(),
            'trend' => $trendData,
        ];
        $chartPayloadJson = json_encode($chartPayload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    @endphp
    <script type="application/json" id="chartPayload">{!! $chartPayloadJson !!}</script>

    <!-- ================= MODAL DETAIL TUGAS BALAI ================= -->
    <div id="jobDetailModal" class="cmd-modal-backdrop" onclick="closeJobModal(event)">
        <div class="cmd-modal-dialog" onclick="event.stopPropagation()">
            <div class="cmd-modal-header">
                <div class="modal-head-main">
                    <div class="cmd-modal-badges">
                        <span id="mJobCode" class="task-code on-dark">-</span>
                        <span id="mJobStatus" class="status-badge-cmd">-</span>
                        <span id="mJobPokja" class="task-pokja-badge on-dark">-</span>
                        <span id="mJobST" class="st-badge-mini on-dark" style="display: none;">
                            <i class="ph-bold ph-file-text"></i> <span id="mJobSTText">-</span>
                        </span>
                    </div>
                    <h3 id="mJobTitle" class="cmd-modal-title">Judul Tugas</h3>
                    <div id="mJobParentWrap" class="cmd-modal-parent" style="display: none;">
                        <i class="ph-bold ph-git-branch"></i> Bagian dari: <span id="mJobParent">-</span>
                    </div>
                </div>
                <button type="button" class="cmd-modal-close" onclick="closeJobModalDirect()" title="Tutup">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>

            <div class="cmd-modal-body">
                <div class="cmd-modal-kpi-grid">
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Laporan Disetujui</div>
                        <div class="cmd-kpi-val" id="mJobLaporanCount">0 Laporan</div>
                        <div class="cmd-kpi-sub">Verifikasi Katim</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Target Hasil</div>
                        <div class="cmd-kpi-val" id="mJobTarget">-</div>
                        <div class="cmd-kpi-sub">Volume sasaran</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Status Penugasan</div>
                        <div class="cmd-kpi-val sm" id="mJobStatusPelaksanaan">-</div>
                        <div class="cmd-kpi-sub">Alur pelaksanaan</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Tenggat Waktu</div>
                        <div class="cmd-kpi-val" id="mJobTenggat">-</div>
                        <div class="cmd-kpi-sub" id="mJobSisaHari">-</div>
                    </div>
                </div>

                <div class="cmd-modal-box">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-info"></i> Deskripsi &amp; Sasaran Tugas</h4>
                    <p id="mJobDesc" class="cmd-box-desc"></p>
                    <div class="cmd-box-expected">
                        <strong><i class="ph-bold ph-target"></i> Hasil yang Diharapkan:</strong>
                        <span id="mJobExpected">-</span>
                    </div>
                </div>

                <div class="cmd-modal-box">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-users"></i> Penanggung Jawab &amp; Tim Pelaksana</h4>

                    <div class="cmd-pic-card">
                        <div class="cmd-pic-avatar" id="mJobPicAvatar">PJ</div>
                        <div>
                            <div class="cmd-pic-name" id="mJobPicName">-</div>
                            <div class="cmd-pic-nip" id="mJobPicNip">-</div>
                            <div class="cmd-pic-jabatan" id="mJobPicJabatan">-</div>
                        </div>
                        <span class="cmd-pic-badge">Penanggung Jawab (PIC)</span>
                    </div>

                    <div id="mJobTeamWrap" style="margin-top: 14px;">
                        <div class="cmd-team-header">
                            <span class="cmd-team-title">Personil Pelaksana:</span>
                            <span id="mJobTeamCount" class="cmd-team-chip">0 Anggota</span>
                        </div>
                        <div id="mJobTeamList" class="cmd-team-list"></div>
                    </div>
                </div>

                <div class="cmd-modal-box" id="mJobUpdateWrap" style="display: none;">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-clock-counter-clockwise"></i> Pembaruan Progres
                        Terakhir</h4>
                    <div class="cmd-update-card">
                        <div class="cmd-update-date"><i class="ph-bold ph-calendar-check"></i> <span
                                id="mJobUpdateDate">-</span></div>
                        <div class="cmd-update-note" id="mJobUpdateNote">-</div>
                    </div>
                </div>

                <div class="cmd-modal-box" id="mJobLaporanSection">
                    <div class="box-title-row">
                        <h4 class="cmd-box-title nomargin"><i class="ph-bold ph-newspaper-clipping"></i> Riwayat Laporan
                            Progres Masuk</h4>
                        <span id="mJobLaporanBadge" class="cmd-team-chip">0 Laporan</span>
                    </div>
                    <div id="mJobLaporanList" class="stack-list"></div>
                </div>
            </div>

            <div class="cmd-modal-footer">
                <button type="button" class="btn-cmd-secondary" onclick="closeJobModalDirect()"><i
                        class="ph-bold ph-x"></i> Tutup</button>
                <a id="mJobLink" href="#" target="_blank" class="btn-cmd-primary">
                    <i class="ph-bold ph-arrow-square-out"></i> Buka Halaman Tugas Lengkap
                </a>
            </div>
        </div>
    </div>

    <!-- ================= MODAL DETAIL PERSONIL & PENUGASAN ================= -->
    <div id="personDetailModal" class="cmd-modal-backdrop" onclick="closePersonModal(event)">
        <div class="cmd-modal-dialog wide" onclick="event.stopPropagation()">
            <div class="cmd-modal-header">
                <div class="modal-head-main person-head">
                    <div id="pPersonAvatar" class="person-avatar">PJ</div>
                    <div class="person-head-text">
                        <div class="cmd-modal-badges tight">
                            <span id="pPersonPokja" class="task-pokja-badge on-dark">BGTK</span>
                            <span id="pPersonRole" class="role-pill role-pill-light">Pegawai</span>
                        </div>
                        <h3 id="pPersonName" class="cmd-modal-title">Nama Pegawai</h3>
                        <div id="pPersonSub" class="person-sub">NIP: - &bull; Jabatan: -</div>
                    </div>
                </div>
                <button type="button" class="cmd-modal-close" onclick="closePersonModalDirect()" title="Tutup">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>

            <div class="cmd-modal-body">
                <div class="cmd-modal-kpi-grid kpi-3">
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Total Beban Aktif</div>
                        <div class="cmd-kpi-val c-blue" id="pPersonActiveBeban">0 Tugas</div>
                        <div class="cmd-kpi-sub">Sedang berjalan</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Sebagai PIC Utama</div>
                        <div class="cmd-kpi-val" id="pPersonPicCount">0 Tugas</div>
                        <div class="cmd-kpi-sub">Penanggung jawab</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Sebagai Anggota Tim</div>
                        <div class="cmd-kpi-val" id="pPersonMemberCount">0 Tugas</div>
                        <div class="cmd-kpi-sub">Tim pelaksana</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Tugas Selesai</div>
                        <div class="cmd-kpi-val c-green" id="pPersonDoneCount">0 Tugas</div>
                        <div class="cmd-kpi-sub">Terverifikasi tuntas</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Kegiatan ST</div>
                        <div class="cmd-kpi-val c-indigo" id="pPersonStCount">0 ST</div>
                        <div class="cmd-kpi-sub">Surat tugas resmi</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Laporan Diajukan</div>
                        <div class="cmd-kpi-val c-teal" id="pPersonRepCount">0 Laporan</div>
                        <div class="cmd-kpi-sub">Progres tugas masuk</div>
                    </div>
                </div>

                <div class="p-tabs">
                    <button type="button" class="p-tab active" id="tabBtnPersonJobs" onclick="switchPersonTab('jobs')">
                        <i class="ph-bold ph-briefcase"></i> Pekerjaan Ditugaskan (<span
                            id="pPersonJobsTabCount">0</span>)
                    </button>
                    <button type="button" class="p-tab" id="tabBtnPersonReports" onclick="switchPersonTab('reports')">
                        <i class="ph-bold ph-newspaper-clipping"></i> Laporan Progres (<span
                            id="pPersonReportsTabCount">0</span>)
                    </button>
                </div>

                <div id="pPersonJobsPanel" class="stack-list"></div>
                <div id="pPersonReportsPanel" class="stack-list" style="display: none;"></div>
            </div>

            <div class="cmd-modal-footer between">
                <button type="button" id="pPersonFilterBtn" class="btn-cmd-secondary accent">
                    <i class="ph-bold ph-funnel"></i> Saring Tabel Berdasarkan Personil Ini
                </button>
                <button type="button" class="btn-cmd-secondary" onclick="closePersonModalDirect()"><i
                        class="ph-bold ph-x"></i> Tutup</button>
            </div>
        </div>
    </div>

    <!-- ================= UNIVERSAL FILE PREVIEW MODAL ================= -->
    <div id="modalPreviewBuktiUniversal" class="cmd-modal-backdrop preview-backdrop"
        onclick="closePreviewBuktiOnBackdrop(event)">
        <div class="cmd-modal-dialog preview-dialog" onclick="event.stopPropagation()">
            <div class="cmd-modal-header preview-header">
                <div class="preview-title-wrap">
                    <div class="preview-icon"><i class="ph-bold ph-file-text" id="previewBuktiIcon"></i></div>
                    <div class="preview-title-text">
                        <div class="preview-eyebrow">Pratinjau Bukti Dukung</div>
                        <div id="previewBuktiTitle" class="preview-name">Nama Berkas</div>
                    </div>
                </div>
                <div class="preview-actions">
                    <a id="previewBuktiExternalBtn" href="#" target="_blank" class="btn-action-outline on-dark"
                        title="Buka di Tab Baru">
                        <i class="ph-bold ph-arrow-square-out"></i> Tab Baru
                    </a>
                    <a id="previewBuktiDownloadBtn" href="#" download class="btn-action-primary" title="Unduh Berkas">
                        <i class="ph-bold ph-download-simple"></i> Unduh
                    </a>
                    <button type="button" class="cmd-modal-close" onclick="closePreviewBukti()"
                        title="Tutup">&times;</button>
                </div>
            </div>
            <div id="previewBuktiBody" class="preview-body"></div>
            <div class="cmd-modal-footer between">
                <div class="preview-ext">Format Berkas: <span id="previewBuktiExtBadge"></span></div>
                <button type="button" class="btn-cmd-secondary" onclick="closePreviewBukti()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        const scrollContainer = document.getElementById('tableScrollContainer');
        const modalBackdrop = document.getElementById('jobDetailModal');

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }

        // ============================================================
        // 1. Tema terang / gelap
        // ============================================================
        function applyThemeIcon() {
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = dark ? 'ph-bold ph-sun' : 'ph-bold ph-moon-stars';
        }

        function toggleTheme() {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            try {
                localStorage.setItem('tanjidor_theme', next);
                localStorage.setItem('bgtkTheme', next);
            } catch (e) { }
            applyThemeIcon();
            updateRowCount();
            initMonitoringCharts();
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('appSidebar') || document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) {
                const isOpen = sidebar.classList.toggle('open');
                if (backdrop) backdrop.classList.toggle('active', isOpen);
                document.body.classList.toggle('sidebar-mobile-open', isOpen);
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('appSidebar') || document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('active');
            document.body.classList.remove('sidebar-mobile-open');
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 900) {
                closeMobileSidebar();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileSidebar();
            }
        });

        (function() {
            const bar = document.getElementById('tanjidorTopProgress');
            window.addEventListener('pageshow', function() {
                document.body.classList.remove('is-page-leaving');
                if (bar) bar.classList.remove('is-loading');
            });
            document.addEventListener('click', function(e) {
                if (e.defaultPrevented) return;
                const link = e.target.closest('a[href]');
                if (!link) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
                if (link.target === '_blank' || link.hasAttribute('download')) return;
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                if (link.origin !== window.location.origin) return;
                if (href.includes('export-excel') || href.includes('export-csv')) return;
                if (link.href === window.location.href) return;

                e.preventDefault();
                closeMobileSidebar();
                document.body.classList.add('is-page-leaving');
                if (bar) bar.classList.add('is-loading');
                setTimeout(function() {
                    window.location.href = link.href;
                }, 150);
            });
        })();

        // ============================================================
        // 2. Diagram Chart.js (gradient, rounded, stacked, area)
        // ============================================================
        const COLORS = {
            selesai: '#12b886', dikerjakan: '#2b8cff', verifikasi: '#8b5cf6',
            belum: '#cbd5e1', late: '#f43f5e', st: '#6366f1', rep: '#0ea5e9'
        };

        function readPayload(doc) {
            try { return JSON.parse(doc.getElementById('chartPayload').textContent); }
            catch (e) {
                return { status: [0, 0, 0, 0], totalPekerjaan: 0, selesai: 0, totalSt: 0, totalLaporan: 0, pokja: [], st: [], report: [], trend: { labels: [], masuk: [], disetujui: [] } };
            }
        }

        let payload = readPayload(document);
        let payloadSignature = JSON.stringify(payload);

        let chartDonutInstance = null;
        let chartBarInstance = null;
        let chartTrendInstance = null;
        let currentChartMode = 'pokja';

        function cssVar(name) {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        }

        function vGradient(chart, c1, c2) {
            const a = chart.chartArea;
            if (!a) return c2;
            const g = chart.ctx.createLinearGradient(0, a.bottom, 0, a.top);
            g.addColorStop(0, c1);
            g.addColorStop(1, c2);
            return g;
        }

        function tooltipStyle() {
            return {
                backgroundColor: 'rgba(8, 20, 38, .94)',
                titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '800' },
                bodyFont: { family: 'Plus Jakarta Sans', size: 11.5 },
                footerFont: { family: 'Plus Jakarta Sans', size: 10.5 },
                cornerRadius: 10,
                padding: 12,
                boxPadding: 5,
                usePointStyle: true,
                borderColor: 'rgba(255,255,255,.08)',
                borderWidth: 1
            };
        }

        function initMonitoringCharts() {
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.color = cssVar('--text-muted');

            const cardBg = cssVar('--bg-card');

            // --- Donut ---
            const ctxDonut = document.getElementById('cmdDonutStatus');
            if (ctxDonut) {
                if (chartDonutInstance) chartDonutInstance.destroy();
                chartDonutInstance = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Dikerjakan', 'Verifikasi', 'Belum Dimulai'],
                        datasets: [{
                            data: payload.status,
                            backgroundColor: [COLORS.selesai, COLORS.dikerjakan, COLORS.verifikasi, COLORS.belum],
                            borderWidth: 0,
                            spacing: 4,
                            borderRadius: 10,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '74%',
                        animation: { animateRotate: true, duration: 1000, easing: 'easeOutQuart' },
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...tooltipStyle(), callbacks: { label: c => ` ${c.label}: ${c.parsed} tugas` } }
                        }
                    }
                });
            }

            renderTrendChart();
            renderBarChart(currentChartMode);
        }

        function renderTrendChart() {
            const ctx = document.getElementById('cmdTrend');
            if (!ctx) return;
            if (chartTrendInstance) chartTrendInstance.destroy();

            const t = payload.trend || { labels: [], masuk: [], disetujui: [] };
            const sum = (t.masuk || []).reduce((a, b) => a + b, 0);
            const sumAcc = (t.disetujui || []).reduce((a, b) => a + b, 0);
            const sEl = document.getElementById('trendSummary');
            if (sEl) sEl.textContent = `${sum} laporan • ${sumAcc} disetujui (14 hari)`;

            const grid = cssVar('--border');

            chartTrendInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: t.labels,
                    datasets: [
                        {
                            label: 'Laporan masuk',
                            data: t.masuk,
                            borderColor: COLORS.dikerjakan,
                            backgroundColor: c => vGradient(c.chart, 'rgba(43,140,255,0)', 'rgba(43,140,255,.38)'),
                            fill: true,
                            tension: .42,
                            borderWidth: 2.6,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: COLORS.dikerjakan,
                            pointHoverBorderWidth: 3
                        },
                        {
                            label: 'Disetujui',
                            data: t.disetujui,
                            borderColor: COLORS.selesai,
                            backgroundColor: c => vGradient(c.chart, 'rgba(18,184,134,0)', 'rgba(18,184,134,.16)'),
                            fill: true,
                            borderDash: [6, 5],
                            tension: .42,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    animation: { duration: 900, easing: 'easeOutCubic' },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 7, boxHeight: 7, font: { size: 10.5, weight: '700' }, padding: 12 } },
                        tooltip: tooltipStyle()
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: grid, drawTicks: false }, border: { display: false } },
                        x: { ticks: { font: { size: 9.5 }, maxTicksLimit: 7, maxRotation: 0 }, grid: { display: false }, border: { display: false } }
                    }
                }
            });
        }

        function switchChartMode(mode) {
            currentChartMode = mode;
            document.querySelectorAll('.btn-cmd-chart-tab').forEach(b => b.classList.remove('active'));
            const btn = document.getElementById(mode === 'pokja' ? 'btnChartModePokja' : (mode === 'st' ? 'btnChartModeSt' : 'btnChartModeLaporan'));
            if (btn) btn.classList.add('active');
            updateChartModeLabels();
            renderBarChart(mode);
        }

        function updateChartModeLabels() {
            const titleEl = document.getElementById('chartModeTitle');
            const subEl = document.getElementById('chartModeSub');
            if (currentChartMode === 'pokja') {
                if (titleEl) titleEl.innerHTML = '<i class="ph-bold ph-chart-bar"></i> Ketuntasan per Tim Kerja';
                if (subEl) subEl.textContent = `(Balai: ${payload.selesai}/${payload.totalPekerjaan} Tuntas)`;
            } else if (currentChartMode === 'st') {
                if (titleEl) titleEl.innerHTML = '<i class="ph-bold ph-file-text"></i> Paling Sering Ikut Surat Tugas';
                if (subEl) subEl.textContent = `(${payload.totalSt} Kegiatan ST)`;
            } else {
                if (titleEl) titleEl.innerHTML = '<i class="ph-bold ph-paper-plane-tilt"></i> Paling Aktif Melaporkan Progres';
                if (subEl) subEl.textContent = `(${payload.totalLaporan} Laporan Masuk)`;
            }
        }

        function renderBarChart(mode) {
            const ctxBar = document.getElementById('cmdBarPokja');
            if (!ctxBar) return;
            if (chartBarInstance) chartBarInstance.destroy();

            const grid = cssVar('--border');
            const textMain = cssVar('--text-body');
            const isPokja = mode === 'pokja';
            const rows = isPokja ? (payload.pokja || []) : (mode === 'st' ? (payload.st || []) : (payload.report || []));
            const labels = rows.map(r => isPokja ? r.kode : r.short_name);

            let datasets;
            if (isPokja) {
                datasets = [
                    { label: 'Selesai', data: rows.map(r => r.selesai), backgroundColor: COLORS.selesai },
                    { label: 'Verifikasi', data: rows.map(r => r.verifikasi), backgroundColor: COLORS.verifikasi },
                    { label: 'Dikerjakan', data: rows.map(r => r.dikerjakan), backgroundColor: COLORS.dikerjakan },
                    { label: 'Belum', data: rows.map(r => r.belum), backgroundColor: COLORS.belum }
                ].map(d => ({ ...d, stack: 's', borderRadius: 5, borderSkipped: false, maxBarThickness: 34 }));
            } else {
                const c1 = mode === 'st' ? 'rgba(99,102,241,.45)' : 'rgba(14,165,233,.45)';
                const c2 = mode === 'st' ? '#6366f1' : '#0ea5e9';
                datasets = [{
                    label: mode === 'st' ? 'Kegiatan ST' : 'Laporan Diajukan',
                    data: rows.map(r => mode === 'st' ? r.st_count : r.laporan_count),
                    backgroundColor: c => vGradient(c.chart, c1, c2),
                    borderRadius: 9,
                    borderSkipped: false,
                    maxBarThickness: 30
                }];
            }

            chartBarInstance = new Chart(ctxBar, {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 700, easing: 'easeOutQuart' },
                    onClick: (e, elements) => {
                        if (!elements || !elements.length) return;
                        const idx = elements[0].index;
                        if (isPokja) {
                            const code = rows[idx].kode;
                            const searchInput = document.getElementById('searchFilterInput');
                            if (searchInput) { searchInput.value = code; clientFilterTable(code); }
                        } else if (rows[idx] && rows[idx].id) {
                            openPersonModalByUserId(rows[idx].id);
                        }
                    },
                    onHover: (e, els) => { e.native.target.style.cursor = els.length ? 'pointer' : 'default'; },
                    plugins: {
                        legend: isPokja
                            ? { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 7, boxHeight: 7, font: { size: 10.5, weight: '700' }, padding: 12 } }
                            : { display: false },
                        tooltip: {
                            ...tooltipStyle(),
                            callbacks: {
                                title: items => {
                                    const r = rows[items[0].dataIndex];
                                    return isPokja ? `${r.kode} - ${r.nama}` : `${r.name} (${r.pokja})`;
                                },
                                label: c => isPokja
                                    ? ` ${c.dataset.label}: ${c.parsed.y} tugas`
                                    : (mode === 'st' ? ` Terlibat: ${c.parsed.y} kegiatan ST` : ` Mengajukan: ${c.parsed.y} laporan`),
                                footer: items => {
                                    const r = rows[items[0].dataIndex];
                                    return isPokja
                                        ? `Total ${r.total} tugas • ${r.terlambat} terlambat`
                                        : `Total ${r.total_jobs} tugas`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            stacked: isPokja,
                            ticks: { precision: 0, font: { size: 10 } },
                            grid: { color: grid, drawTicks: false },
                            border: { display: false }
                        },
                        x: {
                            stacked: isPokja,
                            ticks: { font: { family: 'JetBrains Mono', size: 9.5, weight: '700' }, color: textMain },
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        function switchLeaderboardTab(tab) {
            const isSt = tab === 'st';
            document.getElementById('tabLeadSt')?.classList.toggle('active', isSt);
            document.getElementById('tabLeadReport')?.classList.toggle('active', !isSt);
            const pSt = document.getElementById('panelLeadSt');
            const pRep = document.getElementById('panelLeadReport');
            if (pSt) pSt.style.display = isSt ? 'flex' : 'none';
            if (pRep) pRep.style.display = !isSt ? 'flex' : 'none';
            window.__leadTab = tab;
        }

        function openPersonModalByUserId(userId) {
            if (!userId) return;
            const items = document.querySelectorAll('#assigneeListContainer .assignee-item');
            for (let item of items) {
                try {
                    const staf = JSON.parse(item.dataset.staf);
                    if (Number(staf.id) === Number(userId)) { openPersonModal(item); return; }
                } catch (e) { }
            }
        }

        let currentAssigneeSort = 'active';
        function sortAssigneeList(criteria, btn) {
            currentAssigneeSort = criteria;
            document.querySelectorAll('.btn-sort-pill').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const container = document.getElementById('assigneeListContainer');
            if (!container) return;
            const key = criteria === 'st' ? 'stCount' : (criteria === 'report' ? 'repCount' : 'activeCount');

            const items = Array.from(container.querySelectorAll('.assignee-item'));
            items.sort((a, b) => {
                const vA = parseInt(a.dataset[key] || '0', 10);
                const vB = parseInt(b.dataset[key] || '0', 10);
                if (vB !== vA) return vB - vA;
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            });
            items.forEach(el => container.appendChild(el));
        }

        let isDiagramVisible = true;
        function toggleDiagramBar() {
            isDiagramVisible = !isDiagramVisible;
            document.getElementById('diagramGridContainer').style.display = isDiagramVisible ? 'grid' : 'none';
            document.getElementById('diagramToggleIcon').className = isDiagramVisible ? 'ph-bold ph-caret-up' : 'ph-bold ph-caret-down';
            document.getElementById('diagramBtnText').textContent = isDiagramVisible ? 'Tutup' : 'Buka';
            document.getElementById('btnToggleDiagram').classList.toggle('active', isDiagramVisible);
            document.getElementById('diagramToggleLabel').textContent = isDiagramVisible ? 'Diagram: Aktif' : 'Diagram: Tertutup';
            if (isDiagramVisible) { chartDonutInstance?.resize(); chartBarInstance?.resize(); chartTrendInstance?.resize(); }
        }

        function filterTableByStatusClick(status) {
            window.location.href = "{{ route('monitoring.balai') }}?status=" + status;
        }

        // ============================================================
        // 3. Animasi angka KPI
        // ============================================================
        function animateKpiNumbers() {
            document.querySelectorAll('.count-up[data-count]').forEach(el => {
                const target = parseFloat(el.dataset.count) || 0;
                const dec = parseInt(el.dataset.decimals || '0', 10);
                const fmt = v => v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec });
                const start = performance.now();
                const dur = 1000;
                (function tick(now) {
                    const p = Math.min(1, (now - start) / dur);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = fmt(target * eased);
                    if (p < 1) requestAnimationFrame(tick);
                })(start);
            });
        }

        // ============================================================
        // 4. Helper badge + Modal detail tugas & laporan
        // ============================================================
        function approvalBadge(status) {
            if (status === 'disetujui') return '<span class="status-badge-cmd status-badge-selesai sm"><i class="ph-bold ph-check"></i> Disetujui</span>';
            if (status === 'ditolak') return '<span class="status-badge-cmd status-badge-ditolak sm"><i class="ph-bold ph-x"></i> Perlu Perbaikan</span>';
            return '<span class="status-badge-cmd status-badge-verifikasi sm"><i class="ph-bold ph-clock"></i> Menunggu Persetujuan</span>';
        }

        function jenisBadge(isFinal) {
            return isFinal
                ? '<span class="status-badge-cmd status-badge-final sm"><i class="ph-bold ph-seal-check"></i> Penyelesaian Akhir</span>'
                : '<span class="status-badge-cmd status-badge-kemajuan sm"><i class="ph-bold ph-pencil-simple-line"></i> Kemajuan Berkala</span>';
        }

        function jobStatusBadge(raw) {
            if (raw === 'selesai_terverifikasi') return '<span class="status-badge-cmd status-badge-selesai sm">Selesai</span>';
            if (raw === 'hasil_diajukan') return '<span class="status-badge-cmd status-badge-verifikasi sm">Verifikasi</span>';
            if (raw === 'belum_dimulai') return '<span class="status-badge-cmd st-belum sm">Belum Dimulai</span>';
            return '<span class="status-badge-cmd status-badge-dikerjakan sm">Dikerjakan</span>';
        }

        function evidenceButtons(fileUrl, fileName, link) {
            let html = '';
            if (fileUrl) {
                html += `<button type="button" class="btn-evidence" onclick="previewBuktiDukung('${escapeHtml(fileUrl)}', '${escapeHtml(fileName || 'Berkas Bukti Dukung')}')"><i class="ph-bold ph-eye"></i> Pratinjau Berkas</button>`;
            }
            if (link) {
                html += `<a href="${escapeHtml(link)}" target="_blank" rel="noopener" class="btn-evidence ghost"><i class="ph-bold ph-arrow-square-out"></i> Tautan</a>`;
            }
            return html;
        }

        let isModalOpen = false;

        function openJobModal(job) {
            if (!job) return;
            isModalOpen = true;

            document.getElementById('mJobCode').textContent = job.kode || '-';
            const statusEl = document.getElementById('mJobStatus');
            statusEl.textContent = job.status_label || '-';
            statusEl.className = 'status-badge-cmd ' + (job.status_class || '');
            document.getElementById('mJobPokja').textContent = job.pokja_code || 'BGTK';

            const stEl = document.getElementById('mJobST');
            if (job.nomor_st) {
                document.getElementById('mJobSTText').textContent = 'ST: ' + job.nomor_st;
                stEl.style.display = 'inline-flex';
            } else {
                stEl.style.display = 'none';
            }

            document.getElementById('mJobTitle').textContent = job.judul || '-';
            const parentWrap = document.getElementById('mJobParentWrap');
            if (job.parent) {
                document.getElementById('mJobParent').textContent = job.parent;
                parentWrap.style.display = 'flex';
            } else {
                parentWrap.style.display = 'none';
            }

            document.getElementById('mJobLaporanCount').textContent = (job.laporan_acc_count || 0) + ' Laporan';
            document.getElementById('mJobTarget').textContent = job.target || '-';
            document.getElementById('mJobStatusPelaksanaan').textContent = job.status_label || '-';
            document.getElementById('mJobTenggat').textContent = job.tenggat || '-';

            const sisaEl = document.getElementById('mJobSisaHari');
            if (job.status_raw === 'selesai_terverifikasi') {
                sisaEl.textContent = 'Selesai diverifikasi';
                sisaEl.style.color = 'var(--success)';
            } else if (job.is_terlambat) {
                sisaEl.textContent = 'Terlambat ' + Math.abs(job.sisa_hari) + ' hari';
                sisaEl.style.color = 'var(--danger)';
            } else if (job.sisa_hari !== null && job.sisa_hari >= 0) {
                sisaEl.textContent = 'Sisa ' + job.sisa_hari + ' hari lagi';
                sisaEl.style.color = '';
            } else {
                sisaEl.textContent = '-';
            }

            document.getElementById('mJobDesc').textContent = job.deskripsi || '-';
            document.getElementById('mJobExpected').textContent = job.hasil_diharapkan || '-';

            document.getElementById('mJobPicAvatar').textContent = job.pj_initial || 'PJ';
            document.getElementById('mJobPicName').textContent = job.pj_nama || '-';
            document.getElementById('mJobPicNip').textContent = job.pj_nip || '-';
            document.getElementById('mJobPicJabatan').textContent = job.pj_jabatan || '-';

            const teamWrap = document.getElementById('mJobTeamWrap');
            const teamList = document.getElementById('mJobTeamList');
            teamList.innerHTML = '';
            if (job.pelaksana && job.pelaksana.length > 0) {
                teamWrap.style.display = 'block';
                document.getElementById('mJobTeamCount').textContent = job.pelaksana.length + ' Orang';
                job.pelaksana.forEach(m => {
                    const item = document.createElement('div');
                    item.className = 'cmd-member-item';
                    item.innerHTML = `
                        <div class="cmd-member-avatar">${escapeHtml(m.initial || 'PL')}</div>
                        <div class="member-text">
                            <div class="cmd-member-name" title="${escapeHtml(m.name)}">${escapeHtml(m.name)}</div>
                            <div class="cmd-member-jab" title="${escapeHtml(m.jabatan)}">${escapeHtml(m.jabatan)}</div>
                        </div>`;
                    teamList.appendChild(item);
                });
            } else {
                teamWrap.style.display = 'none';
            }

            const updateWrap = document.getElementById('mJobUpdateWrap');
            if (job.last_update) {
                updateWrap.style.display = 'block';
                document.getElementById('mJobUpdateDate').textContent = 'Diperbarui pada: ' + job.last_update;
                document.getElementById('mJobUpdateNote').textContent = job.last_note ? `"${job.last_note}"` : 'Progres berhasil diperbarui ke sistem.';
            } else {
                updateWrap.style.display = 'none';
            }

            // Riwayat laporan progres
            const listEl = document.getElementById('mJobLaporanList');
            const badgeEl = document.getElementById('mJobLaporanBadge');
            listEl.innerHTML = '';
            const reports = job.laporan_list || [];
            badgeEl.textContent = reports.length + ' Laporan';

            if (reports.length > 0) {
                reports.forEach(rep => {
                    const el = document.createElement('div');
                    el.className = 'report-card';
                    const buktiHtml = evidenceButtons(rep.file_bukti, rep.file_name, rep.link_bukti);
                    el.innerHTML = `
                        <div class="report-top">
                            <div class="report-who">
                                <div class="cmd-member-avatar sm">${escapeHtml(rep.pelapor_initial || 'PL')}</div>
                                <div>
                                    <div class="report-name">${escapeHtml(rep.pelapor || '-')}</div>
                                    <div class="report-date">${escapeHtml(rep.tanggal || '-')}</div>
                                </div>
                            </div>
                            <div class="report-badges">${jenisBadge(rep.is_penyelesaian)}${approvalBadge(rep.status_approval)}</div>
                        </div>
                        <div class="report-text">${escapeHtml(rep.deskripsi || '-')}</div>
                        ${rep.kendala ? `<div class="note-box bad"><strong>Kendala:</strong> ${escapeHtml(rep.kendala)}</div>` : ''}
                        ${rep.tindak_lanjut ? `<div class="note-box info"><strong>Tindak Lanjut:</strong> ${escapeHtml(rep.tindak_lanjut)}</div>` : ''}
                        ${rep.catatan_penolakan ? `<div class="note-box bad"><strong>Catatan Penolakan:</strong> ${escapeHtml(rep.catatan_penolakan)}</div>` : ''}
                        ${buktiHtml ? `<div class="report-evidence">${buktiHtml}</div>` : ''}`;
                    listEl.appendChild(el);
                });
            } else {
                listEl.innerHTML = `
                    <div class="mini-empty">
                        <i class="ph-bold ph-tray"></i>
                        Belum ada laporan progres yang diajukan untuk tugas ini.
                    </div>`;
            }

            document.getElementById('mJobLink').href = job.url_show || '#';
            modalBackdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeJobModalDirect() {
            isModalOpen = false;
            modalBackdrop.classList.remove('show');
            if (!isPersonModalOpen) document.body.style.overflow = '';
        }

        function closeJobModal(event) {
            if (event.target === modalBackdrop) closeJobModalDirect();
        }

        // ============================================================
        // 4b. Modal detail personil
        // ============================================================
        let isPersonModalOpen = false;

        function openPersonModal(el) {
            if (!el || !el.dataset.staf) return;
            try {
                const staf = JSON.parse(el.dataset.staf);
                isPersonModalOpen = true;

                document.getElementById('pPersonAvatar').textContent = (staf.name ? staf.name.substring(0, 2).toUpperCase() : 'ST');
                document.getElementById('pPersonName').textContent = staf.name || '-';
                document.getElementById('pPersonPokja').textContent = staf.pokja_kode || 'BGTK';
                document.getElementById('pPersonRole').textContent = staf.role_label || staf.role || 'Pegawai';
                document.getElementById('pPersonSub').textContent = `NIP: ${staf.nip || 'Non-PNS'} • Jabatan: ${staf.jabatan || '-'}`;

                document.getElementById('pPersonActiveBeban').textContent = (staf.total_active || 0) + ' Tugas';
                document.getElementById('pPersonPicCount').textContent = (staf.pic_count || 0) + ' Tugas';
                document.getElementById('pPersonMemberCount').textContent = (staf.member_count || 0) + ' Tugas';
                document.getElementById('pPersonDoneCount').textContent = (staf.tugas_selesai || 0) + ' Tugas';
                document.getElementById('pPersonStCount').textContent = (staf.st_count || 0) + ' ST';
                document.getElementById('pPersonRepCount').textContent = (staf.laporan_count || 0) + ' Laporan';

                const jobs = staf.jobs || [];
                const reports = staf.laporan || [];
                document.getElementById('pPersonJobsTabCount').textContent = jobs.length;
                document.getElementById('pPersonReportsTabCount').textContent = reports.length;

                // Tab 1: Pekerjaan
                const jobsPanel = document.getElementById('pPersonJobsPanel');
                jobsPanel.innerHTML = '';
                if (jobs.length > 0) {
                    jobs.forEach(j => {
                        const card = document.createElement('div');
                        card.className = 'report-card';
                        const peran = j.is_pic
                            ? '<span class="status-badge-cmd status-badge-dikerjakan sm"><i class="ph-bold ph-crown"></i> PIC Utama</span>'
                            : '<span class="status-badge-cmd st-belum sm"><i class="ph-bold ph-users"></i> Anggota Tim</span>';
                        const stBadge = j.nomor_st ? `<span class="st-badge-mini" title="Surat Tugas: ${escapeHtml(j.nomor_st)}"><i class="ph-bold ph-file-text"></i> ST: ${escapeHtml(j.nomor_st)}</span>` : '';

                        card.innerHTML = `
                            <div class="report-top">
                                <div class="report-badges">
                                    <span class="task-code">${escapeHtml(j.kode || '-')}</span>
                                    ${peran}${stBadge}
                                    <span class="task-pokja-badge sm">${escapeHtml(j.pokja || 'BGTK')}</span>
                                </div>
                                <div>${jobStatusBadge(j.status_raw)}</div>
                            </div>
                            <div class="report-title">${escapeHtml(j.judul || '-')}</div>
                            <div class="report-foot">
                                <div class="report-meta">
                                    <i class="ph-bold ph-calendar"></i> Tenggat:
                                    <span class="${j.is_terlambat ? 'txt-late' : ''}">${escapeHtml(j.tenggat || '-')} ${j.is_terlambat ? '(Terlambat)' : ''}</span>
                                    <span class="sep">•</span>
                                    <i class="ph-bold ph-newspaper-clipping"></i> ${j.laporan_count || 0} Laporan
                                </div>
                                <button type="button" class="btn-cmd-primary sm" onclick="openJobModalById(${j.id})"><i class="ph-bold ph-eye"></i> Detail Tugas</button>
                            </div>`;
                        jobsPanel.appendChild(card);
                    });
                } else {
                    jobsPanel.innerHTML = `<div class="mini-empty"><i class="ph-bold ph-briefcase"></i>Tidak ada pekerjaan yang sedang ditugaskan kepada personil ini.</div>`;
                }

                // Tab 2: Laporan
                const repPanel = document.getElementById('pPersonReportsPanel');
                repPanel.innerHTML = '';
                if (reports.length > 0) {
                    reports.forEach(r => {
                        const card = document.createElement('div');
                        card.className = 'report-card';
                        const buktiHtml = evidenceButtons(r.file_bukti, r.file_name, r.link_bukti);
                        card.innerHTML = `
                            <div class="report-top">
                                <div>
                                    <span class="task-code">${escapeHtml(r.pekerjaan_kode || '-')}</span>
                                    <span class="report-title inline">${escapeHtml(r.pekerjaan_judul || '-')}</span>
                                </div>
                                <div class="report-badges">${jenisBadge(r.is_penyelesaian)}${approvalBadge(r.status_approval)}</div>
                            </div>
                            <div class="report-date"><i class="ph-bold ph-calendar"></i> Tanggal Lapor: <strong>${escapeHtml(r.tanggal || '-')}</strong></div>
                            <div class="report-text boxed">${escapeHtml(r.deskripsi || '-')}</div>
                            ${r.kendala ? `<div class="note-box bad"><strong>Kendala:</strong> ${escapeHtml(r.kendala)}</div>` : ''}
                            <div class="report-foot">
                                <div class="report-evidence">${buktiHtml}</div>
                                <button type="button" class="btn-cmd-secondary sm" onclick="openJobModalById(${r.pekerjaan_id})"><i class="ph-bold ph-arrow-square-out"></i> Buka Tugas Terkait</button>
                            </div>`;
                        repPanel.appendChild(card);
                    });
                } else {
                    repPanel.innerHTML = `<div class="mini-empty"><i class="ph-bold ph-newspaper-clipping"></i>Belum ada riwayat laporan progres yang diajukan oleh personil ini.</div>`;
                }

                switchPersonTab('jobs');

                const filterBtn = document.getElementById('pPersonFilterBtn');
                if (filterBtn) {
                    filterBtn.onclick = function () {
                        closePersonModalDirect();
                        filterTableByAssignee(staf.name);
                    };
                }

                document.getElementById('personDetailModal').classList.add('show');
                document.body.style.overflow = 'hidden';
            } catch (err) {
                console.warn('Gagal menampilkan detail personil:', err);
            }
        }

        function switchPersonTab(tab) {
            const isJobs = tab === 'jobs';
            document.getElementById('tabBtnPersonJobs').classList.toggle('active', isJobs);
            document.getElementById('tabBtnPersonReports').classList.toggle('active', !isJobs);
            document.getElementById('pPersonJobsPanel').style.display = isJobs ? 'flex' : 'none';
            document.getElementById('pPersonReportsPanel').style.display = isJobs ? 'none' : 'flex';
        }

        function closePersonModalDirect() {
            isPersonModalOpen = false;
            const m = document.getElementById('personDetailModal');
            if (m) m.classList.remove('show');
            if (!isModalOpen) document.body.style.overflow = '';
        }

        function closePersonModal(event) {
            const m = document.getElementById('personDetailModal');
            if (event.target === m) closePersonModalDirect();
        }

        function openJobModalById(jobId) {
            if (!jobId) return;
            const numId = Number(jobId);
            if (window.allPekerjaanMap && window.allPekerjaanMap.has(numId)) {
                closePersonModalDirect();
                openJobModal(window.allPekerjaanMap.get(numId));
            } else {
                window.open('/pekerjaan/' + jobId, '_blank');
            }
        }

        // ============================================================
        // 4c. Preview Bukti Dukung
        // ============================================================
        function previewBuktiDukung(fileUrl, fileName = 'Berkas Bukti Dukung') {
            const modal = document.getElementById('modalPreviewBuktiUniversal');
            if (!modal) { window.open(fileUrl, '_blank'); return; }

            const bodyEl = document.getElementById('previewBuktiBody');
            document.getElementById('previewBuktiTitle').textContent = fileName;
            document.getElementById('previewBuktiExternalBtn').href = fileUrl;
            document.getElementById('previewBuktiDownloadBtn').href = fileUrl;

            const cleanUrl = fileUrl.split('?')[0].split('#')[0];
            const ext = cleanUrl.split('.').pop().toLowerCase();
            document.getElementById('previewBuktiExtBadge').textContent = ext.toUpperCase();

            const isImg = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].includes(ext);
            let iconClass = 'ph-bold ph-file-text';
            if (ext === 'pdf') iconClass = 'ph-bold ph-file-pdf';
            else if (isImg) iconClass = 'ph-bold ph-image';
            else if (['doc', 'docx'].includes(ext)) iconClass = 'ph-bold ph-file-doc';
            else if (['xls', 'xlsx'].includes(ext)) iconClass = 'ph-bold ph-file-xls';
            document.getElementById('previewBuktiIcon').className = iconClass;

            bodyEl.innerHTML = '';
            if (isImg) {
                const img = document.createElement('img');
                img.src = fileUrl; img.alt = fileName; img.className = 'preview-img';
                bodyEl.appendChild(img);
            } else if (ext === 'pdf') {
                const iframe = document.createElement('iframe');
                iframe.src = fileUrl; iframe.className = 'preview-frame';
                bodyEl.appendChild(iframe);
            } else {
                bodyEl.innerHTML = `
                    <div class="preview-unsupported">
                        <div class="preview-unsupported-ico"><i class="${iconClass}"></i></div>
                        <h4>Pratinjau Langsung Tidak Didukung</h4>
                        <p>Berkas berformat <strong>.${escapeHtml(ext.toUpperCase())}</strong> perlu dibuka di aplikasi eksternal atau diunduh.</p>
                        <div class="preview-unsupported-actions">
                            <a href="${escapeHtml(fileUrl)}" target="_blank" class="btn-action-outline"><i class="ph-bold ph-arrow-square-out"></i> Buka di Tab Baru</a>
                            <a href="${escapeHtml(fileUrl)}" download class="btn-action-primary"><i class="ph-bold ph-download-simple"></i> Unduh Berkas</a>
                        </div>
                    </div>`;
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closePreviewBukti() {
            const modal = document.getElementById('modalPreviewBuktiUniversal');
            if (modal) modal.style.display = 'none';
            if (!isModalOpen && !isPersonModalOpen) document.body.style.overflow = '';
        }

        function closePreviewBuktiOnBackdrop(e) {
            if (e.target === document.getElementById('modalPreviewBuktiUniversal')) closePreviewBukti();
        }

        // Index semua tugas ke map untuk cross-navigation cepat
        window.allPekerjaanMap = new Map();
        function indexAllJobs() {
            window.allPekerjaanMap = new Map();
            document.querySelectorAll('tr.table-row-item[data-job]').forEach(row => {
                try {
                    const j = JSON.parse(row.dataset.job);
                    if (j && j.id) window.allPekerjaanMap.set(Number(j.id), j);
                } catch (e) { }
            });
        }

        // Klik baris tabel (delegasi, tetap bekerja setelah auto-refresh)
        document.getElementById('commandTable').addEventListener('click', (e) => {
            const row = e.target.closest('tr.table-row-item');
            if (!row || !row.dataset.job) return;
            try { openJobModal(JSON.parse(row.dataset.job)); } catch (err) { console.warn('Data tugas tidak valid', err); }
        });

        // Klik kartu (tampilan kartu)
        document.getElementById('cardGrid').addEventListener('click', (e) => {
            const card = e.target.closest('.job-card');
            if (!card) return;
            const j = window.allPekerjaanMap.get(Number(card.dataset.id));
            if (j) openJobModal(j);
        });

        // ============================================================
        // 5. Jam digital
        // ============================================================
        function updateLiveClock() {
            const now = new Date();
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('liveTimeDisplay').textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB`;
            document.getElementById('liveDateDisplay').textContent =
                now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // ============================================================
        // 6. Segarkan otomatis
        // ============================================================
        const REFRESH_INTERVAL = 30;
        let refreshSecondsLeft = REFRESH_INTERVAL;
        let isAutoRefreshActive = true;
        let isRefreshing = false;

        function renderRefreshLabel() {
            const label = document.getElementById('refreshLabel');
            if (!label) return;
            if (isRefreshing) label.textContent = 'Menyinkron...';
            else if (!isAutoRefreshActive) label.textContent = 'Segarkan: Jeda';
            else label.textContent = `Segarkan: ${refreshSecondsLeft}d`;
        }

        function swapSection(doc, selector, keepScroll) {
            const next = doc.querySelector(selector);
            const cur = document.querySelector(selector);
            if (!next || !cur) return;
            const prev = keepScroll ? cur.scrollTop : 0;
            cur.innerHTML = next.innerHTML;
            if (keepScroll) cur.scrollTop = prev;
        }

        async function refreshDashboardData() {
            if (isRefreshing || isModalOpen || isPersonModalOpen) return;
            isRefreshing = true;
            renderRefreshLabel();

            const refreshIcon = document.querySelector('#btnAutoRefresh i');
            if (refreshIcon) refreshIcon.classList.add('spin-active');

            try {
                const response = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (response.ok) {
                    const doc = new DOMParser().parseFromString(await response.text(), 'text/html');

                    swapSection(doc, '.hero-card', false);
                    swapSection(doc, '.giant-kpi-grid', false);
                    swapSection(doc, '#donutLegend', false);
                    swapSection(doc, '#donutNum', false);
                    swapSection(doc, '.diagram-leaderboard-box', false);
                    swapSection(doc, '#pokjaScrollContainer', true);
                    swapSection(doc, '#assigneeListContainer', true);
                    swapSection(doc, '#paneRadar', true);
                    swapSection(doc, '.stream-tabs', false);
                    swapSection(doc, '#commandTable tbody', false);

                    // Perbarui chart bila data berubah
                    const newPayload = readPayload(doc);
                    const sig = JSON.stringify(newPayload);
                    if (sig !== payloadSignature) {
                        payload = newPayload;
                        payloadSignature = sig;
                        updateChartModeLabels();
                        initMonitoringCharts();
                    }

                    // Pulihkan tab leaderboard yang sedang aktif
                    if (window.__leadTab) switchLeaderboardTab(window.__leadTab);

                    indexAllJobs();
                    if (currentAssigneeSort !== 'active') {
                        const idMap = { st: 'sortBtnSt', report: 'sortBtnReport' };
                        sortAssigneeList(currentAssigneeSort, document.getElementById(idMap[currentAssigneeSort]));
                    }
                    filterAssigneeRows();
                    if (selectedAssigneeName) markSelectedAssignee(selectedAssigneeName);

                    const searchInput = document.getElementById('searchFilterInput');
                    if (searchInput && searchInput.value) clientFilterTable(searchInput.value);
                    else updateRowCount();
                }
            } catch (err) {
                console.warn('Gagal sinkronisasi data monitoring balai:', err);
            } finally {
                isRefreshing = false;
                refreshSecondsLeft = REFRESH_INTERVAL;
                if (refreshIcon) refreshIcon.classList.remove('spin-active');
                renderRefreshLabel();
            }
        }

        function runRefreshCountdown() {
            if (!isAutoRefreshActive || isRefreshing || isModalOpen || isPersonModalOpen) return;
            const active = document.activeElement;
            if (active && active.tagName === 'INPUT' && active.value) {
                refreshSecondsLeft = REFRESH_INTERVAL;
                renderRefreshLabel();
                return;
            }
            refreshSecondsLeft--;
            renderRefreshLabel();
            if (refreshSecondsLeft <= 0) refreshDashboardData();
        }
        setInterval(runRefreshCountdown, 1000);

        function toggleAutoRefresh() {
            isAutoRefreshActive = !isAutoRefreshActive;
            document.getElementById('btnAutoRefresh').classList.toggle('active', isAutoRefreshActive);
            refreshSecondsLeft = REFRESH_INTERVAL;
            renderRefreshLabel();
        }

        // ============================================================
        // 7. Layar penuh & gulir otomatis TV
        // ============================================================
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    document.body.classList.add('fullscreen-mode');
                    document.getElementById('fsLabel').textContent = 'Keluar Layar';
                }).catch(err => window.tanjidorAlert('Layar Penuh', 'Tidak dapat membuka layar penuh: ' + err.message, 'warning'));
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) {
                document.body.classList.remove('fullscreen-mode');
                document.getElementById('fsLabel').textContent = 'Layar Penuh';
            }
        });

        let isAutoScrollActive = false;
        let scrollTimer = null;
        let scrollPausedUntil = 0;
        let reachedBottom = false;

        function renderScrollLabel() {
            document.getElementById('scrollLabel').textContent = 'Gulir TV: ' + (isAutoScrollActive ? 'Hidup' : 'Mati');
            document.getElementById('btnAutoScroll').classList.toggle('active', isAutoScrollActive);
        }

        function toggleAutoScroll() {
            isAutoScrollActive = !isAutoScrollActive;
            renderScrollLabel();
            if (isAutoScrollActive) startAutoScroll();
            else clearInterval(scrollTimer);
        }

        function startAutoScroll() {
            clearInterval(scrollTimer);
            reachedBottom = false;
            scrollPausedUntil = Date.now() + 2500;
            scrollTimer = setInterval(() => {
                if (!isAutoScrollActive || !scrollContainer || isModalOpen || isPersonModalOpen) return;
                const now = Date.now();
                if (now < scrollPausedUntil) return;

                const maxScroll = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                if (maxScroll <= 0) return;

                if (scrollContainer.scrollTop >= maxScroll - 2) {
                    if (!reachedBottom) {
                        reachedBottom = true;
                        scrollPausedUntil = now + 3000;
                    } else {
                        reachedBottom = false;
                        scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
                        scrollPausedUntil = now + 3500;
                    }
                } else {
                    scrollContainer.scrollBy({ top: 1, behavior: 'auto' });
                }
            }, 40);
        }

        // ============================================================
        // 8. Filter cepat + tampilan Tabel / Kartu
        // ============================================================
        let currentView = 'table';
        try { currentView = localStorage.getItem('bgtkView') || 'table'; } catch (e) { }

        function setView(v) {
            currentView = v === 'cards' ? 'cards' : 'table';
            try { localStorage.setItem('bgtkView', currentView); } catch (e) { }
            document.getElementById('streamPanel').classList.toggle('view-cards', currentView === 'cards');
            document.getElementById('btnViewTable').classList.toggle('active', currentView === 'table');
            document.getElementById('btnViewCards').classList.toggle('active', currentView === 'cards');
            renderCards();
        }

        function renderCards() {
            const grid = document.getElementById('cardGrid');
            if (!grid) return;
            if (currentView !== 'cards') { grid.innerHTML = ''; return; }

            const rows = Array.from(document.querySelectorAll('#commandTable tr.table-row-item')).filter(r => r.style.display !== 'none');
            if (!rows.length) {
                grid.innerHTML = '<div class="mini-empty wide"><i class="ph-bold ph-tray"></i>Tidak ada tugas yang sesuai dengan filter ini.</div>';
                return;
            }

            grid.innerHTML = rows.map(r => {
                let j;
                try { j = JSON.parse(r.dataset.job); } catch (e) { return ''; }
                const late = j.is_terlambat && j.status_raw !== 'selesai_terverifikasi';
                const team = (j.pelaksana || []).length;
                return `
                    <article class="job-card ${late ? 'is-late' : ''}" data-id="${j.id}">
                        <div class="job-card-top">
                            <span class="task-code">${escapeHtml(j.kode)}</span>
                            <span class="status-badge-cmd ${escapeHtml(j.status_class)}">${escapeHtml(j.status_label)}</span>
                        </div>
                        <h4 class="job-card-title">${escapeHtml(j.judul)}</h4>
                        <div class="job-card-tags">
                            <span class="task-pokja-badge sm">${escapeHtml(j.pokja_code)}</span>
                            ${j.nomor_st ? '<span class="st-badge-mini"><i class="ph-bold ph-file-text"></i> ST</span>' : ''}
                        </div>
                        <div class="job-card-foot">
                            <div class="job-card-pic">
                                <div class="pic-avatar-mini">${escapeHtml(j.pj_initial)}</div>
                                <span title="${escapeHtml(j.pj_nama)}">${escapeHtml(j.pj_nama)}</span>
                                ${team > 1 ? `<em>+${team - 1}</em>` : ''}
                            </div>
                            <div class="job-card-meta">
                                <span class="${late ? 'txt-late' : ''}"><i class="ph-bold ph-calendar"></i> ${escapeHtml(j.tenggat)}</span>
                                <span><i class="ph-bold ph-newspaper-clipping"></i> ${j.laporan_acc_count}</span>
                            </div>
                        </div>
                    </article>`;
            }).join('');
        }

        function clientFilterTable(query) {
            query = (query || '').toLowerCase().trim();
            document.querySelectorAll('.table-row-item').forEach(row => {
                row.style.display = (!query || row.textContent.toLowerCase().includes(query)) ? '' : 'none';
            });
            updateRowCount();
        }

        function updateRowCount() {
            const rows = Array.from(document.querySelectorAll('.table-row-item'));
            const shown = rows.filter(r => r.style.display !== 'none').length;
            const a = document.getElementById('rowShown');
            const b = document.getElementById('rowTotal');
            if (a) a.textContent = shown;
            if (b) b.textContent = rows.length;
            renderCards();
        }

        // Saklar panel kiri: Tim / Personil / Radar
        function switchSidePane(name) {
            ['pokja', 'staf', 'radar'].forEach(n => {
                const pane = document.getElementById('pane' + n.charAt(0).toUpperCase() + n.slice(1));
                const tab = document.getElementById('sideTab' + n.charAt(0).toUpperCase() + n.slice(1));
                if (pane) pane.hidden = n !== name;
                if (tab) tab.classList.toggle('active', n === name);
            });
            try { localStorage.setItem('bgtkSidePane', name); } catch (e) { }
        }

        let currentAssigneeTab = 'all';
        let selectedAssigneeName = null;

        function switchAssigneeTab(tab, btn) {
            currentAssigneeTab = tab;
            document.querySelectorAll('.btn-assignee-tab').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            filterAssigneeRows();
        }

        function filterAssigneeRows(query) {
            const searchInput = document.getElementById('assigneeSearchInput');
            const q = (typeof query === 'string' ? query : (searchInput ? searchInput.value : '')).toLowerCase().trim();

            document.querySelectorAll('.assignee-item').forEach(item => {
                const cat = item.getAttribute('data-role-category');
                const name = (item.getAttribute('data-name') || '').toLowerCase();
                const matchTab = currentAssigneeTab === 'all' || cat === currentAssigneeTab;
                item.style.display = (matchTab && (!q || name.includes(q))) ? 'flex' : 'none';
            });
        }

        function markSelectedAssignee(name) {
            document.querySelectorAll('.assignee-item').forEach(el => {
                el.classList.toggle('selected-assignee', !!name && el.getAttribute('data-name') === name.toLowerCase());
            });
        }

        function filterTableByAssignee(name) {
            const searchInput = document.getElementById('searchFilterInput');
            if (selectedAssigneeName === name) {
                selectedAssigneeName = null;
                if (searchInput) searchInput.value = '';
                clientFilterTable('');
                markSelectedAssignee(null);
            } else {
                selectedAssigneeName = name;
                if (searchInput) searchInput.value = name;
                clientFilterTable(name);
                markSelectedAssignee(name);
            }
        }

        // ============================================================
        // 9. Pintasan keyboard: F, S, R, T, V, /, Esc
        // ============================================================
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const previewM = document.getElementById('modalPreviewBuktiUniversal');
                if (previewM && previewM.style.display === 'flex') { closePreviewBukti(); return; }
                if (isPersonModalOpen) { closePersonModalDirect(); return; }
                if (isModalOpen) { closeJobModalDirect(); return; }
            }
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            const k = e.key.toLowerCase();
            if (k === 'f') toggleFullscreen();
            else if (k === 's') toggleAutoScroll();
            else if (k === 'r') toggleAutoRefresh();
            else if (k === 't') toggleTheme();
            else if (k === 'v') setView(currentView === 'table' ? 'cards' : 'table');
            else if (k === '/') { e.preventDefault(); document.getElementById('searchFilterInput')?.focus(); }
        });

        document.addEventListener('DOMContentLoaded', function () {
            applyThemeIcon();
            initMonitoringCharts();
            animateKpiNumbers();
            indexAllJobs();
            try { switchSidePane(localStorage.getItem('bgtkSidePane') || 'pokja'); } catch (e) { }
            updateRowCount();
            setView(currentView);
        });
    </script>
</body>

</html>