@extends('layouts.app')

@section('title', 'Rekapitulasi Log Harian Bulanan')
@section('page-title', 'Rekapitulasi Kinerja Bulanan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/log-harian.css') }}">
@endsection

@section('content')

    <!-- Action Toolbar (Disembunyikan saat Cetak) -->
    <div class="rekap-action-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('log-harian.index', ['user_id' => $targetUser->id, 'bulan' => $month, 'tahun' => $year]) }}" class="btn-secondary" style="padding: 7px 14px; font-size: 12.5px; text-decoration: none; border-radius: 8px; font-weight: 700; border: 1px solid var(--border); background: white; color: var(--text-dark); display: inline-flex; align-items: center; gap: 6px;">
                <i class="ph-bold ph-arrow-left"></i>
                <span>Kembali ke Kalender</span>
            </a>

            <!-- Quick Month Switcher -->
            <form action="{{ route('log-harian.rekap') }}" method="GET" style="display: inline-flex; align-items: center; gap: 6px;">
                <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                <select name="bulan" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 6px 10px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none; cursor: pointer;">
                    @foreach($namaBulanIndo as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ $month == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
                <select name="tahun" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 6px 10px; border: 1px solid var(--border); border-radius: 8px; background: white; outline: none; cursor: pointer;">
                    @for($y = 2024; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <!-- Tombol Ekspor CSV -->
            <a href="{{ route('log-harian.export-csv', ['user_id' => $targetUser->id, 'bulan' => $month, 'tahun' => $year]) }}" class="btn-secondary" style="padding: 7px 14px; font-size: 12.5px; text-decoration: none; border-radius: 8px; font-weight: 700; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; display: inline-flex; align-items: center; gap: 6px;">
                <i class="ph-bold ph-file-csv" style="font-size: 16px;"></i>
                <span>Tarik Data CSV (Excel)</span>
            </a>

            <!-- Tombol Cetak Dokumen -->
            <button type="button" onclick="window.print()" class="btn-primary" style="padding: 7px 16px; font-size: 12.5px; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <i class="ph-bold ph-printer" style="font-size: 16px;"></i>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Rekapitulasi Resmi -->
    <div class="rekap-sheet">
        <!-- Kop Dokumen Resmi -->
        <div class="rekap-header-kop">
            <h3>Kementerian Pendidikan Dasar dan Menengah</h3>
            <h2>BALAI GURU DAN TENAGA KEPENDIDIKAN PROVINSI DKI JAKARTA</h2>
            <p>Tata Kelola Kinerja Digital dan Monitoring (TANJIDOR)</p>
        </div>

        <!-- Judul Laporan -->
        <div class="rekap-title-box">
            <h1>Laporan Capaian Kinerja &amp; Catatan Aktivitas Harian</h1>
            <span>Periode: Bulan {{ $namaBulanIndo[$month] }} {{ $year }}</span>
        </div>

        <!-- Identitas Pegawai -->
        <div class="identitas-grid">
            <span class="identitas-label">Nama Pegawai</span>
            <span class="identitas-value">: {{ $targetUser->name }}</span>
            <span class="identitas-label">Jabatan</span>
            <span class="identitas-value">: {{ $targetUser->jabatan ?? 'Pegawai' }}</span>

            <span class="identitas-label">NIP</span>
            <span class="identitas-value">: {{ $targetUser->nip ?? '-' }}</span>
            <span class="identitas-label">Unit Kerja / Pokja</span>
            <span class="identitas-value">: {{ $targetUser->pokja?->nama_pokja ?? 'BGTK DKI Jakarta' }}</span>
        </div>

        <!-- Tabel Catatan Kegiatan Harian -->
        <div class="rekap-table-wrap">
        <table class="rekap-table">
            <thead>
                <tr>
                    <th style="width: 44px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th>Uraian Aktivitas / Kegiatan Harian</th>
                    <th style="width: 180px;">Output / Hasil</th>
                    <th style="width: 90px;">Volume</th>
                    <th style="width: 170px;">Tautan Tugas / SKP</th>
                    <th style="width: 110px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedLogs = $logs->groupBy(fn($item) => \Carbon\Carbon::parse($item->tanggal_kegiatan)->format('Y-m-d'));
                @endphp
                @forelse($groupedLogs as $dateKey => $dayLogs)
                    @php
                        $hasMulti = $dayLogs->count() > 1;
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 700;">{{ $loop->iteration }}</td>
                        <td style="white-space: nowrap; font-weight: 600; text-align: center;">
                            {{ \Carbon\Carbon::parse($dateKey)->format('d/m/Y') }}
                        </td>
                        <td>
                            @foreach($dayLogs as $i => $log)
                                <div class="rekap-cell-item {{ $i > 0 ? 'has-sep' : '' }}">
                                    <strong>@if($hasMulti)<span style="color: var(--primary);">{{ $i + 1 }}.</span> @endif{{ $log->uraian_kegiatan }}</strong>
                                    @if($log->catatan_katim)
                                        <div style="font-size: 10.5px; color: #047857; margin-top: 3px;">
                                            <em>Catatan Katim: {{ $log->catatan_katim }}</em>
                                        </div>
                                    @endif
                                    @if($log->buktiDukung->isNotEmpty())
                                        <div style="font-size: 10.5px; color: #0284c7; margin-top: 2px;">
                                            📎 {{ $log->buktiDukung->pluck('nama_dokumen')->implode(', ') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td>
                            @foreach($dayLogs as $i => $log)
                                <div class="rekap-cell-item {{ $i > 0 ? 'has-sep' : '' }}">
                                    @if($hasMulti)<span style="color: var(--primary); font-weight: 700;">{{ $i + 1 }}.</span> @endif{{ $log->output_kegiatan }}
                                </div>
                            @endforeach
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            @foreach($dayLogs as $i => $log)
                                <div class="rekap-cell-item {{ $i > 0 ? 'has-sep' : '' }}">
                                    {{ $log->volume_capaian }} {{ $log->satuan }}
                                </div>
                            @endforeach
                        </td>
                        <td style="font-size: 11px;">
                            @foreach($dayLogs as $i => $log)
                                <div class="rekap-cell-item {{ $i > 0 ? 'has-sep' : '' }}">
                                    @if($log->pekerjaan)
                                        <div style="color: #0b4a82;"><strong>Tugas:</strong> [{{ $log->pekerjaan->kode_tampilan }}] {{ Str::limit($log->pekerjaan->judul, 28) }}</div>
                                    @endif
                                    @if($log->skpTahunan)
                                        <div style="color: #6f45bd;"><strong>SKP:</strong> {{ Str::limit($log->skpTahunan->rencana_hasil_kerja, 28) }}</div>
                                    @endif
                                    @if(!$log->pekerjaan && !$log->skpTahunan)
                                        <span style="color: #94a3b8;">Umum</span>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 11px;">
                            @foreach($dayLogs as $i => $log)
                                <div class="rekap-cell-item {{ $i > 0 ? 'has-sep' : '' }}">
                                    {{ $log->status_label }}
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 28px; color: #94a3b8;">
                            Belum ada catatan log harian pada periode bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <!-- Lembar Pengesahan / Tanda Tangan -->
        <div class="rekap-ttd-row">
            <div class="rekap-ttd-col">
                <p>Mengetahui / Menyetujui,</p>
                <p><strong>Atasan Langsung / Katim</strong></p>
                <div class="rekap-ttd-space"></div>
                <div class="rekap-ttd-name">{{ $pejabatPenilai?->name ?? '...................................................' }}</div>
                <div class="rekap-ttd-nip">NIP. {{ $pejabatPenilai?->nip ?? '....................................' }}</div>
            </div>

            <div class="rekap-ttd-col">
                <p>Jakarta, {{ \Carbon\Carbon::now()->format('d') }} {{ $namaBulanIndo[$month] }} {{ $year }}</p>
                <p><strong>Pegawai Yang Membuat Laporan,</strong></p>
                <div class="rekap-ttd-space"></div>
                <div class="rekap-ttd-name">{{ $targetUser->name }}</div>
                <div class="rekap-ttd-nip">NIP. {{ $targetUser->nip ?? '....................................' }}</div>
            </div>
        </div>
    </div>

@endsection

