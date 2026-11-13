@php
    $currentLevel = $level ?? 2;
    $paddingLeft = ($currentLevel - 1) * 32;
@endphp

@foreach($children as $child)
    <div class="tree-node-row" style="padding-left: {{ 24 + $paddingLeft }}px; border-left: {{ $currentLevel > 1 ? '3px solid ' . ($currentLevel == 2 ? '#3b82f6' : ($currentLevel == 3 ? '#6366f1' : '#8b5cf6')) : 'none' }};">
        <div class="tree-node-left">
            <span style="color: #94a3b8; font-family: monospace; font-size: 14px; user-select: none;">
                @if($currentLevel == 2) ├─ @elseif($currentLevel == 3) └──├─ @else └──── @endif
            </span>

            <span class="code-badge" style="background: {{ $currentLevel == 2 ? '#1e293b' : ($currentLevel == 3 ? '#334155' : '#475569') }};">
                {{ $child->kode_tampilan }}
            </span>

            <div style="min-width: 0;">
                <div style="font-size: 14.5px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <a href="{{ route('pekerjaan.show', $child) }}" style="color: inherit; text-decoration: none;" class="hover-underline">
                        {{ $child->judul }}
                    </a>
                    @if($child->children->isNotEmpty())
                        <span style="font-size: 11px; background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-weight: 600;">
                            {{ $child->children->count() }} sub
                        </span>
                    @endif
                </div>

                <div style="font-size: 12px; color: var(--text-muted); display: flex; gap: 10px; margin-top: 3px; align-items: center; flex-wrap: wrap;">
                    <span class="badge-pill badge-bobot">
                        <i class="ph-bold ph-scales"></i> Bobot: {{ number_format($child->bobot, 1) }}%
                    </span>
                    <span class="badge-pill badge-pj">
                        <i class="ph-bold ph-user"></i> PJ: {{ $child->penanggungJawab->name }}
                    </span>
                    <span class="badge-pill {{ $child->isTerlambat() ? 'badge-terlambat' : 'badge-tenggat' }}">
                        <i class="ph-bold ph-calendar"></i> Tenggat: {{ $child->tenggat->format('d M Y') }}
                    </span>
                    @if(!$child->isDisetujui())
                        <span class="badge-pill status-{{ $child->status_persetujuan }}">
                            <i class="ph-bold ph-hourglass"></i> {{ str_replace('_', ' ', $child->status_persetujuan) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px; flex-shrink: 0;">
            <!-- Progress Bar -->
            <div style="text-align: right;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div class="progress-track">
                        <div class="progress-fill {{ $child->progres >= 100 ? 'bg-progres-green' : ($child->progres >= 50 ? 'bg-progres-blue' : 'bg-progres-amber') }}" 
                             style="width: {{ $child->progres }}%;"></div>
                    </div>
                    <strong style="font-size: 13px; color: #0f172a; min-width: 44px; display: inline-block;">
                        {{ number_format($child->progres, 1) }}%
                    </strong>
                </div>
                @if($child->children->isNotEmpty())
                    <div style="font-size: 10.5px; color: #64748b; font-style: italic;">(agregasi anak)</div>
                @endif
            </div>

            <!-- Execution Status Badge -->
            <span class="badge-pill exec-{{ $child->status_pelaksanaan }}">
                {{ str_replace('_', ' ', $child->status_pelaksanaan) }}
            </span>

            <!-- Actions -->
            <div style="display: flex; gap: 6px;">
                <a href="{{ route('pekerjaan.show', $child) }}" class="btn-action-sm" title="Lihat Rincian">
                    <i class="ph-bold ph-eye"></i> Detail
                </a>
                @if($child->pembuat_id === Auth::id() || $child->penanggung_jawab_id === Auth::id() || Auth::user()->canManagePekerjaan())
                    <a href="{{ route('pekerjaan.edit', $child) }}" class="btn-action-sm" style="color: #b45309;" title="Edit Subpekerjaan Ini">
                        <i class="ph-bold ph-pencil-simple"></i> Edit
                    </a>
                @endif
                <a href="{{ route('pekerjaan.create', ['parent_id' => $child->id]) }}" class="btn-action-sm btn-add-sub" title="Tambah Subpekerjaan">
                    <i class="ph-bold ph-plus"></i> Sub
                </a>
            </div>
        </div>
    </div>

    <!-- Recursive Call for Grandchildren -->
    @if($child->children->isNotEmpty())
        @include('pekerjaan.partials.tree-row', ['children' => $child->children, 'level' => $currentLevel + 1])
    @endif
@endforeach
