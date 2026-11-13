@extends('layouts.app')

@section('title', 'Dashboard Monitoring Kinerja')
@section('page-title', 'Beranda Kinerja')

@section('styles')
<style>
    /* Hero Banner */
    .hero-card {
        background-color: var(--navy);
        background-image:
            radial-gradient(rgba(255, 255, 255, 0.12) 1.2px, transparent 1.2px),
            linear-gradient(135deg, var(--navy-soft) 0%, var(--navy) 50%, var(--navy-deep) 100%);
        background-size: 22px 22px, 100% 100%;
        border-radius: var(--radius-lg);
        padding: 26px 30px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(11, 74, 130, 0.18);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .hero-card::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }

    .hero-info h2 {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .hero-info p {
        font-size: 13.5px;
        opacity: 0.92;
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .hero-badge {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(4px);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        color: #e0f6fd;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    /* Grid KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 20px 22px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
        animation: kpiCardIn 0.5s ease both;
    }

    .kpi-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(37,99,235,0.03) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .kpi-card:hover::after { opacity: 1; }

    .kpi-card:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 12px 32px rgba(0,0,0,0.10), 0 2px 8px rgba(0,0,0,0.04);
        border-color: rgba(37,99,235,0.3);
    }

    @keyframes kpiCardIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .kpi-card:nth-child(1) { animation-delay: 0.05s; }
    .kpi-card:nth-child(2) { animation-delay: 0.10s; }
    .kpi-card:nth-child(3) { animation-delay: 0.15s; }
    .kpi-card:nth-child(4) { animation-delay: 0.20s; }

    .kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        flex-shrink: 0;
        transition: transform 0.2s;
    }

    .kpi-card:hover .kpi-icon {
        transform: scale(1.12) rotate(-5deg);
    }

    .kpi-blue   { background: #e8f2fb; color: #0a6fb5; }
    .kpi-green  { background: #e3f6ee; color: #0c8a5d; }
    .kpi-amber  { background: #fff3d9; color: #b87400; }
    .kpi-purple { background: #efe8fa; color: #6f45bd; }
    .kpi-rose   { background: #fdeaea; color: #d03232; }

    .kpi-details h3 {
        font-size: 26px;
        font-weight: 900;
        color: var(--text-dark);
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .kpi-details p {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* Hero card pulse shimmer */
    .hero-card {
        position: relative;
        overflow: hidden;
    }
    .hero-card::before {
        content: '';
        position: absolute;
        top: -60%;
        left: -60%;
        width: 220%;
        height: 220%;
        background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.06) 50%, transparent 60%);
        animation: heroShimmer 5s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes heroShimmer {
        0%   { transform: translateX(-40%) translateY(-10%); }
        50%  { transform: translateX(0%) translateY(5%); }
        100% { transform: translateX(-40%) translateY(-10%); }
    }

    /* Section Cards */
    .section-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        margin-bottom: 28px;
        overflow: hidden;
    }

    .section-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-body {
        padding: 24px;
    }

    /* Tables */
    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    th {
        background: #f8fafc;
        text-align: left;
        padding: 12px 16px;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid var(--border);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.05em;
    }

    td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    tr:hover td {
        background: #f8fafc;
    }

    /* Badges Status Workflow */
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .status-draft { background: #f1f5f9; color: #475569; }
    .status-diajukan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .status-revisi_pokja, .status-revisi_katim { background: #fef2f2; color: #b91c1c; }
    .status-disetujui_pokja { background: #ecfdf5; color: #047857; }
    .status-disetujui_katim { background: #f0fdf4; color: #15803d; font-weight: 800; border: 1px solid #86efac; }
    .status-dikonsolidasi_kasubag { background: #fdf4ff; color: #7e22ce; }
    .status-disetujui_kabal { background: #f0fdf4; color: #166534; box-shadow: 0 0 0 1px #22c55e; }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 12px;
        display: block;
    }

    .two-col-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    @media (max-width: 900px) {
        .two-col-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

    <!-- 1. Hero Greeting Banner -->
    <div class="hero-card">
        <div class="hero-info">
            <h2>Hai, {{ $user->name }}!</h2>
            <p>
                <span><i class="ph-bold ph-identification-card"></i> NIP: {{ $user->nip ?? 'Belum Diatur' }}</span>
                <span><i class="ph-bold ph-briefcase"></i> {{ $user->jabatan ?? 'Pegawai' }}</span>
                @if($user->pokja)
                    <span class="hero-badge"><i class="ph-bold ph-tree-structure"></i> {{ $user->pokja->nama_pokja }}</span>
                @endif
            </p>
        </div>
        <div style="width: 64px; height: 64px; background: rgba(255, 255, 255, 0.18); border: 1.5px solid rgba(255, 255, 255, 0.32); border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #ffffff; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08); flex-shrink: 0; backdrop-filter: blur(6px);">
            <i class="ph-bold ph-chart-line-up"></i>
        </div>
    </div>

    <!-- 2. Banner Tugas & Pekerjaan Hierarki Aktif Saya -->
    @if(isset($tugasAktifSaya) && $tugasAktifSaya->isNotEmpty())
        <div class="section-card" style="border-left: 4px solid var(--primary); margin-bottom: 24px;">
            <div class="section-header" style="background: #f8fafc;">
                <h3>
                    <i class="ph-bold ph-list-checks" style="color: var(--primary);"></i> 
                    Tugas & Hierarki Pekerjaan Aktif Anda ({{ $tugasAktifSaya->count() }})
                </h3>
                <a href="{{ route('pekerjaan.saya') }}" style="font-size: 12.5px; font-weight: 700; color: var(--primary); text-decoration: none;">
                    Buka Halaman Tugas Saya &rarr;
                </a>
            </div>
            <div class="section-body" style="padding: 16px 20px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
                    @foreach($tugasAktifSaya as $task)
                        <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 6px;">
                                    <span style="font-family: monospace; font-weight: 800; font-size: 11px; background: #eff6ff; color: #1d4ed8; padding: 2px 6px; border-radius: 4px;">
                                        {{ $task->kode_tampilan }}
                                    </span>
                                    <span style="font-size: 11px; font-weight: 700; color: {{ $task->isTerlambat() ? '#dc2626' : '#64748b' }};">
                                        <i class="ph-bold ph-calendar"></i> {{ $task->tenggat ? $task->tenggat->format('d M') : '-' }}
                                    </span>
                                </div>
                                <a href="{{ route('pekerjaan.show', $task) }}" style="font-weight: 700; font-size: 13.5px; color: #0f172a; text-decoration: none; line-height: 1.3;">
                                    {{ Str::limit($task->judul, 45) }}
                                </a>
                                @if($task->parent)
                                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                        Induk: {{ $task->parent->kode_tampilan }} - {{ Str::limit($task->parent->judul, 25) }}
                                    </div>
                                @endif
                            </div>

                            <div style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 50px; height: 6px; background: #e2e8f0; border-radius: 6px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ $task->progres }}%; background: #2563eb; border-radius: 6px;"></div>
                                    </div>
                                    <span style="font-size: 11.5px; font-weight: 800; color: #1e293b;">{{ number_format($task->progres, 0) }}%</span>
                                </div>

                                <a href="{{ route('pekerjaan.show', $task) }}" style="font-size: 11.5px; font-weight: 700; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 6px; text-decoration: none; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="ph-bold ph-pencil-simple-line"></i> Lapor Progres
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- 3. Konten Spesifik per Role Pengguna -->

    {{-- ROLE: PEGAWAI -- DINONAKTIFKAN SEMENTARA (Kinerja Saya) --}}
    {{-- @if($user->isPegawai())
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon kpi-blue">
                    <i class="ph-duotone ph-paper-plane-tilt"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $countDiajukan }}</h3>
                    <p>Laporan Diajukan</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-green">
                    <i class="ph-duotone ph-seal-check"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $countDisetujui }}</h3>
                    <p>Disetujui Katim</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-rose">
                    <i class="ph-duotone ph-arrow-counter-clockwise"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $countRevisi }}</h3>
                    <p>Perlu Revisi</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-amber">
                    <i class="ph-duotone ph-note-pencil"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $countDraft }}</h3>
                    <p>Draf Belum Diajukan</p>
                </div>
            </div>
        </div>

        <div class="two-col-grid">
            <div class="section-card">
                <div class="section-header">
                    <h3><i class="ph-duotone ph-crosshair"></i> Target Kinerja Bulan Ini</h3>
                    <span style="font-size: 12px; font-weight: 700; color: var(--primary);">Bulan {{ $currentMonth }}/{{ $currentYear }}</span>
                </div>
                <div class="section-body">
                    @forelse($targetBulanan as $tb)
                        <div style="background: #f8fafc; padding: 14px 16px; border-radius: 10px; margin-bottom: 12px; border-left: 4px solid var(--primary);">
                            <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">{{ $tb->uraian_target }}</div>
                            <div style="font-size: 12.5px; color: var(--text-muted);">
                                Target: <strong>{{ number_format($tb->target_kuantitas, 0) }} {{ $tb->satuan }}</strong>
                                <span class="badge-status status-disetujui_pokja" style="margin-left: 8px;">{{ $tb->status }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="ph-duotone ph-calendar-blank"></i>
                            <p>Belum ada target bulanan yang diset untuk bulan ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h3><i class="ph-duotone ph-calendar-check"></i> Rencana Aksi Mingguan</h3>
                </div>
                <div class="section-body">
                    @forelse($rencanaMingguan as $rm)
                        <div style="background: #f8fafc; padding: 14px 16px; border-radius: 10px; margin-bottom: 12px; border-left: 4px solid var(--purple);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-size: 12px; font-weight: 700; color: var(--purple);">Minggu ke-{{ $rm->minggu_ke }}</span>
                                <span class="badge-status status-diajukan">{{ $rm->status }}</span>
                            </div>
                            <div style="font-weight: 600; font-size: 13.5px; margin-bottom: 4px;">{{ $rm->rencana_aksi }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Output: {{ $rm->target_output }}</div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="ph-duotone ph-list-dashes"></i>
                            <p>Belum ada rencana mingguan aktif.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <h3><i class="ph-duotone ph-clipboard-text"></i> Riwayat Laporan Kinerja Harian Terbaru</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Uraian Kegiatan</th>
                            <th>Output & Capaian</th>
                            <th>Status Workflow</th>
                            <th>Catatan Verifikator</th>
                            <th>Nilai Katim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanTerbaru as $lap)
                            <tr>
                                <td style="font-weight: 600; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($lap->tanggal_kegiatan)->format('d M Y') }}
                                </td>
                                <td style="max-width: 280px;">
                                    <strong>{{ $lap->uraian_kegiatan }}</strong>
                                </td>
                                <td>
                                    {{ $lap->output_kegiatan }} ({{ number_format($lap->volume_capaian, 0) }} {{ $lap->satuan }})
                                </td>
                                <td>
                                    <span class="badge-status status-{{ $lap->status }}">
                                        {{ str_replace('_', ' ', $lap->status) }}
                                    </span>
                                </td>
                                <td style="font-size: 12.5px; color: var(--text-muted); max-width: 200px;">
                                    {{ $lap->catatan_pokja ?? ($lap->catatan_katim ?? '-') }}
                                </td>
                                <td>
                                    @if($lap->nilai_kualitas_katim)
                                        <strong style="color: #15803d; font-size: 14px;">{{ number_format($lap->nilai_kualitas_katim, 1) }}</strong>
                                    @else
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="ph-duotone ph-tray"></i>
                                    <p>Belum ada laporan kinerja harian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif --}}

    {{-- ROLE: TIM POKJA --}}
    @if($user->isTimPokja())
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon kpi-amber">
                    <i class="ph-duotone ph-hourglass-high"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $antreanVerifikasi->count() }}</h3>
                    <p>Menunggu Verifikasi</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-green">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $sudahDiverifikasi->count() }}</h3>
                    <p>Telah Anda Verifikasi</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-blue">
                    <i class="ph-duotone ph-users-three"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalAnggota }}</h3>
                    <p>Total Anggota Pokja</p>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <h3><i class="ph-duotone ph-checks"></i> Antrean Verifikasi Laporan Anggota Pokja</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Tanggal Kegiatan</th>
                            <th>Uraian Kegiatan</th>
                            <th>Output</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antreanVerifikasi as $antrean)
                            <tr>
                                <td>
                                    <strong>{{ $antrean->user->name }}</strong><br>
                                    <span style="font-size: 11px; color: var(--text-muted);">NIP: {{ $antrean->user->nip }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($antrean->tanggal_kegiatan)->format('d M Y') }}</td>
                                <td>{{ $antrean->uraian_kegiatan }}</td>
                                <td>{{ $antrean->output_kegiatan }}</td>
                                <td>
                                    <button style="padding: 6px 12px; background: var(--primary); color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                        Periksa Laporan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="ph-duotone ph-check-fat"></i>
                                    <p>Semua laporan anggota pokja telah diverifikasi!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ROLE: KATIM / KORJAFUNG --}}
    @if($user->isKatim())
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon kpi-amber">
                    <i class="ph-duotone ph-clipboard-text"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $antreanEvaluasi->count() }}</h3>
                    <p>Menunggu Evaluasi Katim</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-green">
                    <i class="ph-duotone ph-star"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ number_format($avgNilai, 1) }}</h3>
                    <p>Rata-rata Nilai Tim</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-purple">
                    <i class="ph-duotone ph-certificate"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalDievaluasi }}</h3>
                    <p>Laporan Dinilai Katim</p>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <h3><i class="ph-duotone ph-medal"></i> Antrean Penilaian Kualitas & IKU (Lolos Pokja)</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Tanggal</th>
                            <th>Uraian Kegiatan</th>
                            <th>Verifikator Pokja</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antreanEvaluasi as $eval)
                            <tr>
                                <td>
                                    <strong>{{ $eval->user->name }}</strong><br>
                                    <span style="font-size: 11px; color: var(--text-muted);">{{ $eval->user->jabatan }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($eval->tanggal_kegiatan)->format('d M Y') }}</td>
                                <td>{{ $eval->uraian_kegiatan }}</td>
                                <td>
                                    <span style="font-size: 12px; color: #047857;">
                                        <i class="ph-bold ph-check"></i> {{ $eval->verifikatorPokja?->name ?? 'Tim Pokja' }}
                                    </span>
                                </td>
                                <td>
                                    <button style="padding: 6px 12px; background: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                        Beri Nilai & IKU
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="ph-duotone ph-medal"></i>
                                    <p>Tidak ada antrean penilaian evaluasi saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ROLE: KASUBAG TU --}}
    @if($user->isKasubag())
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon kpi-blue">
                    <i class="ph-duotone ph-users"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalPegawai }}</h3>
                    <p>Total Pegawai Balai</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-green">
                    <i class="ph-duotone ph-file-arrow-up"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $laporanSiapKonsolidasi }}</h3>
                    <p>Laporan Siap Konsolidasi</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-purple">
                    <i class="ph-duotone ph-signature"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $konsolidasiBulanIni?->ttd_kasubag ? 'Sudah TTD' : 'Draf Siap' }}</h3>
                    <p>Status Laporan Balai</p>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <h3><i class="ph-duotone ph-files"></i> Laporan Terakhir Yang Selesai Evaluasi Katim</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Pokja</th>
                            <th>Uraian Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentVerifiedLaporan as $rl)
                            <tr>
                                <td><strong>{{ $rl->user->name }}</strong></td>
                                <td>{{ $rl->pokja?->kode_pokja ?? '-' }}</td>
                                <td>{{ $rl->uraian_kegiatan }}</td>
                                <td>{{ \Carbon\Carbon::parse($rl->tanggal_kegiatan)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge-status status-{{ $rl->status }}">
                                        {{ str_replace('_', ' ', $rl->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">Belum ada laporan disetujui.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Monitoring Struktur Unit Kerja & Pilar Subbag Umum --}}
        @include('dashboard.partials.pokja_monitoring_section')
    @endif

    {{-- ROLE: KEPALA BALAI (KABAL) --}}
    @if($user->isKabal())
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon kpi-purple">
                    <i class="ph-duotone ph-buildings"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalPokjas }}</h3>
                    <p>Unit & Pilar Terdaftar</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-green">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $laporanSelesaiCount }}</h3>
                    <p>Laporan Kinerja Selesai</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-amber">
                    <i class="ph-duotone ph-stamp"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $laporanAntreanApproval->count() }}</h3>
                    <p>Approval Konsolidasi</p>
                </div>
            </div>
        </div>

        {{-- Monitoring Struktur Unit Kerja & Pilar Subbag Umum --}}
        @include('dashboard.partials.pokja_monitoring_section')
    @endif

    {{-- ROLE: SUPER ADMIN --}}
    @if($user->isAdmin())
        <div class="kpi-grid">
            <a href="{{ route('users.index') }}" style="text-decoration: none;" class="kpi-card">
                <div class="kpi-icon kpi-blue">
                    <i class="ph-duotone ph-users"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Pengguna Terdaftar <i class="ph-bold ph-arrow-right" style="font-size: 11px;"></i></p>
                </div>
            </a>

            <a href="{{ route('pokjas.index') }}" style="text-decoration: none;" class="kpi-card">
                <div class="kpi-icon kpi-purple">
                    <i class="ph-duotone ph-tree-structure"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalPokjas }}</h3>
                    <p>Pokja Aktif <i class="ph-bold ph-arrow-right" style="font-size: 11px;"></i></p>
                </div>
            </a>

            <div class="kpi-card">
                <div class="kpi-icon kpi-green">
                    <i class="ph-duotone ph-database"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalLaporan }}</h3>
                    <p>Total Catatan Kinerja</p>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <h3><i class="ph-duotone ph-users-four"></i> Pengguna Sistem Terbaru</h3>
                <a href="{{ route('users.index') }}" style="font-size: 13px; font-weight: 700; color: var(--primary); text-decoration: none;">
                    Kelola Semua Pengguna &rarr;
                </a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Pokja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $ru)
                            <tr>
                                <td><strong>{{ $ru->name }}</strong></td>
                                <td>{{ $ru->nip ?? '-' }}</td>
                                <td>{{ $ru->email }}</td>
                                <td>
                                    <span class="user-role-badge role-{{ $ru->role }}">
                                        {{ str_replace('_', ' ', $ru->role) }}
                                    </span>
                                </td>
                                <td>{{ $ru->pokja?->nama_pokja ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Monitoring Struktur Unit Kerja & Pilar Subbag Umum --}}
        @include('dashboard.partials.pokja_monitoring_section')
    @endif

@endsection

@push('scripts')
<script>
// ============================================================
// KPI Count-Up Animation
// ============================================================
(function() {
    function animateCount(el, from, to, duration) {
        if (isNaN(to)) return;
        const startTime = performance.now();
        const isDecimal = String(to).includes('.');
        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            const current = from + (to - from) * eased;
            el.textContent = isDecimal ? current.toFixed(1) : Math.round(current).toLocaleString('id-ID');
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = isDecimal ? to.toFixed(1) : to.toLocaleString('id-ID');
        }
        requestAnimationFrame(step);
    }

    function startCountUps() {
        document.querySelectorAll('.kpi-details h3').forEach(el => {
            const raw = el.textContent.trim().replace(/[.,]/g, m => m === '.' ? '.' : '');
            // Parse angka: bersihkan titik ribuan Indonesia
            const cleaned = el.textContent.trim().replace(/\./g, '').replace(',', '.');
            const num = parseFloat(cleaned);
            if (!isNaN(num) && num > 0) {
                el.textContent = '0';
                animateCount(el, 0, num, 900);
            }
        });
    }

    // Jalankan saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startCountUps);
    } else {
        setTimeout(startCountUps, 100);
    }
})();
</script>
@endpush
