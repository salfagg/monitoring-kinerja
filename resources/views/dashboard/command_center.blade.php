<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoring Balai | TANJIDOR BGTK DKI Jakarta</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <script>
        // Terapkan tema sebelum render agar tidak berkedip
        try {
            const t = localStorage.getItem('bgtkTheme');
            if (t) document.documentElement.setAttribute('data-theme', t);
        } catch (e) { }
    </script>

    <style>
        /* =====================================================
           TOKEN DESAIN
        ===================================================== */
        :root {
            --navy: #0b4a82;
            --navy-deep: #062f57;
            --navy-soft: #0e63b0;
            --primary: #0a6fb5;
            --primary-dark: #085c98;
            --primary-soft: #e6f2fc;
            --primary-glow: rgba(10, 111, 181, .25);
            --cyan: #22b8e8;

            --bg-base: #eef3f9;
            --bg-card: #ffffff;
            --bg-sub: #f6f9fc;
            --bg-hover: #eef6fd;
            --border: #e1e8f0;
            --border-strong: #c9d5e2;

            --text-main: #0d2640;
            --text-body: #374c63;
            --text-muted: #6a7d93;

            --success: #0c8a5d;
            --success-soft: #e0f6ec;
            --warning: #c47c00;
            --warning-soft: #fff1d6;
            --danger: #d03232;
            --danger-soft: #fdeaea;
            --purple: #6f45bd;
            --purple-soft: #efe8fa;

            --shadow-sm: 0 1px 2px rgba(11, 74, 130, .06), 0 1px 3px rgba(11, 74, 130, .04);
            --shadow-md: 0 6px 20px rgba(11, 74, 130, .09);
            --shadow-lg: 0 16px 40px rgba(11, 74, 130, .16);

            --radius: 18px;
            --body-height: calc(100vh - 330px);
        }

        [data-theme="dark"] {
            --primary: #38a3ec;
            --primary-dark: #7cc4f5;
            --primary-soft: rgba(56, 163, 236, .14);
            --primary-glow: rgba(56, 163, 236, .3);

            --bg-base: #08111d;
            --bg-card: #0f1c2d;
            --bg-sub: #0b1726;
            --bg-hover: #142842;
            --border: #1c3149;
            --border-strong: #2a4766;

            --text-main: #eaf2fb;
            --text-body: #b4c4d6;
            --text-muted: #7f95ad;

            --success: #34d399;
            --success-soft: rgba(52, 211, 153, .14);
            --warning: #fbbf24;
            --warning-soft: rgba(251, 191, 36, .14);
            --danger: #f87171;
            --danger-soft: rgba(248, 113, 113, .14);
            --purple: #a78bfa;
            --purple-soft: rgba(167, 139, 250, .16);

            --shadow-sm: 0 1px 2px rgba(0, 0, 0, .4);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, .35);
            --shadow-lg: 0 18px 44px rgba(0, 0, 0, .55);
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
            transition: background-color .3s ease, color .3s ease;
        }

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-strong);
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* Animasi masuk */
        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .rise {
            animation: rise .55s cubic-bezier(.2, .8, .2, 1) both;
        }

        /* =====================================================
           HEADER
        ===================================================== */
        .top-command-bar {
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 12px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            color: #fff;
            background: linear-gradient(180deg, var(--navy) 0%, var(--navy-deep) 100%);
            box-shadow: 0 2px 12px rgba(6, 47, 87, .18);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-badge {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            color: #fff;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .25);
            flex-shrink: 0;
        }

        .brand-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: .02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-sub {
            font-size: 11.5px;
            color: rgba(255, 255, 255, .8);
            font-weight: 500;
            margin-top: 1px;
        }

        .live-tag {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .09em;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .22);
            padding: 2px 9px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .live-tag::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 0 0 rgba(74, 222, 128, .7);
            animation: ping 1.8s infinite;
        }

        @keyframes ping {
            0% {
                box-shadow: 0 0 0 0 rgba(74, 222, 128, .7);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(74, 222, 128, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(74, 222, 128, 0);
            }
        }

        .live-clock-section {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 20px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .22);
            backdrop-filter: blur(10px);
        }

        .clock-time {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: .03em;
        }

        .clock-date {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, .85);
            border-left: 1px solid rgba(255, 255, 255, .28);
            padding-left: 12px;
        }

        .command-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-control {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 13px;
            border-radius: 11px;
            font: 700 11.5px 'Plus Jakarta Sans', sans-serif;
            color: #fff;
            background: rgba(255, 255, 255, .11);
            border: 1px solid rgba(255, 255, 255, .22);
            cursor: pointer;
            text-decoration: none;
            transition: all .2s ease;
        }

        .btn-control:hover {
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .btn-control.active {
            background: #fff;
            color: var(--navy);
            border-color: #fff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .18);
        }

        .btn-icon {
            padding: 8px 10px;
            font-size: 15px;
        }

        .spin-active {
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* =====================================================
           KONTEN UTAMA
        ===================================================== */
        .command-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 20px 24px 28px;
        }

        /* ---------- KPI ---------- */
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
            --accent: #0b4a82;
            --accent2: #0b4a82;
            --accent-soft: rgba(11, 74, 130, .09);
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr auto;
            grid-template-areas: "title icon" "number icon" "sub sub";
            row-gap: 4px;
            align-items: center;
            padding: 15px 16px 13px;
            border-radius: var(--radius);
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease;
            animation: rise .55s cubic-bezier(.2, .8, .2, 1) both;
        }

        .kpi-box:nth-child(1) {
            animation-delay: .00s;
        }

        .kpi-box:nth-child(2) {
            animation-delay: .06s;
        }

        .kpi-box:nth-child(3) {
            animation-delay: .12s;
        }

        .kpi-box:nth-child(4) {
            animation-delay: .18s;
        }

        .kpi-box:nth-child(5) {
            animation-delay: .24s;
        }

        .kpi-box:nth-child(6) {
            animation-delay: .30s;
        }

        .kpi-box:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Semua KPI satu warna navy; merah hanya untuk peringatan keterlambatan */
        .kpi-red {
            --accent: #c23b3b;
            --accent2: #c23b3b;
            --accent-soft: rgba(194, 59, 59, .10);
            border-color: rgba(194, 59, 59, .35);
        }

        [data-theme="dark"] .kpi-box {
            --accent: #7cc4f5;
            --accent2: #7cc4f5;
            --accent-soft: rgba(124, 196, 245, .12);
        }

        [data-theme="dark"] .kpi-red {
            --accent: #f87171;
            --accent2: #f87171;
            --accent-soft: rgba(248, 113, 113, .14);
        }

        .kpi-title {
            grid-area: title;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--text-muted);
            position: relative;
            z-index: 1;
        }

        .kpi-icon-wrap {
            grid-area: icon;
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            color: var(--accent);
            background: var(--accent-soft);
            position: relative;
            z-index: 1;
        }

        .kpi-number {
            grid-area: number;
            font-size: 30px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -.02em;
            font-variant-numeric: tabular-nums;
            color: var(--text-main);
            position: relative;
            z-index: 1;
        }

        .kpi-subtext {
            grid-area: sub;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10.8px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 2px;
            position: relative;
            z-index: 1;
        }

        .kpi-subtext i {
            color: var(--accent);
        }

        .kpi-mini-track {
            flex: 1;
            height: 6px;
            border-radius: 999px;
            background: var(--accent-soft);
            overflow: hidden;
        }

        .kpi-mini-fill {
            height: 100%;
            border-radius: 999px;
            background: var(--accent);
        }

        /* ---------- DIAGRAM ---------- */
        .diagram-section-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: rise .6s .2s cubic-bezier(.2, .8, .2, 1) both;
        }

        .diagram-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
        }

        .diagram-title,
        .panel-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text-main);
        }

        .title-ico {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--primary);
            background: var(--primary-soft);
            flex-shrink: 0;
        }

        .diagram-hint {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .diagram-btn-toggle {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 9px;
            font: 700 11px 'Plus Jakarta Sans', sans-serif;
            color: var(--text-muted);
            background: var(--bg-card);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all .2s ease;
        }

        .diagram-btn-toggle:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        .diagram-content-grid {
            display: grid;
            grid-template-columns: 340px 1fr 250px;
            gap: 20px;
            align-items: center;
            padding: 18px 22px;
        }

        @media (max-width: 1300px) {
            .diagram-content-grid {
                grid-template-columns: 1fr 1fr;
            }

            .diagram-metric-strip {
                grid-column: span 2;
                flex-direction: row !important;
            }

            .diagram-metric-strip>* {
                flex: 1;
            }
        }

        @media (max-width: 800px) {
            .diagram-content-grid {
                grid-template-columns: 1fr;
            }

            .diagram-metric-strip {
                grid-column: span 1;
                flex-direction: column !important;
            }
        }

        .diagram-donut-box {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .donut-canvas-wrap {
            position: relative;
            width: 150px;
            height: 150px;
            flex-shrink: 0;
        }

        .donut-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .donut-num {
            display: block;
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        .donut-lbl {
            display: block;
            margin-top: 3px;
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .donut-legend-mini {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .legend-pill {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            border-radius: 9px;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-body);
            background: var(--bg-sub);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all .18s ease;
        }

        .legend-pill:hover {
            background: var(--bg-hover);
            border-color: var(--primary);
            transform: translateX(3px);
        }

        .legend-pill.static {
            cursor: default;
        }

        .legend-pill.static:hover {
            transform: none;
            border-color: transparent;
            background: var(--bg-sub);
        }

        .legend-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dot-status {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .diagram-bar-box {
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: 150px;
            padding: 0 20px;
            border-left: 1px dashed var(--border-strong);
            border-right: 1px dashed var(--border-strong);
        }

        @media (max-width: 1300px) {
            .diagram-bar-box {
                border-right: none;
            }
        }

        @media (max-width: 800px) {
            .diagram-bar-box {
                border: none;
                padding: 0;
            }
        }

        .bar-title-sub {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11.5px;
            font-weight: 800;
            color: var(--text-main);
        }

        .bar-title-sub i {
            color: var(--primary);
        }

        .diagram-metric-strip {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .cmd-stat-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 9px 13px;
            border-radius: 12px;
            background: var(--bg-sub);
            border: 1px solid var(--border);
            border-left: 3px solid var(--primary);
        }

        .cmd-stat-left {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .cmd-stat-left i {
            color: var(--primary);
            font-size: 14px;
        }

        .cmd-stat-val {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
            white-space: nowrap;
        }

        /* ---------- GRID DUA KOLOM ---------- */
        .command-body-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 16px;
            height: var(--body-height);
            min-height: 540px;
        }

        .left-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
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
                max-height: 380px;
            }

            .table-responsive {
                max-height: 560px;
            }
        }

        .panel-card {
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            animation: rise .6s .3s cubic-bezier(.2, .8, .2, 1) both;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
            flex-shrink: 0;
        }

        .panel-count {
            font-size: 11px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 999px;
            color: var(--primary-dark);
            background: var(--primary-soft);
            white-space: nowrap;
        }

        .scroll-area {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }

        /* ---------- PANEL SAMPING (Tim Kerja / Personil) ---------- */
        .side-switch {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            margin: 14px;
            padding: 4px;
            border-radius: 12px;
            background: var(--bg-sub);
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .side-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 10px;
            border: none;
            border-radius: 9px;
            font: 700 12.5px 'Plus Jakarta Sans', sans-serif;
            color: var(--text-muted);
            background: transparent;
            cursor: pointer;
            transition: all .18s ease;
        }

        .side-tab i {
            font-size: 16px;
        }

        .side-tab:hover {
            color: var(--text-main);
        }

        .side-tab.active {
            color: var(--navy);
            background: var(--bg-card);
            box-shadow: 0 1px 3px rgba(11, 74, 130, .12), 0 0 0 1px var(--border);
        }

        [data-theme="dark"] .side-tab.active {
            color: var(--primary-dark);
        }

        .side-count {
            padding: 1px 8px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 800;
            color: var(--text-body);
            background: var(--border);
        }

        .side-tab.active .side-count {
            color: var(--primary-dark);
            background: var(--primary-soft);
        }

        .side-pane {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
            border-top: 1px solid var(--border);
        }

        .side-pane[hidden] {
            display: none !important;
        }

        /* ---------- TIM KERJA ---------- */
        .pokja-list {
            display: flex;
            flex-direction: column;
        }

        .pokja-item {
            --pc: var(--navy);
            position: relative;
            display: block;
            padding: 11px 16px 12px;
            color: inherit;
            text-decoration: none;
            border-bottom: 1px solid var(--border);
            transition: background .15s ease;
        }

        [data-theme="dark"] .pokja-item {
            --pc: var(--primary);
        }

        .pokja-item:hover {
            background: var(--bg-hover);
        }

        .pokja-item.active {
            background: var(--primary-soft);
        }

        .pokja-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--pc);
        }

        .pokja-item-all {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: var(--bg-sub);
        }

        .pokja-item-all .lbl {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--text-main);
        }

        .pokja-item-all .lbl i {
            font-size: 16px;
            color: var(--primary);
        }

        .pokja-item-all .cnt {
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            color: var(--text-body);
            background: var(--border);
        }

        .pokja-item-all.active .lbl {
            color: var(--navy);
        }

        [data-theme="dark"] .pokja-item-all.active .lbl {
            color: var(--primary-dark);
        }

        .pokja-item-all.active .cnt {
            color: #fff;
            background: var(--navy);
        }

        [data-theme="dark"] .pokja-item-all.active .cnt {
            color: #06223d;
            background: var(--primary);
        }

        .pokja-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 10px;
        }

        .pokja-name-clean {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pokja-percent {
            flex-shrink: 0;
            font: 700 12px 'JetBrains Mono', monospace;
            color: var(--text-main);
        }

        .pokja-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 5px 0 8px;
        }

        .pokja-code-tag {
            flex-shrink: 0;
            padding: 1px 7px;
            border-radius: 5px;
            font: 700 9.5px 'JetBrains Mono', monospace;
            letter-spacing: .03em;
            color: var(--text-body);
            background: var(--bg-card);
            border: 1px solid var(--border-strong);
        }

        .pokja-done-badge {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .pokja-track {
            height: 4px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--border);
        }

        .pokja-fill {
            height: 100%;
            border-radius: 999px;
            background: var(--pc);
            transition: width 1s ease;
        }

        .pokja-item.is-empty .pokja-name-clean {
            color: var(--text-body);
            font-weight: 600;
        }

        .pokja-item.is-empty .pokja-percent {
            color: var(--text-muted);
        }

        /* ---------- BEBAN KERJA ---------- */
        .assignee-tabs {
            display: flex;
            gap: 5px;
            padding: 9px 12px;
            overflow-x: auto;
            flex-shrink: 0;
            background: var(--bg-sub);
            border-bottom: 1px solid var(--border);
        }

        .btn-assignee-tab {
            padding: 5px 11px;
            border-radius: 999px;
            font: 700 11px 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
            color: var(--text-muted);
            background: var(--bg-card);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-assignee-tab:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-assignee-tab.active {
            color: #fff;
            background: var(--navy);
            border-color: var(--navy);
        }

        [data-theme="dark"] .btn-assignee-tab.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #06223d;
        }

        .assignee-search-wrap {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .assignee-search-input,
        .stream-search input {
            font-family: inherit;
            color: var(--text-main);
            outline: none;
        }

        .assignee-search-input {
            width: 100%;
            padding: 7px 14px;
            font-size: 12px;
            border-radius: 999px;
            background: var(--bg-sub);
            border: 1px solid var(--border);
            transition: all .2s;
        }

        .assignee-search-input:focus {
            border-color: var(--primary);
            background: var(--bg-card);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .assignee-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-bottom: 1px solid var(--border);
            border-left: 3px solid transparent;
            cursor: pointer;
            transition: all .15s ease;
        }

        .assignee-item:hover {
            background: var(--bg-hover);
        }

        .assignee-item.selected-assignee {
            background: var(--primary-soft);
            border-left-color: var(--primary);
        }

        .assignee-avatar,
        .pic-avatar-mini,
        .cmd-member-avatar,
        .cmd-pic-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 800;
            color: var(--primary-dark);
            background: var(--primary-soft);
            border-radius: 50%;
        }

        .assignee-avatar {
            width: 32px;
            height: 32px;
            font-size: 11px;
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
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
            font-size: 10.5px;
            color: var(--text-muted);
        }

        .role-pill {
            padding: 1px 6px;
            border-radius: 5px;
            font-size: 9.5px;
            font-weight: 700;
        }

        .role-pill-pegawai {
            background: var(--bg-sub);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .role-pill-katim {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .role-pill-kor {
            background: var(--purple-soft);
            color: var(--purple);
        }

        .assignee-count-badge {
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 800;
            white-space: nowrap;
        }

        .has-workload {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .zero-workload {
            background: var(--bg-sub);
            color: var(--text-muted);
        }

        .assignee-footer {
            padding: 8px 12px;
            font-size: 10.5px;
            text-align: center;
            color: var(--text-muted);
            background: var(--bg-sub);
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        /* ---------- TABEL ---------- */
        .stream-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 11px 16px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .stream-tabs {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            text-decoration: none;
            color: var(--text-body);
            background: var(--bg-sub);
            border: 1px solid var(--border);
            transition: all .2s ease;
        }

        .tab-btn .num {
            padding: 0 7px;
            border-radius: 999px;
            font-size: 10.5px;
            font-variant-numeric: tabular-nums;
            background: var(--border);
        }

        .tab-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-soft);
            transform: translateY(-1px);
        }

        .tab-btn.active {
            color: #fff;
            background: var(--navy);
            border-color: var(--navy);
        }

        .tab-btn.active .num {
            background: rgba(255, 255, 255, .22);
        }

        [data-theme="dark"] .tab-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #06223d;
        }

        [data-theme="dark"] .tab-btn.active .num {
            background: rgba(6, 34, 61, .2);
        }

        .tab-btn.tab-danger {
            color: var(--danger);
            background: var(--bg-sub);
            border-color: var(--border);
        }

        .tab-btn.tab-danger .num {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .tab-btn.tab-danger.active {
            color: #fff;
            background: var(--danger);
            border-color: var(--danger);
        }

        .tab-btn.tab-danger.active .num {
            background: rgba(255, 255, 255, .25);
            color: #fff;
        }

        .tab-btn.tab-danger.active .num {
            background: rgba(255, 255, 255, .28);
        }

        .stream-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            background: var(--bg-sub);
            border: 1px solid var(--border);
            transition: all .2s ease;
        }

        .stream-search:focus-within {
            border-color: var(--primary);
            background: var(--bg-card);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .stream-search i {
            font-size: 14px;
            color: var(--text-muted);
        }

        .stream-search input {
            width: 230px;
            font-size: 12px;
            background: transparent;
            border: none;
        }

        .btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
            text-decoration: none;
            color: var(--danger);
            background: var(--danger-soft);
            transition: all .2s ease;
        }

        .btn-reset:hover {
            color: #fff;
            background: var(--danger);
        }

        .table-responsive {
            flex: 1 1 auto;
            min-height: 0;
            overflow: auto;
        }

        .command-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
        }

        .command-table th {
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 11px 14px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            white-space: nowrap;
            color: var(--text-muted);
            background: var(--bg-sub);
            border-bottom: 2px solid var(--border-strong);
        }

        .command-table td {
            padding: 11px 14px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            color: var(--text-body);
            transition: background .15s ease;
        }

        .command-table tbody tr {
            cursor: pointer;
        }

        .command-table tbody tr:hover td {
            background: var(--bg-hover);
        }

        .command-table tbody tr:hover td:first-child {
            box-shadow: inset 4px 0 0 var(--primary);
        }

        .command-table tbody tr.row-late td:first-child {
            box-shadow: inset 3px 0 0 var(--danger);
        }

        .task-code {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font: 700 11px 'JetBrains Mono', monospace;
            white-space: nowrap;
            color: var(--primary-dark);
            background: var(--primary-soft);
        }

        .task-title-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: 340px;
        }

        .task-title-text {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.4;
            color: var(--text-main);
        }

        .st-badge-mini {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            flex-shrink: 0;
            padding: 2px 7px;
            border-radius: 5px;
            font-size: 9.5px;
            font-weight: 800;
            white-space: nowrap;
            color: #0284c7;
            background: rgba(34, 184, 232, .16);
        }

        .task-pokja-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 800;
            white-space: nowrap;
            color: var(--text-body);
            background: var(--bg-sub);
            border: 1px solid var(--border-strong);
        }

        .pic-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: 210px;
        }

        .pic-avatar-mini {
            width: 28px;
            height: 28px;
            font-size: 10px;
        }

        .pic-name-clean {
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-main);
        }

        .pic-team-badge {
            padding: 1px 6px;
            border-radius: 5px;
            font-size: 9.5px;
            font-weight: 800;
            white-space: nowrap;
            color: var(--success);
            background: var(--success-soft);
        }

        .due-date-clean {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font: 600 11.5px 'JetBrains Mono', monospace;
            white-space: nowrap;
        }

        .due-date-clean.is-late {
            color: var(--danger);
            font-weight: 700;
        }

        .late-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--danger);
        }

        .progress-meter-clean {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 105px;
        }

        .progress-track-clean {
            flex: 1;
            height: 8px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--border);
        }

        .progress-fill-clean {
            height: 100%;
            border-radius: 999px;
        }

        .progress-num-clean {
            min-width: 36px;
            text-align: right;
            font: 700 11.5px 'JetBrains Mono', monospace;
            color: var(--text-main);
        }

        .status-badge-cmd {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
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
            background: var(--bg-sub);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .btn-detail-cmd {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 9px;
            font: 700 11px 'Plus Jakarta Sans', sans-serif;
            color: var(--primary-dark);
            background: var(--bg-card);
            border: 1px solid var(--border-strong);
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-detail-cmd:hover {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .empty-state {
            padding: 60px 20px !important;
            text-align: center;
            color: var(--text-muted);
            font-weight: 600;
            cursor: default;
        }

        .empty-state i {
            display: block;
            margin-bottom: 8px;
            font-size: 42px;
            opacity: .6;
        }

        /* ---------- MODAL ---------- */
        .cmd-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
            background: rgba(6, 25, 45, .68);
            backdrop-filter: blur(6px);
        }

        .cmd-modal-backdrop.show {
            display: flex;
            animation: fadeIn .2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .cmd-modal-dialog {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 780px;
            max-height: 90vh;
            overflow: hidden;
            border-radius: 22px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: 0 28px 60px rgba(0, 0, 0, .35);
            animation: pop .3s cubic-bezier(.16, 1, .3, 1);
        }

        @keyframes pop {
            from {
                transform: translateY(24px) scale(.97);
                opacity: 0;
            }

            to {
                transform: none;
                opacity: 1;
            }
        }

        .cmd-modal-header {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 22px 26px;
            color: #fff;
            background: var(--navy);
        }

        .cmd-modal-badges {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .cmd-modal-title {
            font-size: 19px;
            font-weight: 800;
            line-height: 1.4;
        }

        .cmd-modal-parent {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 7px;
            font-size: 12px;
            color: #bae6fd;
        }

        .cmd-modal-close {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 50%;
            font-size: 18px;
            color: #fff;
            background: rgba(255, 255, 255, .16);
            cursor: pointer;
            transition: all .25s ease;
        }

        .cmd-modal-close:hover {
            background: rgba(255, 255, 255, .32);
            transform: rotate(90deg);
        }

        .cmd-modal-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 20px 26px;
            overflow-y: auto;
            background: var(--bg-sub);
        }

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
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .cmd-kpi-lbl {
            margin-bottom: 4px;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .cmd-kpi-val {
            font-size: 19px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            color: var(--text-main);
        }

        .cmd-kpi-sub {
            margin-top: 2px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .cmd-kpi-track {
            height: 6px;
            margin-top: 7px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--border);
        }

        .cmd-kpi-fill {
            height: 100%;
            border-radius: 999px;
            transition: width .7s ease;
        }

        .cmd-modal-box {
            padding: 16px;
            border-radius: 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .cmd-box-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 11px;
            padding-bottom: 9px;
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
        }

        .cmd-box-title i {
            color: var(--primary);
            font-size: 17px;
        }

        .cmd-box-desc {
            font-size: 13px;
            line-height: 1.65;
            color: var(--text-body);
        }

        .cmd-box-expected {
            margin-top: 11px;
            padding: 10px 13px;
            border-radius: 10px;
            font-size: 12.5px;
            color: var(--success);
            background: var(--success-soft);
            border: 1px solid rgba(12, 138, 93, .25);
        }

        .cmd-pic-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 14px;
            border-radius: 14px;
            background: var(--bg-sub);
            border: 1px solid var(--border);
        }

        .cmd-pic-avatar {
            width: 46px;
            height: 46px;
            font-size: 16px;
            background: var(--navy);
        }

        .cmd-pic-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
        }

        .cmd-pic-nip {
            margin-top: 2px;
            font: 500 12px 'JetBrains Mono', monospace;
            color: var(--text-muted);
        }

        .cmd-pic-jabatan {
            margin-top: 2px;
            font-size: 12px;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .cmd-pic-badge {
            position: absolute;
            top: 12px;
            right: 14px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            color: var(--primary-dark);
            background: var(--primary-soft);
        }

        .cmd-team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 9px;
        }

        .cmd-team-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .cmd-team-chip {
            padding: 2px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            color: var(--text-body);
            background: var(--border);
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
            gap: 9px;
            padding: 8px 10px;
            border-radius: 10px;
            background: var(--bg-sub);
            border: 1px solid var(--border);
        }

        .cmd-member-avatar {
            width: 28px;
            height: 28px;
            font-size: 10px;
            color: var(--primary-dark);
            background: var(--primary-soft);
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

        .cmd-update-card {
            padding: 13px;
            border-radius: 12px;
            background: var(--warning-soft);
            border: 1px solid rgba(196, 124, 0, .25);
        }

        .cmd-update-date {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            font-size: 11.5px;
            font-weight: 800;
            color: var(--warning);
        }

        .cmd-update-note {
            font-size: 12.5px;
            line-height: 1.55;
            color: var(--text-body);
        }

        .cmd-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 26px;
            background: var(--bg-card);
            border-top: 1px solid var(--border);
        }

        .btn-cmd-secondary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 17px;
            border-radius: 11px;
            font: 700 12.5px 'Plus Jakarta Sans', sans-serif;
            color: var(--text-body);
            background: var(--bg-sub);
            border: 1px solid var(--border-strong);
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-cmd-secondary:hover {
            background: var(--border);
        }

        .btn-cmd-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 19px;
            border: none;
            border-radius: 11px;
            font: 700 12.5px 'Plus Jakarta Sans', sans-serif;
            color: #fff;
            text-decoration: none;
            background: var(--navy);
            transition: all .2s ease;
        }

        .btn-cmd-primary:hover {
            background: var(--navy-soft);
        }

        /* ---------- PERAPIHAN LAYOUT ---------- */
        .command-body-grid {
            grid-template-columns: 340px 1fr;
            gap: 18px;
        }

        .assignee-tabs {
            gap: 6px;
            padding: 0 14px 12px;
            overflow-x: auto;
            scrollbar-width: none;
            background: transparent;
            border-bottom: none;
        }

        .assignee-tabs::-webkit-scrollbar {
            display: none;
        }

        .assignee-search-wrap {
            position: relative;
            padding: 0 14px 12px;
            border-bottom: 1px solid var(--border);
        }

        .assignee-search-wrap i {
            position: absolute;
            left: 28px;
            top: 9px;
            font-size: 14px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .assignee-search-input {
            padding: 8px 14px 8px 34px;
        }

        .assignee-item {
            padding: 10px 16px;
        }

        .assignee-footer {
            background: var(--bg-card);
        }

        .stream-controls {
            padding: 14px 20px;
        }

        .command-table th {
            padding: 12px 16px;
        }

        .command-table td {
            padding: 14px 16px;
        }

        .command-table th:first-child,
        .command-table td:first-child {
            padding-left: 20px;
        }

        .command-table th:last-child,
        .command-table td:last-child {
            padding-right: 20px;
        }

        .task-title-text {
            font-size: 13.5px;
            font-weight: 600;
        }

        .pic-team-badge {
            color: var(--text-muted);
            background: var(--bg-sub);
            border: 1px solid var(--border);
        }

        .progress-track-clean {
            height: 6px;
        }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding: 11px 20px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--bg-sub);
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .table-footer b {
            color: var(--text-main);
            font-variant-numeric: tabular-nums;
        }

        .table-footer i {
            margin-right: 4px;
            color: var(--primary);
        }

        /* ---------- MODE TV ---------- */
        body.fullscreen-mode {
            --body-height: calc(100vh - 320px);
        }

        body.fullscreen-mode .top-command-bar {
            padding: 14px 36px;
        }

        body.fullscreen-mode .command-container {
            padding: 18px 36px 30px;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->
    <header class="top-command-bar">
        <div class="brand-section">
            <div class="brand-badge"><i class="ph-bold ph-shield-check"></i></div>
            <div>
                <div class="brand-title">
                    BGTK PROVINSI DKI JAKARTA
                    <span class="live-tag" style="background: rgba(34, 184, 232, 0.2); color: #22b8e8; border: 1px solid rgba(34, 184, 232, 0.35); font-weight: 800; margin-right: 4px;">TANJIDOR</span>
                    <span class="live-tag">LIVE</span>
                </div>
                <div class="brand-sub">TANJIDOR • Tata Kelola Kinerja Digital dan Monitoring</div>
            </div>
        </div>

        <div class="live-clock-section">
            <div class="clock-time" id="liveTimeDisplay">00:00:00 WIB</div>
            <div class="clock-date" id="liveDateDisplay">-</div>
        </div>

        <div class="command-controls">
            <button type="button" class="btn-control active" id="btnToggleDiagram" onclick="toggleDiagramBar()"
                title="Sembunyikan / tampilkan diagram">
                <i class="ph-bold ph-chart-donut"></i>
                <span id="diagramToggleLabel">Diagram: Aktif</span>
            </button>

            <button type="button" class="btn-control active" id="btnAutoRefresh" onclick="toggleAutoRefresh()"
                title="Perbarui data otomatis (tombol R)">
                <i class="ph-bold ph-arrows-clockwise"></i>
                <span id="refreshLabel">Segarkan: 30d</span>
            </button>

            <button type="button" class="btn-control" id="btnAutoScroll" onclick="toggleAutoScroll()"
                title="Gulir otomatis layar TV (tombol S)">
                <i class="ph-bold ph-arrows-down-up"></i>
                <span id="scrollLabel">Gulir TV: Mati</span>
            </button>

            <button type="button" class="btn-control" onclick="toggleFullscreen()" title="Layar penuh (tombol F)">
                <i class="ph-bold ph-corners-out"></i>
                <span id="fsLabel">Layar Penuh</span>
            </button>

            <button type="button" class="btn-control btn-icon" onclick="toggleTheme()"
                title="Ganti tema terang / gelap (tombol T)">
                <i class="ph-bold ph-moon-stars" id="themeIcon"></i>
            </button>

            <a href="{{ route('dashboard') }}" class="btn-control" title="Kembali ke dashboard utama">
                <i class="ph-bold ph-arrow-square-out"></i>
                <span>Portal</span>
            </a>
        </div>
    </header>

    <main class="command-container">

        <!-- ================= KPI ================= -->
        <div class="giant-kpi-grid">
            <div class="kpi-box kpi-blue">
                <span class="kpi-title">Total Tugas</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-tree-structure"></i></div>
                <div class="kpi-number" data-count="{{ $totalPekerjaan }}">{{ number_format($totalPekerjaan) }}</div>
                <div class="kpi-subtext"><i class="ph-bold ph-check-circle"></i> Seluruh penugasan balai</div>
            </div>

            <div class="kpi-box kpi-cyan">
                <span class="kpi-title">Sedang Dikerjakan</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-rocket-launch"></i></div>
                <div class="kpi-number" data-count="{{ $pekerjaanBerjalan }}">{{ number_format($pekerjaanBerjalan) }}
                </div>
                <div class="kpi-subtext"><i class="ph-bold ph-spinner"></i> Berjalan aktif</div>
            </div>

            <div class="kpi-box kpi-purple">
                <span class="kpi-title">Menunggu Verifikasi</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-seal-check"></i></div>
                <div class="kpi-number" data-count="{{ $menungguVerifikasi }}">{{ number_format($menungguVerifikasi) }}
                </div>
                <div class="kpi-subtext"><i class="ph-bold ph-clock"></i> Menunggu tinjauan Katim</div>
            </div>

            <div class="kpi-box kpi-green">
                <span class="kpi-title">Selesai</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-check-fat"></i></div>
                <div class="kpi-number" data-count="{{ $selesaiTerverifikasi }}">
                    {{ number_format($selesaiTerverifikasi) }}</div>
                <div class="kpi-subtext"><i class="ph-bold ph-check"></i> Hasil terverifikasi</div>
            </div>

            <div class="kpi-box kpi-amber">
                <span class="kpi-title">Rata-Rata Capaian</span>
                <div class="kpi-icon-wrap"><i class="ph-duotone ph-chart-line-up"></i></div>
                <div class="kpi-number" data-count="{{ round($avgProgres, 1) }}" data-decimals="1" data-suffix="%">
                    {{ number_format($avgProgres, 1) }}%</div>
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
                <div class="kpi-number" data-count="{{ $lewatTenggat }}">{{ number_format($lewatTenggat) }}</div>
                <div class="kpi-subtext">
                    @if($lewatTenggat > 0)
                        <i class="ph-bold ph-warning"></i> Perlu perhatian khusus
                    @else
                        <i class="ph-bold ph-check"></i> 100% tepat waktu
                    @endif
                </div>
            </div>
        </div>

        <!-- ================= DIAGRAM ================= -->
        <div class="diagram-section-wrapper" id="diagramSectionWrapper">
            <div class="diagram-header-bar">
                <div class="diagram-title">
                    <span class="title-ico"><i class="ph-bold ph-chart-donut"></i></span>
                    <span>Diagram Analitik Kinerja Real-Time Balai</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="diagram-hint">Klik status atau bar untuk menyaring tabel</span>
                    <button type="button" class="diagram-btn-toggle" onclick="toggleDiagramBar()">
                        <i class="ph-bold ph-caret-up" id="diagramToggleIcon"></i> <span
                            id="diagramBtnText">Tutup</span>
                    </button>
                </div>
            </div>

            <div class="diagram-content-grid" id="diagramGridContainer">
                <!-- Donut -->
                <div class="diagram-donut-box">
                    <div class="donut-canvas-wrap">
                        <canvas id="cmdDonutStatus"></canvas>
                        <div class="donut-center-text">
                            <span class="donut-num">{{ $totalPekerjaan }}</span>
                            <span class="donut-lbl">Total Tugas</span>
                        </div>
                    </div>
                    <div class="donut-legend-mini">
                        <div class="legend-pill" onclick="filterTableByStatusClick('selesai')"
                            title="Filter tugas selesai">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#3d9970"></span>Selesai</span>
                            <strong style="color:var(--text-main)">{{ $statusCounts['selesai'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill" onclick="filterTableByStatusClick('berjalan')"
                            title="Filter tugas dikerjakan">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#0b4a82"></span>Dikerjakan</span>
                            <strong style="color:var(--text-main)">{{ $statusCounts['dikerjakan'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill" onclick="filterTableByStatusClick('menunggu_verifikasi')"
                            title="Filter menunggu verifikasi">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#7f9cc0"></span>Verifikasi</span>
                            <strong
                                style="color:var(--text-main)">{{ $statusCounts['menunggu_verifikasi'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill static">
                            <span class="legend-left"><span class="dot-status" style="background:#d3dce7"></span>Belum
                                Dimulai</span>
                            <strong style="color:var(--text-main)">{{ $statusCounts['belum_dimulai'] ?? 0 }}</strong>
                        </div>
                        <div class="legend-pill" onclick="filterTableByStatusClick('terlambat')"
                            title="Filter tugas terlambat">
                            <span class="legend-left"><span class="dot-status"
                                    style="background:#c23b3b"></span>Terlambat</span>
                            <strong style="color:#c23b3b">{{ $lewatTenggat }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Bar -->
                <div class="diagram-bar-box">
                    <div class="bar-title-sub">
                        <span><i class="ph-bold ph-chart-bar"></i> Capaian Progres per Tim Kerja &amp; Unit</span>
                        <span style="color: var(--primary); font-weight: 800;">Balai:
                            {{ number_format($avgProgres, 1) }}%</span>
                    </div>
                    <div style="flex: 1; position: relative; min-height: 0;">
                        <canvas id="cmdBarPokja"></canvas>
                    </div>
                </div>

                <!-- Metrik -->
                <div class="diagram-metric-strip">
                    <div class="cmd-stat-box">
                        <span class="cmd-stat-left"><i class="ph-bold ph-seal-check"></i> Rasio Selesai</span>
                        <span class="cmd-stat-val">
                            {{ $totalPekerjaan > 0 ? round(($selesaiTerverifikasi / $totalPekerjaan) * 100, 0) : 0 }}%
                        </span>
                    </div>
                    <div class="cmd-stat-box">
                        <span class="cmd-stat-left"><i class="ph-bold ph-clock"></i> Ketepatan</span>
                        <span class="cmd-stat-val"
                            style="color: {{ $lewatTenggat > 0 ? '#c23b3b' : 'var(--text-main)' }};">
                            {{ $lewatTenggat > 0 ? $lewatTenggat . ' Tertunda' : '100% On-Track' }}
                        </span>
                    </div>
                    <div class="cmd-stat-box">
                        <span class="cmd-stat-left"><i class="ph-bold ph-buildings"></i> Unit Aktif</span>
                        <span class="cmd-stat-val">{{ $pokjas->count() }} Tim/Pilar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= BODY ================= -->
        <div class="command-body-grid">

            <!-- Kolom kiri -->
            <div class="left-col">

                <div class="panel-card side-card">

                    <!-- Saklar Tim Kerja / Personil -->
                    <div class="side-switch" role="tablist">
                        <button type="button" class="side-tab active" id="sideTabPokja" data-pane="pokja"
                            onclick="switchSidePane('pokja')">
                            <i class="ph-bold ph-buildings"></i> Tim Kerja
                            <span class="side-count">{{ $pokjas->count() }}</span>
                        </button>
                        <button type="button" class="side-tab" id="sideTabStaf" data-pane="staf"
                            onclick="switchSidePane('staf')">
                            <i class="ph-bold ph-users-three"></i> Personil
                            <span class="side-count">{{ $topAssignees->count() }}</span>
                        </button>
                    </div>

                    <!-- Panel: Capaian Tim Kerja -->
                    <div class="side-pane" id="panePokja">
                        <div class="scroll-area" id="pokjaScrollContainer">
                            <div class="pokja-list">
                                <a href="{{ route('monitoring.balai') }}"
                                    class="pokja-item pokja-item-all {{ !request('pokja_id') ? 'active' : '' }}">
                                    <span class="lbl"><i class="ph-bold ph-squares-four"></i> Semua Unit &amp;
                                        Tim</span>
                                    <span class="cnt">{{ $totalPekerjaan }} tugas</span>
                                </a>

                                @foreach($pokjas as $p)
                                    <a href="{{ route('monitoring.balai', array_merge(request()->except(['pokja_id']), ['pokja_id' => $p->id])) }}"
                                        class="pokja-item {{ $p->total_tugas == 0 ? 'is-empty' : '' }} {{ request('pokja_id') == $p->id ? 'active' : '' }}"
                                        title="{{ $p->nama_pokja }} (Katim: {{ $p->katim_name }})">
                                        <div class="pokja-top">
                                            <span class="pokja-name-clean">{{ $p->nama_pokja }}</span>
                                            <span
                                                class="pokja-percent">{{ $p->total_tugas > 0 ? $p->avg_progres . '%' : '–' }}</span>
                                        </div>
                                        <div class="pokja-meta">
                                            <span class="pokja-code-tag">{{ $p->kode_pokja }}</span>
                                            <span class="pokja-done-badge">
                                                {{ $p->total_tugas > 0 ? $p->tugas_selesai . '/' . $p->total_tugas . ' selesai' : 'Belum ada tugas' }}
                                            </span>
                                        </div>
                                        <div class="pokja-track">
                                            <div class="pokja-fill"
                                                style="width: {{ $p->total_tugas > 0 ? $p->avg_progres : 0 }}%;"></div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Panel: Beban Kerja Personil -->
                    <div class="side-pane" id="paneStaf" hidden>
                        <div class="assignee-tabs">
                            <button type="button" class="btn-assignee-tab active"
                                onclick="switchAssigneeTab('all', this)">
                                Semua {{ $topAssignees->count() }}
                            </button>
                            <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('pegawai', this)">
                                Pelaksana
                                {{ $topAssignees->filter(fn($u) => in_array($u->role, ['pegawai', 'tim_pokja']))->count() }}
                            </button>
                            <button type="button" class="btn-assignee-tab" onclick="switchAssigneeTab('katim', this)">
                                Katim &amp; Koor
                                {{ $topAssignees->filter(fn($u) => in_array($u->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']))->count() }}
                            </button>
                        </div>

                        <div class="assignee-search-wrap">
                            <i class="ph-bold ph-magnifying-glass"></i>
                            <input type="text" id="assigneeSearchInput" placeholder="Cari personil..."
                                class="assignee-search-input" oninput="filterAssigneeRows(this.value)">
                        </div>

                        <div class="scroll-area" id="assigneeListContainer">
                            @foreach($topAssignees as $staf)
                                @php
                                    $isKatimOrKoor = in_array($staf->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']);
                                    $roleCategory = $isKatimOrKoor ? 'katim' : 'pegawai';
                                    $roleBadgeClass = $staf->role === 'pegawai' || $staf->role === 'tim_pokja' ? 'role-pill-pegawai' : ($staf->role === 'katim' ? 'role-pill-katim' : 'role-pill-kor');
                                    $avClass = '';
                                @endphp
                                <div class="assignee-item" data-role-category="{{ $roleCategory }}"
                                    data-name="{{ strtolower($staf->name) }}" data-fullname="{{ $staf->name }}"
                                    onclick="filterTableByAssignee(this.dataset.fullname)"
                                    title="Klik untuk menampilkan tugas {{ $staf->name }}">
                                    <div class="assignee-avatar {{ $avClass }}">{{ strtoupper(substr($staf->name, 0, 2)) }}
                                    </div>
                                    <div style="overflow: hidden; flex: 1;">
                                        <div class="assignee-name" title="{{ $staf->name }}">{{ $staf->name }}</div>
                                        <div class="assignee-sub">
                                            <span>{{ $staf->pokja?->kode_pokja ?? 'BGTK' }}</span>
                                            <span class="role-pill {{ $roleBadgeClass }}">{{ $staf->role_label }}</span>
                                        </div>
                                    </div>
                                    <div
                                        class="assignee-count-badge {{ ($staf->pic_count + $staf->member_count) > 0 ? 'has-workload' : 'zero-workload' }}">
                                        {{ $staf->pic_count + $staf->member_count }} tugas
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="assignee-footer">
                            <i class="ph-bold ph-cursor-click"></i> Klik nama untuk menyaring daftar tugas
                        </div>
                    </div>

                </div>

            </div>

            <!-- Kolom kanan: tabel -->
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
                                placeholder="Cari tugas, kode, atau PIC..." onkeyup="clientFilterTable(this.value)">
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
                                <th>Nama Tugas</th>
                                <th style="width: 85px;">Unit</th>
                                <th style="width: 180px;">Penanggung Jawab</th>
                                <th style="width: 115px;">Tenggat</th>
                                <th style="width: 130px;">Progres</th>
                                <th style="width: 140px;">Status</th>
                                <th style="width: 85px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pekerjaanList as $job)
                                @php
                                    $isTerlambat = $job->isTerlambat();
                                    $sisaHari = \Carbon\Carbon::today()->diffInDays($job->tenggat, false);
                                    $progressColor = $job->progres >= 100 ? '#3d9970' : '#0b6bb0';
                                    $rowLate = $isTerlambat && $job->status_pelaksanaan !== 'selesai_terverifikasi';
                                    $picName = $job->penanggungJawab?->name ?? 'PJ';
                                    $picAv = '';

                                    $statusLabel = match ($job->status_pelaksanaan) {
                                        'selesai_terverifikasi' => 'Selesai',
                                        'hasil_diajukan' => 'Menunggu Verifikasi',
                                        'dikerjakan' => 'Dikerjakan',
                                        'perlu_perbaikan_hasil' => 'Perlu Revisi',
                                        default => 'Belum Dimulai'
                                    };
                                    $statusClass = match ($job->status_pelaksanaan) {
                                        'selesai_terverifikasi' => 'st-selesai',
                                        'hasil_diajukan' => 'st-verifikasi',
                                        'dikerjakan' => 'st-berjalan',
                                        'perlu_perbaikan_hasil' => 'st-terlambat',
                                        default => 'st-belum'
                                    };

                                    $payload = [
                                        'id' => $job->id,
                                        'kode' => $job->kode_tampilan,
                                        'judul' => $job->judul,
                                        'deskripsi' => $job->deskripsi ?? 'Belum ada deskripsi rinci untuk pekerjaan ini.',
                                        'hasil_diharapkan' => $job->hasil_diharapkan ?? 'Tercapainya output kegiatan sesuai sasaran.',
                                        'target' => number_format($job->target, 0) . ' ' . $job->satuan,
                                        'bobot' => number_format($job->bobot, 1) . '%',
                                        'progres' => (float) $job->progres,
                                        'tenggat' => $job->tenggat ? $job->tenggat->format('d/m/Y') : '-',
                                        'is_terlambat' => $isTerlambat,
                                        'sisa_hari' => $sisaHari,
                                        'nomor_st' => $job->nomor_st,
                                        'status_raw' => $job->status_pelaksanaan,
                                        'status_label' => $statusLabel,
                                        'status_class' => $statusClass,
                                        'pokja' => $job->pokja ? $job->pokja->kode_pokja . ' - ' . $job->pokja->nama_pokja : 'BGTK DKI Jakarta',
                                        'pokja_code' => $job->pokja?->kode_pokja ?? 'BGTK',
                                        'parent' => $job->parent ? $job->parent->kode_tampilan . ' - ' . $job->parent->judul : null,
                                        'pj_nama' => $job->penanggungJawab?->name ?? 'Belum Ditugaskan',
                                        'pj_nip' => $job->penanggungJawab?->nip ? 'NIP: ' . $job->penanggungJawab->nip : 'Tenaga Alih Daya / Non-PNS',
                                        'pj_jabatan' => $job->penanggungJawab?->jabatan ?? 'Pegawai',
                                        'pj_initial' => strtoupper(substr($picName, 0, 2)),
                                        'pelaksana' => $job->pelaksana->map(fn($p) => [
                                            'name' => $p->name,
                                            'jabatan' => $p->jabatan ?? 'Pegawai',
                                            'initial' => strtoupper(substr($p->name, 0, 2)),
                                        ])->toArray(),
                                        'url_show' => route('pekerjaan.show', $job),
                                        'last_update' => $job->realisasi->first() ? $job->realisasi->first()->created_at->format('d/m/Y H:i') : null,
                                        'last_note' => $job->realisasi->first()?->catatan ?? null,
                                    ];
                                @endphp
                                <tr class="table-row-item {{ $rowLate ? 'row-late' : '' }}"
                                    data-job="{{ json_encode($payload) }}"
                                    title="Klik untuk melihat rincian tugas {{ $job->kode_tampilan }}">
                                    <td><span class="task-code">{{ $job->kode_tampilan }}</span></td>

                                    <td>
                                        <div class="task-title-cell">
                                            <span class="task-title-text" title="{{ $job->judul }}">{{ $job->judul }}</span>
                                            @if($job->nomor_st)
                                                <span class="st-badge-mini" title="Surat Tugas Nomor: {{ $job->nomor_st }}">
                                                    <i class="ph-bold ph-file-text"></i> ST
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td><span class="task-pokja-badge">{{ $job->pokja?->kode_pokja ?? 'BGTK' }}</span></td>

                                    <td>
                                        <div class="pic-cell">
                                            <div class="pic-avatar-mini {{ $picAv }}">
                                                {{ strtoupper(substr($picName, 0, 2)) }}</div>
                                            <div style="overflow: hidden; flex: 1;">
                                                <div class="pic-name-clean"
                                                    title="{{ $job->penanggungJawab->name ?? '-' }}">
                                                    {{ $job->penanggungJawab->name ?? 'Belum ditugaskan' }}
                                                </div>
                                            </div>
                                            @if($job->pelaksana && $job->pelaksana->count() > 1)
                                                <span class="pic-team-badge"
                                                    title="Tim {{ $job->pelaksana->count() }} orang">+{{ $job->pelaksana->count() - 1 }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="due-date-clean {{ $rowLate ? 'is-late' : '' }}">
                                            @if($rowLate) <span class="late-dot" title="Terlambat"></span> @endif
                                            {{ $job->tenggat ? $job->tenggat->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="progress-meter-clean">
                                            <div class="progress-track-clean">
                                                <div class="progress-fill-clean"
                                                    style="width: {{ min(100, $job->progres) }}%; background: {{ $progressColor }};">
                                                </div>
                                            </div>
                                            <span class="progress-num-clean">{{ number_format($job->progres, 0) }}%</span>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="status-badge-cmd {{ $statusClass }}">
                                            @if($job->status_pelaksanaan === 'selesai_terverifikasi')
                                                <i class="ph-bold ph-check-circle"></i>
                                            @elseif($job->status_pelaksanaan === 'hasil_diajukan')
                                                <i class="ph-bold ph-clock"></i>
                                            @elseif($job->status_pelaksanaan === 'dikerjakan')
                                                <i class="ph-bold ph-spinner"></i>
                                            @elseif($job->status_pelaksanaan === 'perlu_perbaikan_hasil')
                                                <i class="ph-bold ph-warning"></i>
                                            @else
                                                <i class="ph-bold ph-circle-dashed"></i>
                                            @endif
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    <td style="text-align: center;">
                                        <button type="button" class="btn-detail-cmd">
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

                <div class="table-footer">
                    <span><i class="ph-bold ph-list-checks"></i>Menampilkan <b id="rowShown">0</b> dari <b
                            id="rowTotal">0</b> tugas</span>
                    <span><i class="ph-bold ph-cursor-click"></i>Klik baris untuk melihat rincian</span>
                </div>

            </div>
        </div>
    </main>

    <!-- ================= MODAL DETAIL ================= -->
    <div id="jobDetailModal" class="cmd-modal-backdrop" onclick="closeJobModal(event)">
        <div class="cmd-modal-dialog" onclick="event.stopPropagation()">
            <div class="cmd-modal-header">
                <div style="flex: 1; overflow: hidden;">
                    <div class="cmd-modal-badges">
                        <span id="mJobCode" class="task-code" style="font-size: 12px; padding: 3px 9px;">-</span>
                        <span id="mJobStatus" class="status-badge-cmd">-</span>
                        <span id="mJobPokja" class="task-pokja-badge" style="background: rgba(255,255,255,.2);">-</span>
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

            <div class="cmd-modal-body">
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

                <div class="cmd-modal-box">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-info"></i> Deskripsi &amp; Sasaran Tugas</h4>
                    <p id="mJobDesc" class="cmd-box-desc"></p>
                    <div class="cmd-box-expected">
                        <strong><i class="ph-bold ph-target"></i> Hasil yang Diharapkan:</strong>
                        <span id="mJobExpected">-</span>
                    </div>
                </div>

                <div class="cmd-modal-box">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-users"></i> Penanggung Jawab &amp; Tim Pelaksana</h4>

                    <div class="cmd-pic-card">
                        <div class="cmd-pic-avatar" id="mJobPicAvatar">PJ</div>
                        <div>
                            <div class="cmd-pic-name" id="mJobPicName">-</div>
                            <div class="cmd-pic-nip" id="mJobPicNip">-</div>
                            <div class="cmd-pic-jabatan" id="mJobPicJabatan">-</div>
                        </div>
                        <span class="cmd-pic-badge">Penanggung Jawab (PIC)</span>
                    </div>

                    <div id="mJobTeamWrap" style="margin-top: 14px;">
                        <div class="cmd-team-header">
                            <span class="cmd-team-title">Personil Pelaksana:</span>
                            <span id="mJobTeamCount" class="cmd-team-chip">0 Anggota</span>
                        </div>
                        <div id="mJobTeamList" class="cmd-team-list"></div>
                    </div>
                </div>

                <div class="cmd-modal-box" id="mJobUpdateWrap" style="display: none;">
                    <h4 class="cmd-box-title"><i class="ph-bold ph-clock-counter-clockwise"></i> Pembaruan Progres
                        Terakhir</h4>
                    <div class="cmd-update-card">
                        <div class="cmd-update-date"><i class="ph-bold ph-calendar-check"></i> <span
                                id="mJobUpdateDate">-</span></div>
                        <div class="cmd-update-note" id="mJobUpdateNote">-</div>
                    </div>
                </div>
            </div>

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

    <script>
        const scrollContainer = document.getElementById('tableScrollContainer');
        const modalBackdrop = document.getElementById('jobDetailModal');

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }

        // ============================================================
        // 1. Tema terang / gelap
        // ============================================================
        function applyThemeIcon() {
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = dark ? 'ph-bold ph-sun' : 'ph-bold ph-moon-stars';
        }

        function toggleTheme() {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            try { localStorage.setItem('bgtkTheme', next); } catch (e) { }
            applyThemeIcon();
            try { switchSidePane(localStorage.getItem('bgtkSidePane') || 'pokja'); } catch (e) { }
            updateRowCount();
            initMonitoringCharts();
        }

        // ============================================================
        // 2. Diagram Chart.js
        // ============================================================
        let chartDonutInstance = null;
        let chartBarInstance = null;

        function cssVar(name) {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        }

        function initMonitoringCharts() {
            const gridColor = cssVar('--border');
            const textMuted = cssVar('--text-muted');
            const textMain = cssVar('--text-body');
            const cardBg = cssVar('--bg-card');

            const ctxDonut = document.getElementById('cmdDonutStatus');
            if (ctxDonut) {
                if (chartDonutInstance) chartDonutInstance.destroy();
                chartDonutInstance = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Dikerjakan', 'Verifikasi', 'Belum Dimulai'],
                        datasets: [{
                            data: [
                                {{ $statusCounts['selesai'] ?? 0 }},
                                {{ $statusCounts['dikerjakan'] ?? 0 }},
                                {{ $statusCounts['menunggu_verifikasi'] ?? 0 }},
                                {{ $statusCounts['belum_dimulai'] ?? 0 }}
                            ],
                            backgroundColor: ['#3d9970', '#0b4a82', '#7f9cc0', '#d3dce7'],
                            borderWidth: 3,
                            borderColor: cardBg,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        animation: { animateRotate: true, duration: 1100 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#062f57',
                                titleFont: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                                cornerRadius: 8,
                                padding: 10,
                                callbacks: {
                                    label: function (ctx) {
                                        const tot = {{ $totalPekerjaan ?? 0 }};
                                        const val = ctx.parsed || 0;
                                        const pct = tot > 0 ? Math.round((val / tot) * 100) : 0;
                                        return ` ${ctx.label}: ${val} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            const ctxBar = document.getElementById('cmdBarPokja');
            if (ctxBar) {
                if (chartBarInstance) chartBarInstance.destroy();

                const pokjaLabels = {!! json_encode(collect($pokjaChartData ?? [])->pluck('kode_pokja')) !!};
                const pokjaNames = {!! json_encode(collect($pokjaChartData ?? [])->pluck('nama_pokja')) !!};
                const pokjaValues = {!! json_encode(collect($pokjaChartData ?? [])->pluck('avg_progres')) !!};
                const pokjaTotals = {!! json_encode(collect($pokjaChartData ?? [])->pluck('total_tugas')) !!};

                const dark = document.documentElement.getAttribute('data-theme') === 'dark';
                const base = dark ? '#4aa8e8' : '#0b4a82';
                const colors = pokjaValues.map(v => v > 0 ? base : gridColor);

                chartBarInstance = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: pokjaLabels,
                        datasets: [{
                            label: 'Progres (%)',
                            data: pokjaValues,
                            backgroundColor: colors,
                            borderRadius: 7,
                            borderSkipped: false,
                            maxBarThickness: 26
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 900 },
                        onClick: (e, elements) => {
                            if (elements && elements.length > 0) {
                                const code = pokjaLabels[elements[0].index];
                                const searchInput = document.getElementById('searchFilterInput');
                                if (searchInput) {
                                    searchInput.value = code;
                                    clientFilterTable(code);
                                }
                            }
                        },
                        onHover: (e, els) => { e.native.target.style.cursor = els.length ? 'pointer' : 'default'; },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#062f57',
                                titleFont: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                                cornerRadius: 8,
                                padding: 10,
                                callbacks: {
                                    title: (items) => `${pokjaLabels[items[0].dataIndex]} - ${pokjaNames[items[0].dataIndex]}`,
                                    label: (c) => ` Capaian: ${c.parsed.y}% (${pokjaTotals[c.dataIndex]} tugas)`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { callback: v => v + '%', font: { family: 'Plus Jakarta Sans', size: 10 }, color: textMuted, stepSize: 50 },
                                grid: { color: gridColor, drawTicks: false },
                                border: { display: false }
                            },
                            x: {
                                ticks: { font: { family: 'JetBrains Mono', size: 9.5, weight: 'bold' }, color: textMain },
                                grid: { display: false },
                                border: { display: false }
                            }
                        }
                    }
                });
            }
        }

        let isDiagramVisible = true;
        function toggleDiagramBar() {
            isDiagramVisible = !isDiagramVisible;
            document.getElementById('diagramGridContainer').style.display = isDiagramVisible ? 'grid' : 'none';
            document.getElementById('diagramToggleIcon').className = isDiagramVisible ? 'ph-bold ph-caret-up' : 'ph-bold ph-caret-down';
            document.getElementById('diagramBtnText').textContent = isDiagramVisible ? 'Tutup' : 'Buka';
            document.getElementById('btnToggleDiagram').classList.toggle('active', isDiagramVisible);
            document.getElementById('diagramToggleLabel').textContent = isDiagramVisible ? 'Diagram: Aktif' : 'Diagram: Tertutup';
            if (isDiagramVisible) { chartDonutInstance?.resize(); chartBarInstance?.resize(); }
        }

        function filterTableByStatusClick(status) {
            window.location.href = "{{ route('monitoring.balai') }}?status=" + status;
        }

        // ============================================================
        // 3. Animasi angka KPI
        // ============================================================
        function animateKpiNumbers() {
            document.querySelectorAll('.kpi-number[data-count]').forEach(el => {
                const target = parseFloat(el.dataset.count) || 0;
                const dec = parseInt(el.dataset.decimals || '0', 10);
                const suffix = el.dataset.suffix || '';
                const fmt = v => v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }) + suffix;
                const start = performance.now();
                const dur = 900;
                (function tick(now) {
                    const p = Math.min(1, (now - start) / dur);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = fmt(target * eased);
                    if (p < 1) requestAnimationFrame(tick);
                })(start);
            });
        }

        // ============================================================
        // 4. Modal detail tugas
        // ============================================================
        let isModalOpen = false;

        function openJobModal(job) {
            if (!job) return;
            isModalOpen = true;

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

            document.getElementById('mJobTitle').textContent = job.judul || '-';
            const parentWrap = document.getElementById('mJobParentWrap');
            if (job.parent) {
                document.getElementById('mJobParent').textContent = job.parent;
                parentWrap.style.display = 'flex';
            } else {
                parentWrap.style.display = 'none';
            }

            const prog = Math.min(100, Math.max(0, job.progres || 0));
            document.getElementById('mJobProgres').textContent = prog + '%';
            const barEl = document.getElementById('mJobProgresBar');
            barEl.style.width = '0%';
            barEl.style.background = prog >= 100 ? '#3d9970' : '#0b6bb0';
            requestAnimationFrame(() => requestAnimationFrame(() => { barEl.style.width = prog + '%'; }));

            document.getElementById('mJobTarget').textContent = job.target || '-';
            document.getElementById('mJobBobot').textContent = job.bobot || '-';
            document.getElementById('mJobTenggat').textContent = job.tenggat || '-';

            const sisaEl = document.getElementById('mJobSisaHari');
            if (job.status_raw === 'selesai_terverifikasi') {
                sisaEl.textContent = 'Selesai diverifikasi';
                sisaEl.style.color = 'var(--success)';
            } else if (job.is_terlambat) {
                sisaEl.textContent = 'Terlambat ' + Math.abs(job.sisa_hari) + ' hari';
                sisaEl.style.color = 'var(--danger)';
            } else if (job.sisa_hari !== null && job.sisa_hari >= 0) {
                sisaEl.textContent = 'Sisa ' + job.sisa_hari + ' hari lagi';
                sisaEl.style.color = '';
            } else {
                sisaEl.textContent = '-';
            }

            document.getElementById('mJobDesc').textContent = job.deskripsi || '-';
            document.getElementById('mJobExpected').textContent = job.hasil_diharapkan || '-';

            document.getElementById('mJobPicAvatar').textContent = job.pj_initial || 'PJ';
            document.getElementById('mJobPicName').textContent = job.pj_nama || '-';
            document.getElementById('mJobPicNip').textContent = job.pj_nip || '-';
            document.getElementById('mJobPicJabatan').textContent = job.pj_jabatan || '-';

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
                        <div class="cmd-member-avatar">${escapeHtml(m.initial || 'PL')}</div>
                        <div style="overflow:hidden;flex:1;">
                            <div class="cmd-member-name" title="${escapeHtml(m.name)}">${escapeHtml(m.name)}</div>
                            <div class="cmd-member-jab" title="${escapeHtml(m.jabatan)}">${escapeHtml(m.jabatan)}</div>
                        </div>`;
                    teamList.appendChild(item);
                });
            } else {
                teamWrap.style.display = 'none';
            }

            const updateWrap = document.getElementById('mJobUpdateWrap');
            if (job.last_update) {
                updateWrap.style.display = 'block';
                document.getElementById('mJobUpdateDate').textContent = 'Diperbarui pada: ' + job.last_update;
                document.getElementById('mJobUpdateNote').textContent = job.last_note ? `"${job.last_note}"` : 'Progres berhasil diperbarui ke sistem.';
            } else {
                updateWrap.style.display = 'none';
            }

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
            if (event.target === modalBackdrop) closeJobModalDirect();
        }

        // Klik baris tabel (delegasi, tetap bekerja setelah auto-refresh)
        document.getElementById('commandTable').addEventListener('click', (e) => {
            const row = e.target.closest('tr.table-row-item');
            if (!row || !row.dataset.job) return;
            try { openJobModal(JSON.parse(row.dataset.job)); } catch (err) { console.warn('Data tugas tidak valid', err); }
        });

        // ============================================================
        // 5. Jam digital
        // ============================================================
        function updateLiveClock() {
            const now = new Date();
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('liveTimeDisplay').textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB`;
            document.getElementById('liveDateDisplay').textContent =
                now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // ============================================================
        // 6. Segarkan otomatis
        // ============================================================
        const REFRESH_INTERVAL = 30;
        let refreshSecondsLeft = REFRESH_INTERVAL;
        let isAutoRefreshActive = true;
        let isRefreshing = false;

        function renderRefreshLabel() {
            const label = document.getElementById('refreshLabel');
            if (!label) return;
            if (isRefreshing) label.textContent = 'Menyinkron...';
            else if (!isAutoRefreshActive) label.textContent = 'Segarkan: Jeda';
            else label.textContent = `Segarkan: ${refreshSecondsLeft}d`;
        }

        function swapSection(doc, selector, keepScroll) {
            const next = doc.querySelector(selector);
            const cur = document.querySelector(selector);
            if (!next || !cur) return;
            const prev = keepScroll ? cur.scrollTop : 0;
            cur.innerHTML = next.innerHTML;
            if (keepScroll) cur.scrollTop = prev;
        }

        async function refreshDashboardData() {
            if (isRefreshing || isModalOpen) return;
            isRefreshing = true;
            renderRefreshLabel();

            const refreshIcon = document.querySelector('#btnAutoRefresh i');
            if (refreshIcon) refreshIcon.classList.add('spin-active');

            try {
                const response = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (response.ok) {
                    const doc = new DOMParser().parseFromString(await response.text(), 'text/html');

                    swapSection(doc, '.giant-kpi-grid', false);
                    swapSection(doc, '#pokjaScrollContainer', true);
                    swapSection(doc, '#assigneeListContainer', true);
                    swapSection(doc, '.stream-tabs', false);
                    swapSection(doc, '#commandTable tbody', false);

                    filterAssigneeRows();
                    if (selectedAssigneeName) markSelectedAssignee(selectedAssigneeName);
                    updateRowCount();

                    const searchInput = document.getElementById('searchFilterInput');
                    if (searchInput && searchInput.value) clientFilterTable(searchInput.value);
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
            if (refreshSecondsLeft <= 0) refreshDashboardData();
        }
        setInterval(runRefreshCountdown, 1000);

        function toggleAutoRefresh() {
            isAutoRefreshActive = !isAutoRefreshActive;
            document.getElementById('btnAutoRefresh').classList.toggle('active', isAutoRefreshActive);
            refreshSecondsLeft = REFRESH_INTERVAL;
            renderRefreshLabel();
        }

        // ============================================================
        // 7. Layar penuh & gulir otomatis TV
        // ============================================================
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    document.body.classList.add('fullscreen-mode');
                    document.getElementById('fsLabel').textContent = 'Keluar Layar';
                }).catch(err => alert('Tidak dapat membuka layar penuh: ' + err.message));
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

        let isAutoScrollActive = false;
        let scrollTimer = null;
        let scrollPausedUntil = 0;
        let reachedBottom = false;

        function renderScrollLabel() {
            document.getElementById('scrollLabel').textContent = 'Gulir TV: ' + (isAutoScrollActive ? 'Hidup' : 'Mati');
            document.getElementById('btnAutoScroll').classList.toggle('active', isAutoScrollActive);
        }

        function toggleAutoScroll() {
            isAutoScrollActive = !isAutoScrollActive;
            renderScrollLabel();
            if (isAutoScrollActive) startAutoScroll();
            else clearInterval(scrollTimer);
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

        // ============================================================
        // 8. Filter cepat
        // ============================================================
        function clientFilterTable(query) {
            query = (query || '').toLowerCase().trim();
            document.querySelectorAll('.table-row-item').forEach(row => {
                row.style.display = (!query || row.textContent.toLowerCase().includes(query)) ? '' : 'none';
            });
            updateRowCount();
        }

        function updateRowCount() {
            const rows = Array.from(document.querySelectorAll('.table-row-item'));
            const shown = rows.filter(r => r.style.display !== 'none').length;
            const a = document.getElementById('rowShown');
            const b = document.getElementById('rowTotal');
            if (a) a.textContent = shown;
            if (b) b.textContent = rows.length;
        }

        // Saklar panel kiri: Tim Kerja / Personil
        function switchSidePane(name) {
            const isPokja = name !== 'staf';
            document.getElementById('panePokja').hidden = !isPokja;
            document.getElementById('paneStaf').hidden = isPokja;
            document.getElementById('sideTabPokja').classList.toggle('active', isPokja);
            document.getElementById('sideTabStaf').classList.toggle('active', !isPokja);
            try { localStorage.setItem('bgtkSidePane', isPokja ? 'pokja' : 'staf'); } catch (e) { }
        }

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
                const matchTab = currentAssigneeTab === 'all' || cat === currentAssigneeTab;
                item.style.display = (matchTab && (!q || name.includes(q))) ? 'flex' : 'none';
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

        // ============================================================
        // 9. Pintasan keyboard: F, S, R, T, Esc
        // ============================================================
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpen) { closeJobModalDirect(); return; }
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            const k = e.key.toLowerCase();
            if (k === 'f') toggleFullscreen();
            else if (k === 's') toggleAutoScroll();
            else if (k === 'r') toggleAutoRefresh();
            else if (k === 't') toggleTheme();
        });

        document.addEventListener('DOMContentLoaded', function () {
            applyThemeIcon();
            initMonitoringCharts();
            animateKpiNumbers();
        });
    </script>
</body>

</html>