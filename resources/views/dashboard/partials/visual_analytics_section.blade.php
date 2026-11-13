<!-- Section Analitik Visual & Grafik Kinerja Balai (Formal, Bersih & Interaktif) -->
<div class="analytics-section-wrapper" style="margin-bottom: 24px;">
    <div class="analytics-grid">
        <!-- 1. Donut Chart: Komposisi Status Pelaksanaan Pekerjaan -->
        <div class="analytics-card">
            <div class="analytics-header">
                <div>
                    <h3 class="analytics-title">
                        <i class="ph-bold ph-chart-donut"></i>
                        Status Penugasan & Beban Kerja Balai
                    </h3>
                    <p class="analytics-subtitle">Distribusi status dari seluruh target pekerjaan institusi</p>
                </div>
                <span class="analytics-badge-count">
                    {{ $totalPekerjaanBalai ?? 0 }} Penugasan
                </span>
            </div>

            <div class="analytics-body">
                <div class="donut-chart-container">
                    <canvas id="chartStatusTugas"></canvas>
                    <div class="donut-center-metric">
                        <span class="donut-big-num">{{ $totalPekerjaanBalai ?? 0 }}</span>
                        <span class="donut-sub-lbl">Tugas Total</span>
                    </div>
                </div>

                <div class="analytics-legend-grid">
                    <div class="legend-box" style="border-left-color: #10b981;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-check-circle" style="color: #10b981;"></i> Selesai Terverifikasi</span>
                            <span class="legend-val">{{ $statusCounts['selesai'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['selesai'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>

                    <div class="legend-box" style="border-left-color: #0a6fb5;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-rocket-launch" style="color: #0a6fb5;"></i> Sedang Dikerjakan</span>
                            <span class="legend-val">{{ $statusCounts['dikerjakan'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['dikerjakan'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>

                    <div class="legend-box" style="border-left-color: #8b5cf6;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-seal-check" style="color: #8b5cf6;"></i> Menunggu Verifikasi</span>
                            <span class="legend-val">{{ $statusCounts['menunggu_verifikasi'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['menunggu_verifikasi'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>

                    <div class="legend-box" style="border-left-color: #94a3b8;">
                        <div class="legend-row">
                            <span class="legend-name"><i class="ph-fill ph-clock" style="color: #94a3b8;"></i> Belum Dimulai / Draf</span>
                            <span class="legend-val">{{ $statusCounts['belum_dimulai'] ?? 0 }}</span>
                        </div>
                        <div class="legend-pct">
                            {{ ($totalPekerjaanBalai ?? 0) > 0 ? round((($statusCounts['belum_dimulai'] ?? 0) / $totalPekerjaanBalai) * 100, 0) : 0 }}% dari total
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Bar Chart: Capaian Progres per Tim Kerja / Unit -->
        <div class="analytics-card">
            <div class="analytics-header">
                <div>
                    <h3 class="analytics-title">
                        <i class="ph-bold ph-chart-bar"></i>
                        Capaian Progres per Tim Kerja & Unit
                    </h3>
                    <p class="analytics-subtitle">Rata-rata persentase realisasi capaian unit kerja</p>
                </div>
                <span class="analytics-badge-avg">
                    <i class="ph-bold ph-trend-up"></i> Balai: {{ $avgProgresBalai ?? 0 }}%
                </span>
            </div>

            <div class="analytics-body" style="flex-direction: column; justify-content: space-between;">
                <div style="height: 185px; width: 100%; position: relative;">
                    <canvas id="chartPokjaProgres"></canvas>
                </div>

                <!-- Footer Indikator Cepat -->
                <div class="analytics-mini-stats">
                    <div class="mini-stat-item">
                        <span class="stat-lbl"><i class="ph-bold ph-gauge"></i> Rata-Rata Balai</span>
                        <span class="stat-num" style="color: #0a6fb5;">{{ $avgProgresBalai ?? 0 }}%</span>
                    </div>
                    <div class="mini-stat-item">
                        <span class="stat-lbl"><i class="ph-bold ph-shield-check"></i> Ketepatan Waktu</span>
                        <span class="stat-num" style="color: {{ ($pekerjaanTerlambatCount ?? 0) > 0 ? '#d03232' : '#0c8a5d' }};">
                            {{ ($pekerjaanTerlambatCount ?? 0) > 0 ? $pekerjaanTerlambatCount . ' Terlambat' : '100% On-Track' }}
                        </span>
                    </div>
                    <div class="mini-stat-item">
                        <span class="stat-lbl"><i class="ph-bold ph-check-circle"></i> Selesai Tuntas</span>
                        <span class="stat-num" style="color: #0c8a5d;">{{ $statusCounts['selesai'] ?? 0 }} Tugas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Visual Analytics */
    .analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 980px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    .analytics-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .analytics-card:hover {
        box-shadow: var(--shadow-md);
        border-color: rgba(11, 74, 130, 0.2);
    }

    .analytics-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .analytics-title {
        font-size: 14.5px;
        font-weight: 800;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .analytics-title i {
        color: var(--primary);
        font-size: 18px;
    }

    .analytics-subtitle {
        font-size: 11.5px;
        color: var(--text-muted);
        margin: 2px 0 0 0;
    }

    .analytics-badge-count {
        font-size: 11px;
        font-weight: 700;
        background: var(--primary-soft);
        color: var(--primary-dark);
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .analytics-badge-avg {
        font-size: 11px;
        font-weight: 700;
        background: #e3f6ee;
        color: #0c8a5d;
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .analytics-body {
        padding: 18px 20px;
        display: flex;
        gap: 18px;
        align-items: center;
        flex: 1;
    }

    @media (max-width: 600px) {
        .analytics-body {
            flex-direction: column;
        }
    }

    .donut-chart-container {
        position: relative;
        width: 160px;
        height: 160px;
        flex-shrink: 0;
    }

    .donut-center-metric {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }

    .donut-big-num {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-dark);
        display: block;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    .donut-sub-lbl {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 3px;
        display: block;
    }

    .analytics-legend-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        flex: 1;
    }

    .legend-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 7px 10px;
    }

    .legend-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
    }

    .legend-name {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .legend-val {
        font-size: 13px;
        font-weight: 800;
        color: var(--text-dark);
        font-variant-numeric: tabular-nums;
    }

    .legend-pct {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
    }

    .analytics-mini-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
        margin-top: 10px;
        width: 100%;
    }

    .mini-stat-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .stat-lbl {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .stat-num {
        font-size: 14px;
        font-weight: 800;
        margin-top: 2px;
    }
</style>
