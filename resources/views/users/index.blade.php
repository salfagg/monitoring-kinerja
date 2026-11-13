@extends('layouts.app')

@section('title', 'Kelola Pengguna | SIMON-K BGTK')
@section('page-title', 'Kelola Pengguna Sistem')

@section('styles')
<style>
    /* Header Area */
    .page-header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .btn-create-user {
        background: var(--primary);
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.22);
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .btn-create-user:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
    }

    /* KPI Summary Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: var(--radius-md);
        border: 2px solid var(--border);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        text-decoration: none;
        color: inherit;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .stat-card.active {
        border-color: var(--primary);
        background: #f8fafc;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15), 0 4px 12px rgba(37, 99, 235, 0.1);
    }

    .stat-card.active .stat-info h4 {
        color: var(--primary);
    }

    .stat-indicator {
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 3px;
        opacity: 0.85;
    }

    .stat-card:hover .stat-indicator {
        opacity: 1;
        text-decoration: underline;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .icon-blue { background: #eff6ff; color: #2563eb; }
    .icon-purple { background: #fdf4ff; color: #a21caf; }
    .icon-green { background: #ecfdf5; color: #047857; }
    .icon-red { background: #fef2f2; color: #dc2626; }

    .stat-info h4 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .stat-info p {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
    }

    /* Filter & Search Toolbar */
    .filter-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1.3fr 1.3fr auto;
        gap: 14px;
        align-items: flex-end;
    }

    .filter-group label {
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .filter-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .filter-input-wrapper i {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: 16px;
        pointer-events: none;
    }

    .filter-input, .filter-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13.5px;
        color: var(--text-dark);
        background: #f8fafc;
        outline: none;
        transition: var(--transition);
    }

    .filter-input-wrapper .filter-input {
        padding-left: 36px;
    }

    .filter-input:focus, .filter-select:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .btn-filter {
        padding: 10px 20px;
        background: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 42px;
        transition: var(--transition);
    }

    .btn-filter:hover {
        background: #1e293b;
    }

    .btn-reset {
        padding: 10px 14px;
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-reset:hover {
        background: #fecaca;
    }

    /* Table Container Card */
    .table-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 980px;
        font-size: 13px;
    }

    .custom-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .custom-table tbody td {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }

    .custom-table tbody tr:hover td {
        background-color: #fafbfc;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* User Avatar & Info */
    .user-info-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cell-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        font-weight: 800;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    .user-name-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
        line-height: 1.3;
    }

    .user-nip-badge {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Role Pill Badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.03em;
        white-space: nowrap;
        text-transform: uppercase;
    }

    /* Pokja Badges */
    .pokja-code-badge {
        font-family: monospace;
        font-weight: 800;
        font-size: 11px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 2px;
    }

    /* Action Buttons */
    .action-btn-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 14px;
        text-decoration: none;
    }

    .btn-action-icon:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .btn-action-icon.btn-delete:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* Pagination Area & SVG Bug Fix */
    .table-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .pagination-wrapper {
        display: flex;
        align-items: center;
    }

    /* CRITICAL FIX: Eliminate giant Tailwind SVG pagination icons */
    .pagination-wrapper svg {
        width: 16px !important;
        height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }

    .pagination-wrapper nav {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pagination-wrapper nav > div:first-child {
        display: none !important;
    }

    .pagination-wrapper a,
    .pagination-wrapper span.relative,
    .pagination-wrapper span[aria-current="page"] span {
        padding: 6px 12px !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        color: #475569 !important;
        text-decoration: none !important;
        background: #ffffff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 34px !important;
        height: 34px !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
        transition: all 0.15s ease !important;
    }

    .pagination-wrapper a:hover {
        background: #f1f5f9 !important;
        color: #2563eb !important;
        border-color: #cbd5e1 !important;
    }

    .pagination-wrapper span[aria-current="page"] span {
        background: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
    }

    /* Responsive Breakpoints */
    @media (max-width: 992px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
        .page-header-actions {
            flex-direction: column;
            align-items: flex-start;
        }
        .table-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')

    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">Daftar Pengguna Sistem</h2>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 3px;">
                Kelola data seluruh pegawai, penetapan 7 role resmi, dan penempatan unit Tim Kerja BGTK DKI Jakarta
            </p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-create-user">
            <i class="ph-bold ph-user-plus"></i>
            <span>Tambah Pengguna Baru</span>
        </a>
    </div>

    <!-- Quick Stats Metric Cards (Klik untuk Auto-Filter) -->
    <div class="stats-grid">
        <a href="{{ route('users.index', request()->except(['role', 'page'])) }}" 
           class="stat-card {{ !request()->filled('role') ? 'active' : '' }}" 
           title="Klik untuk melihat seluruh pegawai">
            <div class="stat-icon icon-blue">
                <i class="ph-duotone ph-users"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $totalUsers ?? $users->total() }}</h4>
                <p>Total Seluruh Pegawai</p>
                <div class="stat-indicator">
                    <span>Semua Akun</span>
                    <i class="ph-bold ph-arrow-right"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('users.index', array_merge(request()->except(['role', 'page']), ['role' => 'pimpinan_katim'])) }}" 
           class="stat-card {{ request('role') == 'pimpinan_katim' ? 'active' : '' }}" 
           title="Klik untuk memfilter Pimpinan & Ketua Tim Kerja">
            <div class="stat-icon icon-purple">
                <i class="ph-duotone ph-briefcase"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $totalPimpinanKatim ?? 10 }}</h4>
                <p>Pimpinan & Ketua Tim</p>
                <div class="stat-indicator">
                    <span>Auto Filter</span>
                    <i class="ph-bold ph-funnel"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('users.index', array_merge(request()->except(['role', 'page']), ['role' => 'pegawai'])) }}" 
           class="stat-card {{ request('role') == 'pegawai' ? 'active' : '' }}" 
           title="Klik untuk memfilter Pegawai Pelaksana / JF">
            <div class="stat-icon icon-green">
                <i class="ph-duotone ph-user-check"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $totalPegawai ?? 44 }}</h4>
                <p>Pegawai Pelaksana / JF</p>
                <div class="stat-indicator">
                    <span>Auto Filter</span>
                    <i class="ph-bold ph-funnel"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('users.index', array_merge(request()->except(['role', 'page']), ['role' => 'admin'])) }}" 
           class="stat-card {{ request('role') == 'admin' ? 'active' : '' }}" 
           title="Klik untuk memfilter Super Administrator">
            <div class="stat-icon icon-red">
                <i class="ph-duotone ph-shield-check"></i>
            </div>
            <div class="stat-info">
                <h4>{{ $totalAdmin ?? 1 }}</h4>
                <p>Super Administrator</p>
                <div class="stat-indicator">
                    <span>Auto Filter</span>
                    <i class="ph-bold ph-funnel"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="filter-card">
        <form method="GET" action="{{ route('users.index') }}" class="filter-grid">
            <div class="filter-group">
                <label for="search">Cari Nama / NIP / Email</label>
                <div class="filter-input-wrapper">
                    <i class="ph-bold ph-magnifying-glass"></i>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="filter-input" placeholder="Ketik nama, NIP, atau email...">
                </div>
            </div>

            <div class="filter-group">
                <label for="role">Filter Role Resmi</label>
                <select name="role" id="role" class="filter-select">
                    <option value="">-- Semua Role --</option>
                    <option value="pimpinan_katim" {{ request('role') == 'pimpinan_katim' ? 'selected' : '' }}>🌟 Semua Pimpinan & Ketua Tim</option>
                    <option value="kabal" {{ request('role') == 'kabal' ? 'selected' : '' }}>1. Kepala Balai</option>
                    <option value="kasubag" {{ request('role') == 'kasubag' ? 'selected' : '' }}>2. Kepala Subbagian Umum</option>
                    <option value="korjafung" {{ request('role') == 'korjafung' ? 'selected' : '' }}>3. Koordinator Jabatan Fungsional</option>
                    <option value="katim" {{ in_array(request('role'), ['katim', 'katim_korjafung']) ? 'selected' : '' }}>4. Ketua Tim Kerja / Pokja</option>
                    <option value="korlayanan" {{ in_array(request('role'), ['korlayanan', 'koordinator_layanan']) ? 'selected' : '' }}>5. Koordinator Bidang / Layanan</option>
                    <option value="pegawai" {{ in_array(request('role'), ['pegawai', 'tim_pokja']) ? 'selected' : '' }}>6. Pegawai Pelaksana</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>7. Administrator</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="pokja_id">Filter Tim Kerja / Pokja</label>
                <select name="pokja_id" id="pokja_id" class="filter-select">
                    <option value="">-- Semua Unit / Pokja --</option>
                    @foreach($pokjas as $p)
                        <option value="{{ $p->id }}" {{ request('pokja_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->kode_pokja }} - {{ $p->nama_pokja }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-filter" title="Terapkan Filter">
                    <i class="ph-bold ph-funnel"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'role', 'pokja_id']))
                    <a href="{{ route('users.index') }}" class="btn-reset" title="Hapus Filter">
                        <i class="ph-bold ph-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">Pegawai / Pengguna</th>
                        <th style="width: 20%;">Email & Kontak</th>
                        <th style="width: 16%; text-align: center;">Role Sistem</th>
                        <th style="width: 16%;">Penempatan Tim Kerja</th>
                        <th style="width: 14%;">Jabatan</th>
                        <th style="width: 6%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <!-- Pegawai / Pengguna -->
                            <td>
                                <div class="user-info-cell">
                                    <div class="cell-avatar">
                                        {{ strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $u->name), 0, 2) ?: 'BG') }}
                                    </div>
                                    <div>
                                        <div class="user-name-title">{{ $u->name }}</div>
                                        <div class="user-nip-badge">
                                            @if($u->nip)
                                                <i class="ph-bold ph-identification-card" style="color: #94a3b8;"></i>
                                                <span>NIP: {{ $u->nip }}</span>
                                            @else
                                                <span style="color: #94a3b8; font-style: italic;">Non-PNS / Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email & Kontak -->
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $u->email }}</div>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                                    <i class="ph-bold ph-phone" style="font-size: 12px;"></i>
                                    <span>{{ $u->nomor_hp ?: '-' }}</span>
                                </div>
                            </td>

                            <!-- Role Sistem -->
                            <td style="text-align: center;">
                                <span class="role-badge role-{{ $u->role }}">
                                    {{ $u->role_label }}
                                </span>
                            </td>

                            <!-- Penempatan Tim Kerja -->
                            <td>
                                @if($u->pokja)
                                    <div>
                                        <span class="pokja-code-badge">{{ $u->pokja->kode_pokja }}</span>
                                    </div>
                                    <div style="font-size: 11.5px; color: #64748b; line-height: 1.3;">
                                        {{ $u->pokja->nama_pokja }}
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-size: 12px; font-style: italic;">- Non Pokja / Balai -</span>
                                @endif
                            </td>

                            <!-- Jabatan -->
                            <td>
                                <div style="font-size: 12.5px; color: #334155; line-height: 1.4; max-width: 250px;">
                                    {{ $u->jabatan ?: '-' }}
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td style="text-align: center;">
                                <div class="action-btn-group">
                                    <a href="{{ route('users.edit', $u) }}" class="btn-action-icon" title="Ubah Pengguna">
                                        <i class="ph-bold ph-pencil-simple"></i>
                                    </a>

                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengguna {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon btn-delete" title="Hapus Pengguna">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px 20px; text-align: center; color: #64748b;">
                                <i class="ph-duotone ph-user-circle-gear" style="font-size: 40px; color: #94a3b8; display: block; margin-bottom: 8px;"></i>
                                <div style="font-weight: 700; color: #334155; font-size: 15px;">Tidak Ada Pengguna Ditemukan</div>
                                <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Coba ubah kata kunci pencarian atau reset filter role/pokja.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer: Pagination & Info -->
        <div class="table-footer">
            <div style="font-size: 13px; color: #64748b; font-weight: 500;">
                Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> - <strong>{{ $users->lastItem() ?? 0 }}</strong> dari total <strong>{{ $users->total() }}</strong> pengguna
            </div>
            <div class="pagination-wrapper">
                {{ $users->links() }}
            </div>
        </div>
    </div>

@endsection
