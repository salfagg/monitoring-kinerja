<!-- ======================================================================================= -->
<!-- PUSAT MONITORING & PEMANTAUAN SELURUH PEKERJAAN PEGAWAI (UNTUK PIMPINAN KABAL & KASUBAG) -->
<!-- ======================================================================================= -->

<style>
    /* Styling Khusus Pusat Monitoring Pekerjaan Pimpinan */
    .monitoring-section {
        margin-top: 10px;
        margin-bottom: 34px;
    }

    .monitoring-header-box {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-lg);
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.14);
    }

    .monitoring-header-box h2 {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
    }

    .monitoring-header-box p {
        font-size: 13.5px;
        color: #94a3b8;
        max-width: 720px;
        margin: 0;
        line-height: 1.5;
    }

    .header-action-btns {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-header-light {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: var(--radius-md);
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
        backdrop-filter: blur(4px);
    }

    .btn-header-light:hover {
        background: rgba(255, 255, 255, 0.22);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-header-primary {
        background: var(--primary);
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        border-radius: var(--radius-md);
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-header-primary:hover {
        background: var(--primary-hover);
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* Executive KPI Stats Cards */
    .exec-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .exec-kpi-card {
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

    .exec-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }

    .exec-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .exec-kpi-info h4 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .exec-kpi-info p {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
    }

    /* Workload Overview Cards */
    .workload-box {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px 22px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .workload-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .workload-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 12px;
    }

    .workload-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: var(--transition);
        text-decoration: none;
        color: inherit;
    }

    .workload-card:hover {
        background: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
        transform: translateY(-1px);
    }

    .workload-avatar {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        background: #e0e7ff;
        color: #3730a3;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .workload-details {
        overflow: hidden;
        flex: 1;
    }

    .workload-name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .workload-sub {
        font-size: 11px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .workload-badge-row {
        display: flex;
        gap: 6px;
        margin-top: 5px;
    }

    .badge-pic-count {
        font-size: 10.5px;
        font-weight: 800;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 1px 6px;
        border-radius: 4px;
        border: 1px solid #bfdbfe;
    }

    .badge-member-count {
        font-size: 10.5px;
        font-weight: 700;
        background: #f0fdf4;
        color: #166534;
        padding: 1px 6px;
        border-radius: 4px;
        border: 1px solid #bbf7d0;
    }

    /* Filter Bar */
    .monitoring-filter-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.5fr 1.2fr auto;
        gap: 12px;
        align-items: flex-end;
    }

    @media (max-width: 1024px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
    }

    .filter-label {
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .filter-input, .filter-select {
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

    .filter-input:focus, .filter-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* Table Component */
    .monitoring-table-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 28px;
    }

    .monitoring-table-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        background: #fafbfc;
    }

    .monitoring-table-header h3 {
        font-size: 15.5px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .table-monitoring {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table-monitoring th {
        background: #f8fafc;
        text-align: left;
        padding: 12px 16px;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid var(--border);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .table-monitoring td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }

    .table-monitoring tr:hover td {
        background: #f8fafc;
    }

    /* Badges & Meters */
    .badge-code {
        font-family: monospace;
        font-weight: 800;
        font-size: 11px;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #bfdbfe;
        display: inline-block;
    }

    .badge-pokja {
        font-size: 11px;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
        padding: 3px 8px;
        border-radius: 6px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-pill-status {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 9px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .exec-dikerjakan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .exec-belum_dimulai { background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; }
    .exec-hasil_diajukan { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
    .exec-selesai_terverifikasi { background: #f0fdf4; color: #166534; border: 1px solid #86efac; }
    .exec-perlu_perbaikan_hasil { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

    .progress-bar-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
    }

    .progress-track {
        flex: 1;
        height: 7px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.4s ease;
    }

    .btn-supervisi {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        padding: 6px 11px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: var(--transition);
        white-space: nowrap;
    }

    .btn-supervisi:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-supervisi-verify {
        background: #8b5cf6;
        color: #ffffff;
        border: none;
        padding: 6px 11px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: var(--transition);
        white-space: nowrap;
    }

    .btn-supervisi-verify:hover {
        background: #7c3aed;
        color: #ffffff;
        transform: translateY(-1px);
    }
</style>

<div class="monitoring-section" id="monitoringPekerjaanPegawai">
    
    <!-- 1. Header Banner Pemantauan -->
    <div class="monitoring-header-box">
        <div>
            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #60a5fa; letter-spacing: 0.06em; display: inline-flex; align-items: center; gap: 6px;">
                <i class="ph-bold ph-shield-check"></i> Supervisi Eksekutif Balai BGTK
            </span>
            <h2>
                <i class="ph-duotone ph-desktop"></i> Pusat Monitoring Seluruh Pekerjaan Pegawai
            </h2>
            <p>
                Pantau seluruh penugasan resmi, Surat Tugas (ST), tim pelaksana yang terlibat, serta progres realisasi capaian harian seluruh pegawai di semua Tim Kerja / Pokja secara terpadu.
            </p>
        </div>

        <div class="header-action-btns">
            <a href="{{ route('pekerjaan.index') }}" class="btn-header-light">
                <i class="ph-bold ph-tree-structure"></i> Pohon Hierarki
            </a>
            <a href="{{ route('persetujuan.index') }}" class="btn-header-light">
                <i class="ph-bold ph-seal-check"></i> Pusat Approval
            </a>
            @if(Auth::user()->canManagePekerjaan())
                <a href="{{ route('pekerjaan.create') }}" class="btn-header-primary">
                    <i class="ph-bold ph-plus-circle"></i> + Tugaskan Pekerjaan Baru
                </a>
            @endif
        </div>
    </div>

    <!-- 2. Metrik Statistik Global Pekerjaan Balai -->
    <div class="exec-kpi-grid">
        <a href="{{ route('dashboard', array_merge(request()->except(['filter_status', 'page_pekerjaan']), [])) }}#monitoringPekerjaanPegawai" 
           class="exec-kpi-card" style="{{ !request('filter_status') ? 'border-color: #2563eb; background: #fafcff;' : '' }}">
            <div class="exec-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="ph-duotone ph-briefcase"></i>
            </div>
            <div class="exec-kpi-info">
                <h4>{{ number_format($totalSemuaPekerjaan) }}</h4>
                <p>Total Seluruh Tugas</p>
            </div>
        </a>

        <a href="{{ route('dashboard', array_merge(request()->except(['filter_status', 'page_pekerjaan']), ['filter_status' => 'berjalan'])) }}#monitoringPekerjaanPegawai" 
           class="exec-kpi-card" style="{{ request('filter_status') === 'berjalan' ? 'border-color: #0284c7; background: #f0f9ff;' : '' }}">
            <div class="exec-kpi-icon" style="background: #f0f9ff; color: #0284c7;">
                <i class="ph-duotone ph-rocket-launch"></i>
            </div>
            <div class="exec-kpi-info">
                <h4>{{ number_format($countPekerjaanBerjalan) }}</h4>
                <p>Sedang Dikerjakan</p>
            </div>
        </a>

        <a href="{{ route('dashboard', array_merge(request()->except(['filter_status', 'page_pekerjaan']), ['filter_status' => 'menunggu_verifikasi'])) }}#monitoringPekerjaanPegawai" 
           class="exec-kpi-card" style="{{ request('filter_status') === 'menunggu_verifikasi' ? 'border-color: #9333ea; background: #faf5ff;' : '' }}">
            <div class="exec-kpi-icon" style="background: #faf5ff; color: #9333ea;">
                <i class="ph-duotone ph-seal-check"></i>
            </div>
            <div class="exec-kpi-info">
                <h4>{{ number_format($countPekerjaanMenungguVerifikasi) }}</h4>
                <p>Menunggu Verifikasi (100%)</p>
            </div>
        </a>

        <a href="{{ route('dashboard', array_merge(request()->except(['filter_status', 'page_pekerjaan']), ['filter_status' => 'selesai'])) }}#monitoringPekerjaanPegawai" 
           class="exec-kpi-card" style="{{ request('filter_status') === 'selesai' ? 'border-color: #16a34a; background: #f0fdf4;' : '' }}">
            <div class="exec-kpi-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="ph-duotone ph-check-circle"></i>
            </div>
            <div class="exec-kpi-info">
                <h4>{{ number_format($countPekerjaanSelesai) }}</h4>
                <p>Selesai Terverifikasi</p>
            </div>
        </a>

        <a href="{{ route('dashboard', array_merge(request()->except(['filter_status', 'page_pekerjaan']), ['filter_status' => 'terlambat'])) }}#monitoringPekerjaanPegawai" 
           class="exec-kpi-card" style="{{ request('filter_status') === 'terlambat' ? 'border-color: #dc2626; background: #fff5f5;' : '' }}">
            <div class="exec-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="ph-duotone ph-warning-octagon"></i>
            </div>
            <div class="exec-kpi-info">
                <h4>{{ number_format($countPekerjaanTerlambat) }}</h4>
                <p>Lewat Tenggat Waktu</p>
            </div>
        </a>
    </div>

    <!-- 3. Panel Ringkasan Beban Kerja Pegawai (Workload Monitoring) -->
    @if(isset($bebanKerjaPegawai) && $bebanKerjaPegawai->isNotEmpty())
        <div class="workload-box">
            <div class="workload-header">
                <div>
                    <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="ph-duotone ph-users-three" style="color: #2563eb; font-size: 20px;"></i> 
                        Pemantauan Beban Kerja Pegawai Balai (Top Penugasan Aktif)
                    </h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin: 3px 0 0 0;">
                        Klik pada nama pegawai untuk memfilter dan memantau seluruh rincian penugasan yang sedang diemban.
                    </p>
                </div>
                @if(request()->filled('filter_pegawai'))
                    <a href="{{ route('dashboard', request()->except(['filter_pegawai', 'page_pekerjaan'])) }}#monitoringPekerjaanPegawai" 
                       style="font-size: 12px; font-weight: 700; color: #dc2626; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="ph-bold ph-x-circle"></i> Hapus Filter Pegawai
                    </a>
                @endif
            </div>

            <div class="workload-grid">
                @foreach($bebanKerjaPegawai as $staf)
                    @php
                        $isSelectedStaf = request('filter_pegawai') == $staf->id;
                    @endphp
                    <a href="{{ route('dashboard', array_merge(request()->except(['page_pekerjaan']), ['filter_pegawai' => $staf->id])) }}#monitoringPekerjaanPegawai" 
                       class="workload-card" 
                       style="{{ $isSelectedStaf ? 'border-color: #2563eb; background: #eff6ff; box-shadow: 0 0 0 2px rgba(37,99,235,0.2);' : '' }}">
                        <div class="workload-avatar">
                            {{ strtoupper(substr($staf->name, 0, 2)) }}
                        </div>
                        <div class="workload-details">
                            <div class="workload-name" title="{{ $staf->name }}">
                                {{ $staf->name }}
                            </div>
                            <div class="workload-sub">
                                {{ $staf->pokja?->kode_pokja ?? 'BGTK' }} &bull; {{ $staf->role_label }}
                            </div>
                            <div class="workload-badge-row">
                                <span class="badge-pic-count" title="Tugas sebagai Koordinator / PIC Utama">
                                    {{ $staf->tugas_pic_count }} PIC
                                </span>
                                @if($staf->tugas_anggota_count > 0)
                                    <span class="badge-member-count" title="Tugas sebagai Anggota Tim">
                                        +{{ $staf->tugas_anggota_count }} Tim
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($isSelectedStaf)
                            <i class="ph-bold ph-check-circle" style="color: #2563eb; font-size: 18px; margin-left: auto;"></i>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. Filter & Kontrol Pencarian Interaktif -->
    <div class="monitoring-filter-card">
        <form method="GET" action="{{ route('dashboard') }}#monitoringPekerjaanPegawai">
            <div class="filter-grid">
                <!-- 1. Pencarian Teks -->
                <div>
                    <label class="filter-label"><i class="ph-bold ph-magnifying-glass"></i> Cari Tugas / ST</label>
                    <input type="text" 
                           name="q_pekerjaan" 
                           class="filter-input" 
                           value="{{ request('q_pekerjaan') }}" 
                           placeholder="Cari kode, nama pekerjaan, nomor ST...">
                </div>

                <!-- 2. Filter Pokja -->
                <div>
                    <label class="filter-label"><i class="ph-bold ph-buildings"></i> Tim Kerja / Pokja</label>
                    <select name="filter_pokja" class="filter-select">
                        <option value="">-- Semua Pokja --</option>
                        @foreach($allPokjas as $pk)
                            <option value="{{ $pk->id }}" {{ request('filter_pokja') == $pk->id ? 'selected' : '' }}>
                                {{ $pk->kode_pokja }} - {{ Str::limit($pk->nama_pokja, 22) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Filter Pegawai Pelaksana -->
                <div>
                    <label class="filter-label"><i class="ph-bold ph-user"></i> Pegawai Pelaksana</label>
                    <select name="filter_pegawai" class="filter-select">
                        <option value="">-- Seluruh Pegawai Balai --</option>
                        @foreach($allPegawai as $peg)
                            <option value="{{ $peg->id }}" {{ request('filter_pegawai') == $peg->id ? 'selected' : '' }}>
                                {{ $peg->name }} ({{ $peg->pokja?->kode_pokja ?? 'BGTK' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Filter Status Pelaksanaan -->
                <div>
                    <label class="filter-label"><i class="ph-bold ph-funnel"></i> Status Pelaksanaan</label>
                    <select name="filter_status" class="filter-select">
                        <option value="">-- Semua Status --</option>
                        <option value="berjalan" {{ request('filter_status') === 'berjalan' ? 'selected' : '' }}>⚡ Sedang Dikerjakan</option>
                        <option value="menunggu_verifikasi" {{ request('filter_status') === 'menunggu_verifikasi' ? 'selected' : '' }}>🚀 Menunggu Verifikasi (100%)</option>
                        <option value="selesai" {{ request('filter_status') === 'selesai' ? 'selected' : '' }}>🏁 Selesai Terverifikasi</option>
                        <option value="terlambat" {{ request('filter_status') === 'terlambat' ? 'selected' : '' }}>⚠️ Lewat Tenggat Waktu</option>
                        <option value="diajukan" {{ request('filter_status') === 'diajukan' ? 'selected' : '' }}>⏳ Menunggu Persetujuan Rincian</option>
                    </select>
                </div>

                <!-- 5. Tombol Aksi Filter -->
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-header-primary" style="padding: 10px 16px; border-radius: 8px; justify-content: center;">
                        <i class="ph-bold ph-magnifying-glass"></i> Filter
                    </button>
                    @if(request()->hasAny(['q_pekerjaan', 'filter_pokja', 'filter_pegawai', 'filter_status']))
                        <a href="{{ route('dashboard') }}#monitoringPekerjaanPegawai" 
                           class="btn-supervisi" 
                           style="padding: 9px 12px; border-radius: 8px; color: #dc2626; border-color: #fecaca; background: #fff5f5;" 
                           title="Reset Semua Filter">
                            <i class="ph-bold ph-arrows-counter-clockwise"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- 5. Tabel Pemantauan Kinerja Pekerjaan Pegawai -->
    <div class="monitoring-table-card">
        <div class="monitoring-table-header">
            <div>
                <h3>
                    <i class="ph-duotone ph-list-dashes" style="color: #2563eb;"></i> 
                    Daftar Pekerjaan & Realisasi Pegawai Balai
                </h3>
                <span style="font-size: 12px; color: var(--text-muted);">
                    Menampilkan {{ $daftarSemuaPekerjaan->total() }} pekerjaan berdasarkan filter pemantauan aktif.
                </span>
            </div>

            <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-flex; align-items: center; gap: 4px;">
                    <span style="width: 8px; height: 8px; border-radius: 999px; background: #10b981;"></span> Selesai
                </span>
                <span style="display: inline-flex; align-items: center; gap: 4px;">
                    <span style="width: 8px; height: 8px; border-radius: 999px; background: #3b82f6;"></span> Dikerjakan
                </span>
                <span style="display: inline-flex; align-items: center; gap: 4px;">
                    <span style="width: 8px; height: 8px; border-radius: 999px; background: #ef4444;"></span> Lewat Tenggat
                </span>
            </div>
        </div>

        <div class="table-container">
            <table class="table-monitoring">
                <thead>
                    <tr>
                        <th style="width: 70px;">Kode</th>
                        <th>Nama Pekerjaan & Dasar Penugasan</th>
                        <th>Pokja</th>
                        <th>Tim Pelaksana (PIC & Anggota)</th>
                        <th>Bobot & Target</th>
                        <th>Tenggat</th>
                        <th style="width: 140px;">Progres Capaian</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 110px;">Supervisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSemuaPekerjaan as $job)
                        <tr>
                            <!-- Kode -->
                            <td>
                                <span class="badge-code">{{ $job->kode_tampilan }}</span>
                            </td>

                            <!-- Judul & Dasar ST -->
                            <td style="max-width: 320px;">
                                <a href="{{ route('pekerjaan.show', $job) }}" 
                                   style="font-weight: 700; color: #0f172a; text-decoration: none; line-height: 1.35; display: inline-block;"
                                   class="hover-underline">
                                    {{ $job->judul }}
                                </a>

                                @if($job->parent)
                                    <div style="font-size: 11.5px; color: #64748b; margin-top: 3px;">
                                        Induk: <a href="{{ route('pekerjaan.show', $job->parent) }}" style="color: #475569; text-decoration: none;">{{ $job->parent->kode_tampilan }} - {{ Str::limit($job->parent->judul, 30) }}</a>
                                    </div>
                                @endif

                                <div style="display: flex; gap: 6px; align-items: center; margin-top: 5px; flex-wrap: wrap;">
                                    @if($job->nomor_st)
                                        <span style="font-size: 10.5px; font-weight: 700; background: #eff6ff; color: #1d4ed8; padding: 2px 7px; border-radius: 4px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="ph-bold ph-file-text"></i> ST: {{ Str::limit($job->nomor_st, 20) }}
                                        </span>
                                    @endif

                                    @if($job->pembuat && $job->pembuat_id !== $job->penanggung_jawab_id)
                                        <span style="font-size: 11px; color: #64748b; display: inline-flex; align-items: center; gap: 3px;" title="Ditugaskan oleh {{ $job->pembuat->name }}">
                                            <i class="ph-bold ph-user-check"></i> Oleh: {{ Str::limit($job->pembuat->name, 16) }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Pokja -->
                            <td>
                                <span class="badge-pokja">
                                    <i class="ph-bold ph-buildings"></i>
                                    {{ $job->pokja?->kode_pokja ?? 'BGTK' }}
                                </span>
                            </td>

                            <!-- Pegawai Pelaksana (PIC & Tim Multi-Assignee) -->
                            <td style="min-width: 170px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 30px; height: 30px; border-radius: 999px; background: #eff6ff; color: #1d4ed8; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #bfdbfe;">
                                        {{ strtoupper(substr($job->penanggungJawab->name ?? 'P', 0, 2)) }}
                                    </div>
                                    <div style="overflow: hidden;">
                                        <div style="font-weight: 700; font-size: 12.5px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $job->penanggungJawab->name ?? 'Belum Ditugaskan' }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">
                                            {{ $job->penanggungJawab->nip ?? 'PIC Utama' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Multi-Assignee Team Indicator -->
                                @if($job->pelaksana && $job->pelaksana->count() > 1)
                                    @php
                                        $teamNames = $job->pelaksana->where('id', '!=', $job->penanggung_jawab_id)->pluck('name')->implode(', ');
                                    @endphp
                                    <div style="margin-top: 4px; display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; font-weight: 700; background: #f0fdf4; color: #166534; padding: 2px 7px; border-radius: 4px; border: 1px solid #bbf7d0;"
                                         title="Anggota Pelaksana Tambahan: {{ $teamNames }}">
                                        <i class="ph-bold ph-users-three"></i> +{{ $job->pelaksana->count() - 1 }} Anggota Tim
                                    </div>
                                @endif
                            </td>

                            <!-- Target & Bobot -->
                            <td>
                                <div style="font-weight: 700; color: #0f172a;">
                                    {{ number_format($job->target, 0) }} {{ $job->satuan }}
                                </div>
                                <div style="font-size: 11.5px; color: #2563eb; font-weight: 600;">
                                    Bobot: {{ number_format($job->bobot, 1) }}%
                                </div>
                            </td>

                            <!-- Tenggat -->
                            <td style="white-space: nowrap;">
                                @php
                                    $isTerlambat = $job->isTerlambat();
                                    $sisaHari = Carbon\Carbon::today()->diffInDays($job->tenggat, false);
                                @endphp
                                <div style="font-weight: {{ $isTerlambat ? '700' : '600' }}; color: {{ $isTerlambat ? '#dc2626' : '#1e293b' }};">
                                    {{ $job->tenggat ? $job->tenggat->format('d M Y') : '-' }}
                                </div>
                                @if($job->status_pelaksanaan !== 'selesai_terverifikasi')
                                    @if($isTerlambat)
                                        <span style="font-size: 10.5px; font-weight: 800; background: #fee2e2; color: #dc2626; padding: 1px 6px; border-radius: 4px; display: inline-block; margin-top: 2px;">
                                            Lewat {{ abs($sisaHari) }} hari
                                        </span>
                                    @elseif($sisaHari <= 7 && $sisaHari >= 0)
                                        <span style="font-size: 10.5px; font-weight: 700; background: #fef3c7; color: #b45309; padding: 1px 6px; border-radius: 4px; display: inline-block; margin-top: 2px;">
                                            Sisa {{ $sisaHari }} hari
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <!-- Progres Capaian -->
                            <td>
                                <div class="progress-bar-wrap">
                                    <div class="progress-track">
                                        <div class="progress-fill" 
                                             style="width: {{ $job->progres }}%; background: {{ $job->progres >= 100 ? '#10b981' : ($job->progres >= 50 ? '#3b82f6' : '#f59e0b') }};"></div>
                                    </div>
                                    <span style="font-size: 12.5px; font-weight: 800; color: #0f172a;">
                                        {{ number_format($job->progres, 0) }}%
                                    </span>
                                </div>
                                @if($job->realisasi && $job->realisasi->first())
                                    <div style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                                        Update: {{ $job->realisasi->first()->created_at->format('d/m/y') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status Pelaksanaan -->
                            <td>
                                <span class="badge-pill-status exec-{{ $job->status_pelaksanaan }}">
                                    @if($job->status_pelaksanaan === 'selesai_terverifikasi')
                                        <i class="ph-bold ph-seal-check"></i>
                                    @elseif($job->status_pelaksanaan === 'hasil_diajukan')
                                        <i class="ph-bold ph-clock"></i>
                                    @elseif($job->status_pelaksanaan === 'dikerjakan')
                                        <i class="ph-bold ph-spinner"></i>
                                    @endif
                                    {{ str_replace('_', ' ', $job->status_pelaksanaan) }}
                                </span>
                            </td>

                            <!-- Aksi Supervisi -->
                            <td style="text-align: right; white-space: nowrap;">
                                @if($job->status_pelaksanaan === 'hasil_diajukan' || ($job->progres >= 100 && $job->status_pelaksanaan !== 'selesai_terverifikasi'))
                                    <a href="{{ route('pekerjaan.show', $job) }}" class="btn-supervisi-verify" title="Verifikasi Hasil Akhir Pekerjaan">
                                        <i class="ph-bold ph-seal-check"></i> Verifikasi
                                    </a>
                                @else
                                    <a href="{{ route('pekerjaan.show', $job) }}" class="btn-supervisi" title="Pantau Rincian Pekerjaan">
                                        <i class="ph-bold ph-eye"></i> Pantau
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="ph-duotone ph-magnifying-glass"></i>
                                <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                                    Tidak Ada Pekerjaan Ditemukan
                                </h4>
                                <p style="font-size: 13px; color: #64748b; margin: 0;">
                                    Tidak ada data penugasan pekerjaan yang cocok dengan filter pemantauan saat ini.
                                </p>
                                @if(request()->hasAny(['q_pekerjaan', 'filter_pokja', 'filter_pegawai', 'filter_status']))
                                    <div style="margin-top: 14px;">
                                        <a href="{{ route('dashboard') }}#monitoringPekerjaanPegawai" class="btn-header-primary" style="padding: 7px 14px; font-size: 12px;">
                                            <i class="ph-bold ph-arrows-counter-clockwise"></i> Reset Filter
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($daftarSemuaPekerjaan->hasPages())
            <div style="padding: 16px 22px; border-top: 1px solid #f1f5f9; background: #ffffff;">
                {{ $daftarSemuaPekerjaan->fragment('monitoringPekerjaanPegawai')->links() }}
            </div>
        @endif
    </div>

</div>
