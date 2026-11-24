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
            <h2>BALAI GURU PENGGERAK PROVINSI DKI JAKARTA</h2>
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

        <!-- Ringkasan Eksekutif -->
        <div class="rekap-summary-strip">
            <div class="rekap-summary-box">
                <strong>{{ $totalHariAktif }} Hari</strong>
                <span>Hari Kerja Terisi</span>
            </div>
            <div class="rekap-summary-box">
                <strong>{{ $totalLogs }}</strong>
                <span>Total Aktivitas Log</span>
            </div>
            <div class="rekap-summary-box">
                <strong>{{ $totalJamKerja }} Jam</strong>
                <span>Total Durasi Kerja</span>
            </div>
            <div class="rekap-summary-box">
                <strong>{{ $totalVolume }}</strong>
                <span>Total Volume Capaian</span>
            </div>
        </div>

        <!-- Tabel Catatan Kegiatan Harian -->
        <table class="rekap-table">
            <thead>
                <tr>
                    <th style="width: 32px;">No</th>
                    <th style="width: 85px;">Tanggal</th>
                    <th style="width: 85px;">Waktu</th>
                    <th>Uraian Aktivitas / Kegiatan Harian</th>
                    <th style="width: 140px;">Output / Hasil</th>
                    <th style="width: 70px;">Volume</th>
                    <th style="width: 130px;">Tautan Tugas / SKP</th>
                    <th style="width: 90px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    @php
                        $durasiStr = '-';
                        if ($log->jam_mulai && $log->jam_selesai) {
                            $start = \Carbon\Carbon::parse($log->jam_mulai);
                            $end = \Carbon\Carbon::parse($log->jam_selesai);
                            $diff = $start->diffInMinutes($end);
                            $durasiStr = round($diff / 60, 1) . ' Jam';
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 700;">{{ $index + 1 }}</td>
                        <td style="white-space: nowrap; font-weight: 600;">
                            {{ \Carbon\Carbon::parse($log->tanggal_kegiatan)->format('d/m/Y') }}
                        </td>
                        <td style="font-family: monospace; font-size: 11px; text-align: center;">
                            @if($log->jam_mulai && $log->jam_selesai)
                                {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                                <br><small style="color: #64748b;">({{ $durasiStr }})</small>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $log->uraian_kegiatan }}</strong>
                            @if($log->catatan_katim)
                                <div style="font-size: 10.5px; color: #047857; margin-top: 3px;">
                                    <em>Catatan Katim: {{ $log->catatan_katim }}</em>
                                </div>
                            @endif
                            @if($log->buktiDukung->isNotEmpty())
                                <div style="font-size: 10.5px; color: #0284c7; margin-top: 2px;">
                                    ðŸ“Ž {{ $log->buktiDukung->pluck('nama_dokumen')->implode(', ') }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $log->output_kegiatan }}</td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ $log->volume_capaian }} {{ $log->satuan }}
                        </td>
                        <td style="font-size: 11px;">
                            @if($log->pekerjaan)
                                <div style="color: #0b4a82;"><strong>Tugas:</strong> [{{ $log->pekerjaan->kode_tampilan }}] {{ Str::limit($log->pekerjaan->judul, 25) }}</div>
                            @endif
                            @if($log->skpTahunan)
                                <div style="color: #6f45bd;"><strong>SKP:</strong> {{ Str::limit($log->skpTahunan->rencana_hasil_kerja, 25) }}</div>
                            @endif
                            @if(!$log->pekerjaan && !$log->skpTahunan)
                                <span style="color: #94a3b8;">Umum</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 11px;">
                            {{ $log->status_label }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 24px; color: #94a3b8;">
                            Belum ada catatan log harian pada periode bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

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

