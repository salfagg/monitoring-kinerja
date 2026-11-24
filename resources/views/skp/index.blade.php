@extends('layouts.app')

@section('title', ($isManager ? 'Sasaran Kinerja Pegawai (SKP)' : 'SKP Saya') . ' | TANJIDOR')
@section('page-title', $isManager ? 'Sasaran Kinerja Pegawai (SKP)' : 'Sasaran Kinerja Saya (SKP)')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/master-data.css') }}">
@endsection

@section('content')

    <!-- Flash Notification -->
    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600;">
            <i class="ph-bold ph-check-circle" style="font-size: 20px; color: #16a34a;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600;">
            <i class="ph-bold ph-warning-circle" style="font-size: 20px; color: #dc2626;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. KPI Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-icon icon-blue">
                <i class="ph-duotone ph-target"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($totalSkp) }}</h4>
                <p>{{ $isManager ? 'Total Butir SKP' : 'SKP Saya' }}</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-green">
                <i class="ph-duotone ph-seal-check"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($skpDisetujui) }}</h4>
                <p>SKP Disetujui</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-amber">
                <i class="ph-duotone ph-clock"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($skpDiajukan) }}</h4>
                <p>Menunggu Persetujuan</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-gray">
                <i class="ph-duotone ph-notepad"></i>
            </div>
            <div class="summary-info">
                <h4>{{ number_format($skpDraft) }}</h4>
                <p>Draf / Perlu Revisi</p>
            </div>
        </div>
    </div>

    <!-- 2. Toolbar & Filter -->
    <div class="toolbar-wrapper">
        <div class="toolbar-top">
            <div>
                <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">
                    {{ $isManager ? 'Daftar Sasaran Kinerja Pegawai (SKP)' : 'Daftar Sasaran Kinerja Saya (RHK / SKP)' }}
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                    {{ $isManager ? 'Kelola butir rencana hasil kerja (RHK), target sasaran kinerja, dan keterkaitan dengan penugasan balai' : 'Rencana hasil kerja (RHK) tahunan Anda yang dapat dijadikan rujukan penugasan pekerjaan' }}
                </p>
            </div>

            <a href="{{ route('skp.create') }}" class="btn-create-main">
                <i class="ph-bold ph-plus-circle"></i>
                <span>+ Tambah SKP Baru</span>
            </a>
        </div>

        <form method="GET" action="{{ route('skp.index') }}" class="filter-grid">
            <input type="text" name="search" value="{{ request('search') }}" class="filter-control" placeholder="Cari uraian RHK, IKI, atau nama pegawai...">

            <select name="tahun" class="filter-control">
                <option value="semua">-- Semua Tahun --</option>
                @for($y = date('Y') + 1; $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>
                        Tahun {{ $y }}
                    </option>
                @endfor
            </select>

            <select name="status" class="filter-control">
                <option value="">-- Semua Status --</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draf</option>
                <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>Revisi</option>
            </select>

            @if($isManager)
                <select name="user_id" class="filter-control">
                    <option value="">-- Semua Pegawai --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->role_label }})
                        </option>
                    @endforeach
                </select>
            @endif

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-action-icon" style="width: auto; padding: 0 16px; background: #0f172a; color: white; border: none; font-weight: 700; gap: 6px;">
                    <i class="ph-bold ph-funnel"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'tahun', 'status', 'user_id', 'pokja_id']))
                    <a href="{{ route('skp.index') }}" class="btn-action-icon" style="color: #ef4444;" title="Reset Filter">
                        <i class="ph-bold ph-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Tabel SKP -->
    <div class="table-card">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Pegawai & Unit</th>
                        <th style="width: 12%;">Tahun / Aspek</th>
                        <th style="width: 32%;">Rencana Hasil Kerja (RHK) & IKI</th>
                        <th style="width: 14%;">Target Output</th>
                        <th style="width: 10%;">Tautan Pekerjaan</th>
                        <th style="width: 12%;">Status</th>
                        <th style="text-align: right; width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skpList as $s)
                        <tr>
                            <!-- Pegawai & Unit -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 9px;">
                                    <div style="width: 32px; height: 32px; border-radius: 999px; background: #0284c7; color: white; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        {{ strtoupper(substr($s->user?->name ?? 'P', 0, 2)) }}
                                    </div>
                                    <div style="overflow: hidden;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $s->user?->name }}">
                                            {{ $s->user?->name ?? '-' }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            {{ $s->user?->pokja?->nama_pokja ?? 'BGTK DKI Jakarta' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Tahun / Aspek -->
                            <td>
                                <div style="font-weight: 800; color: #0f172a; font-size: 13.5px;">
                                    Tahun {{ $s->tahun }}
                                </div>
                                <span style="font-size: 10.5px; background: #f1f5f9; color: #475569; padding: 2px 7px; border-radius: 4px; font-weight: 700; display: inline-block; margin-top: 3px;">
                                    {{ $s->aspek }}
                                </span>
                            </td>

                            <!-- Rencana Hasil Kerja (RHK) & IKI -->
                            <td>
                                <a href="{{ route('skp.show', $s) }}" style="font-weight: 700; color: #0c4a6e; font-size: 13.5px; text-decoration: none; line-height: 1.4; display: block;">
                                    {{ $s->rencana_kinerja }}
                                </a>
                                @if($s->indikator_kinerja)
                                    <div style="font-size: 11.5px; color: #64748b; margin-top: 5px; line-height: 1.4;">
                                        <strong style="color: #475569;">IKI:</strong> {{ Str::limit($s->indikator_kinerja, 110) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Target Output -->
                            <td>
                                <div style="font-weight: 800; color: #0284c7; font-size: 13.5px;">
                                    {{ (float)$s->target_max }} {{ $s->satuan }}
                                </div>
                                @if($s->target_min > 0 && $s->target_min != $s->target_max)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                        Min: {{ (float)$s->target_min }} {{ $s->satuan }}
                                    </div>
                                @endif
                            </td>

                            <!-- Tautan Pekerjaan Terkait -->
                            <td>
                                @php $jobCount = $s->pekerjaan->count(); @endphp
                                @if($jobCount > 0)
                                    <a href="{{ route('pekerjaan.index', ['search' => $s->rencana_kinerja]) }}" style="display: inline-flex; align-items: center; gap: 4px; background: #e0f2fe; color: #0284c7; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 99px; text-decoration: none;" title="Lihat pekerjaan yang merujuk ke SKP ini">
                                        <i class="ph-bold ph-briefcase"></i>
                                        <span>{{ $jobCount }} Tugas</span>
                                    </a>
                                @else
                                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">
                                        Belum ditautkan
                                    </span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td>
                                <span class="badge-status-skp status-{{ $s->status }}">
                                    @if($s->status === 'disetujui')
                                        <i class="ph-bold ph-seal-check"></i>
                                    @elseif($s->status === 'diajukan')
                                        <i class="ph-bold ph-clock"></i>
                                    @elseif($s->status === 'revisi')
                                        <i class="ph-bold ph-warning-circle"></i>
                                    @else
                                        <i class="ph-bold ph-pencil"></i>
                                    @endif
                                    {{ ucfirst($s->status) }}
                                </span>

                                @if($isManager && $s->status === 'diajukan')
                                    <form action="{{ route('skp.approve', $s) }}" method="POST" style="margin-top: 6px;"
                                          data-confirm="Setujui butir SKP ini?"
                                          data-confirm-title="Persetujuan SKP"
                                          data-confirm-btn="Ya, Setujui"
                                          data-confirm-type="success">
                                        @csrf
                                        <button type="submit" class="btn-approve-quick">
                                            <i class="ph-bold ph-check"></i> Setujui
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('skp.show', $s) }}" class="btn-action-icon" title="Lihat Rincian SKP">
                                        <i class="ph-bold ph-eye"></i>
                                    </a>

                                    <a href="{{ route('skp.edit', $s) }}" class="btn-action-icon" title="Edit SKP" style="color: #b45309;">
                                        <i class="ph-bold ph-pencil-simple"></i>
                                    </a>

                                    <form action="{{ route('skp.destroy', $s) }}" method="POST" style="display: inline;"
                                          data-confirm="Apakah Anda yakin ingin menghapus butir SKP ini?"
                                          data-confirm-title="Hapus Butir SKP"
                                          data-confirm-btn="Ya, Hapus"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon" style="color: #dc2626;" title="Hapus SKP">
                                            <i class="ph-bold ph-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px 20px;">
                                <div style="font-size: 40px; color: #94a3b8; margin-bottom: 10px;">
                                    <i class="ph-duotone ph-target"></i>
                                </div>
                                <h4 style="font-size: 15px; font-weight: 800; color: #334155;">Belum ada Sasaran Kinerja Pegawai (SKP)</h4>
                                <p style="font-size: 13px; color: var(--text-muted); max-width: 420px; margin: 4px auto 16px auto;">
                                    {{ $isManager ? 'Belum ada SKP yang diinput atau sesuai dengan filter pencarian.' : 'Anda belum menambahkan butir rencana hasil kerja (SKP) tahun ini.' }}
                                </p>
                                <a href="{{ route('skp.create') }}" class="btn-create-main">
                                    <i class="ph-bold ph-plus-circle"></i>
                                    <span>Tambah SKP Sekarang</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($skpList->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
                {{ $skpList->links() }}
            </div>
        @endif
    </div>

@endsection

