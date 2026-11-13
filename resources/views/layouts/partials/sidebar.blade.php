<!-- Sidebar Navigation Component -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="ph-bold ph-chart-line-up"></i>
        </div>
        <div class="brand-text">
            <h2>SIMON-K</h2>
            <p>BGTK DKI Jakarta</p>
        </div>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-header">Menu Utama</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ph-duotone ph-squares-four"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('monitoring.balai') }}"
            class="nav-item {{ request()->routeIs('monitoring.balai') ? 'active' : '' }}"
            title="Monitoring Seluruh Pekerjaan BGTK (Big Screen / TV Mode)">
            <i class="ph-duotone ph-television"></i>
            <span>Monitoring Balai</span>
            <span style="margin-left: auto; background: rgba(34, 184, 232, 0.18); border: 1px solid rgba(34, 184, 232, 0.35); color: #0284c7; font-size: 9.5px; font-weight: 800; padding: 2px 7px; border-radius: 999px; letter-spacing: 0.05em;">
                TV MODE
            </span>
        </a>

        @if(Auth::user()->canManagePekerjaan())
        <a href="{{ route('pekerjaan.index') }}"
            class="nav-item {{ request()->routeIs('pekerjaan.index') || request()->routeIs('pekerjaan.show') || request()->routeIs('pekerjaan.create') || request()->routeIs('pekerjaan.edit') ? 'active' : '' }}">
            <i class="ph-duotone ph-briefcase"></i>
            <span>Daftar Pekerjaan</span>
        </a>
        @endif

        <a href="{{ route('pekerjaan.saya') }}"
            class="nav-item {{ request()->routeIs('pekerjaan.saya') ? 'active' : '' }}">
            <i class="ph-duotone ph-list-checks"></i>
            <span>Tugas & Pekerjaan Saya</span>
        </a>

        <a href="{{ route('log-harian.index') }}"
            class="nav-item {{ request()->routeIs('log-harian.*') ? 'active' : '' }}">
            <i class="ph-duotone ph-calendar-check"></i>
            <span>Log Harian</span>
        </a>

        <a href="{{ route('skp.index') }}"
            class="nav-item {{ request()->routeIs('skp.*') ? 'active' : '' }}">
            <i class="ph-duotone ph-target"></i>
            <span>{{ Auth::user()->canManagePekerjaan() ? 'Sasaran Kinerja (SKP)' : 'SKP Saya' }}</span>
        </a>

        <a href="{{ route('persetujuan.index') }}"
            class="nav-item {{ request()->routeIs('persetujuan.*') ? 'active' : '' }}">
            <i class="ph-duotone ph-seal-check"></i>
            <span>Pusat Persetujuan</span>
            @php
                $sidebarUser = Auth::user();
                $pendingProgresQuery = \App\Models\RealisasiPekerjaan::where('status_approval', 'menunggu_persetujuan');
                if ($sidebarUser->isKatim() || $sidebarUser->isKorlayanan() || $sidebarUser->isKorjafung()) {
                    $pendingProgresQuery->whereHas('pekerjaan', function($q) use ($sidebarUser) {
                        $q->where('pembuat_id', $sidebarUser->id)
                          ->orWhere('pokja_id', $sidebarUser->pokja_id);
                    });
                } elseif ($sidebarUser->isPegawai()) {
                    $pendingProgresQuery->where('user_id', $sidebarUser->id);
                }
                $sidebarPendingCount = $pendingProgresQuery->count();
            @endphp
            @if($sidebarPendingCount > 0)
                <span
                    style="margin-left: auto; background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 999px; line-height: 1;">
                    {{ $sidebarPendingCount }}
                </span>
            @endif
        </a>

        {{-- Menu Khusus Pegawai -- DINONAKTIFKAN SEMENTARA --}}
        {{-- @if(Auth::user()->isPegawai())
            <div class="menu-header">Kinerja Saya</div>
            <a href="#skp" class="nav-item">
                <i class="ph-duotone ph-target"></i>
                <span>SKP & Target Bulanan</span>
            </a>
            <a href="#mingguan" class="nav-item">
                <i class="ph-duotone ph-calendar-check"></i>
                <span>Rencana Mingguan</span>
            </a>
            <a href="#laporan" class="nav-item">
                <i class="ph-duotone ph-notepad"></i>
                <span>Laporan Kinerja Harian</span>
            </a>
        @endif --}}

        {{-- Menu Khusus Tim Pokja --}}
        @if(Auth::user()->isTimPokja())
            <div class="menu-header">Verifikasi Pokja</div>
            <a href="#antrean-pokja" class="nav-item">
                <i class="ph-duotone ph-checks"></i>
                <span>Verifikasi Laporan Anggota</span>
            </a>
            <a href="#anggota-pokja" class="nav-item">
                <i class="ph-duotone ph-users-three"></i>
                <span>Daftar Anggota Pokja</span>
            </a>
        @endif

        {{-- Menu Khusus Koordinator Jabatan Fungsional --}}
        @if(Auth::user()->isKorjafung())
            <div class="menu-header">Jabatan Fungsional</div>
            <a href="{{ route('pekerjaan.index') }}" class="nav-item">
                <i class="ph-duotone ph-briefcase"></i>
                <span>Evaluasi Kinerja JF</span>
            </a>
        @endif

        {{-- Menu Khusus Koordinator Bidang / Layanan --}}
        @if(Auth::user()->isKorlayanan())
            <div class="menu-header">Bidang & Layanan</div>
            <a href="{{ route('pekerjaan.index') }}" class="nav-item">
                <i class="ph-duotone ph-folders"></i>
                <span>Pekerjaan Bidang/Layanan</span>
            </a>
        @endif

        {{-- Menu Khusus Katim --}}
        @if(Auth::user()->isKatim())
            <div class="menu-header">Evaluasi Katim</div>
            <a href="#evaluasi-katim" class="nav-item">
                <i class="ph-duotone ph-medal"></i>
                <span>Penilaian Kualitas & IKU</span>
            </a>
            <a href="#rekap-tim" class="nav-item">
                <i class="ph-duotone ph-chart-bar"></i>
                <span>Rekap Kinerja Tim</span>
            </a>
        @endif

        {{-- Menu Khusus Kasubag TU --}}
        @if(Auth::user()->isKasubag())
            <div class="menu-header">Tata Usaha</div>
            <a href="#konsolidasi" class="nav-item">
                <i class="ph-duotone ph-file-text"></i>
                <span>Konsolidasi Laporan Bulanan</span>
            </a>
            <a href="#pegawai-mon" class="nav-item">
                <i class="ph-duotone ph-users"></i>
                <span>Monitoring Kedisiplinan</span>
            </a>
        @endif

        {{-- Menu Khusus Kepala Balai (Kabal) --}}
        @if(Auth::user()->isKabal())
            <div class="menu-header">Pimpinan Balai</div>
            <a href="#approval-kabal" class="nav-item">
                <i class="ph-duotone ph-stamp"></i>
                <span>Approval Laporan Balai</span>
            </a>
            <a href="#rekomendasi" class="nav-item">
                <i class="ph-duotone ph-trophy"></i>
                <span>Rekomendasi & Reward</span>
            </a>
        @endif

        {{-- Menu Khusus Super Admin --}}
        @if(Auth::user()->isAdmin())
            <div class="menu-header">Master Data</div>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="ph-duotone ph-user-gear"></i>
                <span>Kelola Pengguna</span>
            </a>
            <a href="{{ route('pokjas.index') }}" class="nav-item {{ request()->routeIs('pokjas.*') ? 'active' : '' }}">
                <i class="ph-duotone ph-tree-structure"></i>
                <span>Kelola Pokja</span>
            </a>
        @endif
    </nav>

    <!-- Sidebar User Footer -->
    <div class="sidebar-user">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
        </div>
        <div class="user-details">
            <div class="user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
            <span class="user-role-badge role-{{ Auth::user()->role }}" title="{{ Auth::user()->role_label }}">
                {{ Auth::user()->role_label }}
            </span>
        </div>
    </div>
</aside>