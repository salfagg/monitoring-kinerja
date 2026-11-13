@extends('layouts.app')

@section('title', 'Edit Log Harian')
@section('page-title', 'Edit Log Harian')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('log-harian.index', ['bulan' => $logHarian->bulan, 'tahun' => $logHarian->tahun]) }}" style="font-size: 13px; font-weight: 700; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="ph-bold ph-arrow-left"></i>
            Kembali ke Kalender Log Harian
        </a>
    </div>

    <div class="section-card" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; background: white; border: 1px solid var(--border);">
        <div class="section-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, rgba(10, 111, 181, 0.04), transparent);">
            <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                <i class="ph-duotone ph-pencil-simple" style="color: var(--primary);"></i>
                <span>Edit Catatan Log Kinerja Harian</span>
            </h3>
        </div>

        <form action="{{ route('log-harian.update', $logHarian) }}" method="POST" enctype="multipart/form-data" style="padding: 24px;">
            @csrf
            @method('PUT')

            <!-- Tanggal & Jam Kegiatan -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Tanggal Kegiatan <span style="color: red;">*</span>
                    </label>
                    <input type="date" name="tanggal_kegiatan" class="form-control" required value="{{ old('tanggal_kegiatan', $logHarian->tanggal_kegiatan?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', $logHarian->jam_mulai ? substr($logHarian->jam_mulai, 0, 5) : '') }}">
                </div>
                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', $logHarian->jam_selesai ? substr($logHarian->jam_selesai, 0, 5) : '') }}">
                </div>
            </div>

            <!-- Uraian Aktivitas -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                    Uraian Aktivitas / Kegiatan Harian <span style="color: red;">*</span>
                </label>
                <textarea name="uraian_kegiatan" class="form-control" rows="4" required>{{ old('uraian_kegiatan', $logHarian->uraian_kegiatan) }}</textarea>
            </div>

            <!-- Output & Capaian -->
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Output / Hasil <span style="color: red;">*</span>
                    </label>
                    <input type="text" name="output_kegiatan" class="form-control" required value="{{ old('output_kegiatan', $logHarian->output_kegiatan) }}">
                </div>
                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Volume <span style="color: red;">*</span>
                    </label>
                    <input type="number" step="0.1" name="volume_capaian" class="form-control" required value="{{ old('volume_capaian', $logHarian->volume_capaian) }}">
                </div>
                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Satuan <span style="color: red;">*</span>
                    </label>
                    <input type="text" name="satuan" class="form-control" required value="{{ old('satuan', $logHarian->satuan) }}">
                </div>
            </div>

            <!-- Rujukan Tugas & SKP -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">Tautkan ke Tugas / Pekerjaan (Opsional)</label>
                <select name="pekerjaan_id" class="form-control">
                    <option value="">-- Tanpa Rujukan Pekerjaan Tertentu --</option>
                    @foreach($activePekerjaan as $pkj)
                        <option value="{{ $pkj->id }}" {{ $logHarian->pekerjaan_id == $pkj->id ? 'selected' : '' }}>
                            [{{ $pkj->kode_tampilan }}] {{ $pkj->judul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">Rujukan Rencana Hasil Kerja SKP (Opsional)</label>
                <select name="skp_tahunan_id" class="form-control">
                    <option value="">-- Tanpa Rujukan SKP Tertentu --</option>
                    @foreach($skpList as $skp)
                        <option value="{{ $skp->id }}" {{ $logHarian->skp_tahunan_id == $skp->id ? 'selected' : '' }}>
                            [{{ $skp->kode_rhk }}] {{ $skp->rencana_hasil_kerja }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Bukti Dokumen Eksisting -->
            @if($logHarian->buktiDukung->isNotEmpty())
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 12px 14px; margin-bottom: 16px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">Bukti Dokumen Terlampir Saat Ini:</span>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @foreach($logHarian->buktiDukung as $b)
                            @if($b->tipe === 'file')
                                <a href="/storage/{{ $b->file_path }}" target="_blank" style="font-size: 12px; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 4px 10px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="ph-bold ph-file-text"></i> {{ $b->nama_dokumen }}
                                </a>
                            @else
                                <a href="{{ $b->link_url }}" target="_blank" style="font-size: 12px; font-weight: 700; color: #059669; background: #ecfdf5; padding: 4px 10px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="ph-bold ph-arrow-square-out"></i> {{ $b->nama_dokumen }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tambah Bukti Baru -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">Unggah / Ganti Bukti Dukung (Opsional)</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx">
                        <small style="color: var(--text-muted); font-size: 11px;">Upload file baru (PDF, JPG, PNG, DOCX, XLSX)</small>
                    </div>
                    <div>
                        <input type="url" name="link_bukti" class="form-control" placeholder="Atau tempel link Google Drive / Cloud">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 18px;">
                <div>
                    <span style="font-size: 12.5px; color: var(--text-muted);">
                        Status saat ini: <strong style="color: var(--text-dark);">{{ $logHarian->status_label }}</strong>
                    </span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('log-harian.index', ['bulan' => $logHarian->bulan, 'tahun' => $logHarian->tahun]) }}" class="btn-secondary" style="padding: 9px 18px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 700; border: 1px solid var(--border); background: white; color: var(--text-dark);">
                        Batal
                    </a>
                    <button type="submit" name="aksi_status" value="diajukan" class="btn-primary" style="padding: 9px 20px; border-radius: 8px; border: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="ph-bold ph-check"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

