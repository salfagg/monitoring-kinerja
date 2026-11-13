<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | SIMON-K BGTK</title>
    
    <!-- Instant Dark Theme Early Check to Prevent FOUC -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('simon_theme');
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons CDN -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Custom Modern Stylesheet -->
    <style>
        :root {
            /* Palet SIMON-K Resmi Kemendikdasmen BGTK DKI Jakarta */
            --navy: #0b4a82;
            --navy-deep: #083a68;
            --navy-soft: #0e5a9c;
            --primary: #0a6fb5;
            --primary-hover: #085c98;
            --primary-light: #e8f2fb;
            --primary-glow: rgba(10, 111, 181, 0.22);
            --cyan: #22b8e8;
            --cyan-soft: #e0f6fd;

            --secondary: #586d83;
            --success: #0c8a5d;
            --success-light: #e3f6ee;
            --warning: #b87400;
            --warning-light: #fff3d9;
            --danger: #d03232;
            --danger-light: #fdeaea;
            --purple: #6f45bd;
            --purple-light: #efe8fa;

            --bg-body: #eef3f9;
            --card-bg: #ffffff;
            --bg-sub: #f6f9fd;
            --text-dark: #0f2a44;
            --text-body: #33495f;
            --text-muted: #586d83;
            --border: #e1e8f2;
            --border-strong: #cdd9ea;

            --sidebar-w: 270px;
            --radius-lg: 14px;
            --radius-md: 9px;
            --shadow-sm: 0 1px 3px rgba(11, 74, 130, 0.04);
            --shadow-card: 0 4px 18px rgba(11, 74, 130, 0.06), 0 1px 3px rgba(11, 74, 130, 0.02);
            --transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ==================== DARK THEME DESIGN SYSTEM ==================== */
        [data-theme="dark"] {
            --bg-body: #0b1120;
            --card-bg: #151e32;
            --bg-sub: #0f172a;
            --text-dark: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #94a3b8;
            --border: #223049;
            --border-strong: #334155;
            --primary-light: rgba(10, 111, 181, 0.22);
            --success-light: rgba(12, 138, 93, 0.22);
            --warning-light: rgba(184, 116, 0, 0.22);
            --danger-light: rgba(208, 50, 50, 0.22);
            --purple-light: rgba(111, 69, 189, 0.22);
            --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Global fix: prevent giant SVG pagination icons across all pages */
        nav svg, .pagination svg, .pagination-wrapper svg {
            max-width: 18px !important;
            max-height: 18px !important;
            width: 18px !important;
            height: 18px !important;
            display: inline-block !important;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-w);
            background: #ffffff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40;
            transition: var(--transition);
            box-shadow: 2px 0 14px rgba(11, 74, 130, 0.04);
        }

        .sidebar-brand {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: var(--navy);
            background-image:
                radial-gradient(rgba(255, 255, 255, 0.12) 1.2px, transparent 1.2px),
                linear-gradient(135deg, var(--navy-soft) 0%, var(--navy) 100%);
            background-size: 22px 22px, 100% 100%;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            height: 72px;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .brand-text h2 {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.01em;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 10.5px;
            font-weight: 700;
            color: #bae6fd;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .sidebar-menu {
            padding: 16px 14px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-header {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            padding: 12px 14px 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--text-body);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-md);
            transition: var(--transition);
        }

        .nav-item i {
            font-size: 19px;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .nav-item:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .nav-item:hover i {
            color: var(--primary);
        }

        .nav-item.active {
            background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(11, 74, 130, 0.28);
        }

        .nav-item.active i {
            color: #ffffff;
        }

        .sidebar-user {
            padding: 14px 18px;
            background: var(--bg-sub);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--navy), var(--primary));
            color: white;
            font-weight: 800;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(11, 74, 130, 0.2);
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* 7 Role Badges Resmi BGTK */
        .role-admin { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .role-kabal { background: #fdf4ff; color: #a21caf; border: 1px solid #f0abfc; }
        .role-kasubag { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .role-korjafung { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }
        .role-katim, .role-katim_korjafung { background: #e8f2fb; color: #0a6fb5; border: 1px solid #bde0f7; }
        .role-korlayanan, .role-koordinator_layanan { background: #efe8fa; color: #6f45bd; border: 1px solid #dfcff5; }
        .role-tim_pokja { background: #e3f6ee; color: #0c8a5d; border: 1px solid #b7ecd6; }
        .role-pegawai { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== Top Navbar (SIMON-K Navy Style) ===== */
        .top-navbar {
            height: 72px;
            background-color: var(--navy);
            background-image:
                radial-gradient(rgba(255, 255, 255, 0.12) 1.2px, transparent 1.2px),
                linear-gradient(135deg, var(--navy-soft) 0%, var(--navy) 50%, var(--navy-deep) 100%);
            background-size: 22px 22px, 100% 100%;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 35;
            box-shadow: 0 4px 16px rgba(8, 58, 104, 0.18);
            color: #ffffff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar-brand-badge {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .navbar-brand-title {
            font-size: 13.5px;
            font-weight: 800;
            letter-spacing: 0.03em;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1.2;
        }

        .navbar-live-tag {
            font-size: 9.5px;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 999px;
            background: rgba(34, 184, 232, 0.2);
            border: 1px solid rgba(34, 184, 232, 0.4);
            color: #e0f6fd;
            letter-spacing: 0.05em;
        }

        .navbar-page-sub {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
            margin-top: 2px;
        }

        /* Center Clock & Date Capsule Pill */
        .navbar-clock-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(6, 42, 74, 0.65);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.14);
            padding: 6px 16px;
            border-radius: 999px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .clock-pulse {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #4ade80;
            animation: soft-blink 3s ease-in-out infinite;
            flex-shrink: 0;
        }

        @keyframes soft-blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.45; }
        }

        .clock-time {
            font-family: 'JetBrains Mono', monospace, sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.02em;
        }

        .clock-sep {
            color: rgba(255, 255, 255, 0.35);
            font-size: 12px;
        }

        .clock-date {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.88);
        }

        @media (max-width: 1024px) {
            .navbar-clock-pill {
                display: none;
            }
        }

        /* Right Pill Controls */
        .top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            text-decoration: none;
            transition: var(--transition);
            backdrop-filter: blur(6px);
            user-select: none;
        }

        .nav-pill-btn:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.35);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-tv-pill {
            background: rgba(34, 184, 232, 0.18);
            border-color: rgba(34, 184, 232, 0.4);
            color: #e0f6fd;
        }

        .btn-tv-pill:hover {
            background: rgba(34, 184, 232, 0.28);
            color: #ffffff;
        }

        .btn-logout-pill {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        .btn-logout-pill:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #ffffff;
        }

        /* Content Container */
        .content-area {
            padding: 32px;
            flex: 1;
        }

        /* Flash Message Alert */
        .alert-box {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            font-weight: 500;
            animation: fadeIn 0.3s ease;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .top-navbar {
                padding: 0 18px;
            }
            .content-area {
                padding: 18px;
            }
        }

        /* ==========================================================================
           DARK MODE COMPREHENSIVE RULES: High Contrast, Legible, No Black Font on Dark
           ========================================================================== */
        [data-theme="dark"] body {
            background-color: var(--bg-body);
            color: var(--text-dark);
        }

        /* Layout Elements */
        [data-theme="dark"] .sidebar {
            background: #111827;
            border-color: #1f2937;
        }

        [data-theme="dark"] .sidebar-brand {
            border-color: #1f2937;
        }

        [data-theme="dark"] .sidebar-user {
            background: #0f172a;
            border-color: #1f2937;
        }

        [data-theme="dark"] .top-navbar {
            background: #111827;
            border-color: #1f2937;
        }

        [data-theme="dark"] .brand-text h2,
        [data-theme="dark"] .page-header-title,
        [data-theme="dark"] .user-name {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .nav-item {
            color: #94a3b8;
        }

        [data-theme="dark"] .nav-item i {
            color: #64748b;
        }

        [data-theme="dark"] .nav-item:hover {
            background-color: #1f2937;
            color: #60a5fa;
        }

        [data-theme="dark"] .nav-item:hover i {
            color: #60a5fa;
        }

        [data-theme="dark"] .menu-header {
            color: #64748b;
        }

        [data-theme="dark"] .date-badge {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
        }

        [data-theme="dark"] .theme-toggle-btn {
            background: #1e293b;
            color: #fbbf24;
            border-color: #334155;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        [data-theme="dark"] .theme-toggle-btn:hover {
            background: #27354f;
            border-color: #475569;
            color: #fde047;
        }

        [data-theme="dark"] .theme-toggle-btn i {
            color: #fbbf24;
        }

        [data-theme="dark"] .btn-logout {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.25);
        }

        [data-theme="dark"] .btn-logout:hover {
            background: rgba(239, 68, 68, 0.22);
            color: #fca5a5;
        }

        /* Cards, Containers, and Backgrounds */
        [data-theme="dark"] .form-card,
        [data-theme="dark"] .detail-card,
        [data-theme="dark"] .kpi-card,
        [data-theme="dark"] .tabs-wrapper,
        [data-theme="dark"] .card-box,
        [data-theme="dark"] .preview-card-sticky,
        [data-theme="dark"] .stats-card,
        [data-theme="dark"] .table-card {
            background: #151e32 !important;
            border-color: #223049 !important;
            color: #f8fafc;
        }

        [data-theme="dark"] .form-card-header {
            border-bottom-color: #223049 !important;
        }

        /* Headings & Texts: Strictly Light in Dark Mode (NO BLACK TEXT) */
        [data-theme="dark"] h1, 
        [data-theme="dark"] h2, 
        [data-theme="dark"] h3, 
        [data-theme="dark"] h4, 
        [data-theme="dark"] h5, 
        [data-theme="dark"] h6 {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .text-muted,
        [data-theme="dark"] .form-label-hint,
        [data-theme="dark"] .preview-label,
        [data-theme="dark"] .checklist-title,
        [data-theme="dark"] .dropzone-sub,
        [data-theme="dark"] .pelaksana-sub,
        [data-theme="dark"] .form-card-header p {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .form-label {
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .preview-val {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .preview-item {
            border-bottom-color: #223049 !important;
        }

        /* Form Inputs & Controls */
        [data-theme="dark"] .form-input,
        [data-theme="dark"] .form-select,
        [data-theme="dark"] .form-textarea,
        [data-theme="dark"] .search-input,
        [data-theme="dark"] .filter-select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .form-input:focus,
        [data-theme="dark"] .form-select:focus,
        [data-theme="dark"] .form-textarea:focus,
        [data-theme="dark"] .search-input:focus {
            border-color: #3b82f6 !important;
            background-color: #0f172a !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        }

        [data-theme="dark"] .form-input::placeholder,
        [data-theme="dark"] .form-textarea::placeholder,
        [data-theme="dark"] .search-input::placeholder {
            color: #64748b !important;
        }

        [data-theme="dark"] .input-icon {
            color: #64748b;
        }

        /* Tabs and Filter Bars */
        [data-theme="dark"] .tabs-header,
        [data-theme="dark"] .filter-bar {
            background: #0f172a !important;
            border-color: #223049 !important;
        }

        [data-theme="dark"] .tab-btn {
            color: #94a3b8;
        }

        [data-theme="dark"] .tab-btn:hover {
            color: #60a5fa;
            background: rgba(37, 99, 235, 0.08);
        }

        [data-theme="dark"] .tab-btn.active {
            background: #151e32 !important;
            color: #60a5fa !important;
            border-bottom-color: #60a5fa !important;
        }

        /* Tables */
        [data-theme="dark"] .data-table th {
            background: #0f172a !important;
            color: #94a3b8 !important;
            border-bottom-color: #223049 !important;
        }

        [data-theme="dark"] .data-table td {
            border-bottom-color: #1e293b !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .data-table tr:hover td {
            background: #192338 !important;
        }

        /* Outlines & Action Buttons */
        [data-theme="dark"] .btn-action-outline,
        [data-theme="dark"] .btn-cancel-main {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #cbd5e1 !important;
        }

        [data-theme="dark"] .btn-action-outline:hover,
        [data-theme="dark"] .btn-cancel-main:hover {
            background: #27354f !important;
            border-color: #475569 !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] .code-badge {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #93c5fd !important;
        }

        /* Modals */
        [data-theme="dark"] .modal-card {
            background: #151e32 !important;
            border: 1px solid #334155 !important;
            color: #f8fafc;
        }

        [data-theme="dark"] .modal-header {
            background: #0f172a !important;
            border-bottom-color: #223049 !important;
        }

        [data-theme="dark"] .modal-title {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .modal-footer {
            background: #0f172a !important;
            border-top-color: #223049 !important;
        }

        /* Dropzone Upload */
        [data-theme="dark"] .dropzone-container {
            background: #0f172a !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .dropzone-container:hover {
            border-color: #3b82f6 !important;
            background: rgba(37, 99, 235, 0.08) !important;
        }

        [data-theme="dark"] .dropzone-text {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .dropzone-file-info {
            background: #1e293b !important;
            border-color: #3b82f6 !important;
        }

        /* Multi-Assignee Pelaksana Selector */
        [data-theme="dark"] .pelaksana-selection-container {
            background: #0f172a !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .pelaksana-card {
            background: #111827 !important;
            border-color: #1f2937 !important;
            color: #f8fafc;
        }

        [data-theme="dark"] .pelaksana-card:hover {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .pelaksana-card.selected {
            background: rgba(16, 185, 129, 0.15) !important;
            border-color: #059669 !important;
        }

        [data-theme="dark"] .pelaksana-card.is-pic {
            background: rgba(37, 99, 235, 0.2) !important;
            border-color: #3b82f6 !important;
        }

        [data-theme="dark"] .pelaksana-name {
            color: #f8fafc !important;
        }

        [data-theme="dark"] #selectedPelaksanaChips {
            background: #0f172a !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .pelaksana-chip {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .pelaksana-chip.is-pic-chip {
            background: rgba(37, 99, 235, 0.25) !important;
            border-color: #3b82f6 !important;
            color: #93c5fd !important;
        }

        /* Mobile Floating Bar */
        [data-theme="dark"] .mobile-floating-bar {
            background: rgba(17, 24, 39, 0.95) !important;
            border-top-color: #1e293b !important;
        }

        /* General Inline Styles Overrides for Dark Mode */
        [data-theme="dark"] [style*="background: #ffffff"],
        [data-theme="dark"] [style*="background:#ffffff"],
        [data-theme="dark"] [style*="background: white"],
        [data-theme="dark"] [style*="background:white"] {
            background-color: #151e32 !important;
        }

        [data-theme="dark"] [style*="background: #f8fafc"],
        [data-theme="dark"] [style*="background:#f8fafc"],
        [data-theme="dark"] [style*="background: #f1f5f9"],
        [data-theme="dark"] [style*="background:#f1f5f9"] {
            background-color: #0f172a !important;
        }

        [data-theme="dark"] [style*="color: #0f172a"],
        [data-theme="dark"] [style*="color:#0f172a"],
        [data-theme="dark"] [style*="color: #1e293b"],
        [data-theme="dark"] [style*="color:#1e293b"],
        [data-theme="dark"] [style*="color: #334155"],
        [data-theme="dark"] [style*="color:#334155"],
        [data-theme="dark"] [style*="color: #475569"],
        [data-theme="dark"] [style*="color:#475569"],
        [data-theme="dark"] [style*="color: black"],
        [data-theme="dark"] [style*="color: #000000"],
        [data-theme="dark"] [style*="color:#000"],
        [data-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }

        [data-theme="dark"] [style*="color: #64748b"],
        [data-theme="dark"] [style*="color:#64748b"],
        [data-theme="dark"] [style*="color: #94a3b8"],
        [data-theme="dark"] [style*="color:#94a3b8"] {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] [style*="border: 1px solid #e2e8f0"],
        [data-theme="dark"] [style*="border-color: #e2e8f0"],
        [data-theme="dark"] [style*="border: 1px solid #cbd5e1"],
        [data-theme="dark"] [style*="border-color: #cbd5e1"] {
            border-color: #223049 !important;
        }

        /* Ensure pagination, dropdowns, and alert boxes contrast well in dark mode */
        [data-theme="dark"] .pagination .page-link {
            background-color: #151e32;
            border-color: #223049;
            color: #cbd5e1;
        }
        [data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }
        [data-theme="dark"] .alert-info {
            background-color: rgba(30, 58, 138, 0.3) !important;
            border-color: #1e40af !important;
            color: #bfdbfe !important;
        }
        [data-theme="dark"] .alert-success {
            background-color: rgba(6, 78, 59, 0.3) !important;
            border-color: #065f46 !important;
            color: #a7f3d0 !important;
        }
        [data-theme="dark"] .alert-warning {
            background-color: rgba(120, 53, 15, 0.3) !important;
            border-color: #92400e !important;
            color: #fde68a !important;
        }
        [data-theme="dark"] .alert-danger {
            background-color: rgba(127, 29, 29, 0.3) !important;
            border-color: #991b1b !important;
            color: #fecaca !important;
        }
    </style>
    @yield('styles')
</head>
<body>

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
    </script>

    @yield('scripts')
</body>
</html>
