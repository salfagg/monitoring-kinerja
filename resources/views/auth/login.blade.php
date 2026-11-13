<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Portal Kemendikdasmen - BGTK DKI Jakarta</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            width: 100vw;
            display: flex;
            overflow-x: hidden;
            background: #f4f8fb;
        }

        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ========================================================
           SISI KIRI: HERO BANNER (DEEP BLUE & DOT PATTERN)
           ======================================================== */
        .hero-left {
            flex: 1.15;
            background-color: #0c4a7e;
            /* Pola bintik-bintik halus (Dot Grid) persis seperti di screenshot */
            background-image: radial-gradient(rgba(255, 255, 255, 0.22) 1.2px, transparent 1.2px);
            background-size: 22px 22px;
            color: #ffffff;
            padding: 56px 64px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Ambient Glow Orb di belakang teks hero agar terlihat hidup & berdimensi */
        .hero-left::before {
            content: '';
            position: absolute;
            top: 38%;
            left: 10%;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(14, 165, 233, 0.06) 50%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(45px);
            animation: heroOrbPulse 7s ease-in-out infinite alternate;
            z-index: 1;
        }

        /* Logo Kampanye Resmi Kemendikdasmen di Kiri Atas */
        .campaign-header {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .campaign-logo-img {
            height: 68px;
            max-height: 78px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.15));
            transition: transform 0.3s ease;
        }

        .campaign-logo-img:hover {
            transform: scale(1.03);
        }

        /* Konten Utama Sisi Kiri */
        .hero-content {
            margin: 40px 0;
            max-width: 540px;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 15px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.35);
            border-radius: 30px;
            color: #bae6fd;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 16px;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12), inset 0 1px 1px rgba(255, 255, 255, 0.2);
            animation: badgeEntrance 0.75s cubic-bezier(0.16, 1, 0.3, 1) 0.12s both, badgePulseGlow 3.5s ease-in-out 1s infinite alternate;
        }

        .hero-badge .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #38bdf8;
            box-shadow: 0 0 10px #38bdf8, 0 0 18px #38bdf8;
            animation: badgeDotBlink 2s ease-in-out infinite;
        }

        .hero-welcome {
            font-size: 20px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 4px;
            letter-spacing: -0.01em;
            animation: welcomeEntrance 0.75s cubic-bezier(0.16, 1, 0.3, 1) 0.24s both;
        }

        .hero-simonk {
            font-size: 54px;
            font-weight: 900;
            line-height: 1.05;
            margin-bottom: 12px;
            letter-spacing: 0.04em;
            position: relative;
            display: inline-flex;
            align-items: center;
            filter: drop-shadow(0 6px 20px rgba(0, 0, 0, 0.28)) drop-shadow(0 0 25px rgba(56, 189, 248, 0.25));
            cursor: pointer;
            user-select: none;
        }

        /* Karakter Ejaan SIMON-K per Huruf */
        .spell-char {
            display: inline-block;
            color: #ffffff;
            background: linear-gradient(
                110deg,
                #ffffff 0%,
                #ffffff 38%,
                #67e8f9 50%,
                #ffffff 62%,
                #ffffff 100%
            );
            background-size: 240% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0;
            transform: translateY(-20px) scale(0.6);
            filter: blur(5px);
            transition: opacity 0.3s cubic-bezier(0.2, 0.8, 0.2, 1.2),
                        transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1.25),
                        filter 0.3s ease;
        }

        .spell-char.is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }

        .spell-char.just-typed {
            transform: translateY(-5px) scale(1.22);
            filter: drop-shadow(0 0 18px #38bdf8);
        }

        .hero-simonk.is-complete .spell-char {
            animation: simonKShimmer 5.5s linear infinite;
        }

        .hero-tagline {
            font-size: 17px;
            font-weight: 700;
            color: #93c5fd;
            line-height: 1.45;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
            animation: taglineEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.48s both;
        }

        .hero-divider {
            width: 52px;
            height: 3.5px;
            background: linear-gradient(90deg, #38bdf8, #0284c7, rgba(56, 189, 248, 0.2));
            background-size: 200% 100%;
            border-radius: 4px;
            margin-bottom: 16px;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.55);
            animation: dividerExpand 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.6s both, dividerGlow 3s ease-in-out infinite alternate;
        }

        .hero-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.65;
            font-weight: 400;
            max-width: 480px;
        }

        .hero-footer {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* ========================================================
           SISI KANAN: FORM LOGIN (FLOATING WHITE CARD)
           ======================================================== */
        .form-right {
            flex: 1;
            background: #f4f8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 18px;
            /* Border atas biru cerah persis seperti di screenshot */
            border-top: 4px solid #0077b6;
            box-shadow: 0 16px 36px -4px rgba(12, 74, 126, 0.1), 0 0 0 1px rgba(226, 232, 240, 0.8);
            padding: 38px 34px 34px 34px;
        }

        /* Logo Kemendikdasmen di Atas Form */
        .card-logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-logo-img {
            width: 240px;
            max-width: 85%;
            height: auto;
            min-height: 48px;
            object-fit: contain;
        }

        .card-title {
            text-align: center;
            font-size: 21px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }

        .card-subtitle {
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 7px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            /* Warna latar input abu-biru muda persis di screenshot */
            background: #edf3f8;
            border: 1.5px solid #dce6f0;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 13.5px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-size: 13px;
        }

        .form-input:focus {
            background: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.16);
        }

        .btn-toggle-eye {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }

        .btn-toggle-eye:hover {
            color: #0284c7;
        }

        .error-feedback {
            color: #ef4444;
            font-size: 12px;
            margin-top: 6px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            line-height: 1.4;
        }

        .form-input.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fff8f8 !important;
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18) !important;
            border-color: #dc2626 !important;
        }

        /* Container Tombol Masuk di Tengah */
        .btn-masuk-wrap {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        /* Tombol Masuk Berbentuk Pill Biru */
        .btn-masuk {
            background: #0077b6;
            color: #ffffff;
            border: none;
            border-radius: 30px;
            padding: 11px 52px;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(0, 119, 182, 0.28);
            transition: all 0.2s ease;
        }

        .btn-masuk:hover {
            background: #02699f;
            box-shadow: 0 6px 18px rgba(0, 119, 182, 0.38);
            transform: translateY(-1px);
        }

        .btn-masuk:active {
            transform: translateY(0);
        }

        /* Alert Status / Info */
        .alert-toast {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ========================================================
           ANIMASI ELEMEN HERO & ENTRANCE LOGIN
           ======================================================== */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes cardPopIn {
            0% {
                opacity: 0;
                transform: translateY(28px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-24px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* --- Animasi Khusus Teks & Elemen Hero Sisi Kiri --- */
        @keyframes heroOrbPulse {
            0% {
                transform: scale(0.9) translate(0, 0);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.15) translate(25px, -15px);
                opacity: 0.9;
            }
            100% {
                transform: scale(0.95) translate(-10px, 15px);
                opacity: 0.65;
            }
        }

        @keyframes badgeEntrance {
            from {
                opacity: 0;
                transform: translateY(-16px) scale(0.92);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes badgePulseGlow {
            0% {
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12), 0 0 0 rgba(56, 189, 248, 0);
                border-color: rgba(56, 189, 248, 0.35);
            }
            100% {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.16), 0 0 16px rgba(56, 189, 248, 0.35);
                border-color: rgba(125, 211, 252, 0.65);
            }
        }

        @keyframes badgeDotBlink {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.35;
                transform: scale(0.8);
            }
        }

        @keyframes welcomeEntrance {
            from {
                opacity: 0;
                transform: translateY(18px);
                filter: blur(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }

        @keyframes simonKEntrance {
            0% {
                opacity: 0;
                transform: translateY(24px) scale(0.92);
                letter-spacing: -0.01em;
                filter: blur(5px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                letter-spacing: 0.03em;
                filter: blur(0);
            }
        }

        @keyframes simonKShimmer {
            0% {
                background-position: 180% 0;
            }
            100% {
                background-position: -180% 0;
            }
        }

        @keyframes taglineEntrance {
            from {
                opacity: 0;
                transform: translateX(-24px);
                filter: blur(4px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
                filter: blur(0);
            }
        }

        @keyframes dividerExpand {
            from {
                width: 0;
                opacity: 0;
            }
            to {
                width: 52px;
                opacity: 1;
            }
        }

        @keyframes dividerGlow {
            0% {
                background-position: 0% 50%;
                box-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
            }
            100% {
                background-position: 100% 50%;
                box-shadow: 0 0 18px rgba(56, 189, 248, 0.85);
            }
        }

        /* Penerapan Animasi Masuk secara Bertahap (Staggered Animation) */
        .campaign-header {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .hero-footer {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.65s both;
        }

        .login-card {
            animation: cardPopIn 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.12s both;
        }

        /* Efek muncul bertahap yang halus untuk elemen di dalam kartu login */
        .card-logo-wrap {
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.22s both;
        }

        .card-title,
        .card-subtitle {
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.28s both;
        }

        .login-card form .form-group:nth-of-type(1) {
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.34s both;
        }

        .login-card form .form-group:nth-of-type(2) {
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.4s both;
        }

        .btn-masuk-wrap {
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.46s both;
        }

        @media (prefers-reduced-motion: reduce) {

            .campaign-header,
            .hero-badge,
            .hero-badge .badge-dot,
            .hero-welcome,
            .hero-simonk,
            .hero-tagline,
            .hero-divider,
            .hero-left::before,
            .hero-footer,
            .login-card,
            .card-logo-wrap,
            .card-title,
            .card-subtitle,
            .login-card form .form-group,
            .btn-masuk-wrap {
                animation: none !important;
                transform: none !important;
                opacity: 1 !important;
            }

            .spell-char {
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
            }
        }

        /* Responsif untuk Mobile / Tablet */
        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
            }

            .hero-left {
                padding: 40px 28px;
                flex: none;
            }

            .hero-simonk {
                font-size: 38px;
            }

            .hero-welcome {
                font-size: 18px;
            }

            .form-right {
                padding: 32px 18px 48px 18px;
            }

            .login-card {
                padding: 30px 24px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <!-- ========================================================
             SISI KIRI: HERO BANNER (PERSIS GAMBAR REFERENSI)
             ======================================================== -->
        <div class="hero-left">
            <!-- Logo Kampanye Resmi Kemendikdasmen di Kiri Atas -->
            <div class="campaign-header">
                @if(file_exists(public_path('images/pendidikanbermutu.png')))
                    <img src="{{ asset('images/pendidikanbermutu.png') }}" alt="Pendidikan Bermutu Untuk Semua"
                        class="campaign-logo-img">
                @endif

                @if(file_exists(public_path('images/amanah.png')))
                    <img src="{{ asset('images/amanah.png') }}" alt="Amanah" class="campaign-logo-img">
                @endif

                @if(!file_exists(public_path('images/pendidikanbermutu.png')) && !file_exists(public_path('images/ramah.png')))
                    <img src="{{ asset('images/logo-kampanye.svg') }}" alt="Kemendikdasmen Ramah & Pendidikan Bermutu"
                        class="campaign-logo-img">
                @endif
            </div>

            <!-- Konten Headline Kiri -->
            <div class="hero-content">
                <span class="hero-badge">
                    <span class="badge-dot"></span>
                    BGTK PROVINSI DKI JAKARTA
                </span>
                <div class="hero-welcome">Selamat Datang di</div>
                <h1 class="hero-simonk" aria-label="SIMON-K" title="SIMON-K">
                    <span class="spell-char" data-char="S">S</span>
                    <span class="spell-char" data-char="I">I</span>
                    <span class="spell-char" data-char="M">M</span>
                    <span class="spell-char" data-char="O">O</span>
                    <span class="spell-char" data-char="N">N</span>
                    <span class="spell-char" data-char="-">-</span>
                    <span class="spell-char" data-char="K">K</span>
                </h1>
                <div class="hero-tagline">Sistem Informasi Monitoring Kinerja Pegawai</div>
                <div class="hero-divider"></div>
            </div>

            <!-- Footer Kiri -->
            <div class="hero-footer">
                Tim Sistem Informasi • BGTK Provinsi DKI Jakarta © 2026
            </div>
        </div>

        <!-- ========================================================
             SISI KANAN: KARTU FORMULIR LOGIN MELAYANG
             ======================================================== -->
        <div class="form-right">
            <div class="login-card">
                <!-- Logo Resmi BGTK DKI Jakarta di Kepala Kartu -->
                <div class="card-logo-wrap">
                    @if(file_exists(public_path('images/LOGO-BGTK DKI JAKARTA.png')))
                        <img src="{{ asset('images/LOGO-BGTK DKI JAKARTA.png') }}" alt="Logo BGTK DKI Jakarta"
                            class="card-logo-img">
                    @elseif(file_exists(public_path('images/logo-kemendikdasmen.png')))
                        <img src="{{ asset('images/logo-kemendikdasmen.png') }}" alt="Logo Kemendikdasmen"
                            class="card-logo-img">
                    @else
                        <img src="{{ asset('images/logo-kemendikdasmen.svg') }}" alt="Logo Kemendikdasmen"
                            class="card-logo-img">
                    @endif
                </div>

                <h2 class="card-title">Masuk</h2>
                <p class="card-subtitle">Selamat datang, masuk menggunakan akun anda.</p>

                @if(session('info'))
                    <div class="alert-toast">
                        <i class="ph-bold ph-info"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert-toast">
                        <i class="ph-bold ph-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <!-- Field Username / Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Username</label>
                        <div class="input-box">
                            <input type="text" name="email" id="email"
                                class="form-input @if($errors->any()) is-invalid @endif" value="{{ old('email') }}"
                                placeholder="Masukkan username atau email" required autofocus>
                        </div>
                    </div>

                    <!-- Field Kata Sandi -->
                    <div class="form-group">
                        <label class="form-label" for="password">Kata Sandi</label>
                        <div class="input-box">
                            <input type="password" name="password" id="password"
                                class="form-input @if($errors->any()) is-invalid @endif" style="padding-right: 40px;"
                                placeholder="Masukkan kata sandi" required>
                            <button type="button" class="btn-toggle-eye" id="togglePasswordBtn"
                                onclick="togglePasswordVisibility()" title="Tampilkan / Sembunyikan Kata Sandi">
                                <i class="ph-bold ph-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @if($errors->has('password'))
                            <div class="error-feedback">
                                <i class="ph-bold ph-warning-circle"></i>
                                <span>{{ $errors->first('password') }}</span>
                            </div>
                        @elseif($errors->any())
                            <div class="error-feedback">
                                <i class="ph-bold ph-warning-circle"></i>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Masuk Berbentuk Pill Bulat Lonjong di Tengah -->
                    <div class="btn-masuk-wrap">
                        <button type="submit" class="btn-masuk">
                            <span>Masuk</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('ph-eye');
                toggleIcon.classList.add('ph-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('ph-eye-slash');
                toggleIcon.classList.add('ph-eye');
            }
        }

        // ========================================================
        // ANIMASI MENGEJA TEKS SIMON-K PER HURUF (S s/d K)
        // ========================================================
        (function initSimonKSpelling() {
            const container = document.querySelector('.hero-simonk');
            const chars = document.querySelectorAll('.hero-simonk .spell-char');
            if (!chars.length || !container) return;

            let isSpelling = false;
            let cycleTimer = null;

            function spellLetters() {
                if (isSpelling) return;
                isSpelling = true;

                // Reset status
                container.classList.remove('is-complete');
                chars.forEach(ch => ch.classList.remove('is-visible', 'just-typed'));

                // Eja huruf per huruf secara berurutan: S, I, M, O, N, -, K
                const letterDelay = 180; // Kecepatan ejaan per huruf (ms)
                const startDelay = 350;

                chars.forEach((ch, index) => {
                    setTimeout(() => {
                        ch.classList.add('is-visible', 'just-typed');
                        setTimeout(() => ch.classList.remove('just-typed'), 220);

                        // Bila sudah sampai huruf terakhir (K)
                        if (index === chars.length - 1) {
                            setTimeout(() => {
                                container.classList.add('is-complete');

                                // Tahan tampilan teks lengkap selama 4.8 detik agar jelas dibaca
                                cycleTimer = setTimeout(() => {
                                    fadeOutAndRepeat();
                                }, 4800);
                            }, 200);
                        }
                    }, startDelay + (index * letterDelay));
                });
            }

            function fadeOutAndRepeat() {
                // Fade out halus per huruf
                chars.forEach((ch, idx) => {
                    setTimeout(() => {
                        ch.classList.remove('is-visible');
                    }, (chars.length - 1 - idx) * 50);
                });

                setTimeout(() => {
                    isSpelling = false;
                    spellLetters();
                }, (chars.length * 50) + 350);
            }

            // Mulai siklus ejaan pertama kali setelah halaman siap
            setTimeout(spellLetters, 250);

            // Interaksi klik: Bisa dieja ulang kapan saja saat diklik
            container.addEventListener('click', function () {
                clearTimeout(cycleTimer);
                isSpelling = false;
                spellLetters();
            });
        })();
    </script>
</body>

</html>