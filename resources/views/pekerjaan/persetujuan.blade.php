@extends('layouts.app')

@section('title', 'Pusat Persetujuan Laporan Progres | TANJIDOR BGTK')
@section('page-title', 'Pusat Persetujuan Progres')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pekerjaan.css') }}">
@endsection

@section('content')

    <!-- Flash Alerts -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-check-circle" style="font-size: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-info" style="font-size: 20px;"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- 1. Header Banner -->
    <div class="approval-header">
        <div class="header-content">
            <div class="header-tag">
                <i class="ph-bold ph-seal-check"></i> Validasi Progres oleh Katim / Koordinator Pemberi Tugas
            </div>
            <h2>Pusat Persetujuan Laporan Progres Pegawai</h2>
            <p>
                Setiap laporan progres dari pegawai pelaksana divalidasi oleh <strong>Katim atau Koordinator pembuat tugas</strong>. Laporan berkala mendokumentasikan catatan kemajuan, dan laporan penyelesaian tugas akan mengesahkan status pekerjaan menjadi selesai terverifikasi.
            </p>
        </div>
    </div>

    <!-- 2. KPI Summary Cards -->
    <div class="kpi-grid">
        <a href="{{ route('persetujuan.index', ['tab' => 'progres']) }}" class="kpi-card">
            <div class="kpi-icon kpi-amber">
                <i class="ph-duotone ph-clock"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countProgresPending }}</h4>
                <p>Menunggu Persetujuan Progres</p>
            </div>
        </a>

        <a href="{{ route('pekerjaan.index') }}" class="kpi-card">
            <div class="kpi-icon kpi-green">
                <i class="ph-duotone ph-check-circle"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countSelesaiTerverifikasi }}</h4>
                <p>Pekerjaan Tuntas Selesai</p>
            </div>
        </a>

        <a href="{{ route('persetujuan.index', ['tab' => 'riwayat']) }}" class="kpi-card">
            <div class="kpi-icon kpi-blue">
                <i class="ph-duotone ph-clock-counter-clockwise"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $riwayatPersetujuan->total() }}</h4>
                <p>Total Log Keputusan</p>
            </div>
        </a>
    </div>

    <!-- 3. Tabs Navigation & Content -->
    <div class="tabs-wrapper">
        <div class="tabs-header">
            <a href="{{ route('persetujuan.index', array_merge(request()->query(), ['tab' => 'progres'])) }}" 
               class="tab-btn {{ $activeTab === 'progres' ? 'active' : '' }}">
                <i class="ph-bold ph-hourglass-high"></i>
                <span>Antrean Laporan Progres Pegawai</span>
                <span class="tab-badge tab-badge-amber">{{ $countProgresPending }}</span>
            </a>

            <a href="{{ route('persetujuan.index', array_merge(request()->query(), ['tab' => 'riwayat'])) }}" 
               class="tab-btn {{ $activeTab === 'riwayat' ? 'active' : '' }}">
                <i class="ph-bold ph-clock-counter-clockwise"></i>
                <span>Arsip Jejak Audit Keputusan</span>
                <span class="tab-badge tab-badge-gray">{{ $riwayatPersetujuan->total() }}</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('persetujuan.index') }}" class="filter-bar">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="search-box">
                <i class="ph-bold ph-magnifying-glass"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pegawai, uraian progres, atau judul pekerjaan..." class="search-input">
            </div>

            @if(Auth::user()->isAdmin() || Auth::user()->isKabal() || Auth::user()->isKasubag())
                <select name="pokja_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Tim Kerja (Pokja)</option>
                    @foreach($pokjas as $p)
                        <option value="{{ $p->id }}" {{ request('pokja_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_pokja }}
                        </option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="btn-action-outline">
                <i class="ph-bold ph-funnel"></i> Filter
            </button>
            @if(request()->hasAny(['q', 'pokja_id']))
                <a href="{{ route('persetujuan.index', ['tab' => $activeTab]) }}" class="btn-action-outline" style="color: #ef4444;">
                    <i class="ph-bold ph-x"></i> Reset
                </a>
            @endif
        </form>

        <!-- TAB 1: Antrean Laporan Progres Pegawai -->
        @if($activeTab === 'progres')
            @if($antreanProgres->isNotEmpty())
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal Lapor</th>
                                <th>Pegawai Pelapor</th>
                                <th>Pekerjaan & Tim</th>
                                <th>Pemberi Tugas</th>
                                <th>Jenis Laporan</th>
                                <th>Uraian Progres</th>
                                <th>Bukti Dukung</th>
                                <th style="text-align: right;">Aksi Approval</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($antreanProgres as $item)
                                @php
                                    $pekerjaan = $item->pekerjaan;
                                    $isPembuat = ($pekerjaan && $pekerjaan->pembuat_id === Auth::id())
                                        || (Auth::user()->isKatim() && $pekerjaan && $pekerjaan->pokja && $pekerjaan->pokja->katim_id === Auth::id())
                                        || Auth::user()->isAdmin();
                                @endphp
                                <tr>
                                    <td style="white-space: nowrap; font-weight: 600;">
                                        {{ $item->tanggal->format('d M Y') }}
                                        <div style="font-size: 11px; color: #94a3b8;">{{ $item->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 30px; height: 30px; border-radius: 999px; background: #eff6ff; color: #2563eb; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; border: 1px solid #bfdbfe;">
                                                {{ strtoupper(substr($item->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #0f172a;">{{ $item->user->name }}</div>
                                                <div style="font-size: 11px; color: #64748b;">{{ $item->user->nip ?? 'Pegawai' }} &bull; {{ $item->user->role_label }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($pekerjaan)
                                            <a href="{{ route('pekerjaan.show', $pekerjaan) }}" style="font-weight: 700; color: #1e40af; text-decoration: none;" class="hover-underline">
                                                <span style="font-family: monospace; font-size: 11.5px; background: #e0e7ff; color: #3730a3; padding: 1px 6px; border-radius: 4px; margin-right: 4px;">
                                                    {{ $pekerjaan->kode_tampilan }}
                                                </span>
                                                {{ $pekerjaan->judul }}
                                            </a>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                                <i class="ph-bold ph-buildings"></i> {{ $pekerjaan->pokja?->nama_pokja ?? 'BGTK DKI' }}
                                                &bull; Status: <strong>{{ $pekerjaan->status_pelaksanaan_label }}</strong>
                                            </div>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 12.5px;">
                                            {{ $pekerjaan->pembuat->name ?? '-' }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">
                                            {{ $pekerjaan->pembuat->role_label ?? 'Pemberi Tugas' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->isPenyelesaian())
                                            <span class="badge-pill" style="background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; font-size: 11.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px;">
                                                <i class="ph-bold ph-seal-check"></i> Penyelesaian Akhir
                                            </span>
                                            <div style="font-size: 11px; color: #7e22ce; margin-top: 3px; font-weight: 600;">Mengajukan Tuntas</div>
                                        @else
                                            <span class="badge-pill" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px;">
                                                <i class="ph-bold ph-pencil-simple-line"></i> Kemajuan Berkala
                                            </span>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 3px;">Catatan Progres</div>
                                        @endif
                                    </td>
                                    <td style="max-width: 250px;">
                                        <div style="font-size: 12.5px; color: #1e293b; line-height: 1.4;">
                                            {{ $item->deskripsi_progres }}
                                        </div>
                                        @if($item->kendala)
                                            <div style="font-size: 11.5px; color: #dc2626; margin-top: 4px;">
                                                <strong>Kendala:</strong> {{ $item->kendala }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->file_bukti)
                                            <button type="button" 
                                                    onclick="previewBuktiDukung('{{ asset('storage/' . $item->file_bukti) }}', '{{ basename($item->file_bukti) }}')" 
                                                    class="btn-action-outline" 
                                                    style="padding: 4px 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px; color: #0284c7; border-color: #bae6fd; background: #f0f9ff;"
                                                    title="Pratinjau File Bukti Dukung">
                                                <i class="ph-bold ph-eye"></i> File Bukti
                                            </button>
                                        @endif
                                        @if($item->link_bukti)
                                            <a href="{{ $item->link_bukti }}" target="_blank" class="btn-action-outline" style="padding: 4px 8px; font-size: 11.5px; margin-top: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-arrow-square-out"></i> Tautan
                                            </a>
                                        @endif
                                        @if(!$item->file_bukti && !$item->link_bukti)
                                            <span style="color: #94a3b8; font-size: 12px;">Tanpa berkas</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        @if($isPembuat)
                                            <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                                <form action="{{ route('realisasi.approve', $item) }}" method="POST" 
                                                      data-confirm="Setujui laporan ini? {{ $item->isPenyelesaian() ? 'Pekerjaan akan disahkan sebagai Selesai Terverifikasi.' : 'Laporan kemajuan berkala akan disahkan dan dicatat.' }}"
                                                      data-confirm-title="Persetujuan Laporan {{ $item->isPenyelesaian() ? 'Penyelesaian' : 'Kemajuan' }}"
                                                      data-confirm-btn="Ya, Setujui"
                                                      data-confirm-type="success">
                                                    @csrf
                                                    <button type="submit" class="btn-action-primary" title="Setujui laporan progres">
                                                        <i class="ph-bold ph-check"></i> Setujui
                                                    </button>
                                                </form>
                                                <button type="button" class="btn-action-outline" style="color: #dc2626; border-color: #fca5a5;" onclick="openModalTolakProgres('{{ route('realisasi.reject', $item) }}')" title="Tolak dengan catatan perbaikan">
                                                    <i class="ph-bold ph-x"></i> Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">
                                                Menunggu Approval Pembuat Tugas
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 16px 20px;">
                    {{ $antreanProgres->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="ph-duotone ph-check-circle" style="color: #10b981;"></i>
                    </div>
                    <h4>Tidak Ada Antrean Laporan Progres</h4>
                    <p>Semua laporan progres pekerjaan dari pegawai pelaksana telah disetujui atau belum ada pengajuan baru.</p>
                </div>
            @endif
        @endif

        <!-- TAB 2: Arsip Jejak Audit Keputusan -->
        @if($activeTab === 'riwayat')
            @if($riwayatPersetujuan->isNotEmpty())
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pemeriksa</th>
                                <th>Tugas / Pekerjaan</th>
                                <th>Jenis Keputusan</th>
                                <th>Perubahan Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatPersetujuan as $item)
                                <tr>
                                    <td style="white-space: nowrap; font-size: 12px;">{{ $item->created_at->format('d M Y H:i') }}</td>
                                    <td style="font-weight: 700;">{{ $item->user->name }}</td>
                                    <td>
                                        @if($item->pekerjaan)
                                            <a href="{{ route('pekerjaan.show', $item->pekerjaan) }}" style="font-weight: 600; color: #1e40af; text-decoration: none;">
                                                {{ $item->pekerjaan->kode_tampilan }} - {{ $item->pekerjaan->judul }}
                                            </a>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-family: monospace; font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                            {{ str_replace('_', ' ', $item->jenis_aksi) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 12px; color: #64748b;">{{ $item->status_sebelumnya ?? 'Awal' }}</span>
                                        &rarr;
                                        <strong style="font-size: 12.5px; color: #0f172a;">{{ $item->status_baru }}</strong>
                                    </td>
                                    <td>{{ $item->catatan ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 16px 20px;">
                    {{ $riwayatPersetujuan->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="ph-duotone ph-clock-counter-clockwise"></i>
                    </div>
                    <h4>Belum Ada Riwayat Keputusan</h4>
                    <p>Catatan audit trail keputusan persetujuan akan otomatis tercatat di sini.</p>
                </div>
            @endif
        @endif

    </div>

    <!-- Modal Tolak Laporan Progres -->
    <div id="modalTolakProgres" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'modalTolakProgres')">
        <div class="modal-card">
            <form id="formTolakProgres" method="POST">
                @csrf
                <div class="modal-header">
                    <div class="modal-title" style="color: #dc2626;">
                        <i class="ph-bold ph-x-circle"></i> Tolak Laporan Progres Pegawai
                    </div>
                    <button type="button" onclick="closeModal('modalTolakProgres')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
                </div>

                <div class="modal-body">
                    <p style="font-size: 13px; color: #475569; margin-bottom: 14px;">
                        Tuliskan catatan evaluasi atau alasan penolakan agar pegawai pelaksana dapat memperbaiki laporannya:
                    </p>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Catatan / Alasan Penolakan <span class="required" style="color: #ef4444;">*</span></label>
                        <textarea name="catatan" class="form-textarea" rows="3" placeholder="Contoh: Bukti dukung laporan belum lengkap, mohon lampirkan daftar hadir peserta..." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none;"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action-outline" onclick="closeModal('modalTolakProgres')">Batal</button>
                    <button type="submit" class="btn-action-primary" style="background: #dc2626;">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function closeModalOnBackdrop(e, id) {
        if (e.target.id === id) {
            closeModal(id);
        }
    }

    function openModalTolakProgres(actionUrl) {
        const form = document.getElementById('formTolakProgres');
        if (form) {
            form.action = actionUrl;
        }
        openModal('modalTolakProgres');
    }
</script>
@endsection
