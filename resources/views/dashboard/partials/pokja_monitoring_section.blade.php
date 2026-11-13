<!-- Section Monitoring Unit Kerja & Pilar Subbag (Interactive Enhanced) -->
<div class="section-card"
    style="margin-top: 10px; border-radius: var(--radius-md); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div class="dash-pokja-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="dash-pokja-icon-badge">
                <i class="ph-duotone ph-tree-structure"></i>
            </div>
            <div>
                <h3 style="font-size: 14.5px; font-weight: 800; color: var(--text-dark); margin: 0; line-height: 1.2;">
                    Struktur Unit Kerja, 6 Pilar Subbag &amp; Satgas RBI
                </h3>
                <p style="font-size: 11.5px; color: var(--text-muted); margin: 2px 0 0 0;">
                    Klik kartu unit kerja untuk melihat rincian personil dan penanggung jawab
                </p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <!-- Live search -->
            <div style="position: relative;">
                <i class="ph-bold ph-magnifying-glass"
                    style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); font-size: 13px; color: var(--text-muted); pointer-events: none;"></i>
                <input type="text" id="pokjaCardSearch" placeholder="Cari unit kerja..."
                    oninput="searchPokjaCards(this.value)"
                    style="font-size: 12px; padding: 6px 10px 6px 30px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none; width: 180px; transition: all 0.2s;">
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('pokjas.index') }}" class="btn-primary"
                    style="padding: 5px 11px; font-size: 11.5px; border-radius: 6px;">
                    <i class="ph-bold ph-gear-six"></i>
                    <span>Kelola Master</span>
                </a>
            @endif
        </div>
    </div>

    <div style="padding: 12px 18px 16px 18px;">
        @php
            $timKerjaCount = $pokjas->where('tipe', 'tim_kerja')->count();
            $pilarSubbagCount = $pokjas->where('tipe', 'subbag_pilar')->count();
            $kelompokKerjaCount = $pokjas->where('tipe', 'kelompok_kerja')->count();
        @endphp

        <!-- Tab Switcher Minimalis / Compact Pills -->
        <div class="dash-pokja-tabs-bar">
            <button type="button" class="dash-mini-tab active" onclick="switchDashboardPokjaTab('all', this)">
                <span>Semua Unit</span>
                <span class="dash-mini-count">{{ $pokjas->count() }}</span>
            </button>
            <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('tim_kerja', this)">
                <i class="ph-bold ph-buildings" style="font-size: 12px;"></i>
                <span>Tim Kerja &amp; TU Balai</span>
                <span class="dash-mini-count">{{ $timKerjaCount }}</span>
            </button>
            <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('subbag_pilar', this)">
                <i class="ph-bold ph-gear-six" style="font-size: 12px;"></i>
                <span>6 Pilar Operasional Subbag</span>
                <span class="dash-mini-count">{{ $pilarSubbagCount }}</span>
            </button>
            <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('kelompok_kerja', this)">
                <i class="ph-bold ph-lightning" style="font-size: 12px;"></i>
                <span>Satgas Ad Hoc RBI</span>
                <span class="dash-mini-count">{{ $kelompokKerjaCount }}</span>
            </button>
            @if(isset($bebanKerjaPegawai) && $bebanKerjaPegawai->isNotEmpty())
                <button type="button" class="dash-mini-tab" onclick="switchDashboardPokjaTab('beban_kerja', this)"
                    style="border-color: rgba(37,99,235,0.3); color: var(--primary); background: rgba(37,99,235,0.06);">
                    <i class="ph-bold ph-chart-donut" style="font-size: 12px;"></i>
                    <span>Beban Kerja Pegawai &amp; Katim</span>
                    <span class="dash-mini-count"
                        style="background: var(--primary); color: white;">{{ $bebanKerjaPegawai->count() }}</span>
                </button>
            @endif
        </div>

        <!-- Grid Cards Interaktif -->
        <div class="dash-pokja-compact-grid" id="dashboardPokjaGrid">
            @foreach($pokjas as $pIdx => $p)
                @php
                    $isSubbag = ($p->kode_pokja === 'SUBBAG-UMUM');
                    $isPilar = ($p->tipe === 'subbag_pilar');
                    $isAdhoc = ($p->tipe === 'kelompok_kerja');

                    $themeClass = $isSubbag ? 'type-subbag' : ($isPilar ? 'type-pilar' : ($isAdhoc ? 'type-adhoc' : 'type-timker'));
                    $typeBadgeText = $isSubbag ? 'TU Balai' : ($isPilar ? 'Pilar Subbag' : ($isAdhoc ? 'Satgas RBI' : 'Tim Kerja'));
                    $leaderRoleText = $isSubbag ? 'Kasubag' : ($isPilar ? 'PIC Pilar' : ($isAdhoc ? 'Ketua Satgas' : 'Katim'));
                    $leaderName = $p->katim?->name ?? 'Belum Ditunjuk';

                    // Hitung progress pekerjaan
                    $totalTugas = $p->pekerjaan ? $p->pekerjaan->count() : 0;
                    $selesaiTugas = $p->pekerjaan ? $p->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count() : 0;
                    $avgProgres = $totalTugas > 0 ? round($p->pekerjaan->avg('progres'), 0) : 0;
                    $progressPct = $avgProgres;

                    $progressColor = $progressPct >= 75 ? '#10b981' : ($progressPct >= 40 ? '#f59e0b' : '#3b82f6');
                    $progressBg = $progressPct >= 75 ? 'rgba(16,185,129,0.12)' : ($progressPct >= 40 ? 'rgba(245,158,11,0.12)' : 'rgba(59,130,246,0.12)');
                @endphp

                <div class="dash-mini-card {{ $themeClass }}" data-tipe="{{ $p->tipe ?? 'tim_kerja' }}"
                    data-name="{{ strtolower($p->nama_pokja) }} {{ strtolower($p->kode_pokja) }}"
                    onclick="openDashMemberModal('{{ $p->id }}')" title="Klik untuk melihat personil {{ $p->nama_pokja }}"
                    style="animation-delay: {{ $pIdx * 0.04 }}s;">

                    <!-- Header mini card: Kode & Tipe Badge -->
                    <div class="dash-mini-card-head">
                        <span class="dash-mini-code">{{ $p->kode_pokja }}</span>
                        <span class="dash-mini-type">{{ $typeBadgeText }}</span>
                    </div>

                    <!-- Judul Unit -->
                    <div class="dash-mini-card-title" title="{{ $p->nama_pokja }}">
                        {{ $p->nama_pokja }}
                    </div>

                    <!-- Progress bar tugas -->
                    @if($totalTugas > 0)
                        <div style="margin-bottom: 6px;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                                <span style="font-size: 9.5px; color: var(--text-muted); font-weight: 600;">Progres Tugas</span>
                                <span
                                    style="font-size: 9.5px; font-weight: 800; color: {{ $progressColor }};">{{ $progressPct }}%</span>
                            </div>
                            <div style="height: 4px; background: rgba(0,0,0,0.07); border-radius: 4px; overflow: hidden;">
                                <div class="dash-progress-bar" data-width="{{ $progressPct }}"
                                    style="height: 100%; width: 0%; background: {{ $progressColor }}; border-radius: 4px; transition: width 1s ease;">
                                </div>
                            </div>
                            <div style="font-size: 9px; color: var(--text-muted); margin-top: 2px;">
                                {{ $selesaiTugas }}/{{ $totalTugas }} tugas selesai
                            </div>
                        </div>
                    @endif

                    <!-- Footer mini card: Penanggung Jawab & Total Personil -->
                    <div class="dash-mini-card-foot">
                        <div class="dash-mini-leader" title="{{ $leaderRoleText }}: {{ $leaderName }}">
                            <i class="ph-bold ph-user" style="font-size: 11px;"></i>
                            <span>{{ $leaderName }}</span>
                        </div>
                        <div class="dash-mini-members" title="Total personil {{ $p->anggota->count() }} orang">
                            <i class="ph-bold ph-users"></i>
                            <span>{{ $p->anggota->count() }}</span>
                        </div>
                    </div>

                    @if($p->anggota && $p->anggota->count() > 0)
                        <div style="font-size: 10px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 4px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                            title="Pegawai: {{ $p->anggota->pluck('name')->implode(', ') }}">
                            <span style="font-weight: 700; color: var(--primary);">Staf:</span>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $p->anggota->take(2)->pluck('name')->implode(', ') }}{{ $p->anggota->count() > 2 ? ' +' . ($p->anggota->count() - 2) : '' }}
                            </span>
                        </div>
                    @endif

                    <!-- Hover detail overlay -->
                    <div class="dash-card-hover-cta">
                        <i class="ph-bold ph-arrow-square-out" style="font-size: 12px;"></i>
                        <span>Lihat Detail</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty state saat search tidak ketemu -->
        <div id="pokjaSearchEmpty" style="display: none; text-align: center; padding: 24px; color: var(--text-muted);">
            <i class="ph-duotone ph-magnifying-glass" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
            <p style="font-size: 13px;">Tidak ada unit kerja yang cocok dengan pencarian.</p>
        </div>

        @if(isset($bebanKerjaPegawai) && $bebanKerjaPegawai->isNotEmpty())
            <!-- Container Pemantauan Beban Kerja -->
            <div id="dashWorkloadContainer" style="display: none; flex-direction: column; gap: 12px; margin-top: 4px;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; background: var(--bg-hover, #f8fafc); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px;">
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        <button type="button" class="dash-mini-tab active" id="btnWorkloadTabAll"
                            onclick="filterDashWorkload('all', this)">
                            Semua ({{ $bebanKerjaPegawai->count() }})
                        </button>
                        <button type="button" class="dash-mini-tab" onclick="filterDashWorkload('pegawai', this)">
                            Pegawai Pelaksana
                            ({{ $bebanKerjaPegawai->filter(fn($u) => in_array($u->role, ['pegawai', 'tim_pokja']))->count() }})
                        </button>
                        <button type="button" class="dash-mini-tab" onclick="filterDashWorkload('katim', this)">
                            Katim &amp; Koor
                            ({{ $bebanKerjaPegawai->filter(fn($u) => in_array($u->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']))->count() }})
                        </button>
                    </div>
                    <div style="max-width: 260px; width: 100%;">
                        <input type="text" id="dashWorkloadSearch" placeholder="Cari nama pegawai / staf..."
                            oninput="searchDashWorkload(this.value)"
                            style="width: 100%; font-size: 12px; padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; background: white; outline: none;">
                    </div>
                </div>

                <div class="dash-pokja-compact-grid" id="dashWorkloadGrid">
                    @foreach($bebanKerjaPegawai as $staf)
                        @php
                            $isKatimOrKoor = in_array($staf->role, ['katim', 'korjafung', 'korlayanan', 'katim_korjafung']);
                            $cat = $isKatimOrKoor ? 'katim' : 'pegawai';
                            $bebanLevel = $staf->total_beban >= 10 ? 'high' : ($staf->total_beban >= 5 ? 'med' : 'low');
                            $bebanColor = $staf->total_beban >= 10 ? '#dc2626' : ($staf->total_beban >= 5 ? '#d97706' : '#059669');
                            $bebanBg = $staf->total_beban >= 10 ? '#fef2f2' : ($staf->total_beban >= 5 ? '#fffbeb' : '#ecfdf5');
                        @endphp
                        <div class="dash-workload-card" data-cat="{{ $cat }}" data-name="{{ strtolower($staf->name) }}"
                            style="background: white; border: 1px solid var(--border); border-radius: 8px; padding: 10px 12px; display: flex; align-items: center; gap: 10px; transition: all 0.18s; cursor: default;">
                            <div
                                style="width: 34px; height: 34px; border-radius: 50%; background: {{ $isKatimOrKoor ? '#2563eb' : '#64748b' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; flex-shrink: 0;">
                                {{ strtoupper(substr($staf->name, 0, 2)) }}
                            </div>
                            <div style="overflow: hidden; flex: 1;">
                                <div style="font-size: 12.5px; font-weight: 700; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                    title="{{ $staf->name }}">
                                    {{ $staf->name }}
                                </div>
                                <div
                                    style="font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $staf->pokja?->kode_pokja ?? 'BGTK' }} &bull; {{ $staf->role_label }}
                                </div>
                                <!-- Mini bar beban kerja -->
                                <div
                                    style="margin-top: 4px; height: 3px; background: rgba(0,0,0,0.08); border-radius: 3px; overflow: hidden; max-width: 120px;">
                                    <div
                                        style="height: 100%; width: {{ min(100, $staf->total_beban * 10) }}%; background: {{ $bebanColor }}; border-radius: 3px;">
                                    </div>
                                </div>
                            </div>
                            <div style="flex-shrink: 0; text-align: right;">
                                <span
                                    style="font-family: monospace; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: {{ $bebanBg }}; color: {{ $bebanColor }}; display: inline-block;">
                                    {{ $staf->total_beban }} Tugas
                                </span>
                                <div style="font-size: 9.5px; color: var(--text-muted); margin-top: 2px;">
                                    {{ $staf->tugas_pic_count }} PIC &bull; {{ $staf->tugas_anggota_count }} Tim
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Container Modal Personil -->
<div id="dashPokjaModalsContainer">
    @foreach($pokjas as $p)
        @php
            $isSubbag = ($p->kode_pokja === 'SUBBAG-UMUM');
            $isPilar = ($p->tipe === 'subbag_pilar');
            $isAdhoc = ($p->tipe === 'kelompok_kerja');

            $typeBadgeText = $isSubbag ? 'TU Balai' : ($isPilar ? 'Pilar Subbag' : ($isAdhoc ? 'Satgas RBI' : 'Tim Kerja'));
            $leaderRoleText = $isSubbag ? 'Kasubag' : ($isPilar ? 'PIC Pilar' : ($isAdhoc ? 'Ketua Satgas' : 'Katim'));
            $leaderName = $p->katim?->name ?? 'Belum Ditunjuk';

            $totalTugasModal = $p->pekerjaan ? $p->pekerjaan->count() : 0;
            $selesaiModal = $p->pekerjaan ? $p->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count() : 0;
            $berjalanModal = $p->pekerjaan ? $p->pekerjaan->whereIn('status_pelaksanaan', ['dikerjakan', 'belum_dimulai'])->count() : 0;
            $avgProgresModal = $totalTugasModal > 0 ? round($p->pekerjaan->avg('progres'), 1) : 0;
        @endphp

        <div id="dashMemberModal_{{ $p->id }}" class="modal-backdrop" onclick="handleBackdropClick(event, '{{ $p->id }}')">
            <div class="modal-card" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                            <span class="dash-mini-code" style="font-size: 10px;">{{ $p->kode_pokja }}</span>
                            <span class="dash-mini-type">{{ $typeBadgeText }}</span>
                        </div>
                        <h4
                            style="font-size: 14.5px; font-weight: 800; color: var(--text-dark); margin: 0; line-height: 1.3;">
                            {{ $p->nama_pokja }}
                        </h4>
                    </div>
                    <button type="button" class="modal-close" onclick="closeDashMemberModal('{{ $p->id }}')"
                        title="Tutup Modal">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <!-- Mini KPI strip di dalam modal -->
                @if($totalTugasModal > 0)
                    <div
                        style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: var(--border); border-top: none; border-bottom: 1px solid var(--border);">
                        <div style="background: white; padding: 10px 14px; text-align: center;">
                            <div style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ $totalTugasModal }}</div>
                            <div style="font-size: 10px; color: var(--text-muted); font-weight: 600;">Total Tugas</div>
                        </div>
                        <div style="background: white; padding: 10px 14px; text-align: center;">
                            <div style="font-size: 18px; font-weight: 900; color: #10b981;">{{ $selesaiModal }}</div>
                            <div style="font-size: 10px; color: var(--text-muted); font-weight: 600;">Selesai</div>
                        </div>
                        <div style="background: white; padding: 10px 14px; text-align: center;">
                            <div style="font-size: 18px; font-weight: 900; color: #f59e0b;">{{ $avgProgresModal }}%</div>
                            <div style="font-size: 10px; color: var(--text-muted); font-weight: 600;">Avg Progres</div>
                        </div>
                    </div>
                    <!-- Progress bar overall -->
                    <div style="padding: 8px 18px; background: white; border-bottom: 1px solid var(--border);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span style="font-size: 10.5px; color: var(--text-muted); font-weight: 600;">Progres Keseluruhan
                                Unit</span>
                            <span
                                style="font-size: 10.5px; font-weight: 800; color: {{ $avgProgresModal >= 75 ? '#10b981' : ($avgProgresModal >= 40 ? '#f59e0b' : '#3b82f6') }};">{{ $avgProgresModal }}%</span>
                        </div>
                        <div style="height: 6px; background: #f1f5f9; border-radius: 6px; overflow: hidden;">
                            <div
                                style="height: 100%; width: {{ $avgProgresModal }}%; background: {{ $avgProgresModal >= 75 ? '#10b981' : ($avgProgresModal >= 40 ? '#f59e0b' : '#3b82f6') }}; border-radius: 6px; transition: width 0.8s ease;">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="modal-body" style="padding: 14px 18px; max-height: 350px; overflow-y: auto;">
                    <!-- Highlight Penanggung Jawab -->
                    <div class="dash-modal-leader-box">
                        <div>
                            <span
                                style="font-size: 11px; color: var(--text-muted); font-weight: 600; display: block;">{{ $leaderRoleText }}:</span>
                            <strong style="font-size: 13px; color: var(--text-dark);">{{ $leaderName }}</strong>
                        </div>
                        <span class="dash-badge-count">{{ $p->anggota->count() }} Pegawai</span>
                    </div>

                    <div
                        style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
                        Daftar Anggota &amp; Personil
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @forelse($p->anggota as $m)
                            @php
                                $isLeader = ($m->pivot->peran ?? '') === 'ketua' || $m->id === $p->katim_id;
                                $bebanMember = ($m->active_pic_count ?? 0) + ($m->active_member_count ?? 0);
                            @endphp
                            <div class="dash-member-row {{ $isLeader ? 'is-leader' : '' }}">
                                <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                    <div class="dash-member-avatar {{ $isLeader ? 'avatar-leader' : '' }}">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <div
                                            style="font-size: 12.5px; font-weight: 700; color: var(--text-dark); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                            {{ $m->name }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            NIP: {{ $m->nip ?? '-' }} &bull; {{ $m->jabatan ?? 'Pegawai' }}
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                    <span
                                        style="font-family: monospace; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: {{ $bebanMember > 0 ? 'rgba(16,185,129,0.12)' : 'rgba(148,163,184,0.12)' }}; color: {{ $bebanMember > 0 ? '#059669' : '#64748b' }};"
                                        title="{{ $m->active_pic_count ?? 0 }} Tugas PIC, {{ $m->active_member_count ?? 0 }} Tugas Tim">
                                        {{ $bebanMember }} Tugas
                                    </span>
                                    @if($isLeader)
                                        <span class="badge-role-leader">
                                            ⭐ {{ $leaderRoleText }}
                                        </span>
                                    @else
                                        <span class="badge-role-member">
                                            Anggota
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div
                                style="text-align: center; padding: 20px; font-size: 12px; color: var(--text-muted); background: var(--bg-hover, #f8fafc); border-radius: 8px; border: 1px dashed var(--border);">
                                Belum ada personil yang terdaftar pada unit ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="modal-footer"
                    style="padding: 10px 18px; display: flex; justify-content: space-between; align-items: center; gap: 8px; border-top: 1px solid var(--border); background: var(--bg-hover, #f8fafc);">
                    <a href="{{ route('pekerjaan.index', ['pokja_id' => $p->id]) }}"
                        style="font-size: 11.5px; font-weight: 700; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="ph-bold ph-list-bullets"></i>
                        Lihat Daftar Pekerjaan
                    </a>
                    <div style="display: flex; gap: 8px;">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('pokjas.index') }}" class="btn-primary"
                                style="font-size: 11.5px; padding: 5px 11px;">
                                <i class="ph-bold ph-pencil-simple"></i>
                                <span>Master Pokja</span>
                            </a>
                        @endif
                        <button type="button" class="btn-manage-members" style="font-size: 11.5px; padding: 5px 14px;"
                            onclick="closeDashMemberModal('{{ $p->id }}')">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    /* Section Header */
    .dash-pokja-header {
        padding: 12px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .dash-pokja-icon-badge {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    /* Tabs Bar Mini */
    .dash-pokja-tabs-bar {
        display: flex;
        gap: 6px;
        margin-bottom: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .dash-mini-tab {
        background: var(--bg-hover, #f1f5f9);
        color: var(--text-muted);
        border: 1px solid var(--border);
        padding: 4px 10px;
        border-radius: 16px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition);
        white-space: nowrap;
    }

    .dash-mini-tab:hover {
        background: #e2e8f0;
        color: var(--primary);
    }

    .dash-mini-tab.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    .dash-mini-count {
        background: rgba(0, 0, 0, 0.08);
        padding: 0 5px;
        border-radius: 10px;
        font-size: 10.5px;
        line-height: 1.4;
    }

    .dash-mini-tab.active .dash-mini-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Grid Layout Kompak */
    .dash-pokja-compact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(215px, 1fr));
        gap: 10px;
    }

    /* Mini Card dengan Stagger Entrance */
    .dash-mini-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        min-height: 90px;
        position: relative;
        overflow: hidden;
        animation: cardSlideIn 0.4s ease both;
    }

    @keyframes cardSlideIn {
        from {
            opacity: 0;
            transform: translateY(16px) scale(0.97);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .dash-mini-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.04) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .dash-mini-card:hover::before {
        opacity: 1;
    }

    .dash-mini-card:hover {
        transform: translateY(-4px) scale(1.01);
        border-color: var(--primary);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.14), 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    .dash-mini-card:active {
        transform: translateY(-1px) scale(0.995);
    }

    /* Hover CTA */
    .dash-card-hover-cta {
        position: absolute;
        bottom: 7px;
        right: 10px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 9.5px;
        font-weight: 700;
        color: var(--primary);
        opacity: 0;
        transform: translateX(4px);
        transition: all 0.18s;
        pointer-events: none;
    }

    .dash-mini-card:hover .dash-card-hover-cta {
        opacity: 1;
        transform: translateX(0);
    }

    .dash-mini-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .dash-mini-code {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.03em;
        padding: 1.5px 5px;
        border-radius: 4px;
    }

    .dash-mini-type {
        font-size: 9.5px;
        font-weight: 700;
        color: var(--text-muted);
    }

    .dash-mini-card-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1.3;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .dash-mini-card-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 10.5px;
        color: var(--text-muted);
        border-top: 1px dashed var(--border);
        padding-top: 5px;
        margin-top: auto;
    }

    .dash-mini-leader {
        display: flex;
        align-items: center;
        gap: 4px;
        max-width: 140px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 600;
    }

    .dash-mini-members {
        display: flex;
        align-items: center;
        gap: 3px;
        font-weight: 700;
        color: var(--primary);
        background: rgba(37, 99, 235, 0.08);
        padding: 1px 5px;
        border-radius: 4px;
    }

    /* Color Accents by Type */
    .type-subbag .dash-mini-code {
        background: #eef2ff;
        color: #4338ca;
    }

    .type-timker .dash-mini-code {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .type-pilar .dash-mini-code {
        background: #f0f9ff;
        color: #0369a1;
    }

    .type-adhoc .dash-mini-code {
        background: #ecfdf5;
        color: #047857;
    }

    .type-subbag:hover {
        border-color: #6366f1;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.14);
    }

    .type-timker:hover {
        border-color: #2563eb;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.14);
    }

    .type-pilar:hover {
        border-color: #0284c7;
        box-shadow: 0 8px 24px rgba(2, 132, 199, 0.14);
    }

    .type-adhoc:hover {
        border-color: #059669;
        box-shadow: 0 8px 24px rgba(5, 150, 105, 0.14);
    }

    .dash-badge-count {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary);
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }

    /* ========================================================
       MODAL STYLES
       ======================================================== */
    .modal-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        display: none !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 99999 !important;
        padding: 16px !important;
    }

    .modal-backdrop.is-open {
        display: flex !important;
    }

    .modal-card {
        background: #ffffff;
        border-radius: 14px;
        width: 100%;
        max-width: 490px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        animation: dashModalZoom 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
        border: 1px solid var(--border);
    }

    @keyframes dashModalZoom {
        from {
            transform: scale(0.9) translateY(20px);
            opacity: 0;
        }

        to {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(to right, rgba(37, 99, 235, 0.03), transparent);
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg-hover, #f8fafc);
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: var(--transition);
        flex-shrink: 0;
    }

    .modal-close:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fca5a5;
    }

    .dash-modal-leader-box {
        background: var(--bg-hover, #f8fafc);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dash-member-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 11px;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: #ffffff;
        transition: background 0.15s;
    }

    .dash-member-row:hover {
        background: #f8fafc;
    }

    .dash-member-row.is-leader {
        border-color: rgba(37, 99, 235, 0.3);
        background: rgba(37, 99, 235, 0.03);
    }

    .dash-member-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #64748b;
        color: #ffffff;
        font-weight: 700;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .dash-member-avatar.avatar-leader {
        background: var(--primary);
    }

    .badge-role-leader {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        background: rgba(37, 99, 235, 0.12);
        color: var(--primary);
        white-space: nowrap;
    }

    .badge-role-member {
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
        white-space: nowrap;
    }

    /* Search input focus */
    #pokjaCardSearch:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* Workload card hover */
    .dash-workload-card:hover {
        background: #f8fafc !important;
        border-color: var(--primary) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    /* Dark Mode Styling */
    [data-theme="dark"] .dash-workload-card {
        background: #151e32 !important;
        border-color: #223049 !important;
    }

    [data-theme="dark"] .dash-workload-card:hover {
        background: #1e293b !important;
    }

    [data-theme="dark"] #dashWorkloadSearch {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    [data-theme="dark"] #pokjaCardSearch {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    [data-theme="dark"] .dash-mini-card {
        background: #151e32 !important;
        border-color: #223049 !important;
    }

    [data-theme="dark"] .dash-mini-card:hover {
        border-color: #60a5fa !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3) !important;
    }

    [data-theme="dark"] .dash-mini-card-title {
        color: #f8fafc !important;
    }

    [data-theme="dark"] .dash-mini-card-foot {
        border-color: #223049 !important;
        color: #94a3b8 !important;
    }

    [data-theme="dark"] .dash-mini-tab {
        background: #151e32 !important;
        color: #94a3b8 !important;
        border-color: #223049 !important;
    }

    [data-theme="dark"] .dash-mini-tab:hover {
        background: #1e293b !important;
        color: #60a5fa !important;
    }

    [data-theme="dark"] .dash-mini-tab.active {
        background: #2563eb !important;
        color: #ffffff !important;
    }

    [data-theme="dark"] .modal-card {
        background: #151e32 !important;
        border-color: #223049 !important;
    }

    [data-theme="dark"] .dash-modal-leader-box {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .dash-member-row {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    [data-theme="dark"] .dash-member-row.is-leader {
        background: rgba(37, 99, 235, 0.2) !important;
        border-color: rgba(96, 165, 250, 0.4) !important;
    }

    [data-theme="dark"] .badge-role-member {
        background: #334155 !important;
        color: #cbd5e1 !important;
    }

    [data-theme="dark"] .modal-close {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #94a3b8 !important;
    }

    [data-theme="dark"] .type-subbag .dash-mini-code {
        background: rgba(99, 102, 241, 0.2);
        color: #a5b4fc;
    }

    [data-theme="dark"] .type-timker .dash-mini-code {
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
    }

    [data-theme="dark"] .type-pilar .dash-mini-code {
        background: rgba(14, 165, 233, 0.2);
        color: #7dd3fc;
    }

    [data-theme="dark"] .type-adhoc .dash-mini-code {
        background: rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
    }

    [data-theme="dark"] .dash-card-hover-cta {
        color: #60a5fa;
    }
</style>

<script>
    // ========== Tab Switcher ==========
    function switchDashboardPokjaTab(type, btn) {
        document.querySelectorAll('.dash-mini-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const pokjaGrid = document.getElementById('dashboardPokjaGrid');
        const workloadContainer = document.getElementById('dashWorkloadContainer');
        const searchEmpty = document.getElementById('pokjaSearchEmpty');

        if (type === 'beban_kerja') {
            if (pokjaGrid) pokjaGrid.style.display = 'none';
            if (workloadContainer) workloadContainer.style.display = 'flex';
            if (searchEmpty) searchEmpty.style.display = 'none';
            return;
        } else {
            if (pokjaGrid) pokjaGrid.style.display = 'grid';
            if (workloadContainer) workloadContainer.style.display = 'none';
        }

        // Clear search
        const searchInput = document.getElementById('pokjaCardSearch');
        if (searchInput) searchInput.value = '';

        const cards = document.querySelectorAll('.dash-mini-card');
        cards.forEach(card => {
            const cardType = card.getAttribute('data-tipe');
            if (type === 'all') {
                card.style.display = '';
            } else if (type === 'tim_kerja') {
                card.style.display = (cardType === 'tim_kerja') ? '' : 'none';
            } else if (type === 'subbag_pilar') {
                card.style.display = (cardType === 'subbag_pilar' || card.getAttribute('data-name').includes('subbag')) ? '' : 'none';
            } else if (type === 'kelompok_kerja') {
                card.style.display = (cardType === 'kelompok_kerja') ? '' : 'none';
            }
        });
        if (searchEmpty) searchEmpty.style.display = 'none';
    }

    // ========== Search Pokja Cards ==========
    function searchPokjaCards(q) {
        const cards = document.querySelectorAll('.dash-mini-card');
        const searchEmpty = document.getElementById('pokjaSearchEmpty');
        const qLower = q.toLowerCase().trim();
        let visible = 0;

        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const show = !qLower || name.includes(qLower);
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (searchEmpty) searchEmpty.style.display = (visible === 0 && qLower) ? 'block' : 'none';
    }

    // ========== Workload Filter ==========
    let currentWorkloadCat = 'all';

    function filterDashWorkload(cat, btn) {
        currentWorkloadCat = cat;
        if (btn) {
            btn.parentElement.querySelectorAll('.dash-mini-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
        applyDashWorkloadFilter();
    }

    function searchDashWorkload(q) {
        applyDashWorkloadFilter();
    }

    function applyDashWorkloadFilter() {
        const searchInput = document.getElementById('dashWorkloadSearch');
        const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const cards = document.querySelectorAll('.dash-workload-card');

        cards.forEach(card => {
            const cat = card.getAttribute('data-cat');
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const matchCat = (currentWorkloadCat === 'all') || (cat === currentWorkloadCat);
            const matchSearch = !q || name.includes(q);
            card.style.display = (matchCat && matchSearch) ? 'flex' : 'none';
        });
    }

    // ========== Modal ==========
    function openDashMemberModal(id) {
        const m = document.getElementById('dashMemberModal_' + id);
        if (m) {
            m.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDashMemberModal(id) {
        const m = document.getElementById('dashMemberModal_' + id);
        if (m) {
            m.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function handleBackdropClick(e, id) {
        if (e.target.id === 'dashMemberModal_' + id) {
            closeDashMemberModal(id);
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.is-open').forEach(m => {
                m.classList.remove('is-open');
            });
            document.body.style.overflow = '';
        }
    });

    // ========== Animate progress bars on load ==========
    document.addEventListener('DOMContentLoaded', function () {
        // Animate progress bars
        setTimeout(() => {
            document.querySelectorAll('.dash-progress-bar').forEach(bar => {
                const target = bar.getAttribute('data-width');
                bar.style.width = target + '%';
            });
        }, 350);
    });
</script>