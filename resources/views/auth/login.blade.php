<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | TANJIDOR - BGTK DKI Jakarta</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
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
                <h1 class="hero-tanjidor" aria-label="TANJIDOR" title="TANJIDOR">
                    <span class="spell-char" data-char="T">T</span>
                    <span class="spell-char" data-char="A">A</span>
                    <span class="spell-char" data-char="N">N</span>
                    <span class="spell-char" data-char="J">J</span>
                    <span class="spell-char" data-char="I">I</span>
                    <span class="spell-char" data-char="D">D</span>
                    <span class="spell-char" data-char="O">O</span>
                    <span class="spell-char" data-char="R">R</span>
                </h1>
                <div class="hero-tagline">Tata Kelola Kinerja Digital dan Monitoring</div>
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
        // ANIMASI MENGEJA TEKS TANJIDOR PER HURUF (T s/d R)
        // ========================================================
        (function initTanjidorSpelling() {
            const container = document.querySelector('.hero-tanjidor, .hero-simonk');
            const chars = document.querySelectorAll('.hero-tanjidor .spell-char, .hero-simonk .spell-char');
            if (!chars.length || !container) return;

            let isSpelling = false;
            let cycleTimer = null;

            function spellLetters() {
                if (isSpelling) return;
                isSpelling = true;

                // Reset status
                container.classList.remove('is-complete');
                chars.forEach(ch => ch.classList.remove('is-visible', 'just-typed'));

                // Eja huruf per huruf secara berurutan: T, A, N, J, I, D, O, R
                const letterDelay = 180; // Kecepatan ejaan per huruf (ms)
                const startDelay = 350;

                chars.forEach((ch, index) => {
                    setTimeout(() => {
                        ch.classList.add('is-visible', 'just-typed');
                        setTimeout(() => ch.classList.remove('just-typed'), 220);

                        // Bila sudah sampai huruf terakhir (R)
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