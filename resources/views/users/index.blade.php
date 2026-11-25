@extends('layouts.app')

@section('title', 'Kelola Pengguna | TANJIDOR BGTK')
@section('page-title', 'Kelola Pengguna Sistem')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/master-data.css') }}">
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
                    <option value="pimpinan_katim" {{ request('role') == 'pimpinan_katim' ? 'selected' : '' }}>⭐ Semua Pimpinan & Ketua Tim</option>
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
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" style="display: inline-block;"
                                              data-confirm="Apakah Anda yakin ingin menghapus data pengguna {{ $u->name }}? Tindakan ini tidak dapat dibatalkan."
                                              data-confirm-title="Hapus Data Pengguna"
                                              data-confirm-btn="Ya, Hapus Pengguna"
                                              data-confirm-type="danger">
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
