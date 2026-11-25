<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | TANJIDOR BGTK</title>
    
    <!-- Instant Dark Theme Early Check to Prevent FOUC -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('tanjidor_theme') || localStorage.getItem('simon_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons CDN -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Custom Modern Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/tanjidor-theme.css') }}">
    @yield('styles')
</head>
<body class="{{ session('login_transition') ? 'has-login-reveal' : '' }}">

    @if(session('login_transition'))
    <!-- Split-Screen Reveal Overlay setelah Login Berhasil -->
    <div id="loginRevealOverlay" aria-hidden="true">
        <div class="reveal-pane-blue">
            <span class="reveal-tag">BGTK PROVINSI DKI JAKARTA</span>
            <h2>TANJIDOR</h2>
            <p>Tata Kelola Kinerja Digital dan Monitoring</p>
        </div>
        <div class="reveal-pane-white"></div>
    </div>
    <script>
        (function() {
            const overlay = document.getElementById('loginRevealOverlay');
            if (!overlay) return;
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    overlay.classList.add('is-revealing');
                    document.body.classList.add('is-dashboard-revealed');
                });
            });
            setTimeout(function() {
                overlay.remove();
                document.body.classList.remove('has-login-reveal', 'is-dashboard-revealed');
            }, 820);
        })();
    </script>
    @endif

    <!-- Top Navigation Progress Bar -->
    <div id="tanjidorTopProgress" aria-hidden="true"></div>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

    <!-- Sidebar Navigation Partial -->
    @include('layouts.partials.sidebar')

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar Partial -->
        @include('layouts.partials.navbar')

        <!-- Dynamic Content Area -->
        <main class="content-area">
            <!-- Flash Message Alerts Partial -->
            @include('layouts.partials.alerts')

            @yield('content')
        </main>
    </div>

    <!-- Global Scripts -->
    <script>
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('tanjidor_theme', next);
            localStorage.setItem('simon_theme', next);
            updateThemeToggleUI(next);
        }

        function updateThemeToggleUI(theme) {
            const icon = document.getElementById('themeToggleIcon');
            const text = document.getElementById('themeToggleText');
            if (icon && text) {
                if (theme === 'dark') {
                    icon.className = 'ph-bold ph-sun';
                    text.textContent = 'Mode Terang';
                } else {
                    icon.className = 'ph-bold ph-moon';
                    text.textContent = 'Mode Gelap';
                }
            }
        }

        // Sync button text & clock on load
        document.addEventListener('DOMContentLoaded', function() {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeToggleUI(current);
            updateNavbarLiveClock();
        });

        function updateNavbarLiveClock() {
            const timeEl = document.getElementById('navbarLiveTimeDisplay');
            if (timeEl) {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                timeEl.textContent = `${h}:${m}:${s} WIB`;
            }
        }
        setInterval(updateNavbarLiveClock, 1000);

        // Mobile Sidebar Controls
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('appSidebar') || document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) {
                const isOpen = sidebar.classList.toggle('open');
                if (backdrop) backdrop.classList.toggle('active', isOpen);
                document.body.classList.toggle('sidebar-mobile-open', isOpen);
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('appSidebar') || document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('active');
            document.body.classList.remove('sidebar-mobile-open');
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 900) {
                closeMobileSidebar();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileSidebar();
            }
        });
    </script>

    <!-- SweetAlert2 Core Script -->
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script>
        // Global Modern Confirmation Modal Helper
        window.tanjidorConfirm = function(options = {}) {
            const {
                title = 'Konfirmasi Tindakan',
                text = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                html = null,
                icon = 'question',
                confirmButtonText = 'Ya, Lanjutkan',
                cancelButtonText = 'Batal',
                confirmButtonColor = null,
                type = 'primary'
            } = options;

            let iconColor = '#2563eb';
            let btnColor = '#2563eb';

            if (type === 'danger' || icon === 'warning' || icon === 'error') {
                iconColor = '#ef4444';
                btnColor = '#dc2626';
            } else if (type === 'success' || icon === 'success') {
                iconColor = '#10b981';
                btnColor = '#10b981';
            } else if (type === 'warning') {
                iconColor = '#f59e0b';
                btnColor = '#d97706';
            }

            if (confirmButtonColor) {
                btnColor = confirmButtonColor;
            }

            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

            return Swal.fire({
                title: title,
                text: text,
                html: html || undefined,
                icon: icon,
                iconColor: iconColor,
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                confirmButtonColor: btnColor,
                reverseButtons: true,
                focusCancel: type === 'danger',
                background: isDark ? '#151e32' : '#ffffff',
                color: isDark ? '#f8fafc' : '#0f172a',
                customClass: {
                    popup: 'tanjidor-swal-modal',
                    title: 'tanjidor-swal-title',
                    htmlContainer: 'tanjidor-swal-content',
                    confirmButton: 'tanjidor-swal-confirm-btn',
                    cancelButton: 'tanjidor-swal-cancel-btn',
                },
                buttonsStyling: false,
            }).then((result) => result.isConfirmed);
        };

        // Global Alert Modal Helper
        window.tanjidorAlert = function(title, text = '', type = 'info') {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            return Swal.fire({
                title: title,
                text: text,
                icon: type,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#2563eb',
                background: isDark ? '#151e32' : '#ffffff',
                color: isDark ? '#f8fafc' : '#0f172a',
                customClass: {
                    popup: 'tanjidor-swal-modal',
                    title: 'tanjidor-swal-title',
                    htmlContainer: 'tanjidor-swal-content',
                    confirmButton: 'tanjidor-swal-confirm-btn',
                },
                buttonsStyling: false,
            });
        };

        // Intercept form submissions with data-confirm automatically
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.dataset.tanjidorConfirmed === 'true') {
                return;
            }

            if (form.hasAttribute('data-confirm')) {
                e.preventDefault();
                const text = form.getAttribute('data-confirm');
                const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const confirmBtn = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
                const type = form.getAttribute('data-confirm-type') || 
                    (form.querySelector('input[name="_method"][value="DELETE"]') ? 'danger' : 'success');
                const icon = form.getAttribute('data-confirm-icon') || 
                    (type === 'danger' ? 'warning' : (type === 'success' ? 'success' : 'question'));

                window.tanjidorConfirm({
                    title: title,
                    text: text,
                    confirmButtonText: confirmBtn,
                    type: type,
                    icon: icon
                }).then(confirmed => {
                    if (confirmed) {
                        form.dataset.tanjidorConfirmed = 'true';
                        form.submit();
                    }
                });
            }
        });

        // Universal File Preview Modal Logic
        window.previewBuktiDukung = function(fileUrl, fileName = 'Berkas Bukti Dukung') {
            const modal = document.getElementById('modalPreviewBuktiUniversal');
            const titleEl = document.getElementById('previewBuktiTitle');
            const bodyEl = document.getElementById('previewBuktiBody');
            const extBadge = document.getElementById('previewBuktiExtBadge');
            const extBtn = document.getElementById('previewBuktiExternalBtn');
            const dlBtn = document.getElementById('previewBuktiDownloadBtn');
            const iconEl = document.getElementById('previewBuktiIcon');
            const iconBox = document.getElementById('previewBuktiIconBox');

            if (!modal) return;

            // Detect extension from URL or fileName
            const cleanUrl = fileUrl.split('?')[0].split('#')[0];
            const extension = cleanUrl.split('.').pop().toLowerCase();

            titleEl.textContent = fileName || cleanUrl.split('/').pop();
            extBadge.textContent = extension.toUpperCase();
            extBtn.href = fileUrl;
            dlBtn.href = fileUrl;

            // Clear previous content
            bodyEl.innerHTML = '';

            const imageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp'];
            const isImage = imageExts.includes(extension);
            const isPdf = extension === 'pdf';

            if (isImage) {
                iconEl.className = 'ph-bold ph-image';
                iconBox.style.background = '#dcfce7';
                iconBox.style.color = '#16a34a';

                const imgWrapper = document.createElement('div');
                imgWrapper.style.maxWidth = '100%';
                imgWrapper.style.maxHeight = '72vh';
                imgWrapper.style.display = 'flex';
                imgWrapper.style.justifyContent = 'center';
                imgWrapper.style.alignItems = 'center';

                const img = document.createElement('img');
                img.src = fileUrl;
                img.alt = fileName;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '70vh';
                img.style.objectFit = 'contain';
                img.style.borderRadius = '10px';
                img.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
                img.style.background = '#ffffff';

                imgWrapper.appendChild(img);
                bodyEl.appendChild(imgWrapper);
            } else if (isPdf) {
                iconEl.className = 'ph-bold ph-file-pdf';
                iconBox.style.background = '#fee2e2';
                iconBox.style.color = '#dc2626';

                const iframe = document.createElement('iframe');
                iframe.src = fileUrl + '#toolbar=1&navpanes=0';
                iframe.style.width = '100%';
                iframe.style.height = '72vh';
                iframe.style.border = 'none';
                iframe.style.borderRadius = '10px';
                iframe.style.background = '#ffffff';
                iframe.style.boxShadow = '0 4px 12px rgba(0,0,0,0.05)';

                bodyEl.appendChild(iframe);
            } else {
                // Office doc / zip / other format
                iconEl.className = 'ph-bold ph-file-text';
                iconBox.style.background = '#fef3c7';
                iconBox.style.color = '#d97706';

                const docBox = document.createElement('div');
                docBox.style.background = '#ffffff';
                docBox.style.padding = '36px 30px';
                docBox.style.borderRadius = '14px';
                docBox.style.maxWidth = '520px';
                docBox.style.width = '100%';
                docBox.style.textAlign = 'center';
                docBox.style.boxShadow = '0 10px 25px rgba(0,0,0,0.06)';
                docBox.style.border = '1px solid #e2e8f0';

                docBox.innerHTML = `
                    <div style="width: 68px; height: 68px; border-radius: 16px; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 34px; margin-bottom: 16px;">
                        <i class="ph-bold ph-file-arrow-down"></i>
                    </div>
                    <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">${fileName}</h4>
                    <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin: 0 0 20px 0;">
                        Format berkas <strong>.${extension.toUpperCase()}</strong> tidak didukung untuk pratinjau langsung di peramban. Silakan unduh atau buka melalui aplikasi terkait.
                    </p>
                    <div style="display: flex; justify-content: center; gap: 10px;">
                        <a href="${fileUrl}" target="_blank" class="btn-action-outline" style="padding: 9px 16px; font-size: 13px;">
                            <i class="ph-bold ph-arrow-square-out"></i> Buka Tab Baru
                        </a>
                        <a href="${fileUrl}" download class="btn-action-primary" style="padding: 9px 20px; font-size: 13px; background: #0284c7;">
                            <i class="ph-bold ph-download-simple"></i> Unduh Berkas
                        </a>
                    </div>
                `;

                bodyEl.appendChild(docBox);
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closePreviewBukti = function() {
            const modal = document.getElementById('modalPreviewBuktiUniversal');
            const bodyEl = document.getElementById('previewBuktiBody');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
                if (bodyEl) bodyEl.innerHTML = '';
            }
        };

        window.closePreviewBuktiOnBackdrop = function(e) {
            if (e.target.id === 'modalPreviewBuktiUniversal') {
                window.closePreviewBukti();
            }
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('modalPreviewBuktiUniversal');
                if (modal && modal.style.display === 'flex') {
                    window.closePreviewBukti();
                }
            }
        });
    </script>

    <!-- Universal File Preview Modal (Modal Pratinjau Berkas Bukti Dukung) -->
    <div id="modalPreviewBuktiUniversal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.72); backdrop-filter: blur(5px); z-index: 1099; align-items: center; justify-content: center; padding: 20px;" onclick="closePreviewBuktiOnBackdrop(event)">
        <div class="tanjidor-preview-card">
            <!-- Header -->
            <div class="tanjidor-preview-header">
                <div style="display: flex; align-items: center; gap: 12px; overflow: hidden;">
                    <div id="previewBuktiIconBox" style="width: 38px; height: 38px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                        <i class="ph-bold ph-file-text" id="previewBuktiIcon"></i>
                    </div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                            Pratinjau Bukti Dukung
                        </div>
                        <div id="previewBuktiTitle" style="font-size: 14.5px; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 550px;">
                            Nama Berkas
                        </div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <a id="previewBuktiExternalBtn" href="#" target="_blank" class="btn-action-outline" style="padding: 6px 12px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; color: #334155; border-color: #cbd5e1;" title="Buka di Tab Baru">
                        <i class="ph-bold ph-arrow-square-out"></i>
                        <span>Tab Baru</span>
                    </a>
                    <a id="previewBuktiDownloadBtn" href="#" download class="btn-action-primary" style="padding: 6px 14px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; background: #0284c7;" title="Unduh Berkas">
                        <i class="ph-bold ph-download-simple"></i>
                        <span>Unduh</span>
                    </a>
                    <button type="button" onclick="closePreviewBukti()" style="background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; padding: 2px 8px; border-radius: 6px; line-height: 1;" title="Tutup">&times;</button>
                </div>
            </div>

            <!-- Body / Content -->
            <div id="previewBuktiBody" class="tanjidor-preview-body">
                <!-- Dynamically populated -->
            </div>

            <!-- Footer -->
            <div class="tanjidor-preview-footer">
                <div style="font-size: 12px; color: #64748b;">
                    Format Berkas: <span id="previewBuktiExtBadge" style="font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 2px 7px; border-radius: 5px; text-transform: uppercase;"></span>
                </div>
                <button type="button" onclick="closePreviewBukti()" class="btn-action-outline" style="padding: 6px 16px; font-size: 12.5px;">Tutup</button>
            </div>
        </div>
    </div>

    @yield('scripts')
    @stack('scripts')

    <script>
        // Smooth Page Transition saat pindah menu di Sidebar & Navbar
        (function() {
            const bar = document.getElementById('tanjidorTopProgress');

            window.addEventListener('pageshow', function() {
                document.body.classList.remove('is-page-leaving');
                if (bar) bar.classList.remove('is-loading');
            });

            document.addEventListener('click', function(e) {
                if (e.defaultPrevented) return;
                const link = e.target.closest('a[href]');
                if (!link) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
                if (link.target === '_blank' || link.hasAttribute('download')) return;

                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                if (link.origin !== window.location.origin) return;
                if (href.includes('export-excel') || href.includes('export-csv')) return;
                if (link.href === window.location.href) return;

                e.preventDefault();
                if (typeof closeMobileSidebar === 'function') closeMobileSidebar();
                document.body.classList.add('is-page-leaving');
                if (bar) bar.classList.add('is-loading');

                setTimeout(function() {
                    window.location.href = link.href;
                }, 150);
            });
        })();
    </script>
</body>
</html>
