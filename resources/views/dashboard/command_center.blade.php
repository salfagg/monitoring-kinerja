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

    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    <script>
        // Terapkan tema sebelum render agar tidak berkedip
        try {
            const t = localStorage.getItem('bgtkTheme');
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
                    customClass: {
                        popup: 'cmd-swal-modal',
                    }
                });
            }
            alert(title + (text ? ': ' + text : ''));
        };
    </script>

    <link rel="stylesheet" href="{{ asset('css/command-center.css') }}">
</head>

<body>

    <!-- ================= HEADER ================= -->
    <!-- ================= HEADER COMMAND CENTER ================= -->
    <header class="top-command-bar">
        <div class="brand-section">
            <div class="brand-badge"><i class="ph-bold ph-shield-check"></i></div>
            <div>
                <div class="brand-title">
                    BGTK PROVINSI DKI JAKARTA
                    <span class="live-tag"
                        style="background: rgba(34, 184, 232, 0.2); color: #22b8e8; border: 1px solid rgba(34, 184, 232, 0.35); font-weight: 800; margin-right: 4px;">TANJIDOR</span>
                    <span class="live-tag">LIVE</span>
                </div>
                <div class="brand-sub">TANJIDOR â€¢ Tata Kelola Kinerja Digital dan Monitoring</div>
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

        <!-- ================= KPI ================= -->
        <!-- ================= GIANT KPI METRICS STRIP ================= -->
        <div class="giant-kpi-grid">
            <div class="kpi-box kpi-blue">
                <span class="kpi-title">Total Tugas</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-tree-structure"></i></div>
                <div class="kpi-number" data-count="{{ $totalPekerjaan }}">{{ number_format($totalPekerjaan) }}</div>
                <div class="kpi-subtext"><i class="ph-bold ph-check-circle"></i> Seluruh penugasan balai</div>
            </div>

            <div class="kpi-box kpi-cyan">
                <span class="kpi-title">Sedang Dikerjakan</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-rocket-launch"></i></div>
                <div class="kpi-number" data-count="{{ $pekerjaanBerjalan }}">{{ number_format($pekerjaanBerjalan) }}
                </div>
                <div class="kpi-subtext"><i class="ph-bold ph-spinner"></i> Berjalan aktif</div>
            </div>

            <div class="kpi-box kpi-purple">
                <span class="kpi-title">Menunggu Verifikasi</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-seal-check"></i></div>
                <div class="kpi-number" data-count="{{ $menungguVerifikasi }}">{{ number_format($menungguVerifikasi) }}
                </div>
                <div class="kpi-subtext"><i class="ph-bold ph-clock"></i> Menunggu tinjauan Katim</div>
            </div>

            <div class="kpi-box kpi-green">
                <span class="kpi-title">Selesai</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-check-fat"></i></div>
                <div class="kpi-number" data-count="{{ $selesaiTerverifikasi }}">
                    {{ number_format($selesaiTerverifikasi) }}
                </div>
                <div class="kpi-subtext"><i class="ph-bold ph-check"></i> Hasil terverifikasi</div>
            </div>

            <div class="kpi-box kpi-amber">
                <span class="kpi-title">Ketuntasan Tugas</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-seal-check"></i></div>
                <div class="kpi-number" data-count="{{ $selesaiTerverifikasi }}">
                    {{ $selesaiTerverifikasi }} <span
                        style="font-size: 15px; color: var(--text-muted); font-weight: 600;">/
                        {{ $totalPekerjaan }}</span>
                </div>
                <div class="kpi-subtext">
                    <i class="ph-bold ph-check-fat"></i> Tugas selesai terverifikasi
                </div>
            </div>

            <div class="kpi-box {{ $lewatTenggat > 0 ? 'kpi-red is-alert' : 'kpi-green' }}">
                <span class="kpi-title">Terlambat</span>
                <div class="kpi-icon-wrap">
                    <i class="ph-duotone {{ $lewatTenggat > 0 ? 'ph-warning-octagon' : 'ph-check-circle' }}"></i>
                </div>
                <div class="kpi-number" data-count="{{ $lewatTenggat }}">{{ number_format($lewatTenggat) }}</div>
                <div class="kpi-subtext">
                    @if($lewatTenggat > 0)
                        <i class="ph-bold ph-warning"></i> Perlu perhatian khusus
                    @else
                        <i class="ph-bold ph-check"></i> 100% tepat waktu
                    @endif
                </div>
            </div>
        </div>

        <!-- ================= DIAGRAM ================= -->
        <!-- ================= DIAGRAM ANALITIK REAL-TIME ================= -->
        <div class="diagram-section-wrapper" id="diagramSectionWrapper">
            <div class="diagram-header-bar">
                <div class="diagram-title">
                    <span class="title-ico"><i class="ph-bold ph-chart-donut"></i></span>
                    <span>Diagram Analitik Kinerja Real-Time Balai</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="diagram-hint">Klik status atau bar untuk menyaring tabel</span>
                    <button type="button" class="diagram-btn-toggle" onclick="toggleDiagramBar()">
                        <i class="ph-bold ph-caret-up" id="diagramToggleIcon"></i> <span
                            id="diagramBtnText">Tutup</span>
                    </button>
                </div>
            </div>

            <div class="diagram-content-grid" id="diagramGridContainer">
                <!-- Donut Chart Status -->
                <div class="diagram-donut-box">
                    <div class="donut-canvas-wrap">
                        <canvas id="cmdDonutStatus"></canvas>
                        <div class="donut-center-text">
                            <span class="donut-num">{{ $totalPekerjaan }}</span>
                            <span class="donut-lbl">Total Tugas</span>
                        </div>
                    </div>
                    <div class="donut-legend-mini">
                        <div class="legend-pill" onclick="filterTableByStatusClick('selesai')"
                            title="Filter tugas selesai">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#3d9970"></span>Selesai</span>
                            <strong style="color:var(--text-main)">{{ $statusCounts['selesai'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill" onclick="filterTableByStatusClick('berjalan')"
                            title="Filter tugas dikerjakan">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#0b4a82"></span>Dikerjakan</span>
                            <strong style="color:var(--text-main)">{{ $statusCounts['dikerjakan'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill" onclick="filterTableByStatusClick('menunggu_verifikasi')"
                            title="Filter menunggu verifikasi">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#7f9cc0"></span>Verifikasi</span>
                            <strong
                                style="color:var(--text-main)">{{ $statusCounts['menunggu_verifikasi'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill static">
                            <span class="legend-left"><span class="dot-status" style="background:#d3dce7"></span>Belum
                                Dimulai</span>
                            <strong style="color:var(--text-main)">{{ $statusCounts['belum_dimulai'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill" onclick="filterTableByStatusClick('terlambat')"
                            title="Filter tugas terlambat">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#c23b3b"></span>Terlambat</span>
                            <strong style="color:#c23b3b">{{ $lewatTenggat }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart Pokja & Ranking Keaktifan Personil -->
                <div class="diagram-bar-box">
                    <div class="bar-title-sub" style="flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span id="chartModeTitle"><i class="ph-bold ph-chart-bar"></i> Distribusi Ketuntasan per Tim
                                Kerja</span>
                            <span id="chartModeSub"
                                style="color: var(--primary); font-weight: 800; font-size: 11px;">(Balai:
                                {{ $selesaiTerverifikasi }}/{{ $totalPekerjaan }} Tuntas)</span>
                        </div>
                        <div class="cmd-chart-tabs"
                            style="display: inline-flex; background: var(--bg-sub); border: 1px solid var(--border); border-radius: 8px; padding: 2px; gap: 2px;">
                            <button type="button" class="btn-cmd-chart-tab active" id="btnChartModePokja"
                                onclick="switchChartMode('pokja')" title="Lihat Ketuntasan per Tim Kerja">
                                <i class="ph-bold ph-buildings"></i> Tim Kerja
                            </button>
                            <button type="button" class="btn-cmd-chart-tab" id="btnChartModeSt"
                                onclick="switchChartMode('st')"
                                title="Lihat Pegawai Paling Sering Ikut Kegiatan Surat Tugas (ST)">
                                <i class="ph-bold ph-file-text"></i> Kegiatan
                            </button>
                            <button type="button" class="btn-cmd-chart-tab" id="btnChartModeLaporan"
                                onclick="switchChartMode('laporan')"
                                title="Lihat Pegawai Paling Aktif Melaporkan Progres">
                                <i class="ph-bold ph-paper-plane-tilt"></i> Aktifitas
                            </button>
                        </div>
                    </div>
                    <div style="flex: 1; position: relative; min-height: 0;">
                        <canvas id="cmdBarPokja"></canvas>
                    </div>
                </div>

                <!-- Leaderboard & Peringkat Keaktifan Personil -->
                <div class="diagram-leaderboard-box">
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

                    <!-- Panel 1: Top Ikut ST (Kegiatan Balai) -->
                    <div class="lead-list-panel" id="panelLeadSt">
                        @forelse($topStPersonnel as $idx => $st)
                            <div class="lead-item" onclick="openPersonModalByUserId({{ $st->id }})"
                                title="Klik untuk melihat detail penugasan {{ $st->name }}">
                                <div class="lead-rank">
                                    {{ $idx === 0 ? '🥇' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : '#' . ($idx + 1))) }}
                                </div>
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

                    <!-- Panel 2: Top Laporan (Paling Aktif Kerja) -->
                    <div class="lead-list-panel" id="panelLeadReport" style="display: none;">
                        @forelse($topReportPersonnel as $idx => $rp)
                            <div class="lead-item" onclick="openPersonModalByUserId({{ $rp->id }})"
                                title="Klik untuk melihat riwayat laporan {{ $rp->name }}">
                                <div class="lead-rank">
                                    {{ $idx === 0 ? '🥇' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : '#' . ($idx + 1))) }}
                                </div>
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
                                class="ph-bold ph-file-text"></i> <strong>{{ $totalKegiatanSt }}</strong> Kegiatan
                            ST</span>
                        <span>•</span>
                        <span title="Total laporan progres yang telah dihimpun balai"><i
                                class="ph-bold ph-paper-plane-tilt"></i> <strong>{{ $totalLaporanBalai }}</strong>
                            Laporan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= BODY ================= -->
        <div class="command-body-grid">

            <!-- Kolom kiri -->
            <div class="left-col">

                <div class="panel-card side-card">

                    <!-- Saklar Tim Kerja / Personil -->
                    <div class="side-switch" role="tablist">
                        <button type="button" class="side-tab active" id="sideTabPokja" data-pane="pokja"
                            onclick="switchSidePane('pokja')">
                            <i class="ph-bold ph-buildings"></i> Tim Kerja
                            <span class="side-count">{{ $pokjas->count() }}</span>
                        </button>
                        <button type="button" class="side-tab" id="sideTabStaf" data-pane="staf"
                            onclick="switchSidePane('staf')">
                            <i class="ph-bold ph-users-three"></i> Personil
                            <span class="side-count">{{ $topAssignees->count() }}</span>
                        </button>
                    </div>

                    <!-- Panel: Capaian Tim Kerja -->
                    <div class="side-pane" id="panePokja">
                        <div class="scroll-area" id="pokjaScrollContainer">
                            <div class="pokja-list">
                                <a href="{{ route('monitoring.balai') }}"
                                    class="pokja-item pokja-item-all {{ !request('pokja_id') ? 'active' : '' }}">
                                    <span class="lbl"><i class="ph-bold ph-squares-four"></i> Semua Unit &amp;
                                        Tim</span>
                                    <span class="cnt">{{ $totalPekerjaan }} tugas</span>
                                </a>

                                @foreach($pokjas as $p)
                                    <a href="{{ route('monitoring.balai', array_merge(request()->except(['pokja_id']), ['pokja_id' => $p->id])) }}"
                                        class="pokja-item {{ $p->total_tugas == 0 ? 'is-empty' : '' }} {{ request('pokja_id') == $p->id ? 'active' : '' }}"
                                        title="{{ $p->nama_pokja }} (Katim: {{ $p->katim_name }})">
                                        <div class="pokja-top">
                                            <span class="pokja-name-clean">{{ $p->nama_pokja }}</span>
                                            <span
                                                class="pokja-percent">{{ $p->total_tugas > 0 ? $p->tugas_selesai . '/' . $p->total_tugas . ' tuntas' : '-' }}</span>
                                        </div>
                                        <div class="pokja-meta">
                                            <span class="pokja-code-tag">{{ $p->kode_pokja }}</span>
                                            <span class="pokja-done-badge">
                                                {{ $p->total_tugas > 0 ? round(($p->tugas_selesai / $p->total_tugas) * 100, 0) . '% Selesai' : 'Belum ada tugas' }}
                                            </span>
                                        </div>
                                        <div class="pokja-track">
                                            <div class="pokja-fill"
                                                style="width: {{ $p->total_tugas > 0 ? round(($p->tugas_selesai / $p->total_tugas) * 100, 1) : 0 }}%;">
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Panel: Beban Kerja Personil -->
                    <div class="side-pane" id="paneStaf" hidden>
                        <div class="assignee-tabs">
                            <button type="button" class="btn-assignee-tab active"
                                onclick="switchAssigneeTab('all', this)">
                                Semua {{ $topAssignees->count() }}
                            </button>
                            <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('pegawai', this)">
                                Pelaksana
                                {{ $topAssignees->filter(fn($u) => in_array($u->role, ['pegawai', 'tim_pokja']))->count() }}
                            </button>
                            <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('katim', this)">
                                Katim &amp; Koor
                                {{ $topAssignees->filter(fn($u) => in_array($u->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']))->count() }}
                            </button>
                        </div>

                        <div class="assignee-search-wrap">
                            <i class="ph-bold ph-magnifying-glass"></i>
                            <input type="text" id="assigneeSearchInput" placeholder="Cari personil..."
                                class="assignee-search-input" oninput="filterAssigneeRows(this.value)">
                        </div>

                        <!-- Strip Pengurutan Keaktifan Personil -->
                        <div class="assignee-sort-strip"
                            style="display: flex; align-items: center; justify-content: space-between; padding: 6px 14px; background: var(--bg-sub); border-bottom: 1px solid var(--border); font-size: 11px;">
                            <span
                                style="color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 4px;"><i
                                    class="ph-bold ph-arrows-down-up"></i> Urut:</span>
                            <div style="display: flex; gap: 4px;">
                                <button type="button" class="btn-sort-pill active" id="sortBtnActive"
                                    onclick="sortAssigneeList('active', this)"
                                    title="Urutkan dari beban tugas berjalan">Beban</button>
                                <button type="button" class="btn-sort-pill" id="sortBtnSt"
                                    onclick="sortAssigneeList('st', this)"
                                    title="Urutkan dari yang paling banyak ikut Surat Tugas">Sering ST</button>
                                <button type="button" class="btn-sort-pill" id="sortBtnReport"
                                    onclick="sortAssigneeList('report', this)"
                                    title="Urutkan dari yang paling aktif lapor progres">Aktif Lapor</button>
                            </div>
                        </div>

                        <div class="scroll-area" id="assigneeListContainer">
                            @foreach($topAssignees as $staf)
                                @php
                                    $isKatimOrKoor = in_array($staf->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']);
                                    $roleCategory = $isKatimOrKoor ? 'katim' : 'pegawai';
                                    $roleBadgeClass = $staf->role === 'pegawai' || $staf->role === 'tim_pokja' ? 'role-pill-pegawai' : ($staf->role === 'katim' ? 'role-pill-katim' : 'role-pill-kor');
                                    $avClass = '';
                                    $totalActiveVal = $staf->total_active ?? ($staf->pic_count + $staf->member_count);
                                    $stCountVal = $staf->st_count ?? 0;
                                    $repCountVal = $staf->laporan_count ?? 0;
                                @endphp
                                <div class="assignee-item" data-role-category="{{ $roleCategory }}"
                                    data-name="{{ strtolower($staf->name) }}" data-fullname="{{ $staf->name }}"
                                    data-active-count="{{ $totalActiveVal }}" data-st-count="{{ $stCountVal }}"
                                    data-rep-count="{{ $repCountVal }}" data-staf="{{ json_encode($staf) }}"
                                    onclick="openPersonModal(this)"
                                    title="Klik untuk melihat rincian penugasan & laporan {{ $staf->name }}"
                                    style="cursor: pointer;">
                                    <div class="assignee-avatar {{ $avClass }}">{{ strtoupper(substr($staf->name, 0, 2)) }}
                                    </div>
                                    <div style="overflow: hidden; flex: 1;">
                                        <div class="assignee-name" title="{{ $staf->name }}">{{ $staf->name }}</div>
                                        <div class="assignee-sub">
                                            <span>{{ $staf->pokja_kode ?? ($staf->pokja?->kode_pokja ?? 'BGTK') }}</span>
                                            <span class="role-pill {{ $roleBadgeClass }}">{{ $staf->role_label }}</span>
                                        </div>
                                        <div
                                            style="display: flex; gap: 4px; align-items: center; margin-top: 4px; flex-wrap: wrap;">
                                            @if($stCountVal > 0)
                                                <span class="cmd-st-pill"
                                                    title="{{ $stCountVal }} kegiatan berbasis Surat Tugas (ST)">
                                                    <i class="ph-bold ph-file-text"></i> {{ $stCountVal }} ST
                                                </span>
                                            @endif
                                            @if($repCountVal > 0)
                                                <span class="cmd-rep-pill" title="{{ $repCountVal }} laporan progres diajukan">
                                                    <i class="ph-bold ph-paper-plane-tilt"></i> {{ $repCountVal }} Lapor
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div
                                            class="assignee-count-badge {{ $totalActiveVal > 0 ? 'has-workload' : 'zero-workload' }}">
                                            {{ $totalActiveVal }} tugas
                                        </div>
                                        <button type="button"
                                            onclick="event.stopPropagation(); filterTableByAssignee('{{ addslashes($staf->name) }}')"
                                            class="btn-filter-icon" title="Saring tabel utama berdasarkan {{ $staf->name }}"
                                            style="background: none; border: 1px solid var(--border); border-radius: 6px; padding: 4px 6px; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                            <i class="ph-bold ph-funnel" style="font-size: 11.5px;"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="assignee-footer" style="font-size: 11px;">
                            <i class="ph-bold ph-cursor-click"></i> Klik personil untuk melihat detail tugas & laporan
                        </div>
                    </div>

                </div>

            </div>

            <!-- Kolom kanan: tabel -->
            <div class="panel-card">

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
                            Menunggu Verifikasi <span class="num">{{ $menungguVerifikasi }}</span>
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

                    <form method="GET" action="{{ route('monitoring.balai') }}"
                        style="display: flex; gap: 8px; align-items: center;">
                        @if(request('pokja_id')) <input type="hidden" name="pokja_id" value="{{ request('pokja_id') }}">
                        @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

                        <div class="stream-search">
                            <i class="ph-bold ph-magnifying-glass"></i>
                            <input type="text" name="q" id="searchFilterInput" value="{{ request('q') }}"
                                placeholder="Cari tugas, kode, atau PIC..." onkeyup="clientFilterTable(this.value)">
                        </div>

                        @if(request()->hasAny(['pokja_id', 'status', 'q']))
                            <a href="{{ route('monitoring.balai') }}" class="btn-reset" title="Hapus semua filter">
                                <i class="ph-bold ph-x"></i> Hapus filter
                            </a>
                        @endif
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
                                    $sisaHari = \Carbon\Carbon::today()->diffInDays($job->tenggat, false);
                                    $rowLate = $isTerlambat && $job->status_pelaksanaan !== 'selesai_terverifikasi';
                                    $picName = $job->penanggungJawab?->name ?? 'PJ';
                                    $picAv = '';

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
                                                <span class="st-badge-mini" title="Surat Tugas Nomor: {{ $job->nomor_st }}">
                                                    <i class="ph-bold ph-file-text"></i> ST
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td><span class="task-pokja-badge">{{ $job->pokja?->kode_pokja ?? 'BGTK' }}</span></td>

                                    <td>
                                        <div class="pic-cell">
                                            <div class="pic-avatar-mini {{ $picAv }}">
                                                {{ strtoupper(substr($picName, 0, 2)) }}
                                            </div>
                                            <div style="overflow: hidden; flex: 1;">
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
                                        <div
                                            style="font-size: 12px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 5px;">
                                            <i class="ph-bold ph-newspaper-clipping" style="color: #64748b;"></i>
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
                                        <button type="button" class="btn-detail-cmd">
                                            <i class="ph-bold ph-eye"></i> Detail
                                        </button>
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
                </div>

                <div class="table-footer">
                    <span><i class="ph-bold ph-list-checks"></i>Menampilkan <b id="rowShown">0</b> dari <b
                            id="rowTotal">0</b> tugas</span>
                    <span><i class="ph-bold ph-cursor-click"></i>Klik baris untuk melihat rincian</span>
                </div>

            </div>
        </div>
    </main>

    <!-- ================= MODAL DETAIL ================= -->
    <!-- ================= MODAL DETAIL TUGAS BALAI ================= -->
    <div id="jobDetailModal" class="cmd-modal-backdrop" onclick="closeJobModal(event)">
        <div class="cmd-modal-dialog" onclick="event.stopPropagation()">
            <div class="cmd-modal-header">
                <div style="flex: 1; overflow: hidden;">
                    <div class="cmd-modal-badges">
                        <span id="mJobCode" class="task-code" style="font-size: 12px; padding: 3px 9px;">-</span>
                        <span id="mJobStatus" class="status-badge-cmd">-</span>
                        <span id="mJobPokja" class="task-pokja-badge" style="background: rgba(255,255,255,.2);">-</span>
                        <span id="mJobST" class="st-badge-mini" style="display: none;">
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
                        <div class="cmd-kpi-val" id="mJobLaporanCount" style="font-size: 15px;">0 Laporan</div>
                        <div class="cmd-kpi-sub">Verifikasi Katim</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Target Hasil</div>
                        <div class="cmd-kpi-val" id="mJobTarget">-</div>
                        <div class="cmd-kpi-sub">Volume sasaran</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Status Penugasan</div>
                        <div class="cmd-kpi-val" id="mJobStatusPelaksanaan" style="font-size: 13px;">-</div>
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

                <!-- Daftar Riwayat Laporan Progres Masuk -->
                <div class="cmd-modal-box" id="mJobLaporanSection">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 class="cmd-box-title" style="margin-bottom: 0;">
                            <i class="ph-bold ph-newspaper-clipping"></i> Riwayat Laporan Progres Masuk
                        </h4>
                        <span id="mJobLaporanBadge" class="cmd-team-chip">0 Laporan</span>
                    </div>

                    <div id="mJobLaporanList" style="display: flex; flex-direction: column; gap: 10px;">
                        <!-- Diisi via JavaScript -->
                    </div>
                </div>
            </div>

            <div class="cmd-modal-footer">
                <button type="button" class="btn-cmd-secondary" onclick="closeJobModalDirect()">
                    <i class="ph-bold ph-x"></i> Tutup
                </button>
                <a id="mJobLink" href="#" target="_blank" class="btn-cmd-primary">
                    <i class="ph-bold ph-arrow-square-out"></i> Buka Halaman Tugas Lengkap
                </a>
            </div>
        </div>
    </div>

    <!-- ================= MODAL DETAIL PERSONIL & PENUGASAN ================= -->
    <div id="personDetailModal" class="cmd-modal-backdrop" onclick="closePersonModal(event)">
        <div class="cmd-modal-dialog" onclick="event.stopPropagation()" style="max-width: 840px;">
            <div class="cmd-modal-header" style="background: linear-gradient(135deg, #0b4a82 0%, #083a68 100%);">
                <div style="flex: 1; overflow: hidden; display: flex; align-items: center; gap: 14px;">
                    <div id="pPersonAvatar"
                        style="width: 52px; height: 52px; border-radius: 50%; background: rgba(255, 255, 255, 0.18); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; border: 2px solid rgba(255, 255, 255, 0.35); flex-shrink: 0;">
                        PJ
                    </div>
                    <div style="overflow: hidden;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <span id="pPersonPokja" class="task-pokja-badge"
                                style="background: rgba(255, 255, 255, 0.2); color: white;">BGTK</span>
                            <span id="pPersonRole" class="role-pill role-pill-pegawai"
                                style="background: white; color: #0b4a82;">Pegawai</span>
                        </div>
                        <h3 id="pPersonName" class="cmd-modal-title" style="color: white; margin: 0; font-size: 18px;">
                            Nama Pegawai</h3>
                        <div id="pPersonSub" style="font-size: 12px; color: #bae6fd; margin-top: 2px;">NIP: - &bull;
                            Jabatan: -</div>
                    </div>
                </div>
                <button type="button" class="cmd-modal-close" onclick="closePersonModalDirect()" title="Tutup">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>

            <div class="cmd-modal-body" style="padding: 20px 24px;">
                <!-- KPI ringkasan 6 kolom -->
                <div class="cmd-modal-kpi-grid" style="grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Total Beban Aktif</div>
                        <div class="cmd-kpi-val" id="pPersonActiveBeban" style="color: #0a6fb5;">0 Tugas</div>
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
                        <div class="cmd-kpi-val" id="pPersonDoneCount" style="color: #0c8a5d;">0 Tugas</div>
                        <div class="cmd-kpi-sub">Terverifikasi tuntas</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Kegiatan ST</div>
                        <div class="cmd-kpi-val" id="pPersonStCount" style="color: #6366f1;">0 ST</div>
                        <div class="cmd-kpi-sub">Surat tugas resmi</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Laporan Diajukan</div>
                        <div class="cmd-kpi-val" id="pPersonRepCount" style="color: #10b981;">0 Laporan</div>
                        <div class="cmd-kpi-sub">Progres tugas masuk</div>
                    </div>
                </div>

                <!-- Tabs: Tugas vs Laporan -->
                <div style="display: flex; gap: 8px; border-bottom: 2px solid var(--border); margin-top: 4px;">
                    <button type="button" id="tabBtnPersonJobs" onclick="switchPersonTab('jobs')"
                        style="padding: 9px 18px; font-weight: 800; font-size: 13px; border: none; background: none; border-bottom: 3px solid #0a6fb5; color: #0a6fb5; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; margin-bottom: -2px;">
                        <i class="ph-bold ph-briefcase"></i> Pekerjaan Ditugaskan (<span
                            id="pPersonJobsTabCount">0</span>)
                    </button>
                    <button type="button" id="tabBtnPersonReports" onclick="switchPersonTab('reports')"
                        style="padding: 9px 18px; font-weight: 700; font-size: 13px; border: none; background: none; border-bottom: 3px solid transparent; color: var(--text-muted); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; margin-bottom: -2px;">
                        <i class="ph-bold ph-newspaper-clipping"></i> Laporan Progres Diajukan (<span
                            id="pPersonReportsTabCount">0</span>)
                    </button>
                </div>

                <!-- Panel Tab 1: Daftar Pekerjaan Personil -->
                <div id="pPersonJobsPanel" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Diisi via JavaScript -->
                </div>

                <!-- Panel Tab 2: Riwayat Laporan Progres Personil -->
                <div id="pPersonReportsPanel" style="display: none; flex-direction: column; gap: 10px;">
                    <!-- Diisi via JavaScript -->
                </div>
            </div>

            <div class="cmd-modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <button type="button" id="pPersonFilterBtn" class="btn-cmd-secondary"
                    style="color: #0a6fb5; border-color: #bae6fd;">
                    <i class="ph-bold ph-funnel"></i> Saring Tabel Berdasarkan Personil Ini
                </button>
                <button type="button" class="btn-cmd-secondary" onclick="closePersonModalDirect()">
                    <i class="ph-bold ph-x"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ================= UNIVERSAL FILE PREVIEW MODAL ================= -->
    <div id="modalPreviewBuktiUniversal" class="cmd-modal-backdrop"
        style="display: none; z-index: 1100; align-items: center; justify-content: center; padding: 20px;"
        onclick="closePreviewBuktiOnBackdrop(event)">
        <div class="cmd-modal-dialog"
            style="max-width: 900px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;"
            onclick="event.stopPropagation()">
            <div class="cmd-modal-header" style="padding: 14px 20px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 12px; overflow: hidden;">
                    <div id="previewBuktiIconBox"
                        style="width: 36px; height: 36px; border-radius: 8px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                        <i class="ph-bold ph-file-text" id="previewBuktiIcon"></i>
                    </div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 11px; font-weight: 700; color: #bae6fd; text-transform: uppercase;">
                            Pratinjau Bukti Dukung</div>
                        <div id="previewBuktiTitle"
                            style="font-size: 14px; font-weight: 800; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;">
                            Nama Berkas</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <a id="previewBuktiExternalBtn" href="#" target="_blank" class="btn-action-outline"
                        style="padding: 6px 12px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; color: #ffffff; border: 1px solid rgba(255,255,255,0.4); border-radius: 6px;"
                        title="Buka di Tab Baru">
                        <i class="ph-bold ph-arrow-square-out"></i> Tab Baru
                    </a>
                    <a id="previewBuktiDownloadBtn" href="#" download class="btn-action-primary"
                        style="padding: 6px 14px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; background: #0284c7; color: white; border-radius: 6px;"
                        title="Unduh Berkas">
                        <i class="ph-bold ph-download-simple"></i> Unduh
                    </a>
                    <button type="button" class="cmd-modal-close" onclick="closePreviewBukti()" title="Tutup"
                        style="margin-left: 6px;">&times;</button>
                </div>
            </div>
            <div id="previewBuktiBody"
                style="flex: 1; overflow: auto; padding: 16px; background: var(--bg-base, #f1f5f9); display: flex; align-items: center; justify-content: center; min-height: 380px;">
            </div>
            <div class="cmd-modal-footer"
                style="padding: 10px 20px; justify-content: space-between; align-items: center;">
                <div style="font-size: 12px; color: var(--text-muted);">
                    Format Berkas: <span id="previewBuktiExtBadge"
                        style="font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 2px 7px; border-radius: 4px; text-transform: uppercase;"></span>
                </div>
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
            try { localStorage.setItem('bgtkTheme', next); } catch (e) { }
            applyThemeIcon();
            try { switchSidePane(localStorage.getItem('bgtkSidePane') || 'pokja'); } catch (e) { }
            updateRowCount();
            initMonitoringCharts();
        }

        // ============================================================
        // 2. Diagram Chart.js
        // ============================================================
        let chartDonutInstance = null;
        let chartBarInstance = null;

        function cssVar(name) {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        }

        function initMonitoringCharts() {
            const gridColor = cssVar('--border');
            const textMuted = cssVar('--text-muted');
            const textMain = cssVar('--text-body');
            const cardBg = cssVar('--bg-card');

            const ctxDonut = document.getElementById('cmdDonutStatus');
            if (ctxDonut) {
                if (chartDonutInstance) chartDonutInstance.destroy();
                chartDonutInstance = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Dikerjakan', 'Verifikasi', 'Belum Dimulai'],
                        datasets: [{
                            data: [
                                {{ $statusCounts['selesai'] ?? 0 }},
                                {{ $statusCounts['dikerjakan'] ?? 0 }},
                                {{ $statusCounts['menunggu_verifikasi'] ?? 0 }},
                                {{ $statusCounts['belum_dimulai'] ?? 0 }}
                            ],
                            backgroundColor: ['#3d9970', '#0b4a82', '#7f9cc0', '#d3dce7'],
                            borderWidth: 3,
                            borderColor: cardBg,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        animation: { animateRotate: true, duration: 1100 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#062f57',
                                titleFont: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                                cornerRadius: 8,
                                padding: 10,
                                callbacks: {
                                    label: function (ctx) {
                                        const val = ctx.parsed || 0;
                                        return ` ${ctx.label}: ${val} tugas`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            renderBarChart(currentChartMode);
        }

        const pokjaChartData = {
            labels: {!! json_encode(collect($pokjaChartData ?? [])->pluck('kode')) !!},
            names: {!! json_encode(collect($pokjaChartData ?? [])->pluck('nama')) !!},
            data: {!! json_encode(collect($pokjaChartData ?? [])->pluck('selesai')) !!},
            totals: {!! json_encode(collect($pokjaChartData ?? [])->pluck('total')) !!}
        };

        const stChartData = {
            ids: {!! json_encode(collect($stChartData ?? [])->pluck('id')) !!},
            labels: {!! json_encode(collect($stChartData ?? [])->pluck('short_name')) !!},
            names: {!! json_encode(collect($stChartData ?? [])->pluck('name')) !!},
            pokja: {!! json_encode(collect($stChartData ?? [])->pluck('pokja')) !!},
            data: {!! json_encode(collect($stChartData ?? [])->pluck('st_count')) !!},
            totals: {!! json_encode(collect($stChartData ?? [])->pluck('total_jobs')) !!}
        };

        const reportChartData = {
            ids: {!! json_encode(collect($reportChartData ?? [])->pluck('id')) !!},
            labels: {!! json_encode(collect($reportChartData ?? [])->pluck('short_name')) !!},
            names: {!! json_encode(collect($reportChartData ?? [])->pluck('name')) !!},
            pokja: {!! json_encode(collect($reportChartData ?? [])->pluck('pokja')) !!},
            data: {!! json_encode(collect($reportChartData ?? [])->pluck('laporan_count')) !!},
            totals: {!! json_encode(collect($reportChartData ?? [])->pluck('total_jobs')) !!}
        };

        let currentChartMode = 'pokja';

        function switchChartMode(mode) {
            currentChartMode = mode;
            document.querySelectorAll('.btn-cmd-chart-tab').forEach(b => b.classList.remove('active'));
            const btn = document.getElementById(mode === 'pokja' ? 'btnChartModePokja' : (mode === 'st' ? 'btnChartModeSt' : 'btnChartModeLaporan'));
            if (btn) btn.classList.add('active');

            const titleEl = document.getElementById('chartModeTitle');
            const subEl = document.getElementById('chartModeSub');

            if (mode === 'pokja') {
                if (titleEl) titleEl.innerHTML = '<i class="ph-bold ph-chart-bar"></i> Distribusi Ketuntasan per Tim Kerja';
                if (subEl) subEl.textContent = `(Balai: {{ $selesaiTerverifikasi }}/{{ $totalPekerjaan }} Tuntas)`;
            } else if (mode === 'st') {
                if (titleEl) titleEl.innerHTML = '<i class="ph-bold ph-file-text"></i> Pegawai Paling Sering Ikut Kegiatan (Surat Tugas)';
                if (subEl) subEl.textContent = `({{ $totalKegiatanSt }} Kegiatan ST Aktif)`;
            } else {
                if (titleEl) titleEl.innerHTML = '<i class="ph-bold ph-paper-plane-tilt"></i> Pegawai Paling Aktif Melaporkan Progres Tugas';
                if (subEl) subEl.textContent = `({{ $totalLaporanBalai }} Laporan Masuk)`;
            }

            renderBarChart(mode);
        }

        function renderBarChart(mode) {
            const ctxBar = document.getElementById('cmdBarPokja');
            if (!ctxBar) return;
            if (chartBarInstance) chartBarInstance.destroy();

            const gridColor = cssVar('--border');
            const textMuted = cssVar('--text-muted');
            const textMain = cssVar('--text-body');
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';

            let labels = [];
            let data = [];
            let barColor = '';
            let datasetLabel = '';

            if (mode === 'pokja') {
                labels = pokjaChartData.labels;
                data = pokjaChartData.data;
                barColor = dark ? '#34d399' : '#10b981';
                datasetLabel = 'Tugas Selesai';
            } else if (mode === 'st') {
                labels = stChartData.labels;
                data = stChartData.data;
                barColor = dark ? '#818cf8' : '#6366f1';
                datasetLabel = 'Kegiatan ST';
            } else {
                labels = reportChartData.labels;
                data = reportChartData.data;
                barColor = dark ? '#38bdf8' : '#0284c7';
                datasetLabel = 'Laporan Diajukan';
            }

            chartBarInstance = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: datasetLabel,
                        data: data,
                        backgroundColor: barColor,
                        borderRadius: 7,
                        borderSkipped: false,
                        maxBarThickness: 28
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 600 },
                    onClick: (e, elements) => {
                        if (elements && elements.length > 0) {
                            const idx = elements[0].index;
                            if (mode === 'pokja') {
                                const code = pokjaChartData.labels[idx];
                                const searchInput = document.getElementById('searchFilterInput');
                                if (searchInput) {
                                    searchInput.value = code;
                                    clientFilterTable(code);
                                }
                            } else if (mode === 'st') {
                                const userId = stChartData.ids[idx];
                                if (userId) openPersonModalByUserId(userId);
                            } else {
                                const userId = reportChartData.ids[idx];
                                if (userId) openPersonModalByUserId(userId);
                            }
                        }
                    },
                    onHover: (e, els) => { e.native.target.style.cursor = els.length ? 'pointer' : 'default'; },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#062f57',
                            titleFont: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                            cornerRadius: 8,
                            padding: 10,
                            callbacks: {
                                title: (items) => {
                                    const idx = items[0].dataIndex;
                                    if (mode === 'pokja') return `${pokjaChartData.labels[idx]} - ${pokjaChartData.names[idx]}`;
                                    if (mode === 'st') return `${stChartData.names[idx]} (${stChartData.pokja[idx]})`;
                                    return `${reportChartData.names[idx]} (${reportChartData.pokja[idx]})`;
                                },
                                label: (c) => {
                                    const idx = c.dataIndex;
                                    if (mode === 'pokja') return ` Ketuntasan: ${c.parsed.y} dari ${pokjaChartData.totals[idx]} tugas tuntas`;
                                    if (mode === 'st') return ` Terlibat dalam: ${c.parsed.y} kegiatan Surat Tugas (ST)`;
                                    return ` Telah mengajukan: ${c.parsed.y} laporan progres tugas`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { family: 'Plus Jakarta Sans', size: 10 }, color: textMuted },
                            grid: { color: gridColor, drawTicks: false },
                            border: { display: false }
                        },
                        x: {
                            ticks: { font: { family: 'JetBrains Mono', size: 9.5, weight: 'bold' }, color: textMain },
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        function switchLeaderboardTab(tab) {
            const isSt = tab === 'st';
            const tabSt = document.getElementById('tabLeadSt');
            const tabRep = document.getElementById('tabLeadReport');
            const pSt = document.getElementById('panelLeadSt');
            const pRep = document.getElementById('panelLeadReport');
            if (tabSt) tabSt.classList.toggle('active', isSt);
            if (tabRep) tabRep.classList.toggle('active', !isSt);
            if (pSt) pSt.style.display = isSt ? 'flex' : 'none';
            if (pRep) pRep.style.display = !isSt ? 'flex' : 'none';
        }

        function openPersonModalByUserId(userId) {
            if (!userId) return;
            const items = document.querySelectorAll('#assigneeListContainer .assignee-item');
            for (let item of items) {
                try {
                    const staf = JSON.parse(item.dataset.staf);
                    if (Number(staf.id) === Number(userId)) {
                        openPersonModal(item);
                        return;
                    }
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

            const items = Array.from(container.querySelectorAll('.assignee-item'));
            items.sort((a, b) => {
                let valA = 0;
                let valB = 0;
                if (criteria === 'st') {
                    valA = parseInt(a.dataset.stCount || '0', 10);
                    valB = parseInt(b.dataset.stCount || '0', 10);
                } else if (criteria === 'report') {
                    valA = parseInt(a.dataset.repCount || '0', 10);
                    valB = parseInt(b.dataset.repCount || '0', 10);
                } else {
                    valA = parseInt(a.dataset.activeCount || '0', 10);
                    valB = parseInt(b.dataset.activeCount || '0', 10);
                }
                if (valB !== valA) return valB - valA;
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
            if (isDiagramVisible) { chartDonutInstance?.resize(); chartBarInstance?.resize(); }
        }

        function filterTableByStatusClick(status) {
            window.location.href = "{{ route('monitoring.balai') }}?status=" + status;
        }

        // ============================================================
        // 3. Animasi angka KPI
        // ============================================================
        function animateKpiNumbers() {
            document.querySelectorAll('.kpi-number[data-count]').forEach(el => {
                const target = parseFloat(el.dataset.count) || 0;
                const dec = parseInt(el.dataset.decimals || '0', 10);
                const suffix = el.dataset.suffix || '';
                const fmt = v => v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }) + suffix;
                const start = performance.now();
                const dur = 900;
                (function tick(now) {
                    const p = Math.min(1, (now - start) / dur);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = fmt(target * eased);
                    if (p < 1) requestAnimationFrame(tick);
                })(start);
            });
        }

        // ============================================================
        // 4. Modal detail tugas & laporan
        // ============================================================
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

            const laporanEl = document.getElementById('mJobLaporanCount');
            if (laporanEl) laporanEl.textContent = (job.laporan_acc_count || 0) + ' Laporan';

            document.getElementById('mJobTarget').textContent = job.target || '-';
            const statusPelaksanaanEl = document.getElementById('mJobStatusPelaksanaan');
            if (statusPelaksanaanEl) statusPelaksanaanEl.textContent = job.status_label || '-';
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
                        <div style="overflow:hidden;flex:1;">
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

            // Render Riwayat Laporan Progres Masuk
            const laporanListEl = document.getElementById('mJobLaporanList');
            const laporanBadgeEl = document.getElementById('mJobLaporanBadge');
            if (laporanListEl) {
                laporanListEl.innerHTML = '';
                const reports = job.laporan_list || [];
                if (laporanBadgeEl) laporanBadgeEl.textContent = reports.length + ' Laporan';

                if (reports.length > 0) {
                    reports.forEach(rep => {
                        const repItem = document.createElement('div');
                        repItem.style.background = 'var(--bg-card)';
                        repItem.style.border = '1px solid var(--border)';
                        repItem.style.borderRadius = '8px';
                        repItem.style.padding = '12px 14px';

                        let statusBadgeHtml = '';
                        if (rep.status_approval === 'disetujui') {
                            statusBadgeHtml = '<span class="status-badge-cmd status-badge-selesai" style="font-size:10.5px;padding:2px 8px;"><i class="ph-bold ph-check"></i> Disetujui</span>';
                        } else if (rep.status_approval === 'ditolak') {
                            statusBadgeHtml = '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 8px;background:rgba(239,68,68,0.15);color:#ef4444;border-color:rgba(239,68,68,0.3);"><i class="ph-bold ph-x"></i> Perlu Perbaikan</span>';
                        } else {
                            statusBadgeHtml = '<span class="status-badge-cmd status-badge-verifikasi" style="font-size:10.5px;padding:2px 8px;"><i class="ph-bold ph-clock"></i> Menunggu Persetujuan</span>';
                        }

                        const jenisBadgeHtml = rep.is_penyelesaian
                            ? '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 8px;background:rgba(147,51,234,0.15);color:#c084fc;border-color:rgba(147,51,234,0.3);"><i class="ph-bold ph-seal-check"></i> Penyelesaian Akhir</span>'
                            : '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 8px;background:rgba(14,165,233,0.15);color:#38bdf8;border-color:rgba(14,165,233,0.3);"><i class="ph-bold ph-pencil-simple-line"></i> Kemajuan Berkala</span>';

                        let buktiHtml = '';
                        if (rep.file_bukti) {
                            const escapedUrl = escapeHtml(rep.file_bukti);
                            const escapedName = escapeHtml(rep.file_name || 'Berkas Bukti Dukung');
                            buktiHtml += `<button type="button" class="btn-action-outline" style="padding:4px 9px;font-size:11px;border-radius:5px;cursor:pointer;color:#38bdf8;border-color:rgba(56,189,248,0.3);background:rgba(56,189,248,0.08);display:inline-flex;align-items:center;gap:4px;" onclick="previewBuktiDukung('${escapedUrl}', '${escapedName}')"><i class="ph-bold ph-eye"></i> Pratinjau Berkas</button>`;
                        }
                        if (rep.link_bukti) {
                            const escapedLink = escapeHtml(rep.link_bukti);
                            buktiHtml += `<a href="${escapedLink}" target="_blank" class="btn-action-outline" style="padding:4px 9px;font-size:11px;border-radius:5px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;color:var(--text-muted);border:1px solid var(--border);"><i class="ph-bold ph-arrow-square-out"></i> Tautan</a>`;
                        }

                        let kendalaHtml = '';
                        if (rep.kendala) {
                            kendalaHtml = `<div style="margin-top:6px;font-size:11.5px;color:#f87171;background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:4px 8px;border-radius:0 4px 4px 0;"><strong>Kendala:</strong> ${escapeHtml(rep.kendala)}</div>`;
                        }
                        let tindakLanjutHtml = '';
                        if (rep.tindak_lanjut) {
                            tindakLanjutHtml = `<div style="margin-top:4px;font-size:11.5px;color:#38bdf8;background:rgba(14,165,233,0.08);border-left:3px solid #0284c7;padding:4px 8px;border-radius:0 4px 4px 0;"><strong>Tindak Lanjut:</strong> ${escapeHtml(rep.tindak_lanjut)}</div>`;
                        }

                        repItem.innerHTML = `
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="cmd-member-avatar" style="width:28px;height:28px;font-size:11px;">${escapeHtml(rep.pelapor_initial || 'PL')}</div>
                                    <div>
                                        <div style="font-size:12.5px;font-weight:700;color:var(--text-body);">${escapeHtml(rep.pelapor || '-')}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">${escapeHtml(rep.tanggal || '-')}</div>
                                    </div>
                                </div>
                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                    ${jenisBadgeHtml}
                                    ${statusBadgeHtml}
                                </div>
                            </div>
                            <div style="font-size:12px;color:var(--text-body);line-height:1.45;margin-bottom:4px;">
                                ${escapeHtml(rep.deskripsi || '-')}
                            </div>
                            ${kendalaHtml}
                            ${tindakLanjutHtml}
                            ${buktiHtml ? `<div style="margin-top:8px;display:flex;gap:6px;align-items:center;flex-wrap:wrap;">${buktiHtml}</div>` : ''}
                        `;
                        laporanListEl.appendChild(repItem);
                    });
                } else {
                    laporanListEl.innerHTML = `
                        <div style="text-align:center;padding:18px;color:var(--text-muted);font-size:12px;">
                            <i class="ph-bold ph-tray" style="font-size:22px;display:block;margin-bottom:4px;opacity:0.4;"></i>
                            Belum ada laporan progres yang diajukan untuk tugas ini.
                        </div>`;
                }
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
        // 4b. Modal detail personil & tugas/laporannya
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
                const stCountEl = document.getElementById('pPersonStCount');
                if (stCountEl) stCountEl.textContent = (staf.st_count || 0) + ' ST';
                const repCountEl = document.getElementById('pPersonRepCount');
                if (repCountEl) repCountEl.textContent = (staf.laporan_count || 0) + ' Laporan';

                const jobs = staf.jobs || [];
                const reports = staf.laporan || [];

                document.getElementById('pPersonJobsTabCount').textContent = jobs.length;
                document.getElementById('pPersonReportsTabCount').textContent = reports.length;

                // Render Tab 1: Pekerjaan
                const jobsPanel = document.getElementById('pPersonJobsPanel');
                jobsPanel.innerHTML = '';
                if (jobs.length > 0) {
                    jobs.forEach(j => {
                        const jCard = document.createElement('div');
                        jCard.className = 'cmd-pic-card';
                        jCard.style.padding = '12px 14px';
                        jCard.style.background = 'var(--bg-card)';
                        jCard.style.border = '1px solid var(--border)';
                        jCard.style.borderRadius = '8px';

                        const peranBadge = j.is_pic
                            ? '<span class="status-badge-cmd status-badge-dikerjakan" style="font-size:10.5px;padding:2px 7px;"><i class="ph-bold ph-crown"></i> PIC Utama</span>'
                            : '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 7px;background:rgba(148,163,184,0.15);color:var(--text-muted);border-color:var(--border);"><i class="ph-bold ph-users"></i> Anggota Tim</span>';

                        const stBadge = j.nomor_st ? `<span class="st-badge-mini" style="font-size:10px;display:inline-flex;align-items:center;gap:3px;" title="Surat Tugas: ${escapeHtml(j.nomor_st)}"><i class="ph-bold ph-file-text"></i> ST: ${escapeHtml(j.nomor_st)}</span>` : '';

                        let statusBadge = '';
                        if (j.status_raw === 'selesai_terverifikasi') {
                            statusBadge = '<span class="status-badge-cmd status-badge-selesai" style="font-size:10.5px;padding:2px 7px;">Selesai</span>';
                        } else if (j.status_raw === 'hasil_diajukan') {
                            statusBadge = '<span class="status-badge-cmd status-badge-verifikasi" style="font-size:10.5px;padding:2px 7px;">Verifikasi</span>';
                        } else if (j.status_raw === 'belum_dimulai') {
                            statusBadge = '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 7px;background:rgba(148,163,184,0.15);color:var(--text-muted);">Belum Dimulai</span>';
                        } else {
                            statusBadge = '<span class="status-badge-cmd status-badge-dikerjakan" style="font-size:10.5px;padding:2px 7px;">Dikerjakan</span>';
                        }

                        jCard.innerHTML = `
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:6px;">
                                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                    <span class="task-code" style="font-size:11px;">${escapeHtml(j.kode || '-')}</span>
                                    ${peranBadge}
                                    ${stBadge}
                                    <span class="task-pokja-badge" style="font-size:10px;">${escapeHtml(j.pokja || 'BGTK')}</span>
                                </div>
                                <div>${statusBadge}</div>
                            </div>
                            <div style="font-size:13px;font-weight:700;color:var(--text-body);margin-bottom:6px;line-height:1.4;">
                                ${escapeHtml(j.judul || '-')}
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;font-size:11.5px;color:var(--text-muted);flex-wrap:wrap;">
                                <div>
                                    <i class="ph-bold ph-calendar"></i> Tenggat: <span style="font-weight:600;color:${j.is_terlambat ? 'var(--danger)' : 'var(--text-body)'}">${escapeHtml(j.tenggat || '-')} ${j.is_terlambat ? '(Terlambat)' : ''}</span>
                                    <span style="margin:0 4px;">•</span>
                                    <i class="ph-bold ph-newspaper-clipping"></i> ${j.laporan_count || 0} Laporan
                                </div>
                                <button type="button" class="btn-cmd-primary" style="padding:4px 10px;font-size:11px;border-radius:6px;cursor:pointer;" onclick="openJobModalById(${j.id})">
                                    <i class="ph-bold ph-eye"></i> Detail Tugas
                                </button>
                            </div>
                        `;
                        jobsPanel.appendChild(jCard);
                    });
                } else {
                    jobsPanel.innerHTML = `
                        <div style="text-align:center;padding:24px;color:var(--text-muted);font-size:13px;">
                            <i class="ph-bold ph-briefcase" style="font-size:26px;display:block;margin-bottom:6px;opacity:0.4;"></i>
                            Tidak ada pekerjaan yang sedang ditugaskan kepada personil ini.
                        </div>`;
                }

                // Render Tab 2: Laporan Progres
                const repPanel = document.getElementById('pPersonReportsPanel');
                repPanel.innerHTML = '';
                if (reports.length > 0) {
                    reports.forEach(r => {
                        const rCard = document.createElement('div');
                        rCard.style.padding = '12px 14px';
                        rCard.style.background = 'var(--bg-card)';
                        rCard.style.border = '1px solid var(--border)';
                        rCard.style.borderRadius = '8px';

                        let statusBadge = '';
                        if (r.status_approval === 'disetujui') {
                            statusBadge = '<span class="status-badge-cmd status-badge-selesai" style="font-size:10.5px;padding:2px 7px;"><i class="ph-bold ph-check"></i> Disetujui</span>';
                        } else if (r.status_approval === 'ditolak') {
                            statusBadge = '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 7px;background:rgba(239,68,68,0.15);color:#ef4444;border-color:rgba(239,68,68,0.3);"><i class="ph-bold ph-x"></i> Perlu Perbaikan</span>';
                        } else {
                            statusBadge = '<span class="status-badge-cmd status-badge-verifikasi" style="font-size:10.5px;padding:2px 7px;"><i class="ph-bold ph-clock"></i> Menunggu Persetujuan</span>';
                        }

                        const jenisBadge = r.is_penyelesaian
                            ? '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 7px;background:rgba(147,51,234,0.15);color:#c084fc;border-color:rgba(147,51,234,0.3);"><i class="ph-bold ph-seal-check"></i> Penyelesaian Akhir</span>'
                            : '<span class="status-badge-cmd" style="font-size:10.5px;padding:2px 7px;background:rgba(14,165,233,0.15);color:#38bdf8;border-color:rgba(14,165,233,0.3);"><i class="ph-bold ph-pencil-simple-line"></i> Kemajuan Berkala</span>';

                        let fileAction = '';
                        if (r.file_bukti) {
                            const fUrl = escapeHtml(r.file_bukti);
                            const fName = escapeHtml(r.file_name || 'Berkas Bukti Dukung');
                            fileAction += `<button type="button" class="btn-action-outline" style="padding:4px 9px;font-size:11px;border-radius:5px;cursor:pointer;color:#38bdf8;border-color:rgba(56,189,248,0.3);background:rgba(56,189,248,0.08);display:inline-flex;align-items:center;gap:4px;" onclick="previewBuktiDukung('${fUrl}', '${fName}')"><i class="ph-bold ph-eye"></i> Pratinjau Berkas</button>`;
                        }
                        if (r.link_bukti) {
                            const lUrl = escapeHtml(r.link_bukti);
                            fileAction += `<a href="${lUrl}" target="_blank" class="btn-action-outline" style="padding:4px 9px;font-size:11px;border-radius:5px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;color:var(--text-muted);border:1px solid var(--border);"><i class="ph-bold ph-arrow-square-out"></i> Tautan</a>`;
                        }

                        let kendalaBox = '';
                        if (r.kendala) {
                            kendalaBox = `<div style="margin-top:6px;font-size:11.5px;color:#f87171;background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:4px 8px;border-radius:0 4px 4px 0;"><strong>Kendala:</strong> ${escapeHtml(r.kendala)}</div>`;
                        }

                        rCard.innerHTML = `
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                                <div>
                                    <span class="task-code" style="font-size:10.5px;margin-right:6px;">${escapeHtml(r.pekerjaan_kode || '-')}</span>
                                    <span style="font-size:12.5px;font-weight:700;color:var(--text-body);">${escapeHtml(r.pekerjaan_judul || '-')}</span>
                                </div>
                                <div style="display:flex;gap:5px;align-items:center;flex-wrap:wrap;">
                                    ${jenisBadge}
                                    ${statusBadge}
                                </div>
                            </div>
                            <div style="font-size:11.5px;color:var(--text-muted);margin-bottom:6px;">
                                <i class="ph-bold ph-calendar"></i> Tanggal Lapor: <strong>${escapeHtml(r.tanggal || '-')}</strong>
                            </div>
                            <div style="font-size:12px;color:var(--text-body);line-height:1.45;background:var(--bg-table-row, rgba(255,255,255,0.02));padding:8px 10px;border-radius:6px;border:1px solid var(--border);">
                                ${escapeHtml(r.deskripsi || '-')}
                            </div>
                            ${kendalaBox}
                            <div style="margin-top:8px;display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;">
                                <div style="display:flex;gap:6px;align-items:center;">
                                    ${fileAction}
                                </div>
                                <button type="button" class="btn-cmd-secondary" style="padding:4px 10px;font-size:11px;border-radius:5px;cursor:pointer;" onclick="openJobModalById(${r.pekerjaan_id})">
                                    <i class="ph-bold ph-arrow-square-out"></i> Buka Tugas Terkait
                                </button>
                            </div>
                        `;
                        repPanel.appendChild(rCard);
                    });
                } else {
                    repPanel.innerHTML = `
                        <div style="text-align:center;padding:24px;color:var(--text-muted);font-size:13px;">
                            <i class="ph-bold ph-newspaper-clipping" style="font-size:26px;display:block;margin-bottom:6px;opacity:0.4;"></i>
                            Belum ada riwayat laporan progres yang diajukan oleh personil ini.
                        </div>`;
                }

                // Default switch to Tab 'jobs'
                switchPersonTab('jobs');

                // Filter button in person modal footer
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
            const btnJobs = document.getElementById('tabBtnPersonJobs');
            const btnReps = document.getElementById('tabBtnPersonReports');
            const panelJobs = document.getElementById('pPersonJobsPanel');
            const panelReps = document.getElementById('pPersonReportsPanel');

            if (isJobs) {
                btnJobs.style.borderBottomColor = '#0a6fb5';
                btnJobs.style.color = '#0a6fb5';
                btnJobs.style.fontWeight = '800';

                btnReps.style.borderBottomColor = 'transparent';
                btnReps.style.color = 'var(--text-muted)';
                btnReps.style.fontWeight = '700';

                panelJobs.style.display = 'flex';
                panelReps.style.display = 'none';
            } else {
                btnReps.style.borderBottomColor = '#0a6fb5';
                btnReps.style.color = '#0a6fb5';
                btnReps.style.fontWeight = '800';

                btnJobs.style.borderBottomColor = 'transparent';
                btnJobs.style.color = 'var(--text-muted)';
                btnJobs.style.fontWeight = '700';

                panelJobs.style.display = 'none';
                panelReps.style.display = 'flex';
            }
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
                const j = window.allPekerjaanMap.get(numId);
                closePersonModalDirect();
                openJobModal(j);
            } else {
                window.open('/pekerjaan/' + jobId, '_blank');
            }
        }

        // ============================================================
        // 4c. Universal Preview Modal Bukti Dukung
        // ============================================================
        function previewBuktiDukung(fileUrl, fileName = 'Berkas Bukti Dukung') {
            const modal = document.getElementById('modalPreviewBuktiUniversal');
            if (!modal) {
                window.open(fileUrl, '_blank');
                return;
            }

            const titleEl = document.getElementById('previewBuktiTitle');
            const bodyEl = document.getElementById('previewBuktiBody');
            const extBadge = document.getElementById('previewBuktiExtBadge');
            const extBtn = document.getElementById('previewBuktiExternalBtn');
            const dlBtn = document.getElementById('previewBuktiDownloadBtn');
            const iconEl = document.getElementById('previewBuktiIcon');

            if (titleEl) titleEl.textContent = fileName;
            if (extBtn) extBtn.href = fileUrl;
            if (dlBtn) dlBtn.href = fileUrl;

            const cleanUrl = fileUrl.split('?')[0].split('#')[0];
            const ext = cleanUrl.split('.').pop().toLowerCase();
            if (extBadge) extBadge.textContent = ext.toUpperCase();

            let iconClass = 'ph-bold ph-file-text';
            if (['pdf'].includes(ext)) iconClass = 'ph-bold ph-file-pdf';
            else if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].includes(ext)) iconClass = 'ph-bold ph-image';
            else if (['doc', 'docx'].includes(ext)) iconClass = 'ph-bold ph-file-doc';
            else if (['xls', 'xlsx'].includes(ext)) iconClass = 'ph-bold ph-file-xls';
            if (iconEl) iconEl.className = iconClass;

            if (bodyEl) {
                bodyEl.innerHTML = '';
                if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].includes(ext)) {
                    const img = document.createElement('img');
                    img.src = fileUrl;
                    img.alt = fileName;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '70vh';
                    img.style.borderRadius = '8px';
                    img.style.boxShadow = '0 10px 25px rgba(0,0,0,0.3)';
                    img.style.objectFit = 'contain';
                    bodyEl.appendChild(img);
                } else if (ext === 'pdf') {
                    const iframe = document.createElement('iframe');
                    iframe.src = fileUrl;
                    iframe.style.width = '100%';
                    iframe.style.height = '70vh';
                    iframe.style.border = 'none';
                    iframe.style.borderRadius = '8px';
                    iframe.style.background = '#ffffff';
                    bodyEl.appendChild(iframe);
                } else {
                    bodyEl.innerHTML = `
                        <div style="text-align: center; padding: 40px 20px;">
                            <div style="width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 16px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 38px;">
                                <i class="${iconClass}"></i>
                            </div>
                            <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 800; color: var(--text-body);">Pratinjau Langsung Tidak Didukung</h4>
                            <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-muted); max-width: 420px; margin-inline: auto;">
                                Berkas berformat <strong>.${ext.toUpperCase()}</strong> memerlukan aplikasi eksternal atau dapat diunduh untuk membukanya.
                            </p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="${fileUrl}" target="_blank" class="btn-action-outline" style="padding: 8px 16px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border-radius: 6px;">
                                    <i class="ph-bold ph-arrow-square-out"></i> Buka di Tab Baru
                                </a>
                                <a href="${fileUrl}" download class="btn-action-primary" style="padding: 8px 18px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; background: #0284c7; color: white;">
                                    <i class="ph-bold ph-download-simple"></i> Unduh Berkas
                                </a>
                            </div>
                        </div>
                    `;
                }
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
            const modal = document.getElementById('modalPreviewBuktiUniversal');
            if (e.target === modal) closePreviewBukti();
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
            if (isRefreshing || isModalOpen) return;
            isRefreshing = true;
            renderRefreshLabel();

            const refreshIcon = document.querySelector('#btnAutoRefresh i');
            if (refreshIcon) refreshIcon.classList.add('spin-active');

            try {
                const response = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (response.ok) {
                    const doc = new DOMParser().parseFromString(await response.text(), 'text/html');

                    swapSection(doc, '.giant-kpi-grid', false);
                    swapSection(doc, '.diagram-leaderboard-box', false);
                    swapSection(doc, '#pokjaScrollContainer', true);
                    swapSection(doc, '#assigneeListContainer', true);
                    swapSection(doc, '.stream-tabs', false);
                    swapSection(doc, '#commandTable tbody', false);

                    indexAllJobs();
                    if (currentAssigneeSort !== 'active') {
                        sortAssigneeList(currentAssigneeSort, document.getElementById('sortBtn' + (currentAssigneeSort === 'st' ? 'St' : (currentAssigneeSort === 'report' ? 'Report' : 'Active'))));
                    }
                    filterAssigneeRows();
                    if (selectedAssigneeName) markSelectedAssignee(selectedAssigneeName);
                    updateRowCount();

                    const searchInput = document.getElementById('searchFilterInput');
                    if (searchInput && searchInput.value) clientFilterTable(searchInput.value);
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
            if (!isAutoRefreshActive || isRefreshing || isModalOpen) return;
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
                if (!isAutoScrollActive || !scrollContainer || isModalOpen) return;
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
        // 8. Filter cepat
        // ============================================================
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
        }

        // Saklar panel kiri: Tim Kerja / Personil
        function switchSidePane(name) {
            const isPokja = name !== 'staf';
            document.getElementById('panePokja').hidden = !isPokja;
            document.getElementById('paneStaf').hidden = isPokja;
            document.getElementById('sideTabPokja').classList.toggle('active', isPokja);
            document.getElementById('sideTabStaf').classList.toggle('active', !isPokja);
            try { localStorage.setItem('bgtkSidePane', isPokja ? 'pokja' : 'staf'); } catch (e) { }
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
        // 9. Pintasan keyboard: F, S, R, T, Esc
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
        });

        document.addEventListener('DOMContentLoaded', function () {
            applyThemeIcon();
            initMonitoringCharts();
            animateKpiNumbers();
            indexAllJobs();
        });
    </script>
</body>

</html>