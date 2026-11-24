@extends('layouts.app')

@section('title', 'Tugas & Pekerjaan Saya | TANJIDOR BGTK')
@section('page-title', 'Tugas & Pekerjaan Saya')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pekerjaan.css') }}">
@endsection

@section('content')

    <!-- Flash Messages -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-check-circle" style="font-size: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13.5px;">
            <i class="ph-bold ph-warning-circle" style="font-size: 20px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. Hero Banner -->
    <div class="hero-box">
        <h2>Tugas & Pekerjaan Saya</h2>
        <p>
            Kelola dan laporkan kemajuan pekerjaan yang Anda ajukan atau ditugaskan kepada Anda. Catat realisasi berkala, lampirkan bukti dukung, atau laporkan penyelesaian akhir penugasan untuk diverifikasi Katim.
        </p>
    </div>

    <!-- 2. KPI Summary Cards -->
    <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
        <a href="{{ route('pekerjaan.saya', ['tab' => 'berjalan']) }}" class="kpi-card">
            <div class="kpi-icon kpi-blue">
                <i class="ph-duotone ph-rocket-launch"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countBerjalan }}</h4>
                <p>Tugas Aktif & Berjalan</p>
            </div>
        </a>

        <a href="{{ route('pekerjaan.saya', ['tab' => 'selesai']) }}" class="kpi-card">
            <div class="kpi-icon kpi-green">
                <i class="ph-duotone ph-check-circle"></i>
            </div>
            <div class="kpi-info">
                <h4>{{ $countSelesai }}</h4>
                <p>Pekerjaan Selesai Tuntas</p>
            </div>
        </a>
    </div>

    <!-- 3. Tabs Navigation & Content -->
    <div class="tabs-wrapper">
        <div class="tabs-header">
            <a href="{{ route('pekerjaan.saya', ['tab' => 'berjalan']) }}" 
               class="tab-btn {{ $activeTab === 'berjalan' ? 'active' : '' }}">
                <i class="ph-bold ph-lightning"></i>
                <span>Tugas Berjalan & Aktif</span>
                <span class="tab-badge tab-badge-blue">{{ $countBerjalan }}</span>
            </a>

            <a href="{{ route('pekerjaan.saya', ['tab' => 'selesai']) }}" 
               class="tab-btn {{ $activeTab === 'selesai' ? 'active' : '' }}">
                <i class="ph-bold ph-check-fat"></i>
                <span>Pekerjaan Selesai Tuntas</span>
                <span class="tab-badge tab-badge-green">{{ $countSelesai }}</span>
            </a>
        </div>

        <!-- Filter Search Bar -->
        <form method="GET" action="{{ route('pekerjaan.saya') }}" class="filter-bar">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="search-box">
                <i class="ph-bold ph-magnifying-glass"></i>
                <input type="text" name="q" class="search-input" value="{{ request('q') }}" placeholder="Cari kode atau nama tugas Anda...">
            </div>

            <button type="submit" class="btn-action-primary" style="padding: 9px 16px;">
                <i class="ph-bold ph-funnel"></i> Cari
            </button>

            @if(request()->filled('q'))
                <a href="{{ route('pekerjaan.saya', ['tab' => $activeTab]) }}" class="btn-action-outline" style="padding: 8px 12px;">
                    <i class="ph-bold ph-arrows-counter-clockwise"></i> Reset
                </a>
            @endif
        </form>

        <!-- Tasks Table -->
        @if($pekerjaanList->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="ph-duotone ph-list-checks"></i>
                </div>
                <h4>Tidak Ada Pekerjaan pada Kategori Ini</h4>
                <p>
                    @if($activeTab === 'berjalan')
                        Belum ada tugas aktif yang sedang berjalan. Rincian subpekerjaan Anda perlu disetujui oleh Katim terlebih dahulu.
                    @else
                        Tidak ditemukan penugasan yang sesuai dengan filter saat ini.
                    @endif
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pekerjaan & Induk</th>
                            <th>Target & Satuan</th>
                            <th>Tenggat Waktu</th>
                            <th>Aktivitas & Log Laporan</th>
                            <th>Status Pelaksanaan</th>
                            <th style="text-align: right;">Aksi Pelaporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pekerjaanList as $item)
                            <tr>
                                <td>
                                    <span class="code-badge">{{ $item->kode_tampilan }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('pekerjaan.show', $item) }}" style="font-weight: 700; color: #0f172a; text-decoration: none;" class="hover-underline">
                                        {{ $item->judul }}
                                    </a>
                                    @if($item->parent)
                                        <div style="font-size: 11.5px; color: #64748b; margin-top: 3px;">
                                            Induk: <a href="{{ route('pekerjaan.show', $item->parent) }}" style="color: #475569; text-decoration: none;">{{ $item->parent->kode_tampilan }} - {{ Str::limit($item->parent->judul, 35) }}</a>
                                        </div>
                                    @endif
                                    <div style="display: flex; gap: 8px; align-items: center; margin-top: 5px; flex-wrap: wrap;">
                                        @php
                                            $isUserPic = ((int)$item->penanggung_jawab_id === (int)Auth::id());
                                        @endphp
                                        @if($isUserPic)
                                            <span style="font-size: 10.5px; font-weight: 800; background: #faf5ff; color: #7e22ce; padding: 2px 7px; border-radius: 4px; border: 1px solid #e9d5ff; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-star"></i> PIC Utama
                                            </span>
                                        @else
                                            <span style="font-size: 10.5px; font-weight: 700; background: #f0fdf4; color: #166534; padding: 2px 7px; border-radius: 4px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-user-check"></i> Anggota Pelaksana
                                            </span>
                                        @endif
                                        @if($item->pelaksana && $item->pelaksana->count() > 1)
                                            <span style="font-size: 11px; color: #475569; display: inline-flex; align-items: center; gap: 4px;" title="Total {{ $item->pelaksana->count() }} orang pelaksana">
                                                <i class="ph-bold ph-users-three"></i> Tim ({{ $item->pelaksana->count() }} Pegawai)
                                            </span>
                                        @endif
                                        @if($item->nomor_st)
                                            <span style="font-size: 11px; font-weight: 700; background: #eff6ff; color: #1d4ed8; padding: 2px 7px; border-radius: 4px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-file-text"></i> ST: {{ $item->nomor_st }}
                                            </span>
                                        @endif
                                        @if($item->pembuat && $item->pembuat_id !== Auth::id())
                                            <span style="font-size: 11px; color: #64748b; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ph-bold ph-user-check"></i> Pemberi Tugas: <strong>{{ $item->pembuat->name }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ number_format($item->target, 0) }}</strong> {{ $item->satuan }}
                                </td>
                                <td>
                                    <span style="color: {{ $item->isTerlambat() ? '#dc2626' : 'inherit' }}; font-weight: {{ $item->isTerlambat() ? '700' : 'normal' }};">
                                        {{ $item->tenggat ? $item->tenggat->format('d M Y') : '-' }}
                                    </span>
                                    @if($item->isTerlambat())
                                        <div style="font-size: 10.5px; color: #dc2626; font-weight: 700;">Lewat Tenggat</div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $accCount = $item->realisasi->where('status_approval', 'disetujui')->count();
                                        $pendingCount = $item->realisasi->where('status_approval', 'menunggu_persetujuan')->count();
                                    @endphp
                                    <div style="font-size: 12.5px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 5px;">
                                        <i class="ph-bold ph-chats-circle" style="color: #2563eb;"></i>
                                        <span>{{ $accCount }} Laporan Disetujui</span>
                                    </div>
                                    @if($pendingCount > 0)
                                        <div style="font-size: 10.5px; background: #fef3c7; color: #b45309; padding: 2px 7px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 3px; margin-top: 4px;">
                                            <i class="ph-bold ph-clock"></i> {{ $pendingCount }} butuh ACC Katim
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-pill exec-{{ $item->status_pelaksanaan }}">
                                        {{ str_replace('_', ' ', $item->status_pelaksanaan) }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    @if($item->isDisetujui() && $item->status_pelaksanaan !== 'selesai_terverifikasi')
                                        @if($item->status_pelaksanaan === 'belum_dimulai')
                                            <form action="{{ route('pekerjaan.update-progres', $item) }}" method="POST" style="display: inline-block; margin: 0;">
                                                @csrf
                                                <input type="hidden" name="aksi_status" value="mulai_kerja">
                                                <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                                                <button type="submit" class="btn-action-outline" style="border-color: #3b82f6; color: #1d4ed8; font-weight: 700;" title="Mulai Penugasan">
                                                    <i class="ph-bold ph-play"></i> Mulai Kerja
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Tombol Lapor Kemajuan -->
                                        <button type="button" 
                                                class="btn-action-green" 
                                                onclick="openProgressModal({{ $item->id }}, '{{ addslashes($item->kode_tampilan) }}', '{{ addslashes($item->judul) }}', 'kemajuan')">
                                            <i class="ph-bold ph-pencil-simple-line"></i> Lapor Kemajuan
                                        </button>

                                        <!-- Tombol Ajukan Selesai -->
                                        <button type="button" 
                                                class="btn-action-purple" 
                                                onclick="openProgressModal({{ $item->id }}, '{{ addslashes($item->kode_tampilan) }}', '{{ addslashes($item->judul) }}', 'penyelesaian')">
                                            <i class="ph-bold ph-seal-check"></i> Ajukan Selesai
                                        </button>
                                    @endif

                                    @if($item->pembuat_id === Auth::id() || $item->penanggung_jawab_id === Auth::id() || Auth::user()->canManagePekerjaan())
                                        <a href="{{ route('pekerjaan.edit', $item) }}" class="btn-action-outline" style="border-color: #fcd34d; color: #b45309;" title="Edit data pekerjaan">
                                            <i class="ph-bold ph-pencil-simple"></i> Edit
                                        </a>
                                    @endif

                                    <a href="{{ route('pekerjaan.show', $item) }}" class="btn-action-outline">
                                        <i class="ph-bold ph-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9;">
                {{ $pekerjaanList->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- ==================== MODAL LAPOR PROGRES & SELESAIKAN ==================== -->
    <div id="modalUpdateProgresUniversal" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'modalUpdateProgresUniversal')">
        <div class="modal-card">
            <form id="formUpdateProgres" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <div class="modal-title" id="modalProgressHeading">
                        <i class="ph-bold ph-pencil-simple-line"></i> Laporkan Realisasi & Progres
                    </div>
                    <button type="button" onclick="closeModal('modalUpdateProgresUniversal')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
                </div>

                <div class="modal-body">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
                        <div style="font-size: 11.5px; color: #64748b; font-weight: 700; text-transform: uppercase;">Penugasan:</div>
                        <div style="font-weight: 800; color: #0f172a; margin-top: 2px;" id="progressTaskTitle">-</div>
                    </div>

                    <!-- Pilihan Jenis Laporan -->
                    <div style="margin-bottom: 16px;">
                        <label class="form-label" style="margin-bottom: 8px;">Pilih Jenis Laporan <span style="color: #ef4444;">*</span></label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <label id="optJenisKemajuan" style="border: 2px solid #3b82f6; background: #eff6ff; border-radius: 8px; padding: 10px 14px; cursor: pointer; display: flex; align-items: flex-start; gap: 10px;">
                                <input type="radio" name="jenis_laporan" id="radioJenisKemajuan" value="kemajuan" checked style="margin-top: 3px;" onchange="updateModalType('kemajuan')">
                                <div>
                                    <div style="font-weight: 800; font-size: 13px; color: #1d4ed8;"><i class="ph-bold ph-pencil-simple-line"></i> Kemajuan Berkala</div>
                                    <div style="font-size: 11px; color: #475569; margin-top: 2px;">Tugas tetap aktif berjalan, lapor log aktivitas & progres.</div>
                                </div>
                            </label>
                            <label id="optJenisPenyelesaian" style="border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 8px; padding: 10px 14px; cursor: pointer; display: flex; align-items: flex-start; gap: 10px;">
                                <input type="radio" name="jenis_laporan" id="radioJenisPenyelesaian" value="penyelesaian" style="margin-top: 3px;" onchange="updateModalType('penyelesaian')">
                                <div>
                                    <div style="font-weight: 800; font-size: 13px; color: #7e22ce;"><i class="ph-bold ph-seal-check"></i> Penyelesaian Tugas</div>
                                    <div style="font-size: 11px; color: #475569; margin-top: 2px;">Tugas telah tuntas, ajukan pengesahan selesai ke Katim.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="bannerInfoModal" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 12px; color: #1e40af;">
                        <i class="ph-bold ph-info"></i> Laporan kemajuan berkala akan diperiksa dan di-ACC oleh pembuat tugas/Katim.
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Tanggal Pelaporan <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Uraian Kemajuan / Realisasi Pekerjaan <span style="color: #ef4444;">*</span></label>
                        <textarea id="inputDeskripsiProgres" name="deskripsi_progres" class="form-textarea" rows="3" placeholder="Tuliskan kemajuan pekerjaan atau aktivitas yang telah diselesaikan..." required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Kendala (Bila Ada)</label>
                            <textarea name="kendala" class="form-textarea" rows="2" placeholder="Kendala teknis/lapangan..."></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Rencana Tindak Lanjut / Solusi</label>
                            <textarea name="tindak_lanjut" class="form-textarea" rows="2" placeholder="Langkah berikutnya..."></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Unggah Berkas Bukti (PDF, DOCX, ZIP, Gambar)</label>
                        <input type="file" name="file_bukti" class="form-input" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx,.zip">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Atau Tautan Bukti (Google Drive / Cloud Link)</label>
                        <input type="url" name="link_bukti" class="form-input" placeholder="https://drive.google.com/...">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action-outline" onclick="closeModal('modalUpdateProgresUniversal')">Batal</button>
                    <button type="submit" id="btnSubmitProgress" class="btn-action-green">Kirim Laporan Kemajuan ke Pembuat Tugas</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'flex';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'none';
    }

    function closeModalOnBackdrop(e, id) {
        if (e.target.id === id) closeModal(id);
    }

    function updateModalType(type) {
        const radioKemajuan = document.getElementById('radioJenisKemajuan');
        const radioPenyelesaian = document.getElementById('radioJenisPenyelesaian');
        const optKemajuan = document.getElementById('optJenisKemajuan');
        const optPenyelesaian = document.getElementById('optJenisPenyelesaian');
        const banner = document.getElementById('bannerInfoModal');
        const submitBtn = document.getElementById('btnSubmitProgress');
        const heading = document.getElementById('modalProgressHeading');
        const descInput = document.getElementById('inputDeskripsiProgres');

        if (type === 'penyelesaian') {
            if (radioPenyelesaian) radioPenyelesaian.checked = true;
            if (optKemajuan) {
                optKemajuan.style.borderColor = '#cbd5e1';
                optKemajuan.style.background = '#f8fafc';
            }
            if (optPenyelesaian) {
                optPenyelesaian.style.borderColor = '#8b5cf6';
                optPenyelesaian.style.background = '#faf5ff';
            }
            if (banner) {
                banner.style.background = '#faf5ff';
                banner.style.borderColor = '#e9d5ff';
                banner.style.color = '#7e22ce';
                banner.innerHTML = '<i class="ph-bold ph-seal-check"></i> <strong>Mengajukan Penyelesaian Tugas.</strong> Setelah diajukan, tugas akan berstatus Menunggu Verifikasi untuk diperiksa dan disahkan oleh Katim.';
            }
            if (heading) heading.innerHTML = '<i class="ph-bold ph-seal-check"></i> Ajukan Penyelesaian Tugas ke Katim';
            if (submitBtn) {
                submitBtn.textContent = 'Ajukan Penyelesaian Tugas ke Katim';
                submitBtn.className = 'btn-action-purple';
            }
            if (descInput && !descInput.value) {
                descInput.placeholder = 'Jelaskan hasil akhir pekerjaan dan lampirkan berkas/tautan dokumen tuntas...';
            }
        } else {
            if (radioKemajuan) radioKemajuan.checked = true;
            if (optKemajuan) {
                optKemajuan.style.borderColor = '#3b82f6';
                optKemajuan.style.background = '#eff6ff';
            }
            if (optPenyelesaian) {
                optPenyelesaian.style.borderColor = '#cbd5e1';
                optPenyelesaian.style.background = '#f8fafc';
            }
            if (banner) {
                banner.style.background = '#eff6ff';
                banner.style.borderColor = '#bfdbfe';
                banner.style.color = '#1e40af';
                banner.innerHTML = '<i class="ph-bold ph-info"></i> Laporan kemajuan berkala akan diperiksa dan di-ACC oleh pembuat tugas/Katim (tugas tetap aktif berjalan).';
            }
            if (heading) heading.innerHTML = '<i class="ph-bold ph-pencil-simple-line"></i> Laporkan Kemajuan Tugas';
            if (submitBtn) {
                submitBtn.textContent = 'Kirim Laporan Kemajuan ke Pembuat Tugas';
                submitBtn.className = 'btn-action-green';
            }
            if (descInput) {
                descInput.placeholder = 'Tuliskan kemajuan pekerjaan atau aktivitas yang telah diselesaikan...';
            }
        }
    }

    function openProgressModal(id, kode, judul, defaultType) {
        const form = document.getElementById('formUpdateProgres');
        form.action = `/pekerjaan/${id}/progres`;
        document.getElementById('progressTaskTitle').textContent = `[${kode}] ${judul}`;

        const descInput = document.getElementById('inputDeskripsiProgres');
        if (descInput) descInput.value = '';

        updateModalType(defaultType === 'penyelesaian' ? 'penyelesaian' : 'kemajuan');
        openModal('modalUpdateProgresUniversal');
    }
</script>
@endsection
