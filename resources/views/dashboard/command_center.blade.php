<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemantauan Kinerja Terpadu Balai | BGTK DKI Jakarta</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>

    <style>
        :root {
            /* Palet resmi SIMON-K Navy */
            --navy: #0b4a82;
            --navy-deep: #083a68;
            --navy-soft: #0e5a9c;
            --primary: #0a6fb5;
            --primary-dark: #085c98;
            --primary-soft: #e8f2fb;
            --primary-glow: rgba(10, 111, 181, 0.22);
            --cyan: #22b8e8;
            --cyan-soft: #e0f6fd;

            --bg-base: #f0f4f9;
            --bg-card: #ffffff;
            --bg-sub: #f8fafc;
            --border: #e2e8f0;
            --border-strong: #cbd5e1;

            --text-main: #0f2a44;
            --text-body: #33495f;
            --text-muted: #64748b;

            --success: #0c8a5d;
            --success-soft: #e3f6ee;
            --warning: #b87400;
            --warning-soft: #fff3d9;
            --danger: #d03232;
            --danger-soft: #fdeaea;
            --purple: #6f45bd;
            --purple-soft: #efe8fa;

            --shadow-sm: 0 1px 3px rgba(11, 74, 130, 0.05);
            --shadow-md: 0 4px 16px rgba(11, 74, 130, 0.08);
            --shadow-lg: 0 10px 30px rgba(11, 74, 130, 0.12);

            --body-height: calc(100vh - 295px);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-base);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ===== Header Navigasi TV / Command Center ===== */
        .top-command-bar {
            background-color: var(--navy);
            background-image:
                radial-gradient(rgba(255, 255, 255, 0.12) 1.2px, transparent 1.2px),
                linear-gradient(135deg, var(--navy-soft) 0%, var(--navy) 50%, var(--navy-deep) 100%);
            background-size: 22px 22px, 100% 100%;
            padding: 12px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 16px rgba(8, 58, 104, 0.22);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            background: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .brand-title {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.01em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .live-tag {
            font-size: 10px;
            font-weight: 800;
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            padding: 2px 9px;
            border-radius: 999px;
            letter-spacing: 0.08em;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .live-tag::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px #22c55e;
            animation: pulse-dot 1.6s infinite ease-in-out;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.4; }
        }

        .brand-sub {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            margin-top: 1px;
        }

        .live-clock-section {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 7px 18px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(8px);
        }

        .clock-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #38bdf8;
            box-shadow: 0 0 10px #38bdf8;
        }

        .clock-time {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 13.5px;
            color: #ffffff;
            letter-spacing: 0.02em;
        }

        .clock-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
            border-left: 1px solid rgba(255, 255, 255, 0.25);
            padding-left: 12px;
        }

        .command-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-control {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-control:hover {
            background: rgba(255, 255, 255, 0.22);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
        }

        .btn-control.active {
            background: #ffffff;
            color: var(--navy);
            border-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .spin-active {
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* ===== Main Container ===== */
        .command-container {
            padding: 18px 24px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            flex: 1;
        }

        /* ===== Ringkasan KPI Baris Atas ===== */
        .giant-kpi-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 14px;
        }

        @media (max-width: 1400px) {
            .giant-kpi-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .giant-kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .kpi-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent);
            border-radius: 14px;
            padding: 12px 16px;
            display: grid;
            grid-template-columns: 1fr auto;
            grid-template-areas:
                "title icon"
                "number icon"
                "sub sub";
            row-gap: 3px;
            align-items: center;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi-box:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .kpi-blue { --accent: #0a6fb5; --accent-soft: #e8f2fb; }
        .kpi-cyan { --accent: #0e9bc9; --accent-soft: #e0f6fd; }
        .kpi-purple { --accent: #6f45bd; --accent-soft: #efe8fa; }
        .kpi-green { --accent: #0c8a5d; --accent-soft: #e3f6ee; }
        .kpi-amber { --accent: #c47c00; --accent-soft: #fff3d9; }
        .kpi-red { --accent: #d03232; --accent-soft: #fdeaea; }

        .kpi-title {
            grid-area: title;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .kpi-icon-wrap {
            grid-area: icon;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: var(--accent-soft);
            color: var(--accent);
        }

        .kpi-number {
            grid-area: number;
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.15;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }

        .kpi-subtext {
            grid-area: sub;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 2px;
        }

        .kpi-mini-track {
            flex: 1;
            height: 5px;
            background: var(--accent-soft);
            border-radius: 999px;
            overflow: hidden;
        }

        .kpi-mini-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 999px;
        }

        /* ===== Layout Utama Dua Kolom ===== */
        .command-body-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 16px;
            height: var(--body-height);
            min-height: 580px;
        }

        .left-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 0;
        }

        .left-col > .panel-card {
            flex: 1 1 0;
        }

        @media (max-width: 1200px) {
            .command-body-grid {
                grid-template-columns: 1fr;
                height: auto;
                min-height: 0;
            }
            .left-col > .panel-card {
                flex: none;
            }
            .scroll-area {
                max-height: 380px;
            }
            .table-responsive {
                max-height: 560px;
            }
        }

        /* ===== Panel Box ===== */
        .panel-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-top: 3.5px solid var(--primary);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .panel-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            background: #ffffff;
        }

        .panel-title {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-title i {
            font-size: 18px;
            color: var(--primary);
        }

        .panel-count {
            font-size: 11px;
            font-weight: 700;
            color: var(--primary-dark);
            background: var(--primary-soft);
            padding: 2px 9px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .scroll-area {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }

        /* ===== Tim Kerja List (Clean & Compact) ===== */
        .pokja-list {
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            background: var(--bg-sub);
        }

        .pokja-item {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 9px 12px;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .pokja-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-sm);
            transform: translateX(2px);
        }

        .pokja-item.active {
            border-color: var(--primary);
            background: var(--primary-soft);
            box-shadow: 0 0 0 2px var(--primary-glow);
        }

        .pokja-item-all {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pokja-item-all .lbl {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--text-main);
        }

        .pokja-item-all .cnt {
            font-size: 11.5px;
            color: var(--primary-dark);
            font-weight: 700;
        }

        .pokja-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .pokja-title-box {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow: hidden;
            flex: 1;
        }

        .pokja-code-tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            font-weight: 700;
            background: var(--navy);
            color: #ffffff;
            padding: 2px 6px;
            border-radius: 5px;
            flex-shrink: 0;
        }

        .pokja-name-clean {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pokja-percent {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--primary-dark);
            flex-shrink: 0;
        }

        .pokja-meter-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pokja-track {
            flex: 1;
            height: 6px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        .pokja-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary), var(--cyan));
        }

        .pokja-done-badge {
            font-size: 10.5px;
            color: var(--text-muted);
            font-weight: 600;
            white-space: nowrap;
        }

        /* ===== Beban Kerja Personil ===== */
        .assignee-tabs {
            display: flex;
            gap: 4px;
            padding: 8px 12px;
            background: var(--bg-sub);
            border-bottom: 1px solid var(--border);
            overflow-x: auto;
            flex-shrink: 0;
        }

        .btn-assignee-tab {
            background: #ffffff;
            color: var(--text-muted);
            border: 1px solid var(--border);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-assignee-tab:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-assignee-tab.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .assignee-search-wrap {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .assignee-search-input {
            width: 100%;
            background: var(--primary-soft);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 12px;
            font-family: inherit;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
        }

        .assignee-search-input:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 2px var(--primary-glow);
        }

        .assignee-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .assignee-item:hover {
            background: var(--primary-soft);
        }

        .assignee-item.selected-assignee {
            background: var(--primary-soft);
            border-left: 3px solid var(--primary);
        }

        .assignee-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e2e8f0;
            color: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .assignee-avatar.is-katim {
            background: #dbeafe;
            color: var(--primary-dark);
        }

        .assignee-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .assignee-sub {
            font-size: 10.5px;
            color: var(--text-muted);
            display: flex;
            gap: 6px;
            align-items: center;
            margin-top: 1px;
        }

        .role-pill {
            font-size: 9.5px;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 700;
        }

        .role-pill-pegawai { background: #f1f5f9; color: #475569; }
        .role-pill-katim { background: #e0f2fe; color: #0369a1; }
        .role-pill-kor { background: #f3e8ff; color: #7e22ce; }

        .assignee-count-badge {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .has-workload { background: #e0f2fe; color: #0369a1; }
        .zero-workload { background: #f1f5f9; color: #94a3b8; }

        .assignee-footer {
            padding: 8px 12px;
            background: var(--bg-sub);
            border-top: 1px solid var(--border);
            font-size: 10.5px;
            color: var(--text-muted);
            text-align: center;
            flex-shrink: 0;
        }

        /* ===== Aliran Tugas & Tabel Rapi (Clean & Anti-Bala) ===== */
        .stream-controls {
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            flex-shrink: 0;
            background: #ffffff;
        }

        .stream-tabs {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: var(--bg-sub);
            color: var(--text-body);
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-btn .num {
            font-variant-numeric: tabular-nums;
            opacity: 0.8;
            font-weight: 600;
        }

        .tab-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        .tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .tab-btn.tab-danger {
            color: var(--danger);
            border-color: #fca5a5;
            background: var(--danger-soft);
        }

        .tab-btn.tab-danger.active {
            background: var(--danger);
            color: #ffffff;
            border-color: var(--danger);
        }

        .stream-search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-soft);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 5px 14px;
            transition: all 0.2s ease;
        }

        .stream-search:focus-within {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 2px var(--primary-glow);
        }

        .stream-search i {
            color: var(--text-muted);
            font-size: 14px;
        }

        .stream-search input {
            background: transparent;
            border: none;
            color: var(--text-main);
            font-size: 12px;
            outline: none;
            width: 220px;
            font-family: inherit;
        }

        .btn-reset {
            background: var(--danger-soft);
            color: var(--danger);
            border: 1px solid #fca5a5;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-reset:hover {
            background: var(--danger);
            color: #ffffff;
        }

        /* ===== Tabel Tugas Bersih ===== */
        .table-responsive {
            flex: 1 1 auto;
            min-height: 0;
            overflow: auto;
        }

        .command-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .command-table th {
            background: #f8fafc;
            text-align: left;
            padding: 10px 14px;
            font-weight: 800;
            color: var(--navy);
            border-bottom: 2px solid var(--border-strong);
            font-size: 11.5px;
            letter-spacing: 0.02em;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        .command-table td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text-body);
            vertical-align: middle;
        }

        .command-table tbody tr {
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .command-table tbody tr:hover td {
            background: #f1f8fe;
        }

        .command-table tbody tr.row-late td:first-child {
            box-shadow: inset 3.5px 0 0 var(--danger);
        }

        .task-code {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 11px;
            color: var(--primary-dark);
            background: var(--primary-soft);
            padding: 2.5px 7px;
            border-radius: 5px;
            white-space: nowrap;
            display: inline-block;
        }

        .task-title-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: 320px;
        }

        .task-title-text {
            font-weight: 700;
            color: var(--text-main);
            font-size: 13px;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .st-badge-mini {
            font-size: 9.5px;
            font-weight: 700;
            background: var(--cyan-soft);
            color: #0284c7;
            padding: 1px 6px;
            border-radius: 4px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            flex-shrink: 0;
        }

        .task-pokja-badge {
            font-size: 10.5px;
            font-weight: 700;
            background: var(--navy);
            color: #ffffff;
            padding: 2.5px 9px;
            border-radius: 999px;
            white-space: nowrap;
            display: inline-block;
        }

        .pic-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: 200px;
        }

        .pic-avatar-mini {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #e2e8f0;
            color: var(--navy);
            font-weight: 800;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pic-name-clean {
            font-weight: 700;
            color: var(--text-main);
            font-size: 12.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pic-team-badge {
            font-size: 9.5px;
            font-weight: 700;
            background: var(--success-soft);
            color: var(--success);
            padding: 1px 5px;
            border-radius: 4px;
            white-space: nowrap;
        }

        .due-date-clean {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-body);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .due-date-clean.is-late {
            color: var(--danger);
            font-weight: 700;
        }

        .late-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--danger);
            display: inline-block;
        }

        .progress-meter-clean {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 100px;
        }

        .progress-track-clean {
            flex: 1;
            height: 7px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill-clean {
            height: 100%;
            border-radius: 999px;
        }

        .progress-num-clean {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-main);
            min-width: 35px;
            text-align: right;
        }

        .status-badge-cmd {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .st-berjalan { background: var(--primary-soft); color: var(--primary-dark); }
        .st-verifikasi { background: var(--purple-soft); color: var(--purple); }
        .st-selesai { background: var(--success-soft); color: var(--success); }
        .st-terlambat { background: var(--danger-soft); color: var(--danger); }
        .st-belum { background: #f1f5f9; color: #64748b; }

        .btn-detail-cmd {
            background: #ffffff;
            color: var(--primary-dark);
            border: 1px solid var(--border-strong);
            padding: 5px 11px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .btn-detail-cmd:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        /* ===== Modal Detail Tugas (Interactive & Rich) ===== */
        .cmd-modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 42, 68, 0.65);
            backdrop-filter: blur(5px);
            z-index: 1000;
            padding: 20px;
            overflow-y: auto;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease-out;
        }

        .cmd-modal-backdrop.show {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .cmd-modal-dialog {
            background: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 760px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { transform: translateY(20px) scale(0.98); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        .cmd-modal-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, #0b4a82 0%, #083a68 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .cmd-modal-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .cmd-modal-title {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.4;
            color: #ffffff;
        }

        .cmd-modal-parent {
            font-size: 12px;
            color: #bae6fd;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cmd-modal-close {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .cmd-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .cmd-modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
            background: #f8fafc;
        }

        /* 4 KPI Baris Ringkasan di Modal */
        .cmd-modal-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        @media (max-width: 640px) {
            .cmd-modal-kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .cmd-kpi-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: var(--shadow-sm);
        }

        .cmd-kpi-lbl {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 4px;
        }

        .cmd-kpi-val {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            font-variant-numeric: tabular-nums;
        }

        .cmd-kpi-sub {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 2px;
        }

        .cmd-kpi-track {
            height: 5px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 6px;
        }

        .cmd-kpi-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 999px;
        }

        /* Box Informasi di Modal */
        .cmd-modal-box {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            box-shadow: var(--shadow-sm);
        }

        .cmd-box-title {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
        }

        .cmd-box-desc {
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-body);
        }

        .cmd-box-expected {
            margin-top: 10px;
            padding: 9px 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            font-size: 12.5px;
            color: #166534;
        }

        /* Penanggung Jawab Card */
        .cmd-pic-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            position: relative;
        }

        .cmd-pic-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--navy);
            color: #ffffff;
            font-size: 16px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(11, 74, 130, 0.2);
        }

        .cmd-pic-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
        }

        .cmd-pic-nip {
            font-size: 12px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
            margin-top: 2px;
        }

        .cmd-pic-jabatan {
            font-size: 12px;
            color: var(--primary-dark);
            font-weight: 600;
            margin-top: 2px;
        }

        .cmd-pic-badge {
            position: absolute;
            top: 12px;
            right: 14px;
            font-size: 10px;
            font-weight: 700;
            background: #dbeafe;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 999px;
        }

        /* Tim Pelaksana */
        .cmd-team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .cmd-team-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .cmd-team-chip {
            font-size: 11px;
            font-weight: 700;
            background: #e2e8f0;
            color: #475569;
            padding: 1px 7px;
            border-radius: 999px;
        }

        .cmd-team-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        @media (max-width: 600px) {
            .cmd-team-list {
                grid-template-columns: 1fr;
            }
        }

        .cmd-member-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .cmd-member-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .cmd-member-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cmd-member-jab {
            font-size: 10.5px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Riwayat Update Terakhir */
        .cmd-update-card {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 10px;
            padding: 12px;
        }

        .cmd-update-date {
            font-size: 11.5px;
            font-weight: 700;
            color: #b45309;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .cmd-update-note {
            font-size: 12.5px;
            color: #78350f;
            line-height: 1.5;
        }

        /* Footer Modal */
        .cmd-modal-footer {
            padding: 14px 24px;
            background: #ffffff;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cmd-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .btn-cmd-secondary:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-cmd-primary {
            background: var(--navy);
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(11, 74, 130, 0.25);
        }

        .btn-cmd-primary:hover {
            background: var(--primary);
            transform: translateY(-1px);
        }

        /* ===== Fullscreen Mode (TV) ===== */
        body.fullscreen-mode {
            --body-height: calc(100vh - 290px);
        }

        body.fullscreen-mode .top-command-bar {
            padding: 14px 36px;
        }

        body.fullscreen-mode .command-container {
            padding: 20px 36px 30px;
            max-width: none;
        }
    </style>
</head>

<body>

    <!-- 1. Header Pemantauan Balai -->
    <header class="top-command-bar">
        <div class="brand-section">
            <div class="brand-badge">
                <i class="ph-bold ph-shield-check"></i>
            </div>
            <div>
                <div class="brand-title">
                    BGTK PROVINSI DKI JAKARTA
                    <span class="live-tag">LIVE</span>
                </div>
                <div class="brand-sub">Pemantauan Kinerja Terpadu Balai</div>
            </div>
        </div>

        <div class="live-clock-section">
            <div class="clock-pulse" title="Sistem aktif dan terhubung"></div>
            <div class="clock-time" id="liveTimeDisplay">00:00:00 WIB</div>
            <div class="clock-date" id="liveDateDisplay">-</div>
        </div>

        <div class="command-controls">
            <button type="button" class="btn-control active" id="btnAutoRefresh" onclick="toggleAutoRefresh()"
                title="Perbarui data otomatis (tombol R)">
                <i class="ph-bold ph-arrows-clockwise"></i>
                <span id="refreshLabel">Segarkan otomatis: 30d</span>
            </button>

            <button type="button" class="btn-control" id="btnAutoScroll" onclick="toggleAutoScroll()"
                title="Gulir daftar tugas otomatis untuk layar TV (tombol S)">
                <i class="ph-bold ph-arrows-down-up"></i>
                <span id="scrollLabel">Gulir otomatis: Mati</span>
            </button>

            <button type="button" class="btn-control" onclick="toggleFullscreen()"
                title="Tampilan layar penuh (tombol F)">
                <i class="ph-bold ph-corners-out"></i>
                <span id="fsLabel">Layar Penuh</span>
            </button>

            <a href="{{ route('dashboard') }}" class="btn-control" title="Kembali ke dashboard utama">
                <i class="ph-bold ph-arrow-square-out"></i>
                <span>Portal</span>
            </a>
        </div>
    </header>

    <!-- 2. Isi Halaman Command Center -->
    <main class="command-container">

        <!-- 6 KPI Utama Balai -->
        <div class="giant-kpi-grid">
            <div class="kpi-box kpi-blue">
                <span class="kpi-title">Total Tugas</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-tree-structure"></i></div>
                <div class="kpi-number">{{ number_format($totalPekerjaan) }}</div>
                <div class="kpi-subtext">
                    <i class="ph-bold ph-check-circle"></i> Seluruh penugasan balai
                </div>
            </div>

            <div class="kpi-box kpi-cyan">
                <span class="kpi-title">Sedang Dikerjakan</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-rocket-launch"></i></div>
                <div class="kpi-number">{{ number_format($pekerjaanBerjalan) }}</div>
                <div class="kpi-subtext">
                    <i class="ph-bold ph-spinner"></i> Berjalan aktif
                </div>
            </div>

            <div class="kpi-box kpi-purple">
                <span class="kpi-title">Menunggu Verifikasi</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-seal-check"></i></div>
                <div class="kpi-number">{{ number_format($menungguVerifikasi) }}</div>
                <div class="kpi-subtext">
                    <i class="ph-bold ph-clock"></i> Menunggu tinjauan Katim
                </div>
            </div>

            <div class="kpi-box kpi-green">
                <span class="kpi-title">Selesai</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-check-fat"></i></div>
                <div class="kpi-number">{{ number_format($selesaiTerverifikasi) }}</div>
                <div class="kpi-subtext">
                    <i class="ph-bold ph-check"></i> Hasil terverifikasi
                </div>
            </div>

            <div class="kpi-box kpi-amber">
                <span class="kpi-title">Rata-Rata Capaian</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-chart-line-up"></i></div>
                <div class="kpi-number">{{ number_format($avgProgres, 1) }}%</div>
                <div class="kpi-subtext">
                    <div class="kpi-mini-track">
                        <div class="kpi-mini-fill" style="width: {{ min(100, max(0, $avgProgres)) }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="kpi-box {{ $lewatTenggat > 0 ? 'kpi-red is-alert' : 'kpi-green' }}">
                <span class="kpi-title">Terlambat</span>
                <div class="kpi-icon-wrap">
                    <i class="ph-duotone {{ $lewatTenggat > 0 ? 'ph-warning-octagon' : 'ph-check-circle' }}"></i>
                </div>
                <div class="kpi-number">{{ number_format($lewatTenggat) }}</div>
                <div class="kpi-subtext">
                    @if($lewatTenggat > 0)
                        <i class="ph-bold ph-warning"></i> Perlu perhatian tindak lanjut
                    @else
                        <i class="ph-bold ph-check"></i> Tidak ada keterlambatan
                    @endif
                </div>
            </div>
        </div>

        <!-- Body Grid: Kolom Kiri (Pokja & Staf) | Kolom Kanan (Tabel Rapi) -->
        <div class="command-body-grid">

            <!-- Kolom Kiri -->
            <div class="left-col">

                <!-- Capaian Tim Kerja (Compact & Clean) -->
                <div class="panel-card">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="ph-duotone ph-buildings"></i>
                            Capaian Tim Kerja
                        </div>
                        <span class="panel-count">{{ $pokjas->count() }} unit</span>
                    </div>

                    <div class="scroll-area" id="pokjaScrollContainer">
                        <div class="pokja-list">
                            <a href="{{ route('monitoring.balai') }}"
                                class="pokja-item pokja-item-all {{ !request('pokja_id') ? 'active' : '' }}">
                                <span class="lbl">Semua Unit & Tim</span>
                                <span class="cnt">{{ $totalPekerjaan }} tugas</span>
                            </a>

                            @foreach($pokjas as $p)
                                <a href="{{ route('monitoring.balai', array_merge(request()->except(['pokja_id']), ['pokja_id' => $p->id])) }}"
                                    class="pokja-item {{ request('pokja_id') == $p->id ? 'active' : '' }}"
                                    title="{{ $p->nama_pokja }} (Katim: {{ $p->katim_name }})">
                                    <div class="pokja-item-header">
                                        <div class="pokja-title-box">
                                            <span class="pokja-code-tag">{{ $p->kode_pokja }}</span>
                                            <span class="pokja-name-clean">{{ $p->nama_pokja }}</span>
                                        </div>
                                        <span class="pokja-percent">{{ $p->avg_progres }}%</span>
                                    </div>
                                    <div class="pokja-meter-wrap">
                                        <div class="pokja-track">
                                            <div class="pokja-fill" style="width: {{ $p->avg_progres }}%;"></div>
                                        </div>
                                        <span class="pokja-done-badge">{{ $p->tugas_selesai }}/{{ $p->total_tugas }} selesai</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Beban Kerja Personil -->
                <div class="panel-card">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="ph-duotone ph-users-three"></i>
                            Beban Kerja Personil
                        </div>
                        <span class="panel-count">{{ $topAssignees->count() }} staf</span>
                    </div>

                    <div class="assignee-tabs">
                        <button type="button" class="btn-assignee-tab active" onclick="switchAssigneeTab('all', this)">
                            Semua ({{ $topAssignees->count() }})
                        </button>
                        <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('pegawai', this)">
                            Pelaksana ({{ $topAssignees->filter(fn($u) => in_array($u->role, ['pegawai', 'tim_pokja']))->count() }})
                        </button>
                        <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('katim', this)">
                            Katim & Koor ({{ $topAssignees->filter(fn($u) => in_array($u->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']))->count() }})
                        </button>
                    </div>

                    <div class="assignee-search-wrap">
                        <input type="text" id="assigneeSearchInput" placeholder="Cari personil..."
                            class="assignee-search-input" oninput="filterAssigneeRows(this.value)">
                    </div>

                    <div class="scroll-area" id="assigneeListContainer">
                        @foreach($topAssignees as $staf)
                            @php
                                $isKatimOrKoor = in_array($staf->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']);
                                $roleCategory = $isKatimOrKoor ? 'katim' : 'pegawai';
                                $roleBadgeClass = $staf->role === 'pegawai' || $staf->role === 'tim_pokja' ? 'role-pill-pegawai' : ($staf->role === 'katim' ? 'role-pill-katim' : 'role-pill-kor');
                            @endphp
                            <div class="assignee-item" data-role-category="{{ $roleCategory }}"
                                data-name="{{ strtolower($staf->name) }}"
                                onclick="filterTableByAssignee('{{ addslashes($staf->name) }}')"
                                title="Klik untuk menampilkan tugas {{ $staf->name }}">
                                <div class="assignee-avatar {{ $isKatimOrKoor ? 'is-katim' : '' }}">
                                    {{ strtoupper(substr($staf->name, 0, 2)) }}
                                </div>
                                <div style="overflow: hidden; flex: 1;">
                                    <div class="assignee-name" title="{{ $staf->name }}">
                                        {{ $staf->name }}
                                    </div>
                                    <div class="assignee-sub">
                                        <span>{{ $staf->pokja?->kode_pokja ?? 'BGTK' }}</span>
                                        <span class="role-pill {{ $roleBadgeClass }}">{{ $staf->role_label }}</span>
                                    </div>
                                </div>
                                <div class="assignee-count-wrap">
                                    <div class="assignee-count-badge {{ ($staf->pic_count + $staf->member_count) > 0 ? 'has-workload' : 'zero-workload' }}">
                                        {{ $staf->pic_count + $staf->member_count }} tugas
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="assignee-footer">
                        <i class="ph-bold ph-cursor-click"></i> Klik nama untuk menyaring daftar tugas
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Tabel Aliran Tugas (Anti-Bala & Poin Utama Saja) -->
            <div class="panel-card">

                <div class="stream-controls">
                    <div class="stream-tabs">
                        <a href="{{ route('monitoring.balai', request()->except(['status'])) }}"
                            class="tab-btn {{ !request('status') ? 'active' : '' }}">
                            Semua <span class="num">{{ $totalPekerjaan }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'berjalan'])) }}"
                            class="tab-btn {{ request('status') === 'berjalan' ? 'active' : '' }}">
                            Dikerjakan <span class="num">{{ $pekerjaanBerjalan }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'menunggu_verifikasi'])) }}"
                            class="tab-btn {{ request('status') === 'menunggu_verifikasi' ? 'active' : '' }}">
                            Menunggu Verifikasi <span class="num">{{ $menungguVerifikasi }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'terlambat'])) }}"
                            class="tab-btn {{ $lewatTenggat > 0 ? 'tab-danger' : '' }} {{ request('status') === 'terlambat' ? 'active' : '' }}">
                            Terlambat <span class="num">{{ $lewatTenggat }}</span>
                        </a>
                        <a href="{{ route('monitoring.balai', array_merge(request()->except(['status']), ['status' => 'selesai'])) }}"
                            class="tab-btn {{ request('status') === 'selesai' ? 'active' : '' }}">
                            Selesai <span class="num">{{ $selesaiTerverifikasi }}</span>
                        </a>
                    </div>

                    <form method="GET" action="{{ route('monitoring.balai') }}"
                        style="display: flex; gap: 8px; align-items: center;">
                        @if(request('pokja_id')) <input type="hidden" name="pokja_id" value="{{ request('pokja_id') }}"> @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                        <div class="stream-search">
                            <i class="ph-bold ph-magnifying-glass"></i>
                            <input type="text" name="q" id="searchFilterInput" value="{{ request('q') }}"
                                placeholder="Cari tugas, kode, atau PIC..."
                                onkeyup="clientFilterTable(this.value)">
                        </div>

                        @if(request()->hasAny(['pokja_id', 'status', 'q']))
                            <a href="{{ route('monitoring.balai') }}" class="btn-reset" title="Hapus semua filter">
                                <i class="ph-bold ph-x"></i> Hapus filter
                            </a>
                        @endif
                    </form>
                </div>

                <div class="table-responsive" id="tableScrollContainer">
                    <table class="command-table" id="commandTable">
                        <thead>
                            <tr>
                                <th style="width: 75px;">Kode</th>
                                <th>Nama Tugas</th>
                                <th style="width: 85px;">Unit</th>
                                <th style="width: 170px;">Penanggung Jawab</th>
                                <th style="width: 110px;">Tenggat</th>
                                <th style="width: 125px;">Progres</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 80px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pekerjaanList as $job)
                                @php
                                    $isTerlambat = $job->isTerlambat();
                                    $sisaHari = \Carbon\Carbon::today()->diffInDays($job->tenggat, false);
                                    $progressColor = $job->progres >= 100 ? '#0c8a5d' : ($job->progres >= 50 ? '#0a6fb5' : '#e39a13');
                                    $rowLate = $isTerlambat && $job->status_pelaksanaan !== 'selesai_terverifikasi';
                                    
                                    // Status info
                                    $statusLabel = match($job->status_pelaksanaan) {
                                        'selesai_terverifikasi' => 'Selesai',
                                        'hasil_diajukan' => 'Menunggu Verifikasi',
                                        'dikerjakan' => 'Dikerjakan',
                                        'perlu_perbaikan_hasil' => 'Perlu Revisi',
                                        default => 'Belum Dimulai'
                                    };
                                    $statusClass = match($job->status_pelaksanaan) {
                                        'selesai_terverifikasi' => 'st-selesai',
                                        'hasil_diajukan' => 'st-verifikasi',
                                        'dikerjakan' => 'st-berjalan',
                                        'perlu_perbaikan_hasil' => 'st-terlambat',
                                        default => 'st-belum'
                                    };

                                    // Payload JSON untuk Detail Modal
                                    $payload = [
                                        'id' => $job->id,
                                        'kode' => $job->kode_tampilan,
                                        'judul' => $job->judul,
                                        'deskripsi' => $job->deskripsi ?? 'Belum ada deskripsi rinci untuk pekerjaan ini.',
                                        'hasil_diharapkan' => $job->hasil_diharapkan ?? 'Tercapainya output kegiatan sesuai sasaran.',
                                        'target' => number_format($job->target, 0) . ' ' . $job->satuan,
                                        'bobot' => number_format($job->bobot, 1) . '%',
                                        'progres' => (float)$job->progres,
                                        'tenggat' => $job->tenggat ? $job->tenggat->format('d/m/Y') : '-',
                                        'is_terlambat' => $isTerlambat,
                                        'sisa_hari' => $sisaHari,
                                        'nomor_st' => $job->nomor_st,
                                        'status_label' => $statusLabel,
                                        'status_class' => $statusClass,
                                        'pokja' => $job->pokja ? $job->pokja->kode_pokja . ' - ' . $job->pokja->nama_pokja : 'BGTK DKI Jakarta',
                                        'pokja_code' => $job->pokja?->kode_pokja ?? 'BGTK',
                                        'parent' => $job->parent ? $job->parent->kode_tampilan . ' - ' . $job->parent->judul : null,
                                        'pj_nama' => $job->penanggungJawab?->name ?? 'Belum Ditugaskan',
                                        'pj_nip' => $job->penanggungJawab?->nip ? 'NIP: ' . $job->penanggungJawab->nip : 'Tenaga Alih Daya / Non-PNS',
                                        'pj_jabatan' => $job->penanggungJawab?->jabatan ?? 'Pegawai',
                                        'pj_initial' => strtoupper(substr($job->penanggungJawab?->name ?? 'PJ', 0, 2)),
                                        'pelaksana' => $job->pelaksana->map(fn($p) => [
                                            'name' => $p->name,
                                            'jabatan' => $p->jabatan ?? 'Pegawai',
                                            'initial' => strtoupper(substr($p->name, 0, 2))
                                        ])->toArray(),
                                        'url_show' => route('pekerjaan.show', $job),
                                        'last_update' => $job->realisasi->first() ? $job->realisasi->first()->created_at->format('d/m/Y H:i') : null,
                                        'last_note' => $job->realisasi->first()?->catatan ?? null,
                                    ];
                                @endphp
                                <tr class="table-row-item {{ $rowLate ? 'row-late' : '' }}"
                                    onclick="openJobModal({{ json_encode($payload) }})"
                                    title="Klik untuk melihat rincian tugas {{ $job->kode_tampilan }}">
                                    <!-- 1. Kode -->
                                    <td>
                                        <span class="task-code">{{ $job->kode_tampilan }}</span>
                                    </td>

                                    <!-- 2. Tugas (Ringkas & Poin Utama) -->
                                    <td>
                                        <div class="task-title-cell">
                                            <span class="task-title-text" title="{{ $job->judul }}">
                                                {{ $job->judul }}
                                            </span>
                                            @if($job->nomor_st)
                                                <span class="st-badge-mini" title="Surat Tugas Nomor: {{ $job->nomor_st }}">
                                                    <i class="ph-bold ph-file-text"></i> ST
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 3. Unit / Tim -->
                                    <td>
                                        <span class="task-pokja-badge">
                                            {{ $job->pokja?->kode_pokja ?? 'BGTK' }}
                                        </span>
                                    </td>

                                    <!-- 4. Penanggung Jawab -->
                                    <td>
                                        <div class="pic-cell">
                                            <div class="pic-avatar-mini">
                                                {{ strtoupper(substr($job->penanggungJawab?->name ?? 'PJ', 0, 2)) }}
                                            </div>
                                            <div style="overflow: hidden; flex: 1;">
                                                <div class="pic-name-clean" title="{{ $job->penanggungJawab->name ?? '-' }}">
                                                    {{ $job->penanggungJawab->name ?? 'Belum ditugaskan' }}
                                                </div>
                                            </div>
                                            @if($job->pelaksana && $job->pelaksana->count() > 1)
                                                <span class="pic-team-badge" title="Tim {{ $job->pelaksana->count() }} orang">
                                                    +{{ $job->pelaksana->count() - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 5. Tenggat Waktu -->
                                    <td>
                                        <div class="due-date-clean {{ $isTerlambat && $job->status_pelaksanaan !== 'selesai_terverifikasi' ? 'is-late' : '' }}">
                                            @if($isTerlambat && $job->status_pelaksanaan !== 'selesai_terverifikasi')
                                                <span class="late-dot" title="Terlambat"></span>
                                            @endif
                                            {{ $job->tenggat ? $job->tenggat->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>

                                    <!-- 6. Progres Capaian -->
                                    <td>
                                        <div class="progress-meter-clean">
                                            <div class="progress-track-clean">
                                                <div class="progress-fill-clean"
                                                    style="width: {{ $job->progres }}%; background: {{ $progressColor }};">
                                                </div>
                                            </div>
                                            <span class="progress-num-clean">{{ number_format($job->progres, 0) }}%</span>
                                        </div>
                                    </td>

                                    <!-- 7. Status -->
                                    <td>
                                        <span class="status-badge-cmd {{ $statusClass }}">
                                            @if($job->status_pelaksanaan === 'selesai_terverifikasi')
                                                <i class="ph-bold ph-check"></i>
                                            @elseif($job->status_pelaksanaan === 'hasil_diajukan')
                                                <i class="ph-bold ph-clock"></i>
                                            @elseif($job->status_pelaksanaan === 'dikerjakan')
                                                <i class="ph-bold ph-spinner"></i>
                                            @elseif($job->status_pelaksanaan === 'perlu_perbaikan_hasil')
                                                <i class="ph-bold ph-warning"></i>
                                            @endif
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    <!-- 8. Aksi Detail -->
                                    <td style="text-align: center;">
                                        <button type="button" class="btn-detail-cmd"
                                            onclick="event.stopPropagation(); openJobModal({{ json_encode($payload) }})">
                                            <i class="ph-bold ph-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="ph-duotone ph-tray"></i>
                                        Tidak ada tugas yang sesuai dengan filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </main>

    <!-- 3. Modal Detail Tugas Balai (Interactive & Lengkap) -->
    <div id="jobDetailModal" class="cmd-modal-backdrop" onclick="closeJobModal(event)">
        <div class="cmd-modal-dialog" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="cmd-modal-header">
                <div style="flex: 1; overflow: hidden;">
                    <div class="cmd-modal-badges">
                        <span id="mJobCode" class="task-code" style="font-size: 12px; padding: 3px 9px;">-</span>
                        <span id="mJobStatus" class="status-badge-cmd">-</span>
                        <span id="mJobPokja" class="task-pokja-badge">-</span>
                        <span id="mJobST" class="st-badge-mini" style="display: none;">
                            <i class="ph-bold ph-file-text"></i> <span id="mJobSTText">-</span>
                        </span>
                    </div>
                    <h3 id="mJobTitle" class="cmd-modal-title">Judul Tugas</h3>
                    <div id="mJobParentWrap" class="cmd-modal-parent" style="display: none;">
                        <i class="ph-bold ph-git-branch"></i> Bagian dari: <span id="mJobParent">-</span>
                    </div>
                </div>
                <button type="button" class="cmd-modal-close" onclick="closeJobModalDirect()" title="Tutup">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="cmd-modal-body">
                <!-- 4 KPI Baris Ringkas -->
                <div class="cmd-modal-kpi-grid">
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Capaian Progres</div>
                        <div class="cmd-kpi-val" id="mJobProgres">0%</div>
                        <div class="cmd-kpi-track">
                            <div class="cmd-kpi-fill" id="mJobProgresBar" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Target Hasil</div>
                        <div class="cmd-kpi-val" id="mJobTarget">-</div>
                        <div class="cmd-kpi-sub">Volume sasaran</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Bobot Penugasan</div>
                        <div class="cmd-kpi-val" id="mJobBobot">0.0%</div>
                        <div class="cmd-kpi-sub">Kontribusi balai</div>
                    </div>
                    <div class="cmd-kpi-card">
                        <div class="cmd-kpi-lbl">Tenggat Waktu</div>
                        <div class="cmd-kpi-val" id="mJobTenggat">-</div>
                        <div class="cmd-kpi-sub" id="mJobSisaHari">-</div>
                    </div>
                </div>

                <!-- Deskripsi & Hasil -->
                <div class="cmd-modal-box">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-info"></i> Deskripsi & Sasaran Tugas</h4>
                    <p id="mJobDesc" class="cmd-box-desc"></p>
                    <div class="cmd-box-expected">
                        <strong><i class="ph-bold ph-target"></i> Hasil yang Diharapkan:</strong>
                        <span id="mJobExpected">-</span>
                    </div>
                </div>

                <!-- Penanggung Jawab & Tim -->
                <div class="cmd-modal-box">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-users"></i> Penanggung Jawab & Tim Pelaksana</h4>
                    
                    <!-- PIC -->
                    <div class="cmd-pic-card">
                        <div class="cmd-pic-avatar" id="mJobPicAvatar">PJ</div>
                        <div class="cmd-pic-info">
                            <div class="cmd-pic-name" id="mJobPicName">-</div>
                            <div class="cmd-pic-nip" id="mJobPicNip">-</div>
                            <div class="cmd-pic-jabatan" id="mJobPicJabatan">-</div>
                        </div>
                        <span class="cmd-pic-badge">Penanggung Jawab (PIC)</span>
                    </div>

                    <!-- Tim Pelaksana -->
                    <div id="mJobTeamWrap" style="margin-top: 14px;">
                        <div class="cmd-team-header">
                            <span class="cmd-team-title">Personil Pelaksana Lainnya:</span>
                            <span id="mJobTeamCount" class="cmd-team-chip">0 Anggota</span>
                        </div>
                        <div id="mJobTeamList" class="cmd-team-list">
                            <!-- Injected by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Riwayat Update Terakhir -->
                <div class="cmd-modal-box" id="mJobUpdateWrap" style="display: none;">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-clock-counter-clockwise"></i> Pembaruan Progres Terakhir</h4>
                    <div class="cmd-update-card">
                        <div class="cmd-update-date"><i class="ph-bold ph-calendar-check"></i> <span id="mJobUpdateDate">-</span></div>
                        <div class="cmd-update-note" id="mJobUpdateNote">-</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="cmd-modal-footer">
                <button type="button" class="btn-cmd-secondary" onclick="closeJobModalDirect()">
                    <i class="ph-bold ph-x"></i> Tutup
                </button>
                <a id="mJobLink" href="#" target="_blank" class="btn-cmd-primary">
                    <i class="ph-bold ph-arrow-square-out"></i> Buka Halaman Tugas Lengkap
                </a>
            </div>
        </div>
    </div>

    <!-- 4. Skrip Logika Halaman & Modal -->
    <script>
        const STATE_KEY = 'bgtkMonitoringState';
        const scrollContainer = document.getElementById('tableScrollContainer');
        const assigneeContainer = document.getElementById('assigneeListContainer');
        const pokjaContainer = document.getElementById('pokjaScrollContainer');
        const modalBackdrop = document.getElementById('jobDetailModal');

        // Modal Logic
        let isModalOpen = false;

        function openJobModal(job) {
            if (!job) return;
            isModalOpen = true;

            // Badges
            document.getElementById('mJobCode').textContent = job.kode || '-';
            
            const statusEl = document.getElementById('mJobStatus');
            statusEl.textContent = job.status_label || '-';
            statusEl.className = 'status-badge-cmd ' + (job.status_class || '');

            document.getElementById('mJobPokja').textContent = job.pokja_code || 'BGTK';

            const stEl = document.getElementById('mJobST');
            if (job.nomor_st) {
                document.getElementById('mJobSTText').textContent = 'ST: ' + job.nomor_st;
                stEl.style.display = 'inline-flex';
            } else {
                stEl.style.display = 'none';
            }

            // Title & Parent
            document.getElementById('mJobTitle').textContent = job.judul || '-';
            const parentWrap = document.getElementById('mJobParentWrap');
            if (job.parent) {
                document.getElementById('mJobParent').textContent = job.parent;
                parentWrap.style.display = 'flex';
            } else {
                parentWrap.style.display = 'none';
            }

            // KPI Grid
            const prog = Math.min(100, Math.max(0, job.progres || 0));
            document.getElementById('mJobProgres').textContent = prog + '%';
            const barEl = document.getElementById('mJobProgresBar');
            barEl.style.width = prog + '%';
            barEl.style.background = prog >= 100 ? '#0c8a5d' : (prog >= 50 ? '#0a6fb5' : '#e39a13');

            document.getElementById('mJobTarget').textContent = job.target || '-';
            document.getElementById('mJobBobot').textContent = job.bobot || '-';
            document.getElementById('mJobTenggat').textContent = job.tenggat || '-';

            const sisaEl = document.getElementById('mJobSisaHari');
            if (job.status_raw === 'selesai_terverifikasi') {
                sisaEl.textContent = 'Selesai diverifikasi';
                sisaEl.style.color = '#0c8a5d';
            } else if (job.is_terlambat) {
                sisaEl.textContent = 'Terlambat ' + Math.abs(job.sisa_hari) + ' hari';
                sisaEl.style.color = '#d03232';
            } else if (job.sisa_hari !== null && job.sisa_hari >= 0) {
                sisaEl.textContent = 'Sisa ' + job.sisa_hari + ' hari lagi';
                sisaEl.style.color = '#64748b';
            } else {
                sisaEl.textContent = '-';
            }

            // Desc & Expected
            document.getElementById('mJobDesc').textContent = job.deskripsi || '-';
            document.getElementById('mJobExpected').textContent = job.hasil_diharapkan || '-';

            // PIC
            document.getElementById('mJobPicAvatar').textContent = job.pj_initial || 'PJ';
            document.getElementById('mJobPicName').textContent = job.pj_nama || '-';
            document.getElementById('mJobPicNip').textContent = job.pj_nip || '-';
            document.getElementById('mJobPicJabatan').textContent = job.pj_jabatan || '-';

            // Team list
            const teamWrap = document.getElementById('mJobTeamWrap');
            const teamList = document.getElementById('mJobTeamList');
            teamList.innerHTML = '';

            if (job.pelaksana && job.pelaksana.length > 0) {
                teamWrap.style.display = 'block';
                document.getElementById('mJobTeamCount').textContent = job.pelaksana.length + ' Orang';
                job.pelaksana.forEach(m => {
                    const item = document.createElement('div');
                    item.className = 'cmd-member-item';
                    item.innerHTML = `
                        <div class="cmd-member-avatar">${m.initial || 'PL'}</div>
                        <div style="overflow: hidden; flex: 1;">
                            <div class="cmd-member-name" title="${m.name}">${m.name}</div>
                            <div class="cmd-member-jab" title="${m.jabatan}">${m.jabatan}</div>
                        </div>
                    `;
                    teamList.appendChild(item);
                });
            } else {
                teamWrap.style.display = 'none';
            }

            // Update terakhir
            const updateWrap = document.getElementById('mJobUpdateWrap');
            if (job.last_update) {
                updateWrap.style.display = 'block';
                document.getElementById('mJobUpdateDate').textContent = 'Diperbarui pada: ' + job.last_update;
                document.getElementById('mJobUpdateNote').textContent = job.last_note ? `"${job.last_note}"` : 'Progres berhasil diperbarui ke sistem.';
            } else {
                updateWrap.style.display = 'none';
            }

            // Direct Link
            document.getElementById('mJobLink').href = job.url_show || '#';

            modalBackdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeJobModalDirect() {
            isModalOpen = false;
            modalBackdrop.classList.remove('show');
            document.body.style.overflow = '';
        }

        function closeJobModal(event) {
            if (event.target === modalBackdrop) {
                closeJobModalDirect();
            }
        }

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpen) {
                closeJobModalDirect();
            }
        });

        // 1. Jam digital
        function updateLiveClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('liveTimeDisplay').textContent = `${hours}:${minutes}:${seconds} WIB`;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('liveDateDisplay').textContent = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // 2. Segarkan data otomatis (In-Place Fetch)
        const REFRESH_INTERVAL = 30;
        let refreshSecondsLeft = REFRESH_INTERVAL;
        let isAutoRefreshActive = true;
        let isRefreshing = false;

        function renderRefreshLabel() {
            const label = document.getElementById('refreshLabel');
            if (!label) return;
            if (isRefreshing) {
                label.textContent = 'Menyinkronkan...';
            } else if (!isAutoRefreshActive) {
                label.textContent = 'Segarkan otomatis: Jeda';
            } else {
                label.textContent = `Segarkan otomatis: ${refreshSecondsLeft}d`;
            }
        }

        async function refreshDashboardData() {
            if (isRefreshing || isModalOpen) return; // Jangan refresh jika modal terbuka
            isRefreshing = true;
            renderRefreshLabel();

            const refreshIcon = document.querySelector('#btnAutoRefresh i');
            if (refreshIcon) refreshIcon.classList.add('spin-active');

            try {
                const response = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // 1. KPI
                    const newKpi = doc.querySelector('.giant-kpi-grid');
                    const curKpi = document.querySelector('.giant-kpi-grid');
                    if (newKpi && curKpi) curKpi.innerHTML = newKpi.innerHTML;

                    // 2. Pokja
                    const newPokja = doc.getElementById('pokjaScrollContainer');
                    const curPokja = document.getElementById('pokjaScrollContainer');
                    if (newPokja && curPokja) {
                        const prevScroll = curPokja.scrollTop;
                        curPokja.innerHTML = newPokja.innerHTML;
                        curPokja.scrollTop = prevScroll;
                    }

                    // 3. Assignees
                    const newAssignee = doc.getElementById('assigneeListContainer');
                    const curAssignee = document.getElementById('assigneeListContainer');
                    if (newAssignee && curAssignee) {
                        const prevScroll = curAssignee.scrollTop;
                        curAssignee.innerHTML = newAssignee.innerHTML;
                        curAssignee.scrollTop = prevScroll;
                        filterAssigneeRows();
                        if (selectedAssigneeName) markSelectedAssignee(selectedAssigneeName);
                    }

                    // 4. Tabs
                    const newTabs = doc.querySelector('.stream-tabs');
                    const curTabs = document.querySelector('.stream-tabs');
                    if (newTabs && curTabs) curTabs.innerHTML = newTabs.innerHTML;

                    // 5. Table Body
                    const newTbody = doc.querySelector('#commandTable tbody');
                    const curTbody = document.querySelector('#commandTable tbody');
                    if (newTbody && curTbody) {
                        curTbody.innerHTML = newTbody.innerHTML;
                        const searchInput = document.getElementById('searchFilterInput');
                        if (searchInput && searchInput.value) {
                            clientFilterTable(searchInput.value);
                        }
                    }
                }
            } catch (err) {
                console.warn('Gagal sinkronisasi data monitoring balai:', err);
            } finally {
                isRefreshing = false;
                refreshSecondsLeft = REFRESH_INTERVAL;
                if (refreshIcon) refreshIcon.classList.remove('spin-active');
                renderRefreshLabel();
            }
        }

        function runRefreshCountdown() {
            if (!isAutoRefreshActive || isRefreshing || isModalOpen) return;

            const active = document.activeElement;
            if (active && active.tagName === 'INPUT' && active.value) {
                refreshSecondsLeft = REFRESH_INTERVAL;
                renderRefreshLabel();
                return;
            }

            refreshSecondsLeft--;
            renderRefreshLabel();
            if (refreshSecondsLeft <= 0) {
                refreshDashboardData();
            }
        }
        setInterval(runRefreshCountdown, 1000);

        function toggleAutoRefresh() {
            isAutoRefreshActive = !isAutoRefreshActive;
            const btn = document.getElementById('btnAutoRefresh');
            btn.classList.toggle('active', isAutoRefreshActive);
            refreshSecondsLeft = REFRESH_INTERVAL;
            renderRefreshLabel();
        }

        // 3. Layar penuh (TV)
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    document.body.classList.add('fullscreen-mode');
                    document.getElementById('fsLabel').textContent = 'Keluar Layar Penuh';
                }).catch(err => {
                    alert('Tidak dapat membuka layar penuh: ' + err.message);
                });
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) {
                document.body.classList.remove('fullscreen-mode');
                document.getElementById('fsLabel').textContent = 'Layar Penuh';
            }
        });

        // 4. Gulir otomatis untuk layar TV
        let isAutoScrollActive = false;
        let scrollTimer = null;
        let scrollPausedUntil = 0;
        let reachedBottom = false;

        function renderScrollLabel() {
            document.getElementById('scrollLabel').textContent =
                'Gulir otomatis: ' + (isAutoScrollActive ? 'Hidup' : 'Mati');
            document.getElementById('btnAutoScroll').classList.toggle('active', isAutoScrollActive);
        }

        function toggleAutoScroll() {
            isAutoScrollActive = !isAutoScrollActive;
            renderScrollLabel();
            if (isAutoScrollActive) {
                startAutoScroll();
            } else {
                clearInterval(scrollTimer);
            }
        }

        function startAutoScroll() {
            clearInterval(scrollTimer);
            reachedBottom = false;
            scrollPausedUntil = Date.now() + 2500;
            scrollTimer = setInterval(() => {
                if (!isAutoScrollActive || !scrollContainer || isModalOpen) return;
                const now = Date.now();
                if (now < scrollPausedUntil) return;

                const maxScroll = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                if (maxScroll <= 0) return;

                if (scrollContainer.scrollTop >= maxScroll - 2) {
                    if (!reachedBottom) {
                        reachedBottom = true;
                        scrollPausedUntil = now + 3000;
                    } else {
                        reachedBottom = false;
                        scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
                        scrollPausedUntil = now + 3500;
                    }
                } else {
                    scrollContainer.scrollBy({ top: 1, behavior: 'auto' });
                }
            }, 40);
        }

        // 5. Filter cepat pada tabel
        function clientFilterTable(query) {
            query = (query || '').toLowerCase().trim();
            document.querySelectorAll('.table-row-item').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = (!query || text.includes(query)) ? '' : 'none';
            });
        }

        // 6. Beban kerja filter
        let currentAssigneeTab = 'all';
        let selectedAssigneeName = null;

        function switchAssigneeTab(tab, btn) {
            currentAssigneeTab = tab;
            document.querySelectorAll('.btn-assignee-tab').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            filterAssigneeRows();
        }

        function filterAssigneeRows(query) {
            const searchInput = document.getElementById('assigneeSearchInput');
            const q = (typeof query === 'string' ? query : (searchInput ? searchInput.value : '')).toLowerCase().trim();

            document.querySelectorAll('.assignee-item').forEach(item => {
                const cat = item.getAttribute('data-role-category');
                const name = (item.getAttribute('data-name') || '').toLowerCase();
                const matchTab = (currentAssigneeTab === 'all') || (cat === currentAssigneeTab);
                const matchSearch = !q || name.includes(q);
                item.style.display = (matchTab && matchSearch) ? 'flex' : 'none';
            });
        }

        function markSelectedAssignee(name) {
            document.querySelectorAll('.assignee-item').forEach(el => {
                el.classList.toggle('selected-assignee', !!name && el.getAttribute('data-name') === name.toLowerCase());
            });
        }

        function filterTableByAssignee(name) {
            const searchInput = document.getElementById('searchFilterInput');

            if (selectedAssigneeName === name) {
                selectedAssigneeName = null;
                if (searchInput) searchInput.value = '';
                clientFilterTable('');
                markSelectedAssignee(null);
            } else {
                selectedAssigneeName = name;
                if (searchInput) searchInput.value = name;
                clientFilterTable(name);
                markSelectedAssignee(name);
            }
        }

        // 7. Shortcut keyboard: F, S, R
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            const k = e.key.toLowerCase();
            if (k === 'f') toggleFullscreen();
            else if (k === 's') toggleAutoScroll();
            else if (k === 'r') toggleAutoRefresh();
        });
    </script>
</body>

</html>
