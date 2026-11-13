@extends('layouts.app')

@section('title', 'Daftar Pekerjaan & Penugasan | BGTK DKI Jakarta')
@section('page-title', 'Daftar Pekerjaan & Penugasan')

@section('styles')
<style>
    /* Summary KPI Cards */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .summary-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .icon-blue { background: #e8f2fb; color: #0a6fb5; }
    .icon-amber { background: #fff3d9; color: #b87400; }
    .icon-purple { background: #efe8fa; color: #6f45bd; }
    .icon-green { background: #e3f6ee; color: #0c8a5d; }

    .summary-info h4 {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1.1;
    }

    .summary-info p {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
    }

    /* Actions & Filter Toolbar */
    .toolbar-wrapper {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .toolbar-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 14px;
    }

    .btn-create-main {
        background: linear-gradient(135deg, var(--navy) 0%, var(--primary) 100%);
        color: white;
        padding: 9px 18px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(11, 74, 130, 0.22);
        transition: var(--transition);
    }

    .btn-create-main:hover {
        background: linear-gradient(135deg, var(--navy-deep) 0%, var(--primary-hover) 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(11, 74, 130, 0.28);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.2fr auto;
        gap: 12px;
        align-items: center;
    }

    @media (max-width: 768px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
    }

    .filter-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        background: #f8fafc;
        outline: none;
    }

    .filter-control:focus {
        background: #fff;
        border-color: var(--primary);
    }

    /* Modern Table Card */
    .table-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .data-table th {
        background: #f8fafc;
        padding: 14px 18px;
        font-size: 11.5px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid var(--border);
    }

    .data-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #1e293b;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: #fafbfc;
    }

    /* Badges */
    .badge-code {
        font-family: monospace;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 800;
        border: 1px solid #bfdbfe;
        display: inline-block;
        margin-bottom: 4px;
    }

    .badge-status {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .badge-belum_dimulai { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .badge-dikerjakan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .badge-hasil_diajukan { background: #faf5ff; color: #86198f; border: 1px solid #f5d0fe; font-weight: 800; }
    .badge-selesai_terverifikasi { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-weight: 800; }
    .badge-perlu_perbaikan_hasil { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    /* Progress bar */
    .progress-bar-wrap {
        width: 110px;
        height: 7px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        display: inline-block;
        margin-right: 8px;
        vertical-align: middle;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #3b82f6, #10b981);
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: var(--transition);
        cursor: pointer;
    }

    .btn-action-icon:hover {
        background: #ffffff;
        color: #2563eb;
        border-color: #2563eb;
    }
</style>
@endsection

@section('content')

    <!-- 1. KPI Cards Ringkasan Penugasan -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-icon icon-blue">
                <i class="ph-duotone ph-briefcase"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($totalPekerjaan) }}</h4>
                <p>Total Penugasan Balai</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-amber">
                <i class="ph-duotone ph-arrows-clockwise"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($pekerjaanBerjalan) }}</h4>
                <p>Sedang Berjalan</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-purple">
                <i class="ph-duotone ph-seal-check"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($antreanHasil) }}</h4>
                <p>Hasil Diajukan (Menunggu Review)</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-green">
                <i class="ph-duotone ph-check-circle"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($pekerjaanSelesai) }}</h4>
                <p>Selesai Terverifikasi</p>
            </div>
        </div>
    </div>

    <!-- 2. Toolbar & Filter -->
    <div class="toolbar-wrapper">
        <div class="toolbar-top">
            <div>
                <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">Daftar Pekerjaan & Penugasan BGTK</h3>
                <p style="font-size: 13px; color: var(--text-muted);">Kelola penugasan dan pantau progres kerja pegawai di setiap Pokja</p>
            </div>

            @if(Auth::user()->canManagePekerjaan())
                <a href="{{ route('pekerjaan.create') }}" class="btn-create-main">
                    <i class="ph-bold ph-plus-circle"></i>
                    <span>+ Buat Penugasan Baru</span>
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('pekerjaan.index') }}" class="filter-grid">
            <input type="text" name="search" value="{{ request('search') }}" class="filter-control" placeholder="Cari judul pekerjaan atau nomor ST...">

            <select name="pokja_id" class="filter-control">
                <option value="">-- Semua Tim Kerja (Pokja) --</option>
                @foreach($pokjas as $p)
                    <option value="{{ $p->id }}" {{ request('pokja_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->kode_pokja }} - {{ $p->nama_pokja }}
                    </option>
                @endforeach
            </select>

            <select name="status_pelaksanaan" class="filter-control">
                <option value="">-- Semua Status --</option>
                <option value="belum_dimulai" {{ request('status_pelaksanaan') == 'belum_dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                <option value="dikerjakan" {{ request('status_pelaksanaan') == 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                <option value="hasil_diajukan" {{ request('status_pelaksanaan') == 'hasil_diajukan' ? 'selected' : '' }}>Hasil Diajukan</option>
                <option value="selesai_terverifikasi" {{ request('status_pelaksanaan') == 'selesai_terverifikasi' ? 'selected' : '' }}>Selesai Terverifikasi</option>
            </select>

            <select name="sumber_pekerjaan" class="filter-control">
                <option value="">-- Semua Kategori --</option>
                <option value="manual" {{ request('sumber_pekerjaan') == 'manual' ? 'selected' : '' }}>Hanya Pekerjaan Manual</option>
                <option value="skp" {{ request('sumber_pekerjaan') == 'skp' ? 'selected' : '' }}>Hanya Rujukan SKP</option>
            </select>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-action-icon" style="width: auto; padding: 0 16px; background: #0f172a; color: white; border: none; font-weight: 700; gap: 6px;">
                    <i class="ph-bold ph-funnel"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'pokja_id', 'status_pelaksanaan', 'sumber_pekerjaan']))
                    <a href="{{ route('pekerjaan.index') }}" class="btn-action-icon" style="color: #ef4444;" title="Reset Filter">
                        <i class="ph-bold ph-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Tabel Daftar Penugasan Pekerjaan -->
    <div class="table-card">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 32%;">Pekerjaan & Unit Kerja</th>
                        <th style="width: 16%;">Pemberi Tugas</th>
                        <th style="width: 22%;">Tim Pelaksana</th>
                        <th style="width: 12%;">Tenggat</th>
                        <th style="width: 18%;">Progres Akumulasi</th>
                        <th style="width: 12%;">Status</th>
                        <th style="text-align: right; width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pekerjaanList as $p)
                        <tr>
                            <!-- Pekerjaan & Pokja -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <span class="badge-code">{{ $p->kode_tampilan }}</span>
                                    @if($p->isFromSkp())
                                        <span style="font-size: 10px; background: #e0f2fe; color: #0284c7; padding: 2px 7px; border-radius: 6px; font-weight: 800; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 4px;" title="Merujuk ke SKP Pegawai: {{ $p->skpTahunan?->rencana_kinerja }}">
                                            <i class="ph-bold ph-target"></i> SKP
                                        </span>
                                    @else
                                        <span style="font-size: 10px; background: #f8fafc; color: #64748b; padding: 2px 6px; border-radius: 6px; font-weight: 700; border: 1px solid #e2e8f0;">
                                            Manual
                                        </span>
                                    @endif
                                    @if($p->nomor_st)
                                        <span style="font-size: 11px; color: #64748b; font-weight: 600;">
                                            <i class="ph-bold ph-file-text"></i> ST: {{ $p->nomor_st }}
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('pekerjaan.show', $p) }}" style="font-size: 14px; font-weight: 800; color: #0f172a; text-decoration: none; line-height: 1.3; display: block; margin-top: 2px;">
                                    {{ $p->judul }}
                                </a>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                                    <i class="ph-bold ph-buildings" style="color: #3b82f6;"></i>
                                    <span>{{ $p->pokja?->nama_pokja ?? 'BGTK Provinsi DKI Jakarta' }}</span>
                                </div>
                            </td>

                            <!-- Pemberi Tugas (Katim / Koor) -->
                            <td>
                                <div style="font-weight: 700; color: #0f172a;">
                                    {{ $p->pembuat?->name ?? 'Pimpinan Balai' }}
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    {{ $p->pembuat?->role_label ?? 'Pemberi Tugas' }}
                                </div>
                            </td>

                            <!-- Tim Pelaksana (PIC & Anggota) -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 30px; height: 30px; border-radius: 999px; background: #2563eb; color: white; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        {{ strtoupper(substr($p->penanggungJawab->name, 0, 2)) }}
                                    </div>
                                    <div style="overflow: hidden;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $p->penanggungJawab->name }}">
                                            {{ $p->penanggungJawab->name }}
                                        </div>
                                        <div style="font-size: 11px; color: #2563eb; font-weight: 700;">
                                            PIC Utama
                                        </div>
                                    </div>
                                </div>

                                <!-- Anggota Pelaksana Tambahan -->
                                @if($p->pelaksana->where('id', '!=', $p->penanggung_jawab_id)->isNotEmpty())
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px;">
                                        @foreach($p->pelaksana->where('id', '!=', $p->penanggung_jawab_id)->take(3) as $agt)
                                            <span style="font-size: 10px; background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; font-weight: 600;" title="{{ $agt->name }}">
                                                + {{ Str::words($agt->name, 1, '') }}
                                            </span>
                                        @endforeach
                                        @if($p->pelaksana->where('id', '!=', $p->penanggung_jawab_id)->count() > 3)
                                            <span style="font-size: 10px; background: #e2e8f0; color: #334155; padding: 2px 5px; border-radius: 4px; font-weight: 700;">
                                                +{{ $p->pelaksana->where('id', '!=', $p->penanggung_jawab_id)->count() - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Tenggat Waktu -->
                            <td>
                                <div style="font-weight: 700; color: {{ $p->isTerlambat() ? '#dc2626' : '#0f172a' }};">
                                    {{ $p->tenggat->format('d M Y') }}
                                </div>
                                @if($p->isTerlambat())
                                    <span style="font-size: 10px; background: #fee2e2; color: #dc2626; padding: 1px 6px; border-radius: 4px; font-weight: 800; display: inline-block; margin-top: 2px;">
                                        Lewat Tenggat
                                    </span>
                                @else
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        {{ $p->tenggat->diffForHumans() }}
                                    </div>
                                @endif
                            </td>

                            <!-- Progres Akumulasi Tim -->
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="progress-bar-wrap">
                                        <div class="progress-bar-fill" style="width: {{ $p->progres }}%;"></div>
                                    </div>
                                    <strong style="font-size: 13px; font-family: monospace; color: {{ $p->progres >= 100 ? '#059669' : '#2563eb' }};">
                                        {{ number_format($p->progres, 1) }}%
                                    </strong>
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">
                                    {{ $p->realisasi->count() }} kali lapor progres
                                </div>
                            </td>

                            <!-- Status Pelaksanaan -->
                            <td>
                                <span class="badge-status badge-{{ $p->status_pelaksanaan }}">
                                    @if($p->status_pelaksanaan === 'selesai_terverifikasi')
                                        <i class="ph-bold ph-check-circle"></i> Selesai
                                    @elseif($p->status_pelaksanaan === 'hasil_diajukan')
                                        <i class="ph-bold ph-clock"></i> Verifikasi
                                    @elseif($p->status_pelaksanaan === 'dikerjakan')
                                        <i class="ph-bold ph-spinner"></i> Dikerjakan
                                    @else
                                        <i class="ph-bold ph-hourglass"></i> Belum Mulai
                                    @endif
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('pekerjaan.show', $p) }}" class="btn-action-icon" title="Lihat Rincian & Update Progres">
                                        <i class="ph-bold ph-eye"></i>
                                    </a>
                                    @php
                                        $isPicUtama = $p->penanggung_jawab_id === Auth::id();
                                        $isPembuat  = $p->pembuat_id === Auth::id();
                                        $canManage  = Auth::user()->canManagePekerjaan();
                                    @endphp
                                    @if($isPicUtama || $isPembuat || $canManage)
                                        <a href="{{ route('pekerjaan.edit', $p) }}" class="btn-action-icon" style="color: #b45309;" title="Edit Penugasan">
                                            <i class="ph-bold ph-pencil-simple"></i>
                                        </a>
                                    @endif
                                    @if($isPembuat || $canManage)
                                        <button type="button" class="btn-action-icon" style="color: #dc2626;" onclick="confirmDeletePekerjaan('{{ $p->id }}', '{{ addslashes($p->judul) }}')" title="Hapus Pekerjaan">
                                            <i class="ph-bold ph-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="ph-duotone ph-briefcase" style="font-size: 40px; display: block; margin-bottom: 8px;"></i>
                                <p style="font-size: 15px; font-weight: 700; color: #334155;">Belum ada penugasan pekerjaan yang terdaftar.</p>
                                <p style="font-size: 13px; margin-top: 4px;">Ketua Tim atau Koordinator dapat membuat penugasan melalui tombol di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pekerjaanList->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
                {{ $pekerjaanList->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus Sederhana -->
    <div id="modalHapusIndex" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 14px; max-width: 440px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px; color: #dc2626;">
                <i class="ph-bold ph-warning-octagon" style="font-size: 28px;"></i>
                <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Konfirmasi Hapus Pekerjaan</h4>
            </div>
            <p style="font-size: 13.5px; color: #475569; margin-bottom: 20px; line-height: 1.5;">
                Apakah Anda yakin ingin menghapus penugasan: <strong id="deleteJobTitle">-</strong>? Seluruh berkas dan riwayat progres di dalamnya akan ikut terhapus.
            </p>
            <form id="formHapusIndex" method="POST" style="display: flex; justify-content: flex-end; gap: 10px;">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeHapusModal()" style="padding: 9px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; font-weight: 600; font-size: 13px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 20px; border-radius: 8px; border: none; background: #dc2626; color: white; font-weight: 700; font-size: 13px; cursor: pointer;">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function confirmDeletePekerjaan(id, title) {
        document.getElementById('formHapusIndex').action = `/pekerjaan/${id}`;
        document.getElementById('deleteJobTitle').textContent = title;
        document.getElementById('modalHapusIndex').style.display = 'flex';
    }

    function closeHapusModal() {
        document.getElementById('modalHapusIndex').style.display = 'none';
    }
</script>
@endsection
