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
            /* Palet mengikuti halaman login SIMON-K */
            --navy: #0b4a82;
            --navy-deep: #083a68;
            --navy-soft: #0e5a9c;
            --primary: #0a6fb5;
            --primary-dark: #085c98;
            --primary-soft: #e8f2fb;
            --primary-glow: rgba(10, 111, 181, 0.22);
            --cyan: #22b8e8;
            --cyan-soft: #e0f6fd;

            --bg-base: #eef3f9;
            --bg-card: #ffffff;
            --bg-sub: #f6f9fd;
            --border: #e1e8f2;
            --border-strong: #cdd9ea;

            --text-main: #0f2a44;
            --text-body: #33495f;
            --text-muted: #586d83;

            --success: #0c8a5d;
            --success-soft: #e3f6ee;
            --warning: #b87400;
            --warning-soft: #fff3d9;
            --danger: #d03232;
            --danger-soft: #fdeaea;
            --purple: #6f45bd;
            --purple-soft: #efe8fa;

            --shadow-sm: 0 1px 2px rgba(11, 74, 130, 0.06);
            --shadow-md: 0 4px 18px rgba(11, 74, 130, 0.08);

            --body-height: calc(100vh - 330px);
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
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c5d3e6;
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a9bcd6;
        }

        /* ===== Header ===== */
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
            width: 46px;
            height: 46px;
            background: #ffffff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            color: var(--primary);
            flex-shrink: 0;
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
            background: rgba(255, 255, 255, 0.16);
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
            border-radius: 999px;
            background: #4ade80;
        }

        .brand-sub {
            font-size: 12px;
            font-weight: 600;
            color: #b9dcf3;
            margin-top: 2px;
        }

        .live-clock-section {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 999px;
        }

        .clock-pulse {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #4ade80;
            animation: soft-blink 3s ease-in-out infinite;
        }

        @keyframes soft-blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.45;
            }
        }

        @keyframes spin-anim {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .spin-active {
            animation: spin-anim 0.75s linear infinite !important;
        }

        .clock-time {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.04em;
        }

        .clock-date {
            font-size: 12.5px;
            color: #d3e8f8;
            font-weight: 600;
            border-left: 1px solid rgba(255, 255, 255, 0.25);
            padding-left: 14px;
        }

        .command-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-control {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.28);
            padding: 8px 15px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-control:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .btn-control.active {
            background: #ffffff;
            color: var(--primary);
            border-color: #ffffff;
        }

        /* ===== Container ===== */
        .command-container {
            padding: 20px 28px 28px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 2000px;
            width: 100%;
            margin: 0 auto;
        }

        /* ===== Ringkasan (KPI) ===== */
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

        @media (max-width: 500px) {
            .giant-kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        .kpi-box {
            --accent: var(--primary);
            --accent-soft: var(--primary-soft);
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-left: 5px solid var(--accent);
            border-radius: 16px;
            padding: 14px 18px;
            display: grid;
            grid-template-columns: 1fr auto;
            grid-template-areas:
                "title icon"
                "number icon"
                "sub sub";
            row-gap: 4px;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .kpi-blue {
            --accent: #0a6fb5;
            --accent-soft: #e8f2fb;
        }

        .kpi-cyan {
            --accent: #0e9bc9;
            --accent-soft: #e0f6fd;
        }

        .kpi-purple {
            --accent: #6f45bd;
            --accent-soft: #efe8fa;
        }

        .kpi-green {
            --accent: #0c8a5d;
            --accent-soft: #e3f6ee;
        }

        .kpi-amber {
            --accent: #c47c00;
            --accent-soft: #fff3d9;
        }

        .kpi-red {
            --accent: #d03232;
            --accent-soft: #fdeaea;
        }

        .kpi-red.is-alert {
            background: #fff7f7;
            border-color: #f4c4c4;
            border-left-color: #d03232;
        }

        .kpi-title {
            grid-area: title;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .kpi-icon-wrap {
            grid-area: icon;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--accent-soft);
            color: var(--accent);
        }

        .kpi-number {
            grid-area: number;
            font-size: 34px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }

        .kpi-subtext {
            grid-area: sub;
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .kpi-subtext i {
            color: var(--accent);
        }

        .kpi-mini-track {
            flex: 1;
            height: 6px;
            background: var(--accent-soft);
            border-radius: 999px;
            overflow: hidden;
        }

        .kpi-mini-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 999px;
        }

        /* ===== Layout utama: dua kolom, tinggi pas satu layar ===== */
        .command-body-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 20px;
            height: var(--body-height);
            min-height: 620px;
        }

        .left-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-height: 0;
        }

        .left-col>.panel-card {
            flex: 1 1 0;
        }

        @media (max-width: 1200px) {
            .command-body-grid {
                grid-template-columns: 1fr;
                height: auto;
                min-height: 0;
            }

            .left-col>.panel-card {
                flex: none;
            }

            .scroll-area {
                max-height: 420px;
            }

            .table-responsive {
                max-height: 640px;
            }
        }

        /* ===== Panel ===== */
        .panel-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-top: 4px solid var(--primary);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .panel-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .panel-title {
            font-size: 14.5px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .panel-title i {
            font-size: 20px;
            color: var(--primary);
        }

        .panel-count {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--primary-dark);
            background: var(--primary-soft);
            padding: 3px 11px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .scroll-area {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }

        /* ===== Tim Kerja ===== */
        .pokja-list {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: var(--bg-sub);
        }

        .pokja-item {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 14px;
            transition: border-color 0.2s, background 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .pokja-item:hover {
            border-color: var(--primary);
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
            font-size: 13px;
            font-weight: 800;
            color: var(--text-main);
        }

        .pokja-item-all .cnt {
            font-size: 12px;
            color: var(--primary-dark);
            font-weight: 700;
        }

        .pokja-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            gap: 8px;
        }

        .pokja-code-tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 700;
            background: var(--navy);
            color: #ffffff;
            padding: 2px 9px;
            border-radius: 6px;
        }

        .pokja-done {
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .pokja-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 3px;
            line-height: 1.35;
        }

        .pokja-katim {
            font-size: 11.5px;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 9px;
            gap: 8px;
        }

        .pokja-katim strong {
            color: var(--text-body);
            font-weight: 700;
        }

        .personil-chip {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .pokja-meter-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pokja-track {
            flex: 1;
            height: 8px;
            background: #e5edf7;
            border-radius: 999px;
            overflow: hidden;
        }

        .pokja-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary), var(--cyan));
        }

        .pokja-percent {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-dark);
            min-width: 44px;
            text-align: right;
        }

        /* ===== Beban Kerja ===== */
        .assignee-tabs {
            display: flex;
            gap: 6px;
            padding: 10px 14px;
            background: var(--bg-sub);
            border-bottom: 1px solid var(--border);
            overflow-x: auto;
            flex-shrink: 0;
        }

        .btn-assignee-tab {
            background: #ffffff;
            color: var(--text-muted);
            border: 1px solid var(--border);
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11.5px;
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
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .assignee-search-input {
            width: 100%;
            background: var(--primary-soft);
            border: 1.5px solid var(--border);
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 12.5px;
            font-family: inherit;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
        }

        .assignee-search-input::placeholder {
            color: var(--text-muted);
        }

        .assignee-search-input:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .assignee-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 16px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background 0.15s ease;
            border-left: 3px solid transparent;
        }

        .assignee-item:hover {
            background: var(--bg-sub);
        }

        .assignee-item.selected-assignee {
            background: var(--primary-soft);
            border-left-color: var(--primary);
        }

        .assignee-item:last-child {
            border-bottom: none;
        }

        .assignee-avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: #dbe7f5;
            color: var(--navy);
            font-weight: 800;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .assignee-avatar.is-katim {
            background: var(--navy);
            color: #ffffff;
        }

        .assignee-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .assignee-sub {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .role-pill {
            font-size: 10px;
            font-weight: 700;
            padding: 1px 8px;
            border-radius: 999px;
        }

        .role-pill-pegawai {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .role-pill-katim {
            background: var(--warning-soft);
            color: var(--warning);
        }

        .role-pill-kor {
            background: var(--purple-soft);
            color: var(--purple);
        }

        .assignee-count-wrap {
            margin-left: auto;
            flex-shrink: 0;
        }

        .assignee-count-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 11px;
            border-radius: 999px;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .assignee-count-badge.has-workload {
            background: var(--success-soft);
            color: var(--success);
        }

        .assignee-count-badge.zero-workload {
            background: #eef2f7;
            color: #7a8da3;
        }

        .assignee-footer {
            padding: 9px 14px;
            background: var(--bg-sub);
            border-top: 1px solid var(--border);
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
            flex-shrink: 0;
        }

        /* ===== Daftar Tugas ===== */
        .stream-controls {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .stream-tabs {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: var(--bg-sub);
            color: var(--text-body);
            border: 1px solid var(--border);
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .tab-btn .num {
            font-variant-numeric: tabular-nums;
            opacity: 0.75;
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

        .tab-btn.active .num {
            opacity: 0.9;
        }

        .tab-btn.tab-danger {
            color: var(--danger);
            border-color: #f2c3c3;
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
            border: 1.5px solid var(--border);
            border-radius: 999px;
            padding: 6px 16px;
            transition: all 0.2s;
        }

        .stream-search:focus-within {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .stream-search i {
            color: var(--text-muted);
        }

        .stream-search input {
            background: transparent;
            border: none;
            color: var(--text-main);
            font-size: 12.5px;
            outline: none;
            width: 250px;
            font-family: inherit;
        }

        .stream-search input::placeholder {
            color: var(--text-muted);
        }

        .btn-reset {
            background: var(--danger-soft);
            color: var(--danger);
            border: 1px solid #f2c3c3;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-reset:hover {
            background: var(--danger);
            color: #ffffff;
        }

        .table-responsive {
            flex: 1 1 auto;
            min-height: 0;
            overflow: auto;
        }

        .command-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .command-table th {
            background: #f1f6fc;
            text-align: left;
            padding: 12px 16px;
            font-weight: 800;
            color: var(--navy);
            border-bottom: 2px solid var(--border-strong);
            font-size: 12px;
            letter-spacing: 0.02em;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        .command-table td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-body);
            vertical-align: middle;
        }

        .command-table tbody tr:hover td {
            background: var(--primary-soft);
        }

        .command-table tbody tr.row-late td:first-child {
            box-shadow: inset 4px 0 0 var(--danger);
        }

        .task-code {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 11.5px;
            color: var(--primary-dark);
            background: var(--primary-soft);
            padding: 3px 8px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .task-title-link {
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
            font-size: 14px;
            line-height: 1.4;
            display: block;
        }

        .task-title-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .task-meta {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .task-parent {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .task-st-badge {
            font-size: 11px;
            font-weight: 600;
            background: var(--cyan-soft);
            color: #0a6f92;
            padding: 2px 9px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .task-pokja-badge {
            font-size: 11.5px;
            font-weight: 700;
            background: var(--navy);
            color: #ffffff;
            padding: 3px 11px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .pic-name {
            font-weight: 700;
            color: var(--text-main);
            font-size: 13.5px;
        }

        .pic-nip {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .team-count-chip {
            font-size: 11px;
            font-weight: 700;
            background: var(--success-soft);
            color: var(--success);
            padding: 2px 9px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }

        .target-main {
            font-weight: 700;
            color: var(--text-main);
        }

        .target-bobot {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .due-date {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-body);
        }

        .due-date.late {
            color: var(--danger);
        }

        .due-chip {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 999px;
            display: inline-block;
            margin-top: 4px;
            white-space: nowrap;
        }

        .due-chip.late {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .due-chip.soon {
            background: var(--warning-soft);
            color: var(--warning);
        }

        .progress-meter {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 140px;
        }

        .progress-track-cmd {
            flex: 1;
            height: 9px;
            background: #e5edf7;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill-cmd {
            height: 100%;
            border-radius: 999px;
        }

        .progress-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-main);
            min-width: 40px;
            text-align: right;
        }

        .progress-upd {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .status-badge-cmd {
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .st-berjalan {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .st-verifikasi {
            background: var(--purple-soft);
            color: var(--purple);
        }

        .st-selesai {
            background: var(--success-soft);
            color: var(--success);
        }

        .st-terlambat {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .st-belum {
            background: #eef2f7;
            color: #5f7389;
        }

        .empty-state {
            text-align: center;
            padding: 56px 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .empty-state i {
            font-size: 44px;
            color: #b7c6da;
            display: block;
            margin-bottom: 8px;
        }

        /* ===== Mode Layar Penuh (TV) ===== */
        body.fullscreen-mode {
            --body-height: calc(100vh - 318px);
        }

        body.fullscreen-mode .top-command-bar {
            padding: 14px 36px;
        }

        body.fullscreen-mode .command-container {
            padding: 22px 36px 30px;
            max-width: none;
        }

        body.fullscreen-mode .kpi-number {
            font-size: 40px;
        }

        body.fullscreen-mode .command-table td {
            padding: 15px 18px;
            font-size: 14.5px;
        }

        body.fullscreen-mode .task-title-link {
            font-size: 15px;
        }
    </style>
</head>

<body>

    <!-- 1. Bilah Atas -->
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

    <!-- 2. Isi Halaman -->
    <main class="command-container">

        <!-- Ringkasan -->
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
                    <i class="ph-bold ph-spinner"></i> Sudah ada laporan progres
                </div>
            </div>

            <div class="kpi-box kpi-purple">
                <span class="kpi-title">Menunggu Verifikasi</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-seal-check"></i></div>
                <div class="kpi-number">{{ number_format($menungguVerifikasi) }}</div>
                <div class="kpi-subtext">
                    <i class="ph-bold ph-clock"></i> Capaian 100%, menunggu Katim
                </div>
            </div>

            <div class="kpi-box kpi-green">
                <span class="kpi-title">Selesai</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-check-fat"></i></div>
                <div class="kpi-number">{{ number_format($selesaiTerverifikasi) }}</div>
                <div class="kpi-subtext">
                    <i class="ph-bold ph-check"></i> Hasil sudah diverifikasi
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
                <div class="kpi-icon-wrap"><i
                        class="ph-duotone {{ $lewatTenggat > 0 ? 'ph-warning-octagon' : 'ph-check-circle' }}"></i></div>
                <div class="kpi-number">{{ number_format($lewatTenggat) }}</div>
                <div class="kpi-subtext">
                    @if($lewatTenggat > 0)
                        <i class="ph-bold ph-warning"></i> Melewati tenggat, perlu tindak lanjut
                    @else
                        <i class="ph-bold ph-check"></i> Tidak ada tugas terlambat
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom kiri: tim kerja & beban kerja | Kolom kanan: daftar tugas -->
        <div class="command-body-grid">

            <div class="left-col">

                <!-- Capaian Tim Kerja -->
                <div class="panel-card">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="ph-duotone ph-buildings"></i>
                            Capaian Tim Kerja
                        </div>
                        <span class="panel-count">{{ $pokjas->count() }} tim</span>
                    </div>

                    <div class="scroll-area" id="pokjaScrollContainer">
                        <div class="pokja-list">
                            <a href="{{ route('monitoring.balai') }}"
                                class="pokja-item pokja-item-all {{ !request('pokja_id') ? 'active' : '' }}">
                                <span class="lbl">Semua Tim Kerja</span>
                                <span class="cnt">{{ $totalPekerjaan }} tugas</span>
                            </a>

                            @foreach($pokjas as $p)
                                <a href="{{ route('monitoring.balai', array_merge(request()->except(['pokja_id']), ['pokja_id' => $p->id])) }}"
                                    class="pokja-item {{ request('pokja_id') == $p->id ? 'active' : '' }}"
                                    @if(!empty($p->anggota) && $p->anggota->isNotEmpty())
                                    title="Anggota: {{ $p->anggota->pluck('name')->implode(', ') }}" @endif>
                                    <div class="pokja-item-header">
                                        <span class="pokja-code-tag">{{ $p->kode_pokja }}</span>
                                        <span class="pokja-done">{{ $p->tugas_selesai }} dari {{ $p->total_tugas }}
                                            selesai</span>
                                    </div>
                                    <div class="pokja-name">{{ $p->nama_pokja }}</div>
                                    <div class="pokja-katim">
                                        <span>Katim: <strong>{{ $p->katim_name }}</strong></span>
                                        <span class="personil-chip">
                                            <i class="ph-bold ph-users"></i> {{ $p->anggota_count }} orang
                                        </span>
                                    </div>
                                    <div class="pokja-meter-wrap">
                                        <div class="pokja-track">
                                            <div class="pokja-fill" style="width: {{ $p->avg_progres }}%;"></div>
                                        </div>
                                        <div class="pokja-percent">{{ $p->avg_progres }}%</div>
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
                        <span class="panel-count">{{ $topAssignees->count() }} orang</span>
                    </div>

                    <div class="assignee-tabs">
                        <button type="button" class="btn-assignee-tab active" onclick="switchAssigneeTab('all', this)">
                            Semua ({{ $topAssignees->count() }})
                        </button>
                        <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('pegawai', this)">
                            Pelaksana
                            ({{ $topAssignees->filter(fn($u) => in_array($u->role, ['pegawai', 'tim_pokja']))->count() }})
                        </button>
                        <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('katim', this)">
                            Katim & Koordinator
                            ({{ $topAssignees->filter(fn($u) => in_array($u->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']))->count() }})
                        </button>
                    </div>

                    <div class="assignee-search-wrap">
                        <input type="text" id="assigneeSearchInput" placeholder="Cari nama personil..."
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
                                    <div class="assignee-count-badge {{ ($staf->pic_count + $staf->member_count) > 0 ? 'has-workload' : 'zero-workload' }}"
                                        title="{{ $staf->pic_count }} tugas sebagai PIC, {{ $staf->member_count }} tugas sebagai anggota tim">
                                        {{ $staf->pic_count + $staf->member_count }} tugas
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="assignee-footer">
                        <i class="ph-bold ph-cursor-click"></i> Klik nama untuk melihat tugasnya di tabel
                    </div>
                </div>

            </div>

            <!-- Daftar Tugas -->
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
                        @if(request('pokja_id')) <input type="hidden" name="pokja_id" value="{{ request('pokja_id') }}">
                        @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

                        <div class="stream-search">
                            <i class="ph-bold ph-magnifying-glass"></i>
                            <input type="text" name="q" id="searchFilterInput" value="{{ request('q') }}"
                                placeholder="Cari tugas, kode, nama, atau nomor ST..."
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
                                <th style="width: 80px;">Kode</th>
                                <th>Tugas</th>
                                <th style="width: 90px;">Tim</th>
                                <th>Penanggung Jawab</th>
                                <th style="width: 120px;">Target</th>
                                <th style="width: 130px;">Tenggat</th>
                                <th style="width: 170px;">Progres</th>
                                <th style="width: 180px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pekerjaanList as $job)
                                @php
                                    $isTerlambat = $job->isTerlambat();
                                    $sisaHari = Carbon\Carbon::today()->diffInDays($job->tenggat, false);
                                    $progressClass = $job->progres >= 100 ? '#0c8a5d' : ($job->progres >= 50 ? '#0a6fb5' : '#e39a13');
                                    $rowLate = $isTerlambat && $job->status_pelaksanaan !== 'selesai_terverifikasi';
                                @endphp
                                <tr class="table-row-item {{ $rowLate ? 'row-late' : '' }}">
                                    <td>
                                        <span class="task-code">{{ $job->kode_tampilan }}</span>
                                    </td>

                                    <td style="max-width: 360px;">
                                        <a href="{{ route('pekerjaan.show', $job) }}" target="_blank"
                                            class="task-title-link">
                                            {{ $job->judul }}
                                        </a>
                                        @if($job->parent || $job->nomor_st)
                                            <div class="task-meta">
                                                @if($job->nomor_st)
                                                    <span class="task-st-badge">
                                                        <i class="ph-bold ph-file-text"></i> ST {{ $job->nomor_st }}
                                                    </span>
                                                @endif
                                                @if($job->parent)
                                                    <span class="task-parent">
                                                        Bagian dari {{ $job->parent->kode_tampilan }} -
                                                        {{ Str::limit($job->parent->judul, 28) }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="task-pokja-badge">
                                            {{ $job->pokja?->kode_pokja ?? 'BGTK' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="pic-name">
                                            {{ $job->penanggungJawab->name ?? 'Belum ditugaskan' }}
                                        </div>
                                        <div class="pic-nip">
                                            {{ $job->penanggungJawab->nip ?? 'Penanggung jawab utama' }}
                                        </div>

                                        @if($job->pelaksana && $job->pelaksana->count() > 1)
                                            @php
                                                $namesList = $job->pelaksana->pluck('name')->implode(', ');
                                            @endphp
                                            <span class="team-count-chip" title="Anggota pelaksana: {{ $namesList }}">
                                                <i class="ph-bold ph-users-three"></i> Tim {{ $job->pelaksana->count() }} orang
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="target-main">
                                            {{ number_format($job->target, 0) }} {{ $job->satuan }}
                                        </div>
                                        <div class="target-bobot">
                                            Bobot {{ number_format($job->bobot, 1) }}%
                                        </div>
                                    </td>

                                    <td>
                                        <div class="due-date {{ $isTerlambat ? 'late' : '' }}">
                                            {{ $job->tenggat ? $job->tenggat->format('d/m/Y') : '-' }}
                                        </div>
                                        @if($job->status_pelaksanaan !== 'selesai_terverifikasi')
                                            @if($isTerlambat)
                                                <span class="due-chip late">Terlambat {{ abs($sisaHari) }} hari</span>
                                            @elseif($sisaHari <= 7 && $sisaHari >= 0)
                                                <span class="due-chip soon">Sisa {{ $sisaHari }} hari</span>
                                            @endif
                                        @endif
                                    </td>

                                    <td>
                                        <div class="progress-meter">
                                            <div class="progress-track-cmd">
                                                <div class="progress-fill-cmd"
                                                    style="width: {{ $job->progres }}%; background: {{ $progressClass }};">
                                                </div>
                                            </div>
                                            <span class="progress-num">{{ number_format($job->progres, 0) }}%</span>
                                        </div>
                                        @if($job->realisasi && $job->realisasi->first())
                                            <div class="progress-upd">
                                                Diperbarui {{ $job->realisasi->first()->created_at->format('d/m') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($job->status_pelaksanaan === 'selesai_terverifikasi')
                                            <span class="status-badge-cmd st-selesai">
                                                <i class="ph-bold ph-seal-check"></i> Selesai
                                            </span>
                                        @elseif($job->status_pelaksanaan === 'hasil_diajukan')
                                            <span class="status-badge-cmd st-verifikasi">
                                                <i class="ph-bold ph-clock"></i> Menunggu Verifikasi
                                            </span>
                                        @elseif($job->status_pelaksanaan === 'dikerjakan')
                                            <span class="status-badge-cmd st-berjalan">
                                                <i class="ph-bold ph-spinner"></i> Dikerjakan
                                            </span>
                                        @elseif($job->status_pelaksanaan === 'perlu_perbaikan_hasil')
                                            <span class="status-badge-cmd st-terlambat">
                                                <i class="ph-bold ph-arrow-counter-clockwise"></i> Perlu Revisi
                                            </span>
                                        @else
                                            <span class="status-badge-cmd st-belum">
                                                Belum Dimulai
                                            </span>
                                        @endif
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

    <script>
        const STATE_KEY = 'bgtkMonitoringState';
        const scrollContainer = document.getElementById('tableScrollContainer');
        const assigneeContainer = document.getElementById('assigneeListContainer');
        const pokjaContainer = document.getElementById('pokjaScrollContainer');

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

        // 2. Segarkan data otomatis secara berkala di latar belakang (In-Place Fetch)
        //    PENTING: Menggunakan fetch background, BUKAN location.reload(),
        //    agar browser TIDAK keluar dari mode Layar Penuh (Fullscreen) saat data diperbarui.
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
            if (isRefreshing) return;
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

                    // 1. Perbarui KPI Ringkasan Angka
                    const newKpi = doc.querySelector('.giant-kpi-grid');
                    const curKpi = document.querySelector('.giant-kpi-grid');
                    if (newKpi && curKpi) {
                        curKpi.innerHTML = newKpi.innerHTML;
                    }

                    // 2. Perbarui Capaian Tim Kerja (Pokja) tanpa mereset posisi scroll
                    const newPokja = doc.getElementById('pokjaScrollContainer');
                    const curPokja = document.getElementById('pokjaScrollContainer');
                    if (newPokja && curPokja) {
                        const prevScroll = curPokja.scrollTop;
                        curPokja.innerHTML = newPokja.innerHTML;
                        curPokja.scrollTop = prevScroll;
                    }

                    // 3. Perbarui Daftar Beban Kerja Personil tanpa mereset posisi scroll
                    const newAssignee = doc.getElementById('assigneeListContainer');
                    const curAssignee = document.getElementById('assigneeListContainer');
                    if (newAssignee && curAssignee) {
                        const prevScroll = curAssignee.scrollTop;
                        curAssignee.innerHTML = newAssignee.innerHTML;
                        curAssignee.scrollTop = prevScroll;
                        filterAssigneeRows();
                        if (selectedAssigneeName) {
                            markSelectedAssignee(selectedAssigneeName);
                        }
                    }

                    // 4. Perbarui Tab Kategori Personil
                    const newAssigneeTabs = doc.querySelector('.assignee-tabs');
                    const curAssigneeTabs = document.querySelector('.assignee-tabs');
                    if (newAssigneeTabs && curAssigneeTabs) {
                        const activeTab = currentAssigneeTab;
                        curAssigneeTabs.innerHTML = newAssigneeTabs.innerHTML;
                        const tabs = curAssigneeTabs.querySelectorAll('.btn-assignee-tab');
                        tabs.forEach(b => b.classList.remove('active'));
                        if (activeTab === 'pegawai' && tabs[1]) tabs[1].classList.add('active');
                        else if (activeTab === 'katim' && tabs[2]) tabs[2].classList.add('active');
                        else if (tabs[0]) tabs[0].classList.add('active');
                    }

                    // 5. Perbarui Tab Status Aliran Tugas
                    const newTabs = doc.querySelector('.stream-tabs');
                    const curTabs = document.querySelector('.stream-tabs');
                    if (newTabs && curTabs) {
                        curTabs.innerHTML = newTabs.innerHTML;
                    }

                    // 6. Perbarui Isi Tabel Tugas & terapkan filter pencarian yang sedang aktif
                    const newTbody = doc.querySelector('#commandTable tbody');
                    const curTbody = document.querySelector('#commandTable tbody');
                    if (newTbody && curTbody) {
                        curTbody.innerHTML = newTbody.innerHTML;
                        const searchInput = document.getElementById('searchFilterInput');
                        if (searchInput && searchInput.value) {
                            clientFilterTable(searchInput.value);
                        }
                    }

                    // 7. Perbarui angka count panel
                    const newCounts = doc.querySelectorAll('.panel-count');
                    const curCounts = document.querySelectorAll('.panel-count');
                    curCounts.forEach((c, idx) => {
                        if (newCounts[idx]) c.textContent = newCounts[idx].textContent;
                    });
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
            if (!isAutoRefreshActive || isRefreshing) return;

            // Jangan muat ulang saat seseorang sedang mengetik di kolom pencarian
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

        // 4. Gulir otomatis untuk layar TV (berhenti sejenak di ujung atas dan bawah)
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
                if (!isAutoScrollActive || !scrollContainer) return;
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

        // 5b. Daftar beban kerja personil
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

        // 6. Simpan & pulihkan kondisi layar saat halaman disegarkan,
        //    supaya posisi gulir dan pilihan tidak kembali ke awal.
        function saveState() {
            try {
                sessionStorage.setItem(STATE_KEY, JSON.stringify({
                    tableScroll: scrollContainer ? scrollContainer.scrollTop : 0,
                    assigneeScroll: assigneeContainer ? assigneeContainer.scrollTop : 0,
                    pokjaScroll: pokjaContainer ? pokjaContainer.scrollTop : 0,
                    pageScroll: window.scrollY,
                    autoScroll: isAutoScrollActive,
                    autoRefresh: isAutoRefreshActive,
                    assignee: selectedAssigneeName,
                    assigneeTab: currentAssigneeTab,
                    assigneeQuery: (document.getElementById('assigneeSearchInput') || {}).value || ''
                }));
            } catch (e) { /* abaikan */ }
        }

        function restoreState() {
            let state = null;
            try { state = JSON.parse(sessionStorage.getItem(STATE_KEY) || 'null'); } catch (e) { }
            if (!state) return;
            try { sessionStorage.removeItem(STATE_KEY); } catch (e) { }

            if (state.autoRefresh === false) {
                isAutoRefreshActive = false;
                document.getElementById('btnAutoRefresh').classList.remove('active');
                renderRefreshLabel();
            }

            const searchInput = document.getElementById('searchFilterInput');
            if (state.assignee && searchInput && !searchInput.value) {
                selectedAssigneeName = state.assignee;
                searchInput.value = state.assignee;
                clientFilterTable(state.assignee);
                markSelectedAssignee(state.assignee);
            }

            if (state.assigneeTab && state.assigneeTab !== 'all') {
                const tabs = document.querySelectorAll('.btn-assignee-tab');
                const map = { all: 0, pegawai: 1, katim: 2 };
                switchAssigneeTab(state.assigneeTab, tabs[map[state.assigneeTab]]);
            }
            if (state.assigneeQuery) {
                const input = document.getElementById('assigneeSearchInput');
                if (input) { input.value = state.assigneeQuery; filterAssigneeRows(state.assigneeQuery); }
            }

            if (scrollContainer) scrollContainer.scrollTop = state.tableScroll || 0;
            if (assigneeContainer) assigneeContainer.scrollTop = state.assigneeScroll || 0;
            if (pokjaContainer) pokjaContainer.scrollTop = state.pokjaScroll || 0;
            window.scrollTo(0, state.pageScroll || 0);

            if (state.autoScroll) {
                isAutoScrollActive = true;
                renderScrollLabel();
                startAutoScroll();
                scrollPausedUntil = 0;
            }
        }
        window.addEventListener('DOMContentLoaded', restoreState);

        // 7. Pintasan keyboard: F = layar penuh, S = gulir otomatis, R = segarkan otomatis
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