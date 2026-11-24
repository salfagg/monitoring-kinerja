<?php

namespace App\Services;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RealisasiPekerjaan;
use App\Models\RiwayatPersetujuanPekerjaan;
use App\Models\User;
use Illuminate\Http\Request;

class PekerjaanPersetujuanService
{
    /**
     * Membangun dataset lengkap untuk Pusat Persetujuan & Verifikasi Pekerjaan / Progres.
     */
    public function getPersetujuanData(User $user, Request $request): array
    {
        // Base Query untuk filter lingkup kerja / role
        $baseQuery = Pekerjaan::query();

        if ($user->isAdmin() || $user->isKabal() || $user->isKasubag()) {
            // Pimpinan Balai, Kasubag TU, dan Administrator memiliki hak supervisi seluruh unit balai
            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isKatim()) {
            $katimPokjaIds = Pokja::where('katim_id', $user->id)->pluck('id')->toArray();
            if ($user->pokja_id && ! in_array($user->pokja_id, $katimPokjaIds)) {
                $katimPokjaIds[] = $user->pokja_id;
            }

            $baseQuery->where(function ($q) use ($user, $katimPokjaIds) {
                if (! empty($katimPokjaIds)) {
                    $q->whereIn('pokja_id', $katimPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id)
                    ->orWhereHas('parent', function ($p) use ($user, $katimPokjaIds) {
                        $p->where('pembuat_id', $user->id)
                            ->orWhere('penanggung_jawab_id', $user->id);
                        if (! empty($katimPokjaIds)) {
                            $p->orWhereIn('pokja_id', $katimPokjaIds);
                        }
                    });
            });

            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isKorlayanan()) {
            $subbagId = Pokja::where('kode_pokja', 'SUBBAG-UMUM')->value('id');
            $korPokjaIds = array_filter([$user->pokja_id, $subbagId]);

            $baseQuery->where(function ($q) use ($user, $korPokjaIds) {
                if (! empty($korPokjaIds)) {
                    $q->whereIn('pokja_id', $korPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id)
                    ->orWhereHas('parent', function ($p) use ($user, $korPokjaIds) {
                        $p->where('pembuat_id', $user->id)
                            ->orWhere('penanggung_jawab_id', $user->id);
                        if (! empty($korPokjaIds)) {
                            $p->orWhereIn('pokja_id', $korPokjaIds);
                        }
                    });
            });

            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isKorjafung()) {
            $jfPokjaIds = Pokja::where('kode_pokja', 'LIKE', 'TIM%')->pluck('id')->toArray();
            $baseQuery->where(function ($q) use ($user, $jfPokjaIds) {
                if (! empty($jfPokjaIds)) {
                    $q->whereIn('pokja_id', $jfPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id)
                    ->orWhereHas('parent', function ($p) use ($user) {
                        $p->where('pembuat_id', $user->id)
                            ->orWhere('penanggung_jawab_id', $user->id);
                    });
            });

            if ($request->filled('pokja_id')) {
                $baseQuery->where('pokja_id', $request->pokja_id);
            }
        } elseif ($user->isPegawai()) {
            $baseQuery->where(function ($q) use ($user) {
                $q->where('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id);
            });
        }

        // Pencarian Teks
        if ($request->filled('q')) {
            $qText = $request->q;
            $baseQuery->where(function ($q) use ($qText) {
                $q->where('judul', 'like', "%{$qText}%")
                    ->orWhere('kode_tampilan', 'like', "%{$qText}%")
                    ->orWhere('nomor_st', 'like', "%{$qText}%")
                    ->orWhereHas('penanggungJawab', fn ($pj) => $pj->where('name', 'like', "%{$qText}%"));
            });
        }

        // 1. Antrean Persetujuan Laporan Progres Pegawai
        $progresQuery = RealisasiPekerjaan::with(['pekerjaan.pembuat', 'pekerjaan.pokja', 'pekerjaan.penanggungJawab', 'user'])
            ->where('status_approval', 'menunggu_persetujuan');

        if ($user->isAdmin() || $user->isKabal() || $user->isKasubag()) {
            if ($request->filled('pokja_id')) {
                $progresQuery->whereHas('pekerjaan', fn ($q) => $q->where('pokja_id', $request->pokja_id));
            }
        } elseif ($user->isKatim()) {
            $katimPokjaIds = Pokja::where('katim_id', $user->id)->pluck('id')->toArray();
            if ($user->pokja_id && ! in_array($user->pokja_id, $katimPokjaIds)) {
                $katimPokjaIds[] = $user->pokja_id;
            }
            $progresQuery->whereHas('pekerjaan', function ($q) use ($user, $katimPokjaIds) {
                $q->where('pembuat_id', $user->id);
                if (! empty($katimPokjaIds)) {
                    $q->orWhereIn('pokja_id', $katimPokjaIds);
                }
            });
        } elseif ($user->isKorlayanan() || $user->isKorjafung()) {
            $progresQuery->whereHas('pekerjaan', function ($q) use ($user) {
                $q->where('pembuat_id', $user->id);
                if ($user->pokja_id) {
                    $q->orWhere('pokja_id', $user->pokja_id);
                }
            });
        } else {
            $progresQuery->where('user_id', $user->id);
        }

        if ($request->filled('q')) {
            $qText = $request->q;
            $progresQuery->where(function ($q) use ($qText) {
                $q->where('deskripsi_progres', 'like', "%{$qText}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$qText}%"))
                    ->orWhereHas('pekerjaan', fn ($p) => $p->where('judul', 'like', "%{$qText}%")->orWhere('kode_tampilan', 'like', "%{$qText}%"));
            });
        }

        $antreanProgres = $progresQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'page_progres');
        $countProgresPending = (clone $progresQuery)->count();

        // 2. Antrean Persetujuan Struktur Rincian (Draf / Pengajuan Tugas)
        $antreanRincian = (clone $baseQuery)
            ->whereIn('status_persetujuan', ['diajukan', 'perlu_perbaikan'])
            ->with(['parent', 'pembuat', 'penanggungJawab', 'pokja'])
            ->orderByRaw("CASE WHEN status_persetujuan = 'diajukan' THEN 1 WHEN status_persetujuan = 'perlu_perbaikan' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'page_rincian');

        // 3. Antrean Hasil Akhir
        $antreanHasil = (clone $baseQuery)
            ->where('status_pelaksanaan', 'hasil_diajukan')
            ->with(['penanggungJawab', 'pokja', 'realisasi' => fn ($r) => $r->latest()->with('user')])
            ->orderBy('updated_at', 'desc')
            ->paginate(15, ['*'], 'page_hasil');

        // 4. Riwayat Keputusan Persetujuan & Verifikasi
        $riwayatQuery = RiwayatPersetujuanPekerjaan::with(['pekerjaan.pokja', 'user'])
            ->latest();

        if ($user->isAdmin() || $user->isKabal() || $user->isKasubag()) {
            if ($request->filled('pokja_id')) {
                $riwayatQuery->whereHas('pekerjaan', fn ($q) => $q->where('pokja_id', $request->pokja_id));
            }
        } elseif ($user->isKatim()) {
            $katimPokjaIds = Pokja::where('katim_id', $user->id)->pluck('id')->toArray();
            if ($user->pokja_id && ! in_array($user->pokja_id, $katimPokjaIds)) {
                $katimPokjaIds[] = $user->pokja_id;
            }
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user, $katimPokjaIds) {
                if (! empty($katimPokjaIds)) {
                    $q->whereIn('pokja_id', $katimPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id)
                    ->orWhereHas('parent', fn ($p) => $p->where('pembuat_id', $user->id)->orWhere('penanggung_jawab_id', $user->id));
            });
        } elseif ($user->isKorlayanan()) {
            $subbagId = Pokja::where('kode_pokja', 'SUBBAG-UMUM')->value('id');
            $korPokjaIds = array_filter([$user->pokja_id, $subbagId]);
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user, $korPokjaIds) {
                if (! empty($korPokjaIds)) {
                    $q->whereIn('pokja_id', $korPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id)
                    ->orWhereHas('parent', fn ($p) => $p->where('pembuat_id', $user->id)->orWhere('penanggung_jawab_id', $user->id));
            });
        } elseif ($user->isKorjafung()) {
            $jfPokjaIds = Pokja::where('kode_pokja', 'LIKE', 'TIM%')->pluck('id')->toArray();
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user, $jfPokjaIds) {
                if (! empty($jfPokjaIds)) {
                    $q->whereIn('pokja_id', $jfPokjaIds);
                }
                $q->orWhere('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id)
                    ->orWhereHas('parent', fn ($p) => $p->where('pembuat_id', $user->id)->orWhere('penanggung_jawab_id', $user->id));
            });
        } elseif ($user->isPegawai()) {
            $riwayatQuery->whereHas('pekerjaan', function ($q) use ($user) {
                $q->where('pembuat_id', $user->id)
                    ->orWhere('penanggung_jawab_id', $user->id)
                    ->orWhereHas('pelaksana', fn ($sub) => $sub->where('users.id', $user->id));
            });
        }

        $riwayatPersetujuan = $riwayatQuery->paginate(15, ['*'], 'page_riwayat');

        // Metrik Statistik Cepat
        $countRincianPending = (clone $baseQuery)->where('status_persetujuan', 'diajukan')->count();
        $countRincianRevisi = (clone $baseQuery)->where('status_persetujuan', 'perlu_perbaikan')->count();
        $countHasilPending = (clone $baseQuery)->where('status_pelaksanaan', 'hasil_diajukan')->count();
        $countSelesaiTerverifikasi = (clone $baseQuery)->where('status_pelaksanaan', 'selesai_terverifikasi')->count();

        $pokjas = Pokja::orderBy('kode_pokja')->get();
        $activeTab = $request->get('tab', 'progres');
        $canReview = $user->canReviewPekerjaan();

        return compact(
            'antreanProgres',
            'countProgresPending',
            'antreanRincian',
            'antreanHasil',
            'riwayatPersetujuan',
            'countRincianPending',
            'countRincianRevisi',
            'countHasilPending',
            'countSelesaiTerverifikasi',
            'pokjas',
            'activeTab',
            'canReview'
        );
    }
}
