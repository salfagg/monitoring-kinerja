@extends('layouts.app')

@section('title', 'Dashboard | TANJIDOR BGTK')
@section('page-title', 'Beranda Kinerja')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                                    <span class="badge-pill exec-{{ $task->status_pelaksanaan }}" style="font-size: 11px; padding: 2px 8px;">
                                        {{ $task->status_pelaksanaan_label }}
                                    </span>
                                </div>

                                <a href="{{ route('pekerjaan.show', $task) }}" style="font-size: 11.5px; font-weight: 700; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 6px; text-decoration: none; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="ph-bold ph-pencil-simple-line"></i> Lapor Kemajuan
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
    @endif

    {{-- ROLE: KEPALA BALAI (KABAL) --}}
    @if($user->isKabal())
        <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));">
            <div class="kpi-card">
                <div class="kpi-icon kpi-blue">
                    <i class="ph-duotone ph-tree-structure"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalPekerjaanBalai ?? 5 }}</h3>
                    <p>Total Penugasan Balai</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-cyan">
                    <i class="ph-duotone ph-rocket-launch"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $statusCounts['dikerjakan'] ?? 2 }}</h3>
                    <p>Sedang Dikerjakan</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-green">
                    <i class="ph-duotone ph-check-circle"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $statusCounts['selesai'] ?? 2 }}</h3>
                    <p>Penugasan Selesai</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-amber">
                    <i class="ph-duotone ph-chart-line-up"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $avgProgresBalai ?? 0 }}%</h3>
                    <p>Ketuntasan Tugas Balai</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-purple">
                    <i class="ph-duotone ph-buildings"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $totalPokjas ?? 18 }}</h3>
                    <p>Unit & Pilar Terdaftar</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ROLE: SUPER ADMIN --}}
    @if($user->isAdmin())
        <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));">
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
                    <h3>{{ $totalPekerjaanBalai ?? 5 }}</h3>
                    <p>Total Penugasan Balai</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-amber">
                    <i class="ph-duotone ph-chart-line-up"></i>
                </div>
                <div class="kpi-details">
                    <h3>{{ $avgProgresBalai ?? 0 }}%</h3>
                    <p>Ketuntasan Tugas Balai</p>
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
    @endif

    {{-- Section Analitik Visual & Monitoring Struktur Unit Kerja Balai --}}
    @if($user->isAdmin() || $user->isKabal() || $user->isKasubag() || $user->isKorlayanan())
        <!-- ================= VISUAL ANALYTICS SECTION ================= -->
<!-- Section Analitik Visual & Grafik Kinerja Balai (Formal, Bersih & Interaktif) -->
<div class="analytics-section-wrapper" style="margin-bottom: 24px;">
    <div class="analytics-grid">
        <!-- 1. Donut Chart: Komposisi Status Pelaksanaan Pekerjaan -->
        <div class="analytics-card">
            <div class="analytics-header">
                <div>
                    <h3 class="analytics-title">
                        <i class="ph-bold ph-chart-donut"></i>
                        Status Penugasan & Beban Kerja Balai
                    </h3>
                    <p class="analytics-subtitle">Distribusi status dari seluruh target pekerjaan institusi</p>
                </div>
                <span class="analytics-badge-count">
                    {{ $totalPekerjaanBalai ?? 0 }} Penugasan
                </span>
            </div>

            <div class="analytics-body">
                <div class="donut-chart-container">
                    <canvas id="chartStatusTugas"></canvas>
                    <div class="donut-center-metric">
                        <span class="donut-big-num">{{ $totalPekerjaanBalai ?? 0 }}</span>
                        <span class="donut-sub-lbl">Tugas Total</span>
                    </div>
                </div>

                <div class="analytics-legend-grid">
                    <div class="legend-box" style="border-left-color: #10b981;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-check-circle" style="color: #10b981;"></i> Selesai Terverifikasi</span>
                            <span class="legend-val">{{ $statusCounts['selesai'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['selesai'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>

                    <div class="legend-box" style="border-left-color: #0a6fb5;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-rocket-launch" style="color: #0a6fb5;"></i> Sedang Dikerjakan</span>
                            <span class="legend-val">{{ $statusCounts['dikerjakan'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['dikerjakan'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>

                    <div class="legend-box" style="border-left-color: #8b5cf6;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-seal-check" style="color: #8b5cf6;"></i> Menunggu Verifikasi</span>
                            <span class="legend-val">{{ $statusCounts['menunggu_verifikasi'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['menunggu_verifikasi'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>

                    <div class="legend-box" style="border-left-color: #94a3b8;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-clock" style="color: #94a3b8;"></i> Belum Dimulai / Draf</span>
                            <span class="legend-val">{{ $statusCounts['belum_dimulai'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['belum_dimulai'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Bar Chart: Capaian Progres per Tim Kerja / Unit -->
        <div class="analytics-card">
            <div class="analytics-header">
                <div>
                    <h3 class="analytics-title">
                        <i class="ph-bold ph-chart-bar"></i>
                        Tingkat Ketuntasan per Tim Kerja & Unit
                    </h3>
                    <p class="analytics-subtitle">Jumlah penyelesaian tugas per unit kerja</p>
                </div>
                <span class="analytics-badge-avg">
                    <i class="ph-bold ph-trend-up"></i> Balai: {{ $statusCounts['selesai'] ?? 0 }} / {{ $totalPekerjaanBalai ?? 0 }} Tugas Tuntas
                </span>
            </div>

            <div class="analytics-body" style="flex-direction: column; justify-content: space-between;">
                <div style="height: 185px; width: 100%; position: relative;">
                    <canvas id="chartPokjaProgres"></canvas>
                </div>

                <!-- Footer Indikator Cepat -->
                <div class="analytics-mini-stats">
                    <div class="mini-stat-item">
                        <span class="stat-lbl"><i class="ph-bold ph-gauge"></i> Ketuntasan Balai</span>
                        <span class="stat-num" style="color: #0a6fb5;">{{ $statusCounts['selesai'] ?? 0 }} / {{ $totalPekerjaanBalai ?? 0 }}</span>
                    </div>
                    <div class="mini-stat-item">
                        <span class="stat-lbl"><i class="ph-bold ph-shield-check"></i> Ketepatan Waktu</span>
                        <span class="stat-num" style="color: {{ ($pekerjaanTerlambatCount ?? 0) > 0 ? '#d03232' : '#0c8a5d' }};">
                            {{ ($pekerjaanTerlambatCount ?? 0) > 0 ? $pekerjaanTerlambatCount . ' Terlambat' : 'Semua On-Track' }}
                        </span>
                    </div>
                    <div class="mini-stat-item">
                        <span class="stat-lbl"><i class="ph-bold ph-check-circle"></i> Selesai Tuntas</span>
                        <span class="stat-num" style="color: #0c8a5d;">{{ $statusCounts['selesai'] ?? 0 }} Tugas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- ================= POKJA MONITORING SECTION ================= -->
<!-- Section Monitoring Unit Kerja & Pilar Subbag (Interactive Enhanced) -->
<div class="section-card"
    style="margin-top: 10px; border-radius: var(--radius-md); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div class="dash-pokja-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="dash-pokja-icon-badge">
                <i class="ph-duotone ph-tree-structure"></i>
            </div>
            <div>
                <h3 style="font-size: 14.5px; font-weight: 800; color: var(--text-dark); margin: 0; line-height: 1.2;">
                    Struktur Unit Kerja, 6 Pilar Subbag &amp; Satgas RBI
                </h3>
                <p style="font-size: 11.5px; color: var(--text-muted); margin: 2px 0 0 0;">
                    Klik kartu unit kerja untuk melihat rincian personil dan penanggung jawab
                </p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <!-- Live search -->
            <div style="position: relative;">
                <i class="ph-bold ph-magnifying-glass"
                    style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); font-size: 13px; color: var(--text-muted); pointer-events: none;"></i>
                <input type="text" id="pokjaCardSearch" placeholder="Cari unit kerja..."
                    oninput="searchPokjaCards(this.value)"
                    style="font-size: 12px; padding: 6px 10px 6px 30px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none; width: 180px; transition: all 0.2s;">
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('pokjas.index') }}" class="btn-primary"
                    style="padding: 5px 11px; font-size: 11.5px; border-radius: 6px;">
                    <i class="ph-bold ph-gear-six"></i>
                    <span>Kelola Master</span>
                </a>
            @endif
        </div>
    </div>

    <div style="padding: 12px 18px 16px 18px;">
        @php
            $timKerjaCount = $pokjas->where('tipe', 'tim_kerja')->count();
            $pilarSubbagCount = $pokjas->where('tipe', 'subbag_pilar')->count();
            $kelompokKerjaCount = $pokjas->where('tipe', 'kelompok_kerja')->count();
        @endphp

        <!-- Tab Switcher Minimalis / Compact Pills -->
        <div class="dash-pokja-tabs-bar">
            <button type="button" class="dash-mini-tab active" onclick="switchDashboardPokjaTab('all', this)">
                <span>Semua Unit</span>
                <span class="dash-mini-count">{{ $pokjas->count() }}</span>
            </button>
            <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('tim_kerja', this)">
                <i class="ph-bold ph-buildings" style="font-size: 12px;"></i>
                <span>Tim Kerja &amp; TU Balai</span>
                <span class="dash-mini-count">{{ $timKerjaCount }}</span>
            </button>
            <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('subbag_pilar', this)">
                <i class="ph-bold ph-gear-six" style="font-size: 12px;"></i>
                <span>6 Pilar Operasional Subbag</span>
                <span class="dash-mini-count">{{ $pilarSubbagCount }}</span>
            </button>
            <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('kelompok_kerja', this)">
                <i class="ph-bold ph-lightning" style="font-size: 12px;"></i>
                <span>Satgas Ad Hoc RBI</span>
                <span class="dash-mini-count">{{ $kelompokKerjaCount }}</span>
            </button>
            @if(isset($bebanKerjaPegawai) && $bebanKerjaPegawai->isNotEmpty())
                <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('beban_kerja', this)"
                    style="border-color: rgba(37,99,235,0.3); color: var(--primary); background: rgba(37,99,235,0.06);">
                    <i class="ph-bold ph-chart-donut" style="font-size: 12px;"></i>
                    <span>Beban Kerja Pegawai &amp; Katim</span>
                    <span class="dash-mini-count"
                        style="background: var(--primary); color: white;">{{ $bebanKerjaPegawai->count() }}</span>
                </button>
            @endif
        </div>

        <!-- Grid Cards Interaktif -->
        <div class="dash-pokja-compact-grid" id="dashboardPokjaGrid">
            @foreach($pokjas as $pIdx => $p)
                @php
                    $isSubbag = ($p->kode_pokja === 'SUBBAG-UMUM');
                    $isPilar = ($p->tipe === 'subbag_pilar');
                    $isAdhoc = ($p->tipe === 'kelompok_kerja');

                    $themeClass = $isSubbag ? 'type-subbag' : ($isPilar ? 'type-pilar' : ($isAdhoc ? 'type-adhoc' : 'type-timker'));
                    $typeBadgeText = $isSubbag ? 'TU Balai' : ($isPilar ? 'Pilar Subbag' : ($isAdhoc ? 'Satgas RBI' : 'Tim Kerja'));
                    $leaderRoleText = $isSubbag ? 'Kasubag' : ($isPilar ? 'PIC Pilar' : ($isAdhoc ? 'Ketua Satgas' : 'Katim'));
                    $leaderName = $p->katim?->name ?? 'Belum Ditunjuk';

                    // Hitung ketuntasan pekerjaan
                    $totalTugas = $p->pekerjaan ? $p->pekerjaan->count() : 0;
                    $selesaiTugas = $p->pekerjaan ? $p->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count() : 0;
                    $ketuntasan = $totalTugas > 0 ? round(($selesaiTugas / $totalTugas) * 100, 0) : 0;
                    $progressPct = $ketuntasan;

                    $progressColor = $progressPct >= 75 ? '#10b981' : ($progressPct >= 40 ? '#f59e0b' : '#3b82f6');
                    $progressBg = $progressPct >= 75 ? 'rgba(16,185,129,0.12)' : ($progressPct >= 40 ? 'rgba(245,158,11,0.12)' : 'rgba(59,130,246,0.12)');
                @endphp

                <div class="dash-mini-card {{ $themeClass }}" data-tipe="{{ $p->tipe ?? 'tim_kerja' }}"
                    data-name="{{ strtolower($p->nama_pokja) }} {{ strtolower($p->kode_pokja) }}"
                    onclick="openDashMemberModal('{{ $p->id }}')" title="Klik untuk melihat personil {{ $p->nama_pokja }}"
                    style="animation-delay: {{ $pIdx * 0.04 }}s;">

                    <!-- Header mini card: Kode & Tipe Badge -->
                    <div class="dash-mini-card-head">
                        <span class="dash-mini-code">{{ $p->kode_pokja }}</span>
                        <span class="dash-mini-type">{{ $typeBadgeText }}</span>
                    </div>

                    <!-- Judul Unit -->
                    <div class="dash-mini-card-title" title="{{ $p->nama_pokja }}">
                        {{ $p->nama_pokja }}
                    </div>

                    <!-- Progress bar tugas -->
                    @if($totalTugas > 0)
                        <div style="margin-bottom: 6px;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                                <span style="font-size: 9.5px; color: var(--text-muted); font-weight: 600;">Ketuntasan</span>
                                <span
                                    style="font-size: 9.5px; font-weight: 800; color: #10b981;">{{ $selesaiTugas }}/{{ $totalTugas }} Tuntas</span>
                            </div>
                            <div style="font-size: 9px; color: var(--text-muted); margin-top: 2px;">
                                {{ $p->pekerjaan->whereIn('status_pelaksanaan', ['belum_dimulai', 'dikerjakan'])->count() }} sedang aktif
                            </div>
                        </div>
                    @endif

                    <!-- Footer mini card: Penanggung Jawab & Total Personil -->
                    <div class="dash-mini-card-foot">
                        <div class="dash-mini-leader" title="{{ $leaderRoleText }}: {{ $leaderName }}">
                            <i class="ph-bold ph-user" style="font-size: 11px;"></i>
                            <span>{{ $leaderName }}</span>
                        </div>
                        <div class="dash-mini-members" title="Total personil {{ $p->anggota->count() }} orang">
                            <i class="ph-bold ph-users"></i>
                            <span>{{ $p->anggota->count() }}</span>
                        </div>
                    </div>

                    @if($p->anggota && $p->anggota->count() > 0)
                        <div style="font-size: 10px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 4px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                            title="Pegawai: {{ $p->anggota->pluck('name')->implode(', ') }}">
                            <span style="font-weight: 700; color: var(--primary);">Staf:</span>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $p->anggota->take(2)->pluck('name')->implode(', ') }}{{ $p->anggota->count() > 2 ? ' +' . ($p->anggota->count() - 2) : '' }}
                            </span>
                        </div>
                    @endif

                    <!-- Hover detail overlay -->
                    <div class="dash-card-hover-cta">
                        <i class="ph-bold ph-arrow-square-out" style="font-size: 12px;"></i>
                        <span>Lihat Detail</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty state saat search tidak ketemu -->
        <div id="pokjaSearchEmpty" style="display: none; text-align: center; padding: 24px; color: var(--text-muted);">
            <i class="ph-duotone ph-magnifying-glass" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
            <p style="font-size: 13px;">Tidak ada unit kerja yang cocok dengan pencarian.</p>
        </div>

        @if(isset($bebanKerjaPegawai) && $bebanKerjaPegawai->isNotEmpty())
            <!-- Container Pemantauan Beban Kerja -->
            <div id="dashWorkloadContainer" style="display: none; flex-direction: column; gap: 12px; margin-top: 4px;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; background: var(--bg-hover, #f8fafc); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px;">
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        <button type="button" class="dash-mini-tab active" id="btnWorkloadTabAll"
                            onclick="filterDashWorkload('all', this)">
                            Semua ({{ $bebanKerjaPegawai->count() }})
                        </button>
                        <button type="button" class="dash-mini-tab" onclick="filterDashWorkload('pegawai', this)">
                            Pegawai Pelaksana
                            ({{ $bebanKerjaPegawai->filter(fn($u) => in_array($u->role, ['pegawai', 'tim_pokja']))->count() }})
                        </button>
                        <button type="button" class="dash-mini-tab" onclick="filterDashWorkload('katim', this)">
                            Katim &amp; Koor
                            ({{ $bebanKerjaPegawai->filter(fn($u) => in_array($u->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']))->count() }})
                        </button>
                    </div>
                    <div style="max-width: 260px; width: 100%;">
                        <input type="text" id="dashWorkloadSearch" placeholder="Cari nama pegawai / staf..."
                            oninput="searchDashWorkload(this.value)"
                            style="width: 100%; font-size: 12px; padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; background: white; outline: none;">
                    </div>
                </div>

                <div class="dash-pokja-compact-grid" id="dashWorkloadGrid">
                    @foreach($bebanKerjaPegawai as $staf)
                        @php
                            $isKatimOrKoor = in_array($staf->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']);
                            $cat = $isKatimOrKoor ? 'katim' : 'pegawai';
                            $bebanLevel = $staf->total_beban >= 10 ? 'high' : ($staf->total_beban >= 5 ? 'med' : 'low');
                            $bebanColor = $staf->total_beban >= 10 ? '#dc2626' : ($staf->total_beban >= 5 ? '#d97706' : '#059669');
                            $bebanBg = $staf->total_beban >= 10 ? '#fef2f2' : ($staf->total_beban >= 5 ? '#fffbeb' : '#ecfdf5');
                        @endphp
                        <div class="dash-workload-card" data-cat="{{ $cat }}" data-name="{{ strtolower($staf->name) }}"
                            style="background: white; border: 1px solid var(--border); border-radius: 8px; padding: 10px 12px; display: flex; align-items: center; gap: 10px; transition: all 0.18s; cursor: default;">
                            <div
                                style="width: 34px; height: 34px; border-radius: 50%; background: {{ $isKatimOrKoor ? '#2563eb' : '#64748b' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; flex-shrink: 0;">
                                {{ strtoupper(substr($staf->name, 0, 2)) }}
                            </div>
                            <div style="overflow: hidden; flex: 1;">
                                <div style="font-size: 12.5px; font-weight: 700; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                    title="{{ $staf->name }}">
                                    {{ $staf->name }}
                                </div>
                                <div
                                    style="font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $staf->pokja?->kode_pokja ?? 'BGTK' }} &bull; {{ $staf->role_label }}
                                </div>
                                <!-- Mini bar beban kerja -->
                                <div
                                    style="margin-top: 4px; height: 3px; background: rgba(0,0,0,0.08); border-radius: 3px; overflow: hidden; max-width: 120px;">
                                    <div
                                        style="height: 100%; width: {{ min(100, $staf->total_beban * 10) }}%; background: {{ $bebanColor }}; border-radius: 3px;">
                                    </div>
                                </div>
                            </div>
                            <div style="flex-shrink: 0; text-align: right;">
                                <span
                                    style="font-family: monospace; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: {{ $bebanBg }}; color: {{ $bebanColor }}; display: inline-block;">
                                    {{ $staf->total_beban }} Tugas
                                </span>
                                <div style="font-size: 9.5px; color: var(--text-muted); margin-top: 2px;">
                                    {{ $staf->tugas_pic_count }} PIC &bull; {{ $staf->tugas_anggota_count }} Tim
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Container Modal Personil -->
<div id="dashPokjaModalsContainer">
    @foreach($pokjas as $p)
        @php
            $isSubbag = ($p->kode_pokja === 'SUBBAG-UMUM');
            $isPilar = ($p->tipe === 'subbag_pilar');
            $isAdhoc = ($p->tipe === 'kelompok_kerja');

            $typeBadgeText = $isSubbag ? 'TU Balai' : ($isPilar ? 'Pilar Subbag' : ($isAdhoc ? 'Satgas RBI' : 'Tim Kerja'));
            $leaderRoleText = $isSubbag ? 'Kasubag' : ($isPilar ? 'PIC Pilar' : ($isAdhoc ? 'Ketua Satgas' : 'Katim'));
            $leaderName = $p->katim?->name ?? 'Belum Ditunjuk';

            $totalTugasModal = $p->pekerjaan ? $p->pekerjaan->count() : 0;
            $selesaiModal = $p->pekerjaan ? $p->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count() : 0;
            $berjalanModal = $p->pekerjaan ? $p->pekerjaan->whereIn('status_pelaksanaan', ['dikerjakan', 'belum_dimulai'])->count() : 0;
            $ketuntasanModal = $totalTugasModal > 0 ? round(($selesaiModal / $totalTugasModal) * 100, 1) : 0;
        @endphp

        <div id="dashMemberModal_{{ $p->id }}" class="modal-backdrop" onclick="handleBackdropClick(event, '{{ $p->id }}')">
            <div class="modal-card" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                            <span class="dash-mini-code" style="font-size: 10px;">{{ $p->kode_pokja }}</span>
                            <span class="dash-mini-type">{{ $typeBadgeText }}</span>
                        </div>
                        <h4
                            style="font-size: 14.5px; font-weight: 800; color: var(--text-dark); margin: 0; line-height: 1.3;">
                            {{ $p->nama_pokja }}
                        </h4>
                    </div>
                    <button type="button" class="modal-close" onclick="closeDashMemberModal('{{ $p->id }}')"
                        title="Tutup Modal">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <!-- Mini KPI strip di dalam modal -->
                @if($totalTugasModal > 0)
                    <div
                        style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: var(--border); border-top: none; border-bottom: 1px solid var(--border);">
                        <div style="background: white; padding: 10px 14px; text-align: center;">
                            <div style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ $totalTugasModal }}</div>
                            <div style="font-size: 10px; color: var(--text-muted); font-weight: 600;">Total Tugas</div>
                        </div>
                        <div style="background: white; padding: 10px 14px; text-align: center;">
                            <div style="font-size: 18px; font-weight: 900; color: #10b981;">{{ $selesaiModal }}</div>
                            <div style="font-size: 10px; color: var(--text-muted); font-weight: 600;">Selesai</div>
                        </div>
                        <div style="background: white; padding: 10px 14px; text-align: center;">
                            <div style="font-size: 18px; font-weight: 900; color: #0284c7;">{{ $berjalanModal }}</div>
                            <div style="font-size: 10px; color: var(--text-muted); font-weight: 600;">Berjalan</div>
                        </div>
                    </div>
                    <!-- Progress bar overall -->
                    <div style="padding: 8px 18px; background: white; border-bottom: 1px solid var(--border);">
                        @php
                            $ketuntasanModal = $totalTugasModal > 0 ? round(($selesaiModal / $totalTugasModal) * 100, 1) : 0;
                        @endphp
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span style="font-size: 10.5px; color: var(--text-muted); font-weight: 600;">Tingkat Ketuntasan Unit</span>
                            <span style="font-size: 10.5px; font-weight: 800; color: #10b981;">{{ $selesaiModal }}/{{ $totalTugasModal }} Selesai ({{ $ketuntasanModal }}%)</span>
                        </div>
                        <div style="height: 6px; background: #f1f5f9; border-radius: 6px; overflow: hidden;">
                            <div
                                style="height: 100%; width: {{ $ketuntasanModal }}%; background: #10b981; border-radius: 6px; transition: width 0.8s ease;">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="modal-body" style="padding: 14px 18px; max-height: 350px; overflow-y: auto;">
                    <!-- Highlight Penanggung Jawab -->
                    <div class="dash-modal-leader-box">
                        <div>
                            <span
                                style="font-size: 11px; color: var(--text-muted); font-weight: 600; display: block;">{{ $leaderRoleText }}:</span>
                            <strong style="font-size: 13px; color: var(--text-dark);">{{ $leaderName }}</strong>
                        </div>
                        <span class="dash-badge-count">{{ $p->anggota->count() }} Pegawai</span>
                    </div>

                    <div
                        style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
                        Daftar Anggota &amp; Personil
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @forelse($p->anggota as $m)
                            @php
                                $isLeader = ($m->pivot->peran ?? '') === 'ketua' || $m->id === $p->katim_id;
                                $bebanMember = ($m->active_pic_count ?? 0) + ($m->active_member_count ?? 0);
                            @endphp
                            <div class="dash-member-row {{ $isLeader ? 'is-leader' : '' }}">
                                <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                    <div class="dash-member-avatar {{ $isLeader ? 'avatar-leader' : '' }}">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <div
                                            style="font-size: 12.5px; font-weight: 700; color: var(--text-dark); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                            {{ $m->name }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            NIP: {{ $m->nip ?? '-' }} &bull; {{ $m->jabatan ?? 'Pegawai' }}
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                    <span
                                        style="font-family: monospace; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: {{ $bebanMember > 0 ? 'rgba(16,185,129,0.12)' : 'rgba(148,163,184,0.12)' }}; color: {{ $bebanMember > 0 ? '#059669' : '#64748b' }};"
                                        title="{{ $m->active_pic_count ?? 0 }} Tugas PIC, {{ $m->active_member_count ?? 0 }} Tugas Tim">
                                        {{ $bebanMember }} Tugas
                                    </span>
                                    @if($isLeader)
                                        <span class="badge-role-leader">
                                            â­ {{ $leaderRoleText }}
                                        </span>
                                    @else
                                        <span class="badge-role-member">
                                            Anggota
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div
                                style="text-align: center; padding: 20px; font-size: 12px; color: var(--text-muted); background: var(--bg-hover, #f8fafc); border-radius: 8px; border: 1px dashed var(--border);">
                                Belum ada personil yang terdaftar pada unit ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="modal-footer"
                    style="padding: 10px 18px; display: flex; justify-content: space-between; align-items: center; gap: 8px; border-top: 1px solid var(--border); background: var(--bg-hover, #f8fafc);">
                    <a href="{{ route('pekerjaan.index', ['pokja_id' => $p->id]) }}"
                        style="font-size: 11.5px; font-weight: 700; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="ph-bold ph-list-bullets"></i>
                        Lihat Daftar Pekerjaan
                    </a>
                    <div style="display: flex; gap: 8px;">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('pokjas.index') }}" class="btn-primary"
                                style="font-size: 11.5px; padding: 5px 11px;">
                                <i class="ph-bold ph-pencil-simple"></i>
                                <span>Master Pokja</span>
                            </a>
                        @endif
                        <button type="button" class="btn-manage-members" style="font-size: 11.5px; padding: 5px 14px;"
                            onclick="closeDashMemberModal('{{ $p->id }}')">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    // ========== Tab Switcher ==========
    function switchDashboardPokjaTab(type, btn) {
        document.querySelectorAll('.dash-mini-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const pokjaGrid = document.getElementById('dashboardPokjaGrid');
        const workloadContainer = document.getElementById('dashWorkloadContainer');
        const searchEmpty = document.getElementById('pokjaSearchEmpty');

        if (type === 'beban_kerja') {
            if (pokjaGrid) pokjaGrid.style.display = 'none';
            if (workloadContainer) workloadContainer.style.display = 'flex';
            if (searchEmpty) searchEmpty.style.display = 'none';
            return;
        } else {
            if (pokjaGrid) pokjaGrid.style.display = 'grid';
            if (workloadContainer) workloadContainer.style.display = 'none';
        }

        // Clear search
        const searchInput = document.getElementById('pokjaCardSearch');
        if (searchInput) searchInput.value = '';

        const cards = document.querySelectorAll('.dash-mini-card');
        cards.forEach(card => {
            const cardType = card.getAttribute('data-tipe');
            if (type === 'all') {
                card.style.display = '';
            } else if (type === 'tim_kerja') {
                card.style.display = (cardType === 'tim_kerja') ? '' : 'none';
            } else if (type === 'subbag_pilar') {
                card.style.display = (cardType === 'subbag_pilar' || card.getAttribute('data-name').includes('subbag')) ? '' : 'none';
            } else if (type === 'kelompok_kerja') {
                card.style.display = (cardType === 'kelompok_kerja') ? '' : 'none';
            }
        });
        if (searchEmpty) searchEmpty.style.display = 'none';
    }

    // ========== Search Pokja Cards ==========
    function searchPokjaCards(q) {
        const cards = document.querySelectorAll('.dash-mini-card');
        const searchEmpty = document.getElementById('pokjaSearchEmpty');
        const qLower = q.toLowerCase().trim();
        let visible = 0;

        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const show = !qLower || name.includes(qLower);
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (searchEmpty) searchEmpty.style.display = (visible === 0 && qLower) ? 'block' : 'none';
    }

    // ========== Workload Filter ==========
    let currentWorkloadCat = 'all';

    function filterDashWorkload(cat, btn) {
        currentWorkloadCat = cat;
        if (btn) {
            btn.parentElement.querySelectorAll('.dash-mini-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
        applyDashWorkloadFilter();
    }

    function searchDashWorkload(q) {
        applyDashWorkloadFilter();
    }

    function applyDashWorkloadFilter() {
        const searchInput = document.getElementById('dashWorkloadSearch');
        const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const cards = document.querySelectorAll('.dash-workload-card');

        cards.forEach(card => {
            const cat = card.getAttribute('data-cat');
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const matchCat = (currentWorkloadCat === 'all') || (cat === currentWorkloadCat);
            const matchSearch = !q || name.includes(q);
            card.style.display = (matchCat && matchSearch) ? 'flex' : 'none';
        });
    }

    // ========== Modal ==========
    function openDashMemberModal(id) {
        const m = document.getElementById('dashMemberModal_' + id);
        if (m) {
            m.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDashMemberModal(id) {
        const m = document.getElementById('dashMemberModal_' + id);
        if (m) {
            m.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function handleBackdropClick(e, id) {
        if (e.target.id === 'dashMemberModal_' + id) {
            closeDashMemberModal(id);
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.is-open').forEach(m => {
                m.classList.remove('is-open');
            });
            document.body.style.overflow = '';
        }
    });

    // ========== Animate progress bars on load ==========
    document.addEventListener('DOMContentLoaded', function () {
        // Animate progress bars
        setTimeout(() => {
            document.querySelectorAll('.dash-progress-bar').forEach(bar => {
                const target = bar.getAttribute('data-width');
                bar.style.width = target + '%';
            });
        }, 350);
    });
</script>
    @endif

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
// ============================================================
// Visual Charts Initialization (Chart.js)
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // 1. Donut Chart Status Pelaksanaan
    const ctxStatus = document.getElementById('chartStatusTugas');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Dikerjakan', 'Menunggu Verifikasi', 'Belum Dimulai'],
                datasets: [{
                    data: [
                        {{ $statusCounts['selesai'] ?? 0 }},
                        {{ $statusCounts['dikerjakan'] ?? 0 }},
                        {{ $statusCounts['menunggu_verifikasi'] ?? 0 }},
                        {{ $statusCounts['belum_dimulai'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#10b981', // Selesai
                        '#0a6fb5', // Dikerjakan
                        '#8b5cf6', // Menunggu Verifikasi
                        '#94a3b8'  // Belum Dimulai
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b4a82',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = {{ $totalPekerjaanBalai ?? 0 }};
                                const val = context.parsed || 0;
                                const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                return ` ${context.label}: ${val} tugas (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Bar Chart Capaian Progres per Pokja
    const ctxPokja = document.getElementById('chartPokjaProgres');
    if (ctxPokja) {
        const pokjaLabels = {!! json_encode(collect($chartPokjas ?? [])->pluck('kode')) !!};
        const pokjaNames = {!! json_encode(collect($chartPokjas ?? [])->pluck('nama')) !!};
        const pokjaValues = {!! json_encode(collect($chartPokjas ?? [])->pluck('progres')) !!};
        const pokjaTotals = {!! json_encode(collect($chartPokjas ?? [])->pluck('total')) !!};

        new Chart(ctxPokja, {
            type: 'bar',
            data: {
                labels: pokjaLabels,
                datasets: [{
                    label: 'Capaian Progres',
                    data: pokjaValues,
                    backgroundColor: pokjaValues.map(v => v >= 75 ? '#10b981' : (v >= 40 ? '#0a6fb5' : (v > 0 ? '#38bdf8' : '#e2e8f0'))),
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 22,
                    maxBarThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b4a82',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(items) {
                                const idx = items[0].dataIndex;
                                return `${pokjaLabels[idx]} - ${pokjaNames[idx]}`;
                            },
                            label: function(context) {
                                const idx = context.dataIndex;
                                return ` Capaian: ${context.parsed.y}% (Total: ${pokjaTotals[idx]} tugas)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: val => val + '%',
                            font: { family: 'Plus Jakarta Sans', size: 10.5 },
                            color: '#64748b',
                            stepSize: 25
                        },
                        grid: { color: '#f1f5f9' },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            font: { family: 'JetBrains Mono', size: 10.5, weight: 'bold' },
                            color: '#334155'
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }
});

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
            const cleaned = el.textContent.trim().replace(/\./g, '').replace(',', '.');
            const num = parseFloat(cleaned);
            if (!isNaN(num) && num > 0) {
                el.textContent = '0';
                animateCount(el, 0, num, 900);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startCountUps);
    } else {
        setTimeout(startCountUps, 100);
    }
})();
</script>
@endpush
