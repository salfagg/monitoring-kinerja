@extends('layouts.app')

@section('title', "Edit Pekerjaan: {$pekerjaan->judul} | TANJIDOR")
@section('page-title', "Edit Pekerjaan: {$pekerjaan->kode_tampilan}")

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endsection

@section('content')

<form id="formPekerjaan" action="{{ route('pekerjaan.update', $pekerjaan) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-page-layout">
        
        <!-- ==================== KOLOM KIRI: FORMULIR INPUT ==================== -->
        <div class="form-main-content">
            
            <!-- SECTION KHUSUS: Kategori & Sumber Penugasan (Manual vs SKP) -->
            <div class="form-card" style="border: 2px solid #bae6fd; background: linear-gradient(180deg, #f8fafc 0%, #f0f9ff 100%);">
                <div class="form-card-header" style="border-bottom-color: #e2e8f0;">
                    <div class="card-step-badge" style="background: #0284c7; color: #ffffff;">
                        <i class="ph-bold ph-git-fork"></i>
                    </div>
                    <div>
                        <h3>Kategori & Sumber Penugasan</h3>
                        <p>Pilih apakah pekerjaan ini merupakan tugas mandiri/manual atau ditautkan langsung ke target SKP pegawai.</p>
                    </div>
                </div>

                @php
                    $currentSumber = old('sumber_pekerjaan', $pekerjaan->sumber_pekerjaan ?? 'manual');
                    $currentSkpId = old('skp_tahunan_id', $pekerjaan->skp_tahunan_id);
                @endphp

                <!-- Pilihan 2 Card Sumber Penugasan -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px;">
                    <!-- Option 1: Manual -->
                    <label class="sumber-card {{ $currentSumber === 'manual' ? 'active' : '' }}" id="cardSumberManual" onclick="switchSumberPekerjaan('manual')">
                        <input type="radio" name="sumber_pekerjaan" value="manual" id="radioManual" {{ $currentSumber === 'manual' ? 'checked' : '' }} style="display: none;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div class="sumber-icon-box" style="background: #e2e8f0; color: #334155;">
                                <i class="ph-bold ph-pencil-line"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 13.5px; color: #0f172a; margin-bottom: 2px;">
                                    Pekerjaan Mandiri / Manual
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; line-height: 1.4;">
                                    Penugasan umum, kegiatan lapangan, atau pekerjaan insidental yang diisi bebas.
                                </div>
                            </div>
                        </div>
                        <div class="sumber-check-badge">
                            <i class="ph-bold ph-check"></i>
                        </div>
                    </label>

                    <!-- Option 2: Merujuk ke SKP -->
                    <label class="sumber-card {{ $currentSumber === 'skp' ? 'active' : '' }}" id="cardSumberSkp" onclick="switchSumberPekerjaan('skp')">
                        <input type="radio" name="sumber_pekerjaan" value="skp" id="radioSkp" {{ $currentSumber === 'skp' ? 'checked' : '' }} style="display: none;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div class="sumber-icon-box" style="background: #e0f2fe; color: #0284c7;">
                                <i class="ph-bold ph-target"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 13.5px; color: #0284c7; margin-bottom: 2px; display: flex; align-items: center; gap: 6px;">
                                    <span>Merujuk ke SKP Pegawai</span>
                                    <span style="font-size: 9.5px; background: #0284c7; color: #fff; padding: 1px 6px; border-radius: 99px; font-weight: 800;">TERHUBUNG</span>
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; line-height: 1.4;">
                                    Terhubung ke Sasaran Kinerja Pegawai (RHK). Judul, target & satuan otomatis terisi!
                                </div>
                            </div>
                        </div>
                        <div class="sumber-check-badge">
                            <i class="ph-bold ph-check"></i>
                        </div>
                    </label>
                </div>

                <!-- Panel Pemilihan SKP (Hanya Tampil Jika Rujukan SKP Dipilih) -->
                <div id="panelRujukanSkp" style="{{ $currentSumber === 'skp' ? 'display: block;' : 'display: none;' }} background: #ffffff; border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 18px; margin-top: 14px; box-shadow: 0 4px 16px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="font-weight: 800; font-size: 13px; color: #0369a1; display: flex; align-items: center; gap: 6px;">
                            <i class="ph-bold ph-crosshair"></i> Pilih Butir Rencana Kinerja SKP (RHK)
                        </div>
                        <span style="font-size: 11px; color: #0284c7; background: #f0f9ff; padding: 2px 8px; border-radius: 99px; font-weight: 700;">
                            âœ¨ Auto-Fill Form
                        </span>
                    </div>

                    <div class="form-group" style="margin-bottom: 8px;">
                        <div class="input-wrapper">
                            <i class="ph-bold ph-list-star input-icon" style="color: #0284c7;"></i>
                            <select id="skp_tahunan_id" name="skp_tahunan_id" class="form-select @error('skp_tahunan_id') is-invalid @enderror" onchange="handleSkpSelection(this)">
                                <option value="">-- Pilih Rencana Hasil Kerja (SKP Pegawai) --</option>
                                @foreach($skpList as $skp)
                                    <option value="{{ $skp->id }}" 
                                            data-rhk="{{ $skp->rencana_kinerja }}"
                                            data-target="{{ (float)($skp->target_max ?: ($skp->target_min ?: 1)) }}"
                                            data-satuan="{{ $skp->satuan }}"
                                            data-user-id="{{ $skp->user_id }}"
                                            data-user-name="{{ $skp->user?->name ?? 'Pegawai' }}"
                                            data-tahun="{{ $skp->tahun }}"
                                            data-iki="{{ $skp->indikator_kinerja }}"
                                            {{ $currentSkpId == $skp->id ? 'selected' : '' }}>
                                        [{{ $skp->tahun }}] {{ $skp->user?->name ?? 'Pegawai' }} — {{ Str::limit($skp->rencana_kinerja, 75) }} ({{ (float)$skp->target_max }} {{ $skp->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('skp_tahunan_id')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Info Alert SKP Terpilih -->
                    <div id="skpSelectedInfo" style="{{ $pekerjaan->skpTahunan ? 'display: block;' : 'display: none;' }} background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; margin-top: 10px;">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <i class="ph-bold ph-check-circle" style="color: #16a34a; font-size: 18px; margin-top: 1px;"></i>
                            <div style="font-size: 12px; color: #166534; line-height: 1.45;">
                                <div><strong>RHK Terpilih:</strong> <span id="skpRhkText">{{ $pekerjaan->skpTahunan?->rencana_kinerja ?? '-' }}</span></div>
                                <div style="margin-top: 3px; font-size: 11px; color: #15803d;">
                                    Pegawai Pemilik: <strong id="skpOwnerText">{{ $pekerjaan->skpTahunan?->user?->name ?? '-' }}</strong> â€¢ Target: <strong id="skpTargetText">{{ (float)($pekerjaan->skpTahunan?->target_max ?? 1) }} {{ $pekerjaan->skpTahunan?->satuan ?? '' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: Identitas & Tim Kerja -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-step-badge">1</div>
                    <div>
                        <h3>Identitas & Tim Kerja</h3>
                        <p>Perbarui kode/nomor pekerjaan, tim kerja naungan, dan nama tugas resmi.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="kode_tampilan" class="form-label">
                            <span>Kode / No. Pekerjaan <span class="required">*</span></span>
                            <span class="form-label-hint">Misal: PK-01, 1, atau 02</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-list-numbers input-icon"></i>
                            <input type="text" 
                                   id="kode_tampilan" 
                                   name="kode_tampilan" 
                                   class="form-input @error('kode_tampilan') is-invalid @enderror" 
                                   value="{{ old('kode_tampilan', $pekerjaan->kode_tampilan) }}" 
                                   placeholder="Misal: 1 atau PK-01" 
                                   required
                                   oninput="updateLivePreview()">
                        </div>
                        @error('kode_tampilan')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pokja_id" class="form-label">
                            <span>Tim Kerja (Pokja)</span>
                            <span class="form-label-hint">Unit Pelaksana</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-buildings input-icon"></i>
                            <select id="pokja_id" name="pokja_id" class="form-select @error('pokja_id') is-invalid @enderror" onchange="updateLivePreview()">
                                <option value="">-- Umum / Tanpa Pokja --</option>
                                @foreach($pokjas as $p)
                                    <option value="{{ $p->id }}" {{ old('pokja_id', $pekerjaan->pokja_id) == $p->id ? 'selected' : '' }} data-name="{{ $p->nama_pokja }}">
                                        {{ $p->kode_pokja }} - {{ $p->nama_pokja }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('pokja_id')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="judul" class="form-label">
                        <span>Nama / Judul Pekerjaan <span class="required">*</span></span>
                        <span id="charCount" class="form-label-hint">0 / 255 karakter</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="ph-bold ph-briefcase input-icon"></i>
                        <input type="text" 
                               id="judul" 
                               name="judul" 
                               maxlength="255"
                               class="form-input @error('judul') is-invalid @enderror" 
                               value="{{ old('judul', $pekerjaan->judul) }}" 
                               placeholder="Contoh: Pelaksanaan Workshop Peningkatan Mutu Guru Penggerak" 
                               required
                               oninput="updateLivePreview()">
                    </div>
                    @error('judul')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SECTION 2: Dasar Penugasan & Surat Tugas (ST) -->
            <div class="form-card" style="background: linear-gradient(180deg, #ffffff 0%, #fffdf9 100%);">
                <div class="form-card-header">
                    <div class="card-step-badge">2</div>
                    <div>
                        <h3>Dasar Penugasan & Surat Tugas (ST)</h3>
                        <p>Perbarui referensi nomor Surat Tugas resmi atau unggah berkas pengganti.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="nomor_st" class="form-label">
                            <span>Nomor Surat Tugas (ST)</span>
                            <span class="form-label-hint">Register BGTK</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-file-text input-icon"></i>
                            <input type="text" 
                                   id="nomor_st" 
                                   name="nomor_st" 
                                   class="form-input @error('nomor_st') is-invalid @enderror" 
                                   value="{{ old('nomor_st', $pekerjaan->nomor_st) }}" 
                                   placeholder="Contoh: 0451/B7.3/KP.02.00/2026"
                                   oninput="updateLivePreview()">
                        </div>
                        @error('nomor_st')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_st" class="form-label">
                            <span>Tanggal Surat Tugas (ST)</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-calendar-blank input-icon"></i>
                            <input type="date" 
                                   id="tanggal_st" 
                                   name="tanggal_st" 
                                   class="form-input @error('tanggal_st') is-invalid @enderror" 
                                   value="{{ old('tanggal_st', $pekerjaan->tanggal_st?->format('Y-m-d')) }}"
                                   onchange="updateLivePreview()">
                        </div>
                        @error('tanggal_st')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Custom Interactive File Dropzone -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Unggah Dokumen ST Baru (PDF / Gambar / Docx)</span>
                        <span class="form-label-hint">Kosongkan jika tidak ingin mengubah</span>
                    </label>

                    <input type="file" 
                           id="file_st" 
                           name="file_st" 
                           accept=".pdf,.png,.jpg,.jpeg,.docx" 
                           style="display: none;" 
                           onchange="handleFileSelect(this)">

                    <div id="dropzoneBox" class="dropzone-container" onclick="document.getElementById('file_st').click()">
                        <div class="dropzone-icon">
                            <i class="ph-bold ph-cloud-arrow-up"></i>
                        </div>
                        <div class="dropzone-text">Pilih Berkas atau Seret ke Sini</div>
                        <div class="dropzone-sub">Mendukung format PDF, PNG, JPG, JPEG, atau DOCX (Maks 10MB)</div>

                        @if($pekerjaan->file_st)
                            <div id="dropzoneFileInfo" class="dropzone-file-info" onclick="event.stopPropagation()">
                                <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                    <i class="ph-bold ph-file-pdf" style="color: #ef4444; font-size: 24px;"></i>
                                    <div style="overflow: hidden;">
                                        <div id="fileNameDisplay" style="font-weight: 700; color: #0f172a; font-size: 13px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                            {{ basename($pekerjaan->file_st) }}
                                        </div>
                                        <div id="fileSizeDisplay" style="font-size: 11px; color: #64748b;">Berkas Terunggah Saat Ini</div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <button type="button" onclick="previewBuktiDukung('{{ asset('storage/' . $pekerjaan->file_st) }}', 'Surat Tugas: {{ basename($pekerjaan->file_st) }}')" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-weight: 700; border-radius: 6px; padding: 3px 8px; font-size: 11.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="ph-bold ph-eye"></i> Pratinjau
                                    </button>
                                    <a href="{{ asset('storage/' . $pekerjaan->file_st) }}" download target="_blank" style="color: #64748b; font-weight: 600; text-decoration: underline; font-size: 12px;">
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        @else
                            <div id="dropzoneFileInfo" class="dropzone-file-info" style="display: none;" onclick="event.stopPropagation()">
                                <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                    <i class="ph-bold ph-file-pdf" style="color: #ef4444; font-size: 24px;"></i>
                                    <div style="overflow: hidden;">
                                        <div id="fileNameDisplay" style="font-weight: 700; color: #0f172a; font-size: 13px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">-</div>
                                        <div id="fileSizeDisplay" style="font-size: 11px; color: #64748b;">-</div>
                                    </div>
                                </div>
                                <button type="button" onclick="removeSelectedFile()" style="background: none; border: none; color: #ef4444; font-weight: 700; cursor: pointer; font-size: 13px;">
                                    Ganti
                                </button>
                            </div>
                        @endif
                    </div>
                    @error('file_st')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SECTION 3: Sasaran Target Output & Kriteria -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-step-badge">3</div>
                    <div>
                        <h3>Target Output & Kriteria Hasil</h3>
                        <p>Kuantifikasi sasaran kuantitas dan kriteria penerimaan hasil akhir pekerjaan.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="target" class="form-label">
                            <span>Target Kuantitas <span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-target input-icon"></i>
                            <input type="number" 
                                   id="target" 
                                   name="target" 
                                   step="0.01" 
                                   min="0.01"
                                   class="form-input @error('target') is-invalid @enderror" 
                                   value="{{ old('target', $pekerjaan->target) }}" 
                                   required
                                   oninput="updateLivePreview()">
                        </div>
                        @error('target')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="satuan" class="form-label">
                            <span>Satuan Ukuran <span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-tag input-icon"></i>
                            <input type="text" 
                                   id="satuan" 
                                   name="satuan" 
                                   class="form-input @error('satuan') is-invalid @enderror" 
                                   value="{{ old('satuan', $pekerjaan->satuan) }}" 
                                   placeholder="Dokumen, Kegiatan, Paket" 
                                   required
                                   oninput="updateLivePreview()">
                        </div>
                        @error('satuan')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" style="margin-top: 18px;">
                    <label for="hasil_diharapkan" class="form-label">
                        <span>Hasil / Output yang Diharapkan <span class="required">*</span></span>
                        <span class="form-label-hint">Kriteria selesai saat verifikasi akhir</span>
                    </label>
                    <textarea id="hasil_diharapkan" 
                              name="hasil_diharapkan" 
                              class="form-textarea @error('hasil_diharapkan') is-invalid @enderror" 
                              rows="2"
                              placeholder="Misal: Laporan pelaksanaan kegiatan terverifikasi..." 
                              required
                              oninput="updateLivePreview()">{{ old('hasil_diharapkan', $pekerjaan->hasil_diharapkan) }}</textarea>
                    @error('hasil_diharapkan')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">
                        <span>Deskripsi Lingkup Kerja</span>
                        <span class="form-label-hint">Latar belakang & rincian teknis pelaksanaan</span>
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              class="form-textarea @error('deskripsi') is-invalid @enderror" 
                              rows="2"
                              placeholder="Jelaskan ruang lingkup dan langkah kerja..."
                              oninput="updateLivePreview()">{{ old('deskripsi', $pekerjaan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SECTION 4: Penugasan Pelaksana & Tenggat Waktu -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-step-badge">4</div>
                    <div>
                        <h3>Penugasan Pelaksana & Tenggat Waktu</h3>
                        <p>Perbarui Koordinator / PIC Utama serta pilih pegawai pelaksana yang bertugas.</p>
                    </div>
                </div>

                <div class="form-grid-2">
                    @php
                        $myPokjaId = Auth::user()->pokja_id;
                        $teamUsers = $users->filter(fn($u) => $myPokjaId && $u->pokja_id == $myPokjaId);
                        $otherUsers = $users->reject(fn($u) => $myPokjaId && $u->pokja_id == $myPokjaId);
                        $currentPicId = old('penanggung_jawab_id', $pekerjaan->penanggung_jawab_id);
                        $existingPelaksanaIds = old('pelaksana_ids', $pekerjaan->pelaksana->pluck('id')->toArray());
                    @endphp
                    <div class="form-group">
                        <label for="penanggung_jawab_id" class="form-label">
                            <span>Koordinator / PIC Utama <span class="required">*</span></span>
                            <span class="form-label-hint">Ketua pelaksana tugas</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-user-check input-icon"></i>
                            <select id="penanggung_jawab_id" 
                                    name="penanggung_jawab_id" 
                                    class="form-select @error('penanggung_jawab_id') is-invalid @enderror" 
                                    required
                                    onchange="handlePicChange()">
                                <option value="">-- Pilih PIC Utama --</option>
                                @if($teamUsers->isNotEmpty())
                                    <optgroup label="â­ Anggota Tim Kerja / Pokja Anda">
                                        @foreach($teamUsers as $u)
                                            <option value="{{ $u->id }}" {{ $currentPicId == $u->id ? 'selected' : '' }} data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}">
                                                {{ $u->name }} ({{ $u->nip ?? 'Pegawai' }}) - {{ $u->role_label }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                <optgroup label="👥 Seluruh Pegawai BGTK">
                                    @foreach($otherUsers as $u)
                                        <option value="{{ $u->id }}" {{ $currentPicId == $u->id ? 'selected' : '' }} data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}">
                                            {{ $u->name }} ({{ $u->nip ?? 'Pegawai' }}) - {{ $u->pokja?->nama_pokja ?? 'BGTK' }} ({{ $u->role_label }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        @error('penanggung_jawab_id')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tenggat" class="form-label">
                            <span>Tenggat Waktu Selesai <span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-calendar-check input-icon"></i>
                            <input type="date" 
                                   id="tenggat" 
                                   name="tenggat" 
                                   class="form-input @error('tenggat') is-invalid @enderror" 
                                   value="{{ old('tenggat', $pekerjaan->tenggat ? $pekerjaan->tenggat->format('Y-m-d') : date('Y-12-31')) }}" 
                                   required
                                   onchange="updateLivePreview()">
                        </div>
                        @error('tenggat')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Tim Pelaksana Tambahan (Multi-Select) -->
                <div class="form-group" style="margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <label class="form-label" style="margin-bottom: 0;">
                            <span>Anggota Tim Pelaksana (Multi-Select)</span>
                            <span class="form-label-hint">Pilih anggota tim yang akan berkontribusi melaporkan progres tugas</span>
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span id="pelaksanaCountBadge" style="background: #eff6ff; color: #1d4ed8; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; border: 1px solid #bfdbfe;">
                                1 Pegawai Terpilih (PIC)
                            </span>
                            @if($teamUsers->isNotEmpty())
                                <button type="button" class="btn-action-outline" style="font-size: 11.5px; padding: 4px 10px; border-radius: 6px;" onclick="selectAllPokja()">
                                    <i class="ph-bold ph-users-three"></i> + Semua Pokja Saya
                                </button>
                            @endif
                            <button type="button" class="btn-action-outline" style="font-size: 11.5px; padding: 4px 10px; border-radius: 6px; color: #dc2626; border-color: #fecaca;" onclick="resetAnggotaTambahan()">
                                <i class="ph-bold ph-x"></i> Reset Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Selected Assignees Chips Container -->
                    <div id="selectedPelaksanaChips" style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; min-height: 38px; padding: 6px 10px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: var(--radius-md); align-items: center;">
                        <!-- Chips rendered via JS -->
                    </div>

                    <!-- Search Input Filter -->
                    <div class="input-wrapper" style="margin-bottom: 10px;">
                        <i class="ph-bold ph-magnifying-glass input-icon"></i>
                        <input type="text" 
                               id="searchPelaksana" 
                               class="form-input" 
                               placeholder="Cari nama pegawai, NIP, atau tim kerja untuk ditugaskan..." 
                               oninput="filterPelaksanaList(this.value)">
                    </div>

                    <!-- Scrollable Assignee Cards Grid -->
                    <div class="pelaksana-selection-container" style="max-height: 270px; overflow-y: auto; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 12px; background: #ffffff;">
                        @if($teamUsers->isNotEmpty())
                            <div class="pelaksana-group-title" style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; letter-spacing: 0.04em; margin-bottom: 8px; padding: 2px 4px;">
                                â­ Anggota Tim Kerja / Pokja Anda
                            </div>
                            <div class="pelaksana-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px; margin-bottom: 16px;">
                                @foreach($teamUsers as $u)
                                    <label class="pelaksana-card" id="pelaksana-card-{{ $u->id }}" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}" data-is-my-pokja="1">
                                        <input type="checkbox" 
                                               name="pelaksana_ids[]" 
                                               value="{{ $u->id }}" 
                                               class="pelaksana-checkbox" 
                                               id="pelaksana_chk_{{ $u->id }}"
                                               {{ (in_array($u->id, $existingPelaksanaIds)) || $currentPicId == $u->id ? 'checked' : '' }}
                                               onchange="syncPelaksanaSelection()">
                                        <div class="pelaksana-avatar">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                                        <div class="pelaksana-meta">
                                            <div class="pelaksana-name">{{ $u->name }}</div>
                                            <div class="pelaksana-sub">{{ $u->role_label }} &bull; {{ $u->nip ?? 'NIP -' }}</div>
                                        </div>
                                        <span class="pic-tag" id="pic_tag_{{ $u->id }}" style="display: none;">PIC UTAMA</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="pelaksana-group-title" style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.04em; margin-bottom: 8px; padding: 2px 4px;">
                            👥 Seluruh Pegawai BGTK
                        </div>
                        <div class="pelaksana-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px;">
                            @foreach($otherUsers as $u)
                                <label class="pelaksana-card" id="pelaksana-card-{{ $u->id }}" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-pokja="{{ $u->pokja?->nama_pokja ?? 'BGTK' }}" data-is-my-pokja="0">
                                    <input type="checkbox" 
                                           name="pelaksana_ids[]" 
                                           value="{{ $u->id }}" 
                                           class="pelaksana-checkbox" 
                                           id="pelaksana_chk_{{ $u->id }}"
                                           {{ (in_array($u->id, $existingPelaksanaIds)) || $currentPicId == $u->id ? 'checked' : '' }}
                                           onchange="syncPelaksanaSelection()">
                                    <div class="pelaksana-avatar">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                                    <div class="pelaksana-meta">
                                        <div class="pelaksana-name">{{ $u->name }}</div>
                                        <div class="pelaksana-sub">{{ $u->pokja?->nama_pokja ?? 'BGTK' }} &bull; {{ $u->role_label }}</div>
                                    </div>
                                    <span class="pic-tag" id="pic_tag_{{ $u->id }}" style="display: none;">PIC UTAMA</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if(Auth::user()->canManagePekerjaan())
                    <div class="form-group">
                        <label for="status_persetujuan" class="form-label">
                            <span>Status Persetujuan <span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i class="ph-bold ph-shield-check input-icon"></i>
                            <select id="status_persetujuan" name="status_persetujuan" class="form-select @error('status_persetujuan') is-invalid @enderror" required onchange="updateLivePreview()">
                                <option value="disetujui" {{ old('status_persetujuan', $pekerjaan->status_persetujuan) == 'disetujui' ? 'selected' : '' }}>
                                    Disetujui Langsung (Pekerjaan Aktif)
                                </option>
                                <option value="draft" {{ old('status_persetujuan', $pekerjaan->status_persetujuan) == 'draft' ? 'selected' : '' }}>
                                    Draf (Belum aktif)
                                </option>
                            </select>
                        </div>
                        @error('status_persetujuan')
                            <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Tombol Aksi Desktop -->
            <div class="form-actions-desktop">
                <a href="{{ route('pekerjaan.show', $pekerjaan) }}" class="btn-cancel-main">
                    <i class="ph-bold ph-arrow-left"></i>
                    <span>Batal</span>
                </a>
                <button type="submit" class="btn-submit-main">
                    <i class="ph-bold ph-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </div>

        <!-- ==================== KOLOM KANAN: LIVE SUMMARY PREVIEW ==================== -->
        <div class="form-preview-sidebar">
            <div class="preview-card-sticky">
                <div class="preview-header">
                    <div style="display: flex; gap: 6px; align-items: center; margin-bottom: 8px; flex-wrap: wrap;">
                        <div class="preview-badge-status" id="previewBadgeStatus">
                            <i class="ph-bold ph-pencil-simple"></i> <span id="previewStatusText">{{ strtoupper(str_replace('_', ' ', $pekerjaan->status_persetujuan)) }}</span>
                        </div>
                        <div class="preview-badge-status" id="previewSumberBadge" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); font-size: 11px;">
                            <i class="ph-bold {{ $pekerjaan->isFromSkp() ? 'ph-target' : 'ph-pencil-line' }}" id="previewSumberIcon"></i> <span id="previewSumberText">{{ $pekerjaan->isFromSkp() ? 'Rujukan SKP' : 'Manual' }}</span>
                        </div>
                    </div>
                    <div style="font-family: monospace; font-size: 13px; font-weight: 800; color: #fde68a; margin-bottom: 4px;" id="previewKode">
                        {{ $pekerjaan->kode_tampilan }}
                    </div>
                    <h4 class="preview-title" id="previewJudul">
                        {{ $pekerjaan->judul }}
                    </h4>
                </div>

                <div class="preview-body">
                    <div class="preview-item" id="previewSkpRow" style="{{ $pekerjaan->isFromSkp() ? 'display: flex;' : 'display: none;' }} background: #f0f9ff; margin: -10px -18px 12px -18px; padding: 10px 18px; border-bottom: 1px solid #e0f2fe;">
                        <span class="preview-label" style="color: #0369a1;"><i class="ph-bold ph-target"></i> SKP Terkait</span>
                        <span class="preview-val" id="previewSkpVal" style="font-size: 11.5px; color: #0284c7; font-weight: 700; text-align: right; max-width: 60%;">{{ Str::limit($pekerjaan->skpTahunan?->rencana_kinerja ?? '-', 45) }}</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-buildings"></i> Tim Kerja</span>
                        <span class="preview-val" id="previewPokja">{{ $pekerjaan->pokja?->nama_pokja ?? 'BGTK DKI Jakarta' }}</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-user"></i> PIC Utama</span>
                        <span class="preview-val" id="previewPic">{{ $pekerjaan->penanggungJawab->name }}</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-users"></i> Tim Pelaksana</span>
                        <span class="preview-val" id="previewAnggotaCount">{{ $pekerjaan->pelaksana->count() ?: 1 }} Orang</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-target"></i> Target</span>
                        <span class="preview-val" id="previewTarget">{{ number_format($pekerjaan->target, 0) }} {{ $pekerjaan->satuan }}</span>
                    </div>

                    <div class="preview-item">
                        <span class="preview-label"><i class="ph-bold ph-calendar"></i> Tenggat</span>
                        <span class="preview-val" id="previewTenggat">{{ $pekerjaan->tenggat ? $pekerjaan->tenggat->format('d M Y') : '-' }}</span>
                    </div>

                    <div class="preview-item" id="previewStRow" style="{{ $pekerjaan->nomor_st ? 'display: flex;' : 'display: none;' }}">
                        <span class="preview-label"><i class="ph-bold ph-file-text"></i> Dasar ST</span>
                        <span class="preview-val" style="font-family: monospace; color: #1e40af;" id="previewStVal">{{ $pekerjaan->nomor_st ?? '-' }}</span>
                    </div>

                    <!-- Checklist Kelengkapan Form -->
                    <div class="form-checklist">
                        <div class="checklist-title">
                            <span>Kelengkapan Data</span>
                            <span id="completenessPercent">100%</span>
                        </div>
                        <div class="progress-micro">
                            <div id="completenessBar" class="progress-micro-fill" style="width: 100%; background: #d97706;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Mobile Floating Action Bar -->
    <div class="mobile-floating-bar">
        <a href="{{ route('pekerjaan.show', $pekerjaan) }}" class="btn-cancel-main">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <button type="submit" class="btn-submit-main">
            <i class="ph-bold ph-check"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

@endsection

@section('scripts')
<script>
    // ==========================================
    // Switch Sumber Penugasan (Manual vs SKP)
    // ==========================================
    function switchSumberPekerjaan(sumber) {
        const cardManual = document.getElementById('cardSumberManual');
        const cardSkp = document.getElementById('cardSumberSkp');
        const radioManual = document.getElementById('radioManual');
        const radioSkp = document.getElementById('radioSkp');
        const panelSkp = document.getElementById('panelRujukanSkp');
        const previewSumberText = document.getElementById('previewSumberText');
        const previewSumberIcon = document.getElementById('previewSumberIcon');
        const previewSkpRow = document.getElementById('previewSkpRow');

        if (sumber === 'skp') {
            radioSkp.checked = true;
            cardSkp.classList.add('active');
            cardManual.classList.remove('active');
            panelSkp.style.display = 'block';

            if (previewSumberText) previewSumberText.textContent = 'Rujukan SKP';
            if (previewSumberIcon) previewSumberIcon.className = 'ph-bold ph-target';
            
            // Check if SKP already selected
            const skpSelect = document.getElementById('skp_tahunan_id');
            if (skpSelect && skpSelect.value) {
                handleSkpSelection(skpSelect);
            }
        } else {
            radioManual.checked = true;
            cardManual.classList.add('active');
            cardSkp.classList.remove('active');
            panelSkp.style.display = 'none';

            if (previewSumberText) previewSumberText.textContent = 'Manual';
            if (previewSumberIcon) previewSumberIcon.className = 'ph-bold ph-pencil-line';
            if (previewSkpRow) previewSkpRow.style.display = 'none';
        }

        updateLivePreview();
    }

    // Handler Saat Pengguna Memilih Butir SKP
    function handleSkpSelection(selectEl) {
        const val = selectEl.value;
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const previewSkpRow = document.getElementById('previewSkpRow');
        const previewSkpVal = document.getElementById('previewSkpVal');
        const skpInfo = document.getElementById('skpSelectedInfo');

        if (!val || !selectedOption) {
            if (previewSkpRow) previewSkpRow.style.display = 'none';
            if (skpInfo) skpInfo.style.display = 'none';
            return;
        }

        const rhk = selectedOption.getAttribute('data-rhk') || '';
        const target = selectedOption.getAttribute('data-target') || '1';
        const satuan = selectedOption.getAttribute('data-satuan') || 'Laporan';
        const userId = selectedOption.getAttribute('data-user-id') || '';
        const userName = selectedOption.getAttribute('data-user-name') || '-';
        const iki = selectedOption.getAttribute('data-iki') || '';

        // Auto-fill input fields if judul is different or empty
        const inputJudul = document.getElementById('judul');
        if (inputJudul && !inputJudul.value) {
            inputJudul.value = rhk;
        }

        const inputTarget = document.getElementById('target');
        if (inputTarget && !inputTarget.value) {
            inputTarget.value = target;
        }

        const inputSatuan = document.getElementById('satuan');
        if (inputSatuan && !inputSatuan.value) {
            inputSatuan.value = satuan;
        }

        const inputHasil = document.getElementById('hasil_diharapkan');
        if (inputHasil && (!inputHasil.value || inputHasil.value.trim() === '')) {
            inputHasil.value = iki ? `Tercapainya indikator: ${iki}` : `Terselesaikannya target: ${rhk}`;
        }

        // Show Info Box
        if (skpInfo) {
            document.getElementById('skpRhkText').textContent = rhk;
            document.getElementById('skpOwnerText').textContent = userName;
            document.getElementById('skpTargetText').textContent = `${target} ${satuan}`;
            skpInfo.style.display = 'block';
        }

        // Update preview
        if (previewSkpRow && previewSkpVal) {
            previewSkpVal.textContent = rhk.length > 50 ? rhk.substring(0, 48) + '...' : rhk;
            previewSkpRow.style.display = 'flex';
        }

        updateLivePreview();
    }

    // Live Summary Preview Updates
    function updateLivePreview() {
        const kode = document.getElementById('kode_tampilan')?.value || '-';
        const judul = document.getElementById('judul')?.value || 'Nama pekerjaan akan tampil di sini...';
        const target = document.getElementById('target')?.value || '0';
        const satuan = document.getElementById('satuan')?.value || '';
        const tenggat = document.getElementById('tenggat')?.value || '-';
        const nomorSt = document.getElementById('nomor_st')?.value || '';
        
        const pokjaSelect = document.getElementById('pokja_id');
        const pokjaText = pokjaSelect && pokjaSelect.selectedIndex > 0 ? pokjaSelect.options[pokjaSelect.selectedIndex].getAttribute('data-name') || pokjaSelect.options[pokjaSelect.selectedIndex].text : 'BGTK DKI Jakarta';

        const statusSelect = document.getElementById('status_persetujuan');
        const statusText = statusSelect ? statusSelect.options[statusSelect.selectedIndex].text : 'Disetujui Langsung';

        // Update elements
        document.getElementById('previewKode').textContent = kode;
        document.getElementById('previewJudul').textContent = judul;
        document.getElementById('previewPokja').textContent = pokjaText;
        document.getElementById('previewTarget').textContent = `${target} ${satuan}`;
        document.getElementById('previewStatusText').textContent = statusText.split('(')[0].trim();
        
        if (tenggat !== '-') {
            const dateObj = new Date(tenggat);
            if (!isNaN(dateObj.getTime())) {
                document.getElementById('previewTenggat').textContent = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            }
        }

        // ST row toggle
        const stRow = document.getElementById('previewStRow');
        if (nomorSt.trim() !== '') {
            stRow.style.display = 'flex';
            document.getElementById('previewStVal').textContent = nomorSt;
        } else {
            stRow.style.display = 'none';
        }

        // Char count for judul
        const charCount = document.getElementById('judul')?.value.length || 0;
        document.getElementById('charCount').textContent = `${charCount} / 255 karakter`;

        // Calculate completeness
        calculateCompleteness();
    }

    // Hitung Kelengkapan Pengisian Form
    function calculateCompleteness() {
        const fields = [
            document.getElementById('kode_tampilan')?.value.trim(),
            document.getElementById('judul')?.value.trim(),
            document.getElementById('target')?.value.trim(),
            document.getElementById('satuan')?.value.trim(),
            document.getElementById('hasil_diharapkan')?.value.trim(),
            document.getElementById('penanggung_jawab_id')?.value.trim(),
            document.getElementById('tenggat')?.value.trim()
        ];

        let filled = 0;
        fields.forEach(f => { if (f && f !== '') filled++; });
        
        const percent = Math.round((filled / fields.length) * 100);
        document.getElementById('completenessPercent').textContent = `${percent}%`;
        document.getElementById('completenessBar').style.width = `${percent}%`;
    }

    // ==========================================
    // Multi-Assignee Interactive Selector Logic
    // ==========================================
    function handlePicChange() {
        const picSelect = document.getElementById('penanggung_jawab_id');
        const picId = picSelect.value;
        const selectedOption = picSelect.options[picSelect.selectedIndex];
        const picName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.text : '-';

        document.getElementById('previewPic').textContent = picName;

        // Auto check PIC card & mark as PIC tag
        if (picId) {
            const picChk = document.getElementById(`pelaksana_chk_${picId}`);
            if (picChk) {
                picChk.checked = true;
            }
        }

        syncPelaksanaSelection();
    }

    function syncPelaksanaSelection() {
        const picId = document.getElementById('penanggung_jawab_id')?.value;
        const allCards = document.querySelectorAll('.pelaksana-card');
        const chipsContainer = document.getElementById('selectedPelaksanaChips');
        chipsContainer.innerHTML = '';

        let count = 0;

        allCards.forEach(card => {
            const id = card.getAttribute('data-id');
            const name = card.getAttribute('data-name');
            const chk = document.getElementById(`pelaksana_chk_${id}`);
            const tag = document.getElementById(`pic_tag_${id}`);

            if (chk && chk.checked) {
                count++;
                card.classList.add('selected');

                const isPic = (id === picId);
                if (isPic) {
                    card.classList.add('is-pic');
                    if (tag) tag.style.display = 'inline-block';
                } else {
                    card.classList.remove('is-pic');
                    if (tag) tag.style.display = 'none';
                }

                // Add chip
                const chip = document.createElement('div');
                chip.className = `pelaksana-chip ${isPic ? 'is-pic-chip' : ''}`;
                chip.innerHTML = `
                    <span>${isPic ? 'â­ ' : ''}${name}</span>
                    ${!isPic ? `<button type="button" class="btn-remove-chip" onclick="uncheckPelaksana('${id}')" title="Hapus dari penugasan">&times;</button>` : ''}
                `;
                chipsContainer.appendChild(chip);

            } else {
                card.classList.remove('selected');
                card.classList.remove('is-pic');
                if (tag) tag.style.display = 'none';
            }
        });

        if (count === 0) {
            chipsContainer.innerHTML = '<span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">Belum ada anggota yang dipilih. Centang daftar pegawai di bawah.</span>';
        }

        document.getElementById('pelaksanaCountBadge').textContent = `${count} Pegawai Terpilih`;
        document.getElementById('previewAnggotaCount').textContent = `${count} Orang`;
    }

    function uncheckPelaksana(id) {
        const chk = document.getElementById(`pelaksana_chk_${id}`);
        if (chk) {
            chk.checked = false;
            syncPelaksanaSelection();
        }
    }

    function selectAllPokja() {
        document.querySelectorAll('.pelaksana-card[data-is-my-pokja="1"]').forEach(card => {
            const id = card.getAttribute('data-id');
            const chk = document.getElementById(`pelaksana_chk_${id}`);
            if (chk) chk.checked = true;
        });
        syncPelaksanaSelection();
    }

    function resetAnggotaTambahan() {
        const picId = document.getElementById('penanggung_jawab_id')?.value;
        document.querySelectorAll('.pelaksana-checkbox').forEach(chk => {
            if (chk.value !== picId) {
                chk.checked = false;
            }
        });
        syncPelaksanaSelection();
    }

    function filterPelaksanaList(keyword) {
        const q = keyword.toLowerCase().trim();
        document.querySelectorAll('.pelaksana-card').forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // ==========================================
    // File Dropzone ST Handler
    // ==========================================
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('fileNameDisplay').textContent = file.name;
            document.getElementById('fileSizeDisplay').textContent = formatBytes(file.size);
            document.getElementById('dropzoneFileInfo').style.display = 'flex';
        }
    }

    function removeSelectedFile() {
        const fileInput = document.getElementById('file_st');
        fileInput.value = '';
        document.getElementById('dropzoneFileInfo').style.display = 'none';
    }

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Init on Page Load
    document.addEventListener('DOMContentLoaded', () => {
        handlePicChange();
        const initialSumber = document.querySelector('input[name="sumber_pekerjaan"]:checked')?.value || '{{ $pekerjaan->sumber_pekerjaan ?? "manual" }}';
        switchSumberPekerjaan(initialSumber);
        updateLivePreview();
    });
</script>
@endsection
