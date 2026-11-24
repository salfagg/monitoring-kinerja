@extends('layouts.app')

@section('title', 'Daftar Pekerjaan & Penugasan | TANJIDOR BGTK')
@section('page-title', 'Daftar Pekerjaan & Penugasan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pekerjaan.css') }}">
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

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('rekap-penugasan.index') }}" class="btn-create-main" style="background: #10b981; border: 1px solid #059669;" title="Lihat Rekap Penugasan & Surat Tugas per Orang (Tarik Excel)">
                    <i class="ph-bold ph-file-xls"></i>
                    <span>Rekap Penugasan & ST</span>
                </a>
                @if(Auth::user()->canManagePekerjaan())
                    <a href="{{ route('pekerjaan.create') }}" class="btn-create-main">
                        <i class="ph-bold ph-plus-circle"></i>
                        <span>+ Buat Penugasan Baru</span>
                    </a>
                @endif
            </div>
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
                        <th style="width: 30%;">Pekerjaan & Unit Kerja</th>
                        <th style="width: 16%;">Pemberi Tugas</th>
                        <th style="width: 20%;">Tim Pelaksana</th>
                        <th style="width: 12%;">Tenggat</th>
                        <th style="width: 20%;">Status & Aktivitas</th>
                        <th style="width: 12%;">Persetujuan</th>
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

                            <!-- Status & Aktivitas -->
                            <td>
                                <div>
                                    @if($p->status_pelaksanaan === 'selesai_terverifikasi')
                                        <span class="badge-status badge-disetujui" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                                            <i class="ph-bold ph-check-circle"></i> Selesai
                                        </span>
                                    @elseif($p->status_pelaksanaan === 'hasil_diajukan')
                                        <span class="badge-status" style="background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; font-size: 11px; padding: 3px 8px; font-weight: 700;">
                                            <i class="ph-bold ph-clock"></i> Verifikasi Hasil
                                        </span>
                                    @elseif($p->status_pelaksanaan === 'dikerjakan')
                                        <span class="badge-status badge-perlu_perbaikan" style="background: #eff6ff; color: #2563eb; border-color: #bfdbfe; font-size: 11px; padding: 3px 8px; font-weight: 700;">
                                            <i class="ph-bold ph-spinner"></i> Dikerjakan
                                        </span>
                                    @elseif($p->status_pelaksanaan === 'perlu_perbaikan_hasil')
                                        <span class="badge-status badge-dibatalkan" style="background: #fef2f2; color: #dc2626; border-color: #fecaca; font-size: 11px; padding: 3px 8px; font-weight: 700;">
                                            <i class="ph-bold ph-warning"></i> Perlu Revisi
                                        </span>
                                    @else
                                        <span class="badge-status badge-dibatalkan" style="background: #f8fafc; color: #64748b; border-color: #e2e8f0; font-size: 11px; padding: 3px 8px; font-weight: 700;">
                                            <i class="ph-bold ph-circle-dashed"></i> Belum Mulai
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px; display: flex; flex-direction: column; gap: 3px;">
                                    @if($p->children->isNotEmpty())
                                        <span><i class="ph-bold ph-git-fork"></i> {{ $p->children->where('status_pelaksanaan', 'selesai_terverifikasi')->count() }}/{{ $p->children->count() }} Subtugas Tuntas</span>
                                    @else
                                        <span><i class="ph-bold ph-chats-circle"></i> {{ $p->realisasi->where('status_approval', 'disetujui')->count() }} Laporan di-ACC</span>
                                    @endif

                                    @php $pendingCnt = $p->realisasi->where('status_approval', 'menunggu_persetujuan')->count(); @endphp
                                    @if($pendingCnt > 0)
                                        <span style="font-size: 10px; background: #fef3c7; color: #b45309; padding: 1px 6px; border-radius: 4px; font-weight: 700; width: fit-content;">
                                            <i class="ph-bold ph-bell"></i> {{ $pendingCnt }} butuh ACC Katim
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Persetujuan -->
                            <td>
                                <span class="badge-status badge-{{ $p->status_persetujuan }}">
                                    {{ str_replace('_', ' ', $p->status_persetujuan) }}
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

    <!-- Form Hidden Hapus Pekerjaan -->
    <form id="formHapusIndex" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
<script>
    function confirmDeletePekerjaan(id, title) {
        window.tanjidorConfirm({
            title: 'Konfirmasi Hapus Pekerjaan',
            text: `Apakah Anda yakin ingin menghapus penugasan "${title}"? Seluruh berkas dan riwayat progres di dalamnya akan ikut terhapus.`,
            confirmButtonText: 'Ya, Hapus',
            type: 'danger',
            icon: 'warning'
        }).then(confirmed => {
            if (confirmed) {
                const form = document.getElementById('formHapusIndex');
                form.action = `/pekerjaan/${id}`;
                form.submit();
            }
        });
    }
</script>
@endsection
