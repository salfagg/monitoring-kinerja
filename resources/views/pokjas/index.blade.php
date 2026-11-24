@extends('layouts.app')

@section('title', 'Kelola Pokja & Pilar Subbag Umum | TANJIDOR')
@section('page-title', 'Kelola Master Pokja & Struktur Unit Kerja')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/master-data.css') }}">
@endsection

@section('content')

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: #065f46; font-size: 13.5px; font-weight: 600;">
            <i class="ph-bold ph-check-circle" style="font-size: 20px; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Page Header -->
    <div class="pokja-page-header">
        <div>
            <h2>Master Kelompok Kerja (Pokja) &amp; Pilar Subbag Umum</h2>
            <p>Kelola struktur tim kerja teknis, 6 pilar operasional tata usaha, satuan tugas ad hoc RBI, dan pembagian personil.</p>
        </div>
        <a href="{{ route('pokjas.create') }}" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; padding: 9px 18px; border-radius: 8px;">
            <i class="ph-bold ph-plus-circle" style="font-size: 16px;"></i>
            <span>Tambah Pokja / Pilar Baru</span>
        </a>
    </div>

    @php
        $timKerjaCount = $pokjas->where('tipe', 'tim_kerja')->count();
        $pilarSubbagCount = $pokjas->where('tipe', 'subbag_pilar')->count();
        $kelompokKerjaCount = $pokjas->where('tipe', 'kelompok_kerja')->count();
        $totalPersonilTerdaftar = $pokjas->pluck('anggota')->flatten()->unique('id')->count();
    @endphp

    <!-- 2. KPI Summary Cards Strip -->
    <div class="pokja-kpi-strip">
        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #e8f2fb; color: #0a6fb5;">
                <i class="ph-duotone ph-tree-structure"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $pokjas->count() }}</h4>
                <p>Total Unit &amp; Pilar</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="ph-duotone ph-buildings"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $timKerjaCount }}</h4>
                <p>Tim Kerja &amp; TU Balai</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #f0f9ff; color: #0284c7;">
                <i class="ph-duotone ph-gear-six"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $pilarSubbagCount }}</h4>
                <p>6 Pilar Subbag Umum</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #ecfdf5; color: #059669;">
                <i class="ph-duotone ph-lightning"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $kelompokKerjaCount }}</h4>
                <p>Satgas Ad Hoc (Pokja RBI)</p>
            </div>
        </div>

        <div class="pokja-kpi-card">
            <div class="pokja-kpi-icon" style="background: #efe8fa; color: #6f45bd;">
                <i class="ph-duotone ph-users-three"></i>
            </div>
            <div class="pokja-kpi-content">
                <h4>{{ $totalPersonilTerdaftar }}</h4>
                <p>Pegawai Ditugaskan</p>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar: Filter Tabs & Live Instant Search -->
    <div class="pokja-toolbar">
        <div class="pokja-tabs-wrapper">
            <button type="button" class="pokja-tab-btn active" onclick="switchPokjaTab('all', this)">
                <i class="ph-bold ph-squares-four"></i>
                <span>Semua Unit &amp; Pilar</span>
                <span class="pokja-tab-count">{{ $pokjas->count() }}</span>
            </button>
            <button type="button" class="pokja-tab-btn" onclick="switchPokjaTab('tim_kerja', this)">
                <i class="ph-bold ph-buildings"></i>
                <span>Tim Kerja &amp; TU Balai</span>
                <span class="pokja-tab-count">{{ $timKerjaCount }}</span>
            </button>
            <button type="button" class="pokja-tab-btn" onclick="switchPokjaTab('subbag_pilar', this)">
                <i class="ph-bold ph-gear-six"></i>
                <span>6 Pilar Operasional Subbag Umum</span>
                <span class="pokja-tab-count">{{ $pilarSubbagCount }}</span>
            </button>
            <button type="button" class="pokja-tab-btn" onclick="switchPokjaTab('kelompok_kerja', this)">
                <i class="ph-bold ph-lightning"></i>
                <span>Satgas Ad Hoc (Pokja RBI)</span>
                <span class="pokja-tab-count">{{ $kelompokKerjaCount }}</span>
            </button>
        </div>

        <!-- Live Search Input -->
        <div class="pokja-search-box">
            <i class="ph-bold ph-magnifying-glass"></i>
            <input type="text" id="pokjaLiveSearch" class="pokja-search-input" placeholder="Cari nama unit, kode, atau katim..." oninput="handlePokjaLiveSearch(this.value)">
        </div>
    </div>

    <!-- 4. Subbag Pilar Info Banner (Tampil jika tab Semua atau Subbag aktif) -->
    <div class="pilar-banner" id="pilarBanner">
        <div class="pilar-banner-icon">
            <i class="ph-bold ph-gear-fine"></i>
        </div>
        <div>
            <h4>6 Pilar Operasional Subbagian Umum (Tata Usaha &amp; Operasional)</h4>
            <p>Ruang lingkup kerja harian yang menjaga stabilitas, efisiensi administrasi, BMN, OSDM, keuangan, dan tata kelola BGTK Provinsi DKI Jakarta.</p>
        </div>
    </div>

    <!-- 5. Pokja Cards Grid -->
    <div class="pokja-grid" id="pokjaCardsGrid">
        @forelse($pokjas as $pokja)
            @php
                $isSubbag = ($pokja->kode_pokja === 'SUBBAG-UMUM');
                $isPilar = ($pokja->tipe === 'subbag_pilar');
                $isAdhoc = ($pokja->tipe === 'kelompok_kerja');

                $themeClass = $isSubbag ? 'type-subbag' : ($isPilar ? 'type-pilar' : ($isAdhoc ? 'type-adhoc' : 'type-timker'));
                $typeBadgeText = $isSubbag ? 'TU Balai' : ($isPilar ? 'Pilar Subbag' : ($isAdhoc ? 'Satgas RBI' : 'Tim Kerja'));
                $leaderRoleText = $isSubbag ? 'Kepala Subbagian (Kasubag)' : ($isPilar ? 'Koordinator / PIC Pilar' : ($isAdhoc ? 'Ketua Satgas' : 'Ketua Tim (Katim)'));
                $leaderName = $pokja->katim?->name ?? 'Belum Ditunjuk';

                $totalTugas = $pokja->pekerjaan_count ?? ($pokja->pekerjaan ? $pokja->pekerjaan->count() : 0);
                $selesaiTugas = ($totalTugas > 0 && $pokja->pekerjaan) ? $pokja->pekerjaan->where('status_pelaksanaan', 'selesai_terverifikasi')->count() : 0;
            @endphp

            <div class="pokja-card {{ $themeClass }}" 
                 data-tipe="{{ $pokja->tipe ?? 'tim_kerja' }}"
                 data-search="{{ strtolower($pokja->nama_pokja . ' ' . $pokja->kode_pokja . ' ' . $leaderName) }}">
                
                <div>
                    <!-- Header Card: Kode & Type Badge -->
                    <div class="pokja-card-head">
                        <span class="pokja-code-badge">{{ $pokja->kode_pokja }}</span>
                        <span class="pokja-type-pill">
                            <i class="ph-bold {{ $isSubbag ? 'ph-shield-check' : ($isPilar ? 'ph-gear' : ($isAdhoc ? 'ph-sparkle' : 'ph-briefcase')) }}"></i>
                            <span>{{ $typeBadgeText }}</span>
                        </span>
                    </div>

                    <!-- Judul & Deskripsi -->
                    <h3 class="pokja-card-title">{{ $pokja->nama_pokja }}</h3>
                    <p class="pokja-card-desc">{{ $pokja->deskripsi ?? 'Belum ada deskripsi spesifik untuk ruang lingkup unit kerja ini.' }}</p>

                    <!-- Khusus Subbag Umum: Preview 6 Pilar Naungan -->
                    @if($isSubbag)
                        <div class="subbag-pilar-chips">
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-coins"></i> Keuangan</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-users"></i> OSDM</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-envelope"></i> Persuratan</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-desktop"></i> Sistem Info</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-archive"></i> BMN &amp; RT</span>
                            <span class="subbag-pilar-chip"><i class="ph-bold ph-shield"></i> K3</span>
                        </div>
                    @endif
                </div>

                <div>
                    <!-- Penanggung Jawab (Katim / Kasubag / PIC) Profile Box -->
                    <div class="pokja-leader-box">
                        <div class="leader-avatar">
                            {{ strtoupper(substr($leaderName, 0, 1)) }}
                        </div>
                        <div class="leader-info">
                            <div class="leader-role-label">{{ $leaderRoleText }}</div>
                            <div class="leader-name" title="{{ $leaderName }}">{{ $leaderName }}</div>
                        </div>
                    </div>

                    <!-- Team Snapshot & Tugas Count Strip -->
                    <div class="pokja-team-snapshot">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="avatar-stack">
                                @forelse($pokja->anggota->take(3) as $m)
                                    <div class="avatar-stack-item" title="{{ $m->name }}">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                @empty
                                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Kosong</span>
                                @endforelse
                            </div>
                            <span style="font-size: 11px; font-weight: 700; color: #475569;">
                                {{ $pokja->anggota->count() }} Pegawai
                            </span>
                        </div>

                        <div>
                            @if($totalTugas > 0)
                                <span class="pokja-task-count" title="Ketuntasan tugas unit kerja">
                                    <i class="ph-bold ph-check"></i> {{ $selesaiTugas }}/{{ $totalTugas }} Selesai
                                </span>
                            @else
                                <span style="font-size: 11px; color: #94a3b8;">Belum ada tugas</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    <div class="pokja-card-foot">
                        <div style="display: flex; gap: 6px;">
                            <!-- Tombol Lihat Detail Proper -->
                            <button type="button" class="btn-card-detail" onclick="openDetailModal('{{ $pokja->id }}')" title="Buka Detail Lengkap Unit Kerja Ini">
                                <i class="ph-bold ph-info"></i>
                                <span>Detail</span>
                            </button>

                            <!-- Tombol Kelola Personil -->
                            <button type="button" class="btn-card-personil" onclick="openManageMembersModal('{{ $pokja->id }}')" title="Atur Personil Pegawai">
                                <i class="ph-bold ph-user-plus"></i>
                                <span>Personil</span>
                            </button>
                        </div>

                        <!-- Edit & Delete Icons -->
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('pokjas.edit', $pokja) }}" class="btn-action-icon btn-edit" title="Ubah Identitas Pokja">
                                <i class="ph-bold ph-pencil-simple"></i>
                            </a>

                            <form action="{{ route('pokjas.destroy', $pokja) }}" method="POST" style="display: inline-block;"
                                  data-confirm="Apakah Anda yakin ingin menghapus unit / kelompok kerja &quot;{{ addslashes($pokja->nama_pokja) }}&quot;? Seluruh keanggotaan personil di unit ini akan dilepaskan secara aman."
                                  data-confirm-title="Hapus Unit Kerja"
                                  data-confirm-btn="Ya, Hapus Unit"
                                  data-confirm-type="danger">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-icon btn-delete" title="Hapus Pokja / Unit">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =========================================================================
                 MODAL 1: DETAIL LENGKAP POKJA (Sangat Proper, Elegan & Rapi)
                 ========================================================================= -->
            <div id="detailModal_{{ $pokja->id }}" class="modal-backdrop" onclick="closeDetailModalOnBackdrop(event, '{{ $pokja->id }}')">
                <div class="modal-card" style="max-width: 650px;" onclick="event.stopPropagation()">
                    <div class="modal-header">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="pokja-code-badge">{{ $pokja->kode_pokja }}</span>
                            <span class="pokja-type-pill">{{ $typeBadgeText }}</span>
                        </div>
                        <button type="button" class="modal-close" onclick="closeDetailModal('{{ $pokja->id }}')" title="Tutup">
                            <i class="ph-bold ph-x"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Judul & Deskripsi Unit -->
                        <div style="margin-bottom: 16px;">
                            <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">{{ $pokja->nama_pokja }}</h2>
                            <p style="font-size: 13px; color: #475569; line-height: 1.6; margin: 0;">
                                {{ $pokja->deskripsi ?? 'Belum ada deskripsi ruang lingkup yang dicatat untuk unit ini.' }}
                            </p>
                        </div>

                        <!-- Banner Penanggung Jawab -->
                        <div class="detail-leader-banner">
                            <div class="detail-leader-avatar">
                                {{ strtoupper(substr($leaderName, 0, 1)) }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">
                                    {{ $leaderRoleText }}
                                </span>
                                <strong style="font-size: 14.5px; color: #0f172a; display: block;">{{ $leaderName }}</strong>
                                <span style="font-size: 11.5px; color: #64748b;">
                                    NIP: {{ $pokja->katim?->nip ?? '-' }} &bull; {{ $pokja->katim?->jabatan ?? 'Pegawai' }}
                                </span>
                            </div>
                        </div>

                        <!-- 3-Col KPI Stats Unit -->
                        <div class="detail-stats-grid">
                            <div class="detail-stat-box">
                                <strong>{{ $pokja->anggota->count() }}</strong>
                                <span>Personil Anggota</span>
                            </div>
                            <div class="detail-stat-box">
                                <strong style="color: #059669;">{{ $totalTugas }}</strong>
                                <span>Tugas / Pekerjaan</span>
                            </div>
                            <div class="detail-stat-box">
                                <strong style="color: #d97706;">{{ $avgProgres }}%</strong>
                                <span>Rata-rata Capaian</span>
                            </div>
                        </div>

                        <!-- Daftar Anggota & Personil Lengkap -->
                        <div style="margin-top: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 12px; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <i class="ph-bold ph-users-three"></i> Daftar Seluruh Anggota ({{ $pokja->anggota->count() }} Orang)
                                </span>
                                <button type="button" onclick="closeDetailModal('{{ $pokja->id }}'); openManageMembersModal('{{ $pokja->id }}');" style="font-size: 11.5px; font-weight: 700; color: var(--primary); background: none; border: none; cursor: pointer;">
                                    <i class="ph-bold ph-pencil-simple"></i> Kelola Anggota
                                </button>
                            </div>

                            <div style="max-height: 240px; overflow-y: auto; padding-right: 4px;">
                                @forelse($pokja->anggota as $anggota)
                                    @php
                                        $isLeaderPerson = ($anggota->pivot->peran ?? '') === 'ketua' || $anggota->id === $pokja->katim_id;
                                    @endphp
                                    <div class="detail-member-row {{ $isLeaderPerson ? 'is-leader' : '' }}">
                                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                            <div style="width: 30px; height: 30px; border-radius: 50%; background: {{ $isLeaderPerson ? '#2563eb' : '#64748b' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; flex-shrink: 0;">
                                                {{ strtoupper(substr($anggota->name, 0, 1)) }}
                                            </div>
                                            <div style="min-width: 0;">
                                                <div style="font-size: 12.5px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $anggota->name }}
                                                </div>
                                                <div style="font-size: 11px; color: #64748b;">
                                                    NIP: {{ $anggota->nip ?? '-' }} &bull; {{ $anggota->jabatan ?? $anggota->role }}
                                                </div>
                                            </div>
                                        </div>
                                        <div style="flex-shrink: 0; margin-left: 8px;">
                                            @if($isLeaderPerson)
                                                <span style="font-size: 10.5px; font-weight: 700; padding: 2px 7px; border-radius: 4px; background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                                                    â­ {{ $leaderRoleText }}
                                                </span>
                                            @else
                                                <span style="font-size: 10.5px; font-weight: 600; padding: 2px 7px; border-radius: 4px; background: #f1f5f9; color: #475569;">
                                                    Anggota
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div style="text-align: center; padding: 20px; font-size: 12px; color: var(--text-muted); background: #f8fafc; border-radius: 8px; border: 1px dashed var(--border);">
                                        Belum ada personil yang terdaftar pada unit ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if($totalTugas > 0)
                            <a href="{{ route('pekerjaan.index', ['pokja_id' => $pokja->id]) }}" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none; margin-right: auto; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="ph-bold ph-list-bullets"></i>
                                Lihat Daftar Pekerjaan Unit Ini &rarr;
                            </a>
                        @endif
                        <a href="{{ route('pokjas.edit', $pokja) }}" class="btn-card-personil" style="text-decoration: none; font-size: 12px; padding: 7px 14px;">
                            <i class="ph-bold ph-pencil-simple"></i> Ubah Pokja
                        </a>
                        <button type="button" class="btn-primary" onclick="closeDetailModal('{{ $pokja->id }}')" style="font-size: 12px; padding: 7px 16px; border-radius: 8px;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <!-- =========================================================================
                 MODAL 2: KELOLA PERSONIL & ANGGOTA (Fleksibel Checklist BGTK)
                 ========================================================================= -->
            <div id="manageMembersModal_{{ $pokja->id }}" class="modal-backdrop" onclick="closeManageMembersModalOnBackdrop(event, '{{ $pokja->id }}')">
                <div class="modal-card" style="max-width: 600px;" onclick="event.stopPropagation()">
                    <form action="{{ route('pokjas.sync-anggota', $pokja) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <div>
                                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 2px 0;">
                                    Kelola Personil: {{ $pokja->nama_pokja }}
                                </h3>
                                <span style="font-size: 11.5px; color: #64748b;">
                                    Pilih pegawai dari seluruh staf BGTK (Fleksibel Multi-Kelompok)
                                </span>
                            </div>
                            <button type="button" class="modal-close" onclick="closeManageMembersModal('{{ $pokja->id }}')">
                                <i class="ph-bold ph-x"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <!-- Live search personil input -->
                            <div style="position: relative;">
                                <i class="ph-bold ph-magnifying-glass" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                                <input type="text" 
                                       class="user-search-input" 
                                       placeholder="Cari nama pegawai atau NIP staf..." 
                                       oninput="filterUserList(this, 'userList_{{ $pokja->id }}')">
                            </div>

                            @php
                                $existingMemberIds = $pokja->anggota->pluck('id')->toArray();
                            @endphp

                            <div class="user-checklist-container" id="userList_{{ $pokja->id }}">
                                @foreach($allUsers as $u)
                                    @php
                                        $isChecked = in_array($u->id, $existingMemberIds);
                                        $isLeader = ($u->id == $pokja->katim_id);
                                    @endphp
                                    <label class="user-check-item {{ $isLeader ? 'leader-item' : '' }}" data-search="{{ strtolower($u->name . ' ' . $u->nip) }}">
                                        <input type="checkbox" 
                                               name="anggota_ids[]" 
                                               value="{{ $u->id }}" 
                                               {{ $isChecked || $isLeader ? 'checked' : '' }}
                                               {{ $isLeader ? 'disabled' : '' }}>
                                        @if($isLeader)
                                            <input type="hidden" name="anggota_ids[]" value="{{ $u->id }}">
                                        @endif
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-size: 12.5px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                                                <span>{{ $u->name }}</span>
                                                @if($isLeader)
                                                    <span style="font-size: 10px; color: #2563eb; background: #eff6ff; padding: 1px 6px; border-radius: 4px; font-weight: 800;">
                                                        â­ {{ $isPilar ? 'PIC Pilar' : 'Ketua' }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                                NIP: {{ $u->nip ?? '-' }} &bull; {{ $u->jabatan ?? $u->role }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-card-personil" onclick="closeManageMembersModal('{{ $pokja->id }}')" style="font-size: 12px; padding: 7px 14px;">
                                Batal
                            </button>
                            <button type="submit" class="btn-primary" style="font-size: 12px; padding: 7px 16px; border-radius: 8px;">
                                <i class="ph-bold ph-floppy-disk"></i>
                                <span>Simpan Perubahan Personil</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="ph-duotone ph-tree-structure" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
                <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">Belum Ada Pokja / Unit</h4>
                <p style="font-size: 13px; margin: 0;">Silakan tambahkan pokja atau unit kerja baru menggunakan tombol di atas.</p>
            </div>
        @endforelse
    </div>

    <!-- Empty State saat filter search tidak ada hasil -->
    <div id="pokjaSearchEmptyState" style="display: none; text-align: center; padding: 40px; color: var(--text-muted); background: white; border: 1px dashed var(--border); border-radius: 12px; margin-bottom: 30px;">
        <i class="ph-duotone ph-magnifying-glass" style="font-size: 40px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
        <h4 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">Tidak Ada Unit Kerja yang Cocok</h4>
        <p style="font-size: 12.5px; margin: 0;">Coba gunakan kata kunci pencarian nama atau kode pokja yang berbeda.</p>
    </div>

@endsection

@section('scripts')
<script>
    let currentTabType = 'all';

    // Tab Filter Functionality
    function switchPokjaTab(type, btn) {
        currentTabType = type;
        document.querySelectorAll('.pokja-tab-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const banner = document.getElementById('pilarBanner');
        if (banner) {
            banner.style.display = (type === 'subbag_pilar' || type === 'all') ? 'flex' : 'none';
        }

        applyPokjaFilters();
    }

    // Live Instant Search
    function handlePokjaLiveSearch(query) {
        applyPokjaFilters();
    }

    function applyPokjaFilters() {
        const searchInput = document.getElementById('pokjaLiveSearch');
        const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const cards = document.querySelectorAll('.pokja-card');
        const emptyState = document.getElementById('pokjaSearchEmptyState');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardType = card.getAttribute('data-tipe');
            const searchKey = card.getAttribute('data-search') || '';

            const matchTab = (currentTabType === 'all') || (cardType === currentTabType);
            const matchQuery = !q || searchKey.includes(q);

            if (matchTab && matchQuery) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Modal Detail Pokja
    function openDetailModal(pokjaId) {
        const modal = document.getElementById('detailModal_' + pokjaId);
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDetailModal(pokjaId) {
        const modal = document.getElementById('detailModal_' + pokjaId);
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function closeDetailModalOnBackdrop(e, pokjaId) {
        if (e.target.id === 'detailModal_' + pokjaId) {
            closeDetailModal(pokjaId);
        }
    }

    // Modal Manage Members
    function openManageMembersModal(pokjaId) {
        const modal = document.getElementById('manageMembersModal_' + pokjaId);
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeManageMembersModal(pokjaId) {
        const modal = document.getElementById('manageMembersModal_' + pokjaId);
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function closeManageMembersModalOnBackdrop(e, pokjaId) {
        if (e.target.id === 'manageMembersModal_' + pokjaId) {
            closeManageMembersModal(pokjaId);
        }
    }

    // Live search filter in manage members modal
    function filterUserList(input, containerId) {
        const query = input.value.toLowerCase().trim();
        const container = document.getElementById(containerId);
        if (!container) return;

        const items = container.querySelectorAll('.user-check-item');
        items.forEach(item => {
            const searchKey = item.getAttribute('data-search') || '';
            if (searchKey.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Escape Key Listener to close any open modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.is-open').forEach(m => {
                m.classList.remove('is-open');
            });
            document.body.style.overflow = '';
        }
    });
</script>
@endsection
