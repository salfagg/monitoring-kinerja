<!-- Top Navbar Component (SIMON-K Navy Style) -->
<header class="top-navbar">
    <div class="navbar-left">
        <div class="navbar-brand-badge">
            <i class="ph-bold ph-shield-check"></i>
        </div>
        <div>
            <div class="navbar-brand-title">
                <span>BGTK PROVINSI DKI JAKARTA</span>
                <span class="navbar-live-tag">SIMON-K</span>
            </div>
            <div class="navbar-page-sub">
                @yield('page-title', 'Sistem Informasi Monitoring Kinerja Pegawai')
            </div>
        </div>
    </div>

    <!-- Center Clock & Date Capsule Pill -->
    <div class="navbar-clock-pill">
        <div class="clock-pulse" title="Sistem terhubung dan aktif"></div>
        <div class="clock-time" id="navbarLiveTimeDisplay">{{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
        <div class="clock-sep">|</div>
        <div class="clock-date" id="navbarLiveDateDisplay">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    <div class="top-actions">
        <!-- Shortcut to Monitoring Balai (TV Mode) -->
        <a href="{{ route('monitoring.balai') }}" class="nav-pill-btn btn-tv-pill"
            title="Buka Mode Pemantauan Layar Penuh / TV">
            <i class="ph-bold ph-television"></i>
            <span>Monitoring Balai</span>
        </a>

        <!-- Toggle Dark / Light Mode Button -->
        <button type="button" id="themeToggleBtn" class="nav-pill-btn" onclick="toggleTheme()"
            title="Ubah Mode Tampilan (Terang / Gelap)">
            <i id="themeToggleIcon" class="ph-bold ph-moon"></i>
            <span id="themeToggleText">Mode Gelap</span>
        </button>

        <!-- Logout Form -->
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="nav-pill-btn btn-logout-pill" title="Keluar dari sistem">
                <i class="ph-bold ph-sign-out"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</header>