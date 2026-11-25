@extends('layouts.app')

@section('title', 'Ubah Pokja | TANJIDOR')
@section('page-title', 'Ubah Kelompok Kerja (Pokja)')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<link rel="stylesheet" href="{{ asset('css/master-data.css') }}">
<style>
    .personil-management-box {
        margin-top: 28px;
        padding-top: 24px;
        border-top: 1.5px dashed var(--border);
    }
    .user-check-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 9px;
        border: 1.5px solid var(--border);
        background: #ffffff;
        cursor: pointer;
        transition: all 0.15s ease;
        margin-bottom: 6px;
    }
    .user-check-item:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .user-check-item.is-selected {
        border-color: #93c5fd;
        background: #f0f7ff;
    }
    .user-check-item.leader-item {
        background: rgba(37, 99, 235, 0.06);
        border-color: rgba(37, 99, 235, 0.35);
    }
    .user-check-item input[type="checkbox"] {
        width: 17px;
        height: 17px;
        accent-color: var(--primary);
        cursor: pointer;
    }
    .btn-tool-sm {
        font-size: 11.5px;
        padding: 6px 12px;
        border-radius: 7px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        cursor: pointer;
        color: #334155;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
    }
    .btn-tool-sm:hover {
        background: #f1f5f9;
        color: var(--primary);
        border-color: var(--primary);
    }
    .preview-member-mini-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 10px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 11.5px;
    }
    .preview-member-mini-card.is-leader {
        background: rgba(37, 99, 235, 0.04);
        border-color: rgba(37, 99, 235, 0.25);
    }
    .preview-member-mini-card .mini-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #64748b;
        color: #ffffff;
        font-size: 10px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .preview-member-mini-card.is-leader .mini-avatar {
        background: var(--primary);
    }
    [data-theme="dark"] .user-check-item {
        background: #151e32 !important;
        border-color: #223049 !important;
    }
    [data-theme="dark"] .user-check-item.is-selected {
        background: #1e293b !important;
        border-color: #3b82f6 !important;
    }
    [data-theme="dark"] .btn-tool-sm {
        background: #1e293b;
        border-color: #334155;
        color: #e2e8f0;
    }
    [data-theme="dark"] .preview-member-mini-card {
        background: #151e32;
        border-color: #223049;
    }
</style>
@endsection

@section('content')

    <!-- Navigation Breadcrumb -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('pokjas.index') }}" style="color: var(--primary); font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="ph-bold ph-arrow-left"></i>
            <span>Kembali ke Master Pokja</span>
        </a>
    </div>

    <div class="form-layout-grid">
        <!-- Form Isian Edit Pokja -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon-box">
                    <i class="ph-bold ph-pencil-line"></i>
                </div>
                <div>
                    <h2>Ubah Identitas Pokja: {{ $pokja->nama_pokja }}</h2>
                    <p>Perbarui rincian kelompok kerja, kode unit, atau ganti Ketua Tim (Katim)</p>
                </div>
            </div>

            <form action="{{ route('pokjas.update', $pokja) }}" method="POST" id="pokjaEditForm">
                @csrf
                @method('PUT')

                <!-- Nama Pokja -->
                <div class="form-group">
                    <label class="form-label" for="nama_pokja">
                        <i class="ph-bold ph-folder-notch-open"></i>
                        <span>Nama Kelompok Kerja (Pokja)</span>
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" 
                               name="nama_pokja" 
                               id="nama_pokja" 
                               class="form-input" 
                               value="{{ old('nama_pokja', $pokja->nama_pokja) }}" 
                               placeholder="Contoh: Tim Kerja 1: Pemetaan & Evaluasi" 
                               required
                               autocomplete="off">
                        <i class="ph-bold ph-buildings prefix-icon"></i>
                    </div>
                    @error('nama_pokja') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Kode Pokja -->
                <div class="form-group">
                    <label class="form-label" for="kode_pokja">
                        <i class="ph-bold ph-hash"></i>
                        <span>Kode Pokja (Unik)</span>
                        <span class="required">*</span>
                        <span class="form-label-hint">Huruf kapital</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" 
                               name="kode_pokja" 
                               id="kode_pokja" 
                               class="form-input" 
                               value="{{ old('kode_pokja', $pokja->kode_pokja) }}" 
                               placeholder="Contoh: TIM-01 atau POKJA-ULT" 
                               style="text-transform: uppercase;" 
                               required
                               autocomplete="off">
                        <i class="ph-bold ph-tag prefix-icon"></i>
                    </div>
                    @error('kode_pokja') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Kategori / Tipe Unit -->
                <div class="form-group">
                    <label class="form-label" for="tipe">
                        <i class="ph-bold ph-tree-structure"></i>
                        <span>Kategori Unit Kerja</span>
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <select name="tipe" id="tipe" class="form-select" required>
                            <option value="tim_kerja" {{ old('tipe', $pokja->tipe) == 'tim_kerja' ? 'selected' : '' }}>
                                Tim Kerja Teknis / Tata Usaha Balai
                            </option>
                            <option value="subbag_pilar" {{ old('tipe', $pokja->tipe) == 'subbag_pilar' ? 'selected' : '' }}>
                                Pilar Operasional Subbagian Umum
                            </option>
                            <option value="kelompok_kerja" {{ old('tipe', $pokja->tipe) == 'kelompok_kerja' ? 'selected' : '' }}>
                                Satuan Tugas Ad Hoc (Kelompok Kerja Penugasan RBI)
                            </option>
                        </select>
                        <i class="ph-bold ph-squares-four prefix-icon"></i>
                    </div>
                    @error('tipe') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Penunjukan Katim / Kasubag -->
                <div class="form-group">
                    <label class="form-label" for="katim_id">
                        <i class="ph-bold ph-user-circle-gear"></i>
                        <span>Tunjuk Ketua Tim (Katim) / Kepala Subbagian</span>
                        <span class="form-label-hint">Dapat disesuaikan</span>
                    </label>
                    <div class="input-wrapper">
                        <select name="katim_id" id="katim_id" class="form-select">
                            <option value="">-- Belum Menunjuk Katim / Kasubag --</option>
                            @foreach($potentialKatims as $user)
                                <option value="{{ $user->id }}" 
                                        data-name="{{ $user->name }}" 
                                        data-role="{{ $user->role }}"
                                        {{ old('katim_id', $pokja->katim_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} (NIP: {{ $user->nip ?? '-' }}) — {{ $user->jabatan ?? $user->role }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ph-bold ph-user prefix-icon"></i>
                    </div>
                    @error('katim_id') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- Deskripsi & Ruang Lingkup -->
                <div class="form-group">
                    <label class="form-label" for="deskripsi">
                        <i class="ph-bold ph-text-align-left"></i>
                        <span>Deskripsi & Ruang Lingkup Pokja</span>
                    </label>
                    <div class="input-wrapper">
                        <textarea name="deskripsi" 
                                  id="deskripsi" 
                                  class="form-textarea" 
                                  placeholder="Tuliskan fokus bidang tugas, tema kerja, atau ruang lingkup operasional Pokja ini...">{{ old('deskripsi', $pokja->deskripsi) }}</textarea>
                        <i class="ph-bold ph-note-pencil prefix-icon"></i>
                    </div>
                    @error('deskripsi') 
                        <div class="error-feedback"><i class="ph-bold ph-warning-circle"></i> {{ $message }}</div> 
                    @enderror
                </div>

                <!-- =========================================================================
                     KELOLA & RINCIAN PERSONIL POKJA
                     ========================================================================= -->
                <div class="personil-management-box">
                    <input type="hidden" name="manage_members_submitted" value="1">
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <h3 style="font-size: 15.5px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                                <i class="ph-bold ph-users-three" style="color: var(--primary);"></i>
                                <span>Kelola & Rincian Personil Pokja</span>
                                <span class="badge-count" id="memberCountBadge" style="font-size: 11.5px; background: rgba(37, 99, 235, 0.1); color: #2563eb; padding: 2px 9px; border-radius: 999px; font-weight: 800;">
                                    {{ $pokja->anggota->count() }} Terpilih
                                </span>
                            </h3>
                            <p style="font-size: 12.5px; color: #64748b; margin: 0;">
                                Pilih dan centang personil pegawai BGTK untuk unit kerja ini. Katim otomatis masuk sebagai Ketua.
                            </p>
                        </div>

                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button type="button" class="btn-tool-sm" onclick="selectAllMembers(true)">
                                <i class="ph-bold ph-check-square"></i>
                                <span>Pilih Semua</span>
                            </button>
                            <button type="button" class="btn-tool-sm" onclick="selectAllMembers(false)">
                                <i class="ph-bold ph-square"></i>
                                <span>Kosongkan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Pencarian Pegawai -->
                    <div style="position: relative; margin-bottom: 12px;">
                        <i class="ph-bold ph-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; pointer-events: none;"></i>
                        <input type="text" 
                               id="searchPegawaiInput"
                               class="form-input" 
                               style="padding-left: 42px !important; font-size: 13px; background: #ffffff;" 
                               placeholder="Cari nama pegawai, NIP, atau jabatan staf..." 
                               oninput="filterEditMemberList(this.value)">
                    </div>

                    <!-- Daftar Checklist Pegawai -->
                    <div class="user-checklist-container" id="editMemberListContainer" style="max-height: 380px; overflow-y: auto; border: 1.5px solid var(--border); border-radius: 10px; padding: 10px; background: #f8fafc;">
                        @php
                            $currentMemberIds = $pokja->anggota->pluck('id')->toArray();
                            $activeKatimId = old('katim_id', $pokja->katim_id);
                        @endphp

                        @foreach($allUsers as $u)
                            @php
                                $isKatim = ($u->id == $activeKatimId);
                                $isMember = in_array($u->id, $currentMemberIds);
                                $isChecked = $isMember || $isKatim;
                            @endphp
                            <label class="user-check-item {{ $isKatim ? 'leader-item' : '' }} {{ $isChecked ? 'is-selected' : '' }}" 
                                   id="userRow_{{ $u->id }}"
                                   data-search="{{ strtolower($u->name . ' ' . $u->nip . ' ' . $u->jabatan . ' ' . $u->role) }}">
                                <input type="checkbox" 
                                       name="anggota_ids[]" 
                                       value="{{ $u->id }}" 
                                       class="member-checkbox"
                                       id="chk_user_{{ $u->id }}"
                                       data-user-id="{{ $u->id }}"
                                       data-user-name="{{ $u->name }}"
                                       data-user-nip="{{ $u->nip ?? '-' }}"
                                       data-user-role="{{ $u->jabatan ?? $u->role }}"
                                       {{ $isChecked ? 'checked' : '' }}
                                       {{ $isKatim ? 'disabled' : '' }}
                                       onchange="handleMemberToggle(this)">
                                
                                @if($isKatim)
                                    <input type="hidden" name="anggota_ids[]" value="{{ $u->id }}" id="hiddenKatim_{{ $u->id }}" class="katim-hidden-input">
                                @endif

                                <div class="user-avatar" style="width: 32px; height: 32px; border-radius: 50%; background: {{ $isKatim ? 'linear-gradient(135deg, #1e40af, #3b82f6)' : '#64748b' }}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; flex-shrink: 0;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>

                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <span class="user-name-text">{{ $u->name }}</span>
                                        <span class="badge-leader-tag" id="badgeLeader_{{ $u->id }}" style="font-size: 10px; color: #2563eb; background: #eff6ff; padding: 1px 7px; border-radius: 4px; font-weight: 800; display: {{ $isKatim ? 'inline-flex' : 'none' }}; align-items: center; gap: 4px;">
                                            <i class="ph-fill ph-star" style="color: #f59e0b;"></i> Katim / Ketua Unit
                                        </span>
                                    </div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                        NIP: {{ $u->nip ?? '-' }} &bull; {{ $u->jabatan ?? $u->role }}
                                    </div>
                                </div>

                                <div class="member-action-indicator" style="flex-shrink: 0;">
                                    <span class="member-status-tag" id="statusTag_{{ $u->id }}" style="font-size: 11px; font-weight: 700; color: {{ $isKatim ? '#2563eb' : ($isChecked ? '#059669' : '#94a3b8') }};">
                                        {{ $isKatim ? 'Ketua' : ($isChecked ? 'Anggota' : 'Bukan Anggota') }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions" style="justify-content: space-between;">
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <button type="submit" class="btn-submit">
                            <i class="ph-bold ph-floppy-disk"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                        <a href="{{ route('pokjas.index') }}" class="btn-cancel">
                            <i class="ph-bold ph-x"></i>
                            <span>Batal</span>
                        </a>
                    </div>

                    <button type="button" 
                            class="btn-cancel" 
                            style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;"
                            onclick="window.tanjidorConfirm({
                                title: 'Konfirmasi Hapus Unit Kerja',
                                text: 'Apakah Anda yakin ingin menghapus unit / kelompok kerja &quot;{{ addslashes($pokja->nama_pokja) }}&quot;? Seluruh keanggotaan personil akan dilepaskan secara aman.',
                                confirmButtonText: 'Ya, Hapus Unit',
                                type: 'danger',
                                icon: 'warning'
                            }).then(confirmed => { if(confirmed) document.getElementById('deletePokjaForm').submit(); })">
                        <i class="ph-bold ph-trash"></i>
                        <span>Hapus Unit</span>
                    </button>
                </div>
            </form>

            <form id="deletePokjaForm" action="{{ route('pokjas.destroy', $pokja) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>

        <!-- Kolom Kanan: Live Preview Card -->
        <div>
            <div class="preview-card-sticky">
                <div class="preview-header-tag">
                    <span class="preview-title-badge">
                        <span class="live-pulse"></span>
                        Pratinjau Kartu Pokja
                    </span>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600;">Live Preview</span>
                </div>

                <div class="preview-pokja-box">
                    <span class="preview-badge" id="previewBadge">{{ $pokja->kode_pokja }}</span>
                    <div class="preview-nama" id="previewNama">{{ $pokja->nama_pokja }}</div>
                    <div class="preview-desc" id="previewDesc">{{ $pokja->deskripsi ?? 'Belum ada deskripsi untuk Pokja ini.' }}</div>

                    <div class="preview-meta-box">
                        <div class="preview-meta-row">
                            <span style="color: #64748b;">
                                <i class="ph-bold ph-user-circle"></i> 
                                <span id="previewKatimLabel">{{ $pokja->kode_pokja === 'SUBBAG-UMUM' ? 'Kepala Subbagian (Kasubag):' : 'Ketua Tim (Katim):' }}</span>
                            </span>
                            <strong id="previewKatim" style="color: #0f172a;">{{ $pokja->katim?->name ?? 'Belum Ditunjuk' }}</strong>
                        </div>
                        <div class="preview-meta-row">
                            <span style="color: #64748b;"><i class="ph-bold ph-users-three"></i> Total Anggota:</span>
                            <strong id="previewMemberCount" style="color: var(--primary);">{{ $pokja->anggota->count() }} Pegawai</strong>
                        </div>
                    </div>

                    <!-- Mini Live Roster of Selected Members in Preview -->
                    <div style="margin-top: 14px; border-top: 1px dashed var(--border); padding-top: 12px;">
                        <div style="font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                            <span>Daftar Personil Terpilih:</span>
                            <span id="previewSelectedCountBadge" style="font-size: 11px; color: var(--primary); font-weight: 800;">{{ $pokja->anggota->count() }} personil</span>
                        </div>
                        <div id="previewMemberList" style="max-height: 200px; overflow-y: auto; display: flex; flex-direction: column; gap: 5px; padding-right: 4px;">
                            <!-- Populated dynamically by updatePreviewMembers() -->
                        </div>
                    </div>
                </div>

                <div class="stats-info-box">
                    <strong><i class="ph-bold ph-shield-check"></i> Status Pokja:</strong>
                    Saat ini kelompok kerja ini menaungi <strong id="previewStatsMemberCount">{{ $pokja->anggota->count() }} pegawai</strong>. Perubahan data di sini akan langsung disinkronkan ke seluruh modul penugasan dan dashboard pimpinan.
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputNama = document.getElementById('nama_pokja');
        const inputKode = document.getElementById('kode_pokja');
        const inputDesc = document.getElementById('deskripsi');
        const selectKatim = document.getElementById('katim_id');

        const previewNama = document.getElementById('previewNama');
        const previewBadge = document.getElementById('previewBadge');
        const previewDesc = document.getElementById('previewDesc');
        const previewKatim = document.getElementById('previewKatim');
        const previewKatimLabel = document.getElementById('previewKatimLabel');
        const previewMemberCount = document.getElementById('previewMemberCount');
        const previewSelectedCountBadge = document.getElementById('previewSelectedCountBadge');
        const previewStatsMemberCount = document.getElementById('previewStatsMemberCount');
        const previewMemberList = document.getElementById('previewMemberList');
        const memberCountBadge = document.getElementById('memberCountBadge');

        let currentKatimId = '{{ old("katim_id", $pokja->katim_id) }}';

        function updatePreview() {
            // Update Nama
            if (inputNama.value.trim() !== '') {
                previewNama.textContent = inputNama.value;
            } else {
                previewNama.textContent = 'Nama Pokja Akan Tampil Di Sini';
            }

            // Update Kode (auto-caps)
            if (inputKode.value.trim() !== '') {
                previewBadge.textContent = inputKode.value.toUpperCase();
            } else {
                previewBadge.textContent = 'KODE-POKJA';
            }

            // Update Deskripsi
            if (inputDesc.value.trim() !== '') {
                previewDesc.textContent = inputDesc.value;
            } else {
                previewDesc.textContent = 'Belum ada deskripsi untuk Pokja ini.';
            }

            // Update Katim / Kasubag
            const selectedOpt = selectKatim.options[selectKatim.selectedIndex];
            const isSubbag = inputKode.value.trim().toUpperCase() === 'SUBBAG-UMUM' || (selectedOpt && selectedOpt.getAttribute('data-role') === 'kasubag');
            if (previewKatimLabel) {
                previewKatimLabel.textContent = isSubbag ? 'Kepala Subbagian (Kasubag):' : 'Ketua Tim (Katim):';
            }

            if (selectKatim.value !== '' && selectedOpt) {
                previewKatim.textContent = selectedOpt.getAttribute('data-name') || selectedOpt.text.split('(')[0].trim();
            } else {
                previewKatim.textContent = 'Belum Ditunjuk';
            }

            updatePreviewMembers();
        }

        window.updatePreviewMembers = function() {
            const allCheckboxes = document.querySelectorAll('.member-checkbox');
            let selectedMembers = [];
            const activeKatim = selectKatim.value;

            allCheckboxes.forEach(chk => {
                const userId = chk.getAttribute('data-user-id');
                const isKatim = (userId === activeKatim);
                const isChecked = chk.checked || isKatim;

                if (isChecked) {
                    selectedMembers.push({
                        id: userId,
                        name: chk.getAttribute('data-user-name'),
                        nip: chk.getAttribute('data-user-nip'),
                        role: chk.getAttribute('data-user-role'),
                        isLeader: isKatim
                    });
                }
            });

            // Urutkan: Katim paling atas, selanjutnya sesuai nama
            selectedMembers.sort((a, b) => {
                if (a.isLeader) return -1;
                if (b.isLeader) return 1;
                return a.name.localeCompare(b.name);
            });

            const count = selectedMembers.length;
            if (previewMemberCount) previewMemberCount.textContent = count + ' Pegawai';
            if (previewSelectedCountBadge) previewSelectedCountBadge.textContent = count + ' personil';
            if (previewStatsMemberCount) previewStatsMemberCount.textContent = count + ' pegawai';
            if (memberCountBadge) memberCountBadge.textContent = count + ' Terpilih';

            // Render mini list in preview card
            if (previewMemberList) {
                if (selectedMembers.length === 0) {
                    previewMemberList.innerHTML = '<div style="text-align: center; padding: 12px; font-size: 11px; color: #94a3b8; background: #f8fafc; border-radius: 6px; border: 1px dashed var(--border);">Belum ada personil yang dipilih.</div>';
                } else {
                    let html = '';
                    selectedMembers.forEach(m => {
                        const initial = m.name.charAt(0).toUpperCase();
                        html += `
                            <div class="preview-member-mini-card ${m.isLeader ? 'is-leader' : ''}">
                                <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                                    <div class="mini-avatar">${initial}</div>
                                    <div style="min-width: 0;">
                                        <div style="font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">${m.name}</div>
                                        <div style="font-size: 10px; color: #64748b;">${m.role}</div>
                                    </div>
                                </div>
                                <span style="font-size: 10px; font-weight: 700; color: ${m.isLeader ? '#2563eb' : '#64748b'};">${m.isLeader ? 'Ketua' : 'Anggota'}</span>
                            </div>
                        `;
                    });
                    previewMemberList.innerHTML = html;
                }
            }
        };

        window.handleMemberToggle = function(chk) {
            const userId = chk.getAttribute('data-user-id');
            const row = document.getElementById('userRow_' + userId);
            const statusTag = document.getElementById('statusTag_' + userId);
            const isKatim = (userId === selectKatim.value);

            if (chk.checked || isKatim) {
                if (row) row.classList.add('is-selected');
                if (statusTag) {
                    statusTag.textContent = isKatim ? 'Ketua' : 'Anggota';
                    statusTag.style.color = isKatim ? '#2563eb' : '#059669';
                }
            } else {
                if (row) row.classList.remove('is-selected');
                if (statusTag) {
                    statusTag.textContent = 'Bukan Anggota';
                    statusTag.style.color = '#94a3b8';
                }
            }

            updatePreviewMembers();
        };

        window.selectAllMembers = function(check) {
            const activeKatim = selectKatim.value;
            const container = document.getElementById('editMemberListContainer');
            if (!container) return;

            const visibleCheckboxes = container.querySelectorAll('.member-checkbox');
            visibleCheckboxes.forEach(chk => {
                const userId = chk.getAttribute('data-user-id');
                const row = document.getElementById('userRow_' + userId);
                if (row && row.style.display !== 'none') {
                    if (userId !== activeKatim) {
                        chk.checked = check;
                        handleMemberToggle(chk);
                    }
                }
            });

            updatePreviewMembers();
        };

        window.filterEditMemberList = function(query) {
            const q = query.toLowerCase().trim();
            const container = document.getElementById('editMemberListContainer');
            if (!container) return;

            const items = container.querySelectorAll('.user-check-item');
            items.forEach(item => {
                const searchKey = item.getAttribute('data-search') || '';
                if (searchKey.includes(q)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        };

        // Katim Change Event Handler
        selectKatim.addEventListener('change', function() {
            const newKatimId = this.value;

            // Lepaskan katim lama jika ada
            if (currentKatimId && currentKatimId !== newKatimId) {
                const oldChk = document.getElementById('chk_user_' + currentKatimId);
                const oldRow = document.getElementById('userRow_' + currentKatimId);
                const oldBadge = document.getElementById('badgeLeader_' + currentKatimId);
                const oldHidden = document.getElementById('hiddenKatim_' + currentKatimId);
                const oldStatus = document.getElementById('statusTag_' + currentKatimId);

                if (oldChk) oldChk.disabled = false;
                if (oldRow) oldRow.classList.remove('leader-item');
                if (oldBadge) oldBadge.style.display = 'none';
                if (oldHidden) oldHidden.remove();
                if (oldStatus) {
                    oldStatus.textContent = (oldChk && oldChk.checked) ? 'Anggota' : 'Bukan Anggota';
                    oldStatus.style.color = (oldChk && oldChk.checked) ? '#059669' : '#94a3b8';
                }
            }

            // Kunci katim baru
            if (newKatimId) {
                const newChk = document.getElementById('chk_user_' + newKatimId);
                const newRow = document.getElementById('userRow_' + newKatimId);
                const newBadge = document.getElementById('badgeLeader_' + newKatimId);
                const newStatus = document.getElementById('statusTag_' + newKatimId);

                if (newChk) {
                    newChk.checked = true;
                    newChk.disabled = true;
                }
                if (newRow) {
                    newRow.classList.add('leader-item', 'is-selected');
                    // Tambahkan hidden input jika belum ada
                    if (!document.getElementById('hiddenKatim_' + newKatimId)) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'anggota_ids[]';
                        hiddenInput.value = newKatimId;
                        hiddenInput.id = 'hiddenKatim_' + newKatimId;
                        hiddenInput.className = 'katim-hidden-input';
                        newRow.appendChild(hiddenInput);
                    }
                }
                if (newBadge) newBadge.style.display = 'inline-flex';
                if (newStatus) {
                    newStatus.textContent = 'Ketua';
                    newStatus.style.color = '#2563eb';
                }
            }

            currentKatimId = newKatimId;
            updatePreview();
        });

        inputNama.addEventListener('input', updatePreview);
        inputKode.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            updatePreview();
        });
        inputDesc.addEventListener('input', updatePreview);

        // Initial preview sync
        updatePreview();
    });
</script>
@endsection
