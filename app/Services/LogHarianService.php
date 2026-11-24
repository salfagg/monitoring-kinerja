<?php

namespace App\Services;

use App\Models\LogHarian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogHarianService
{
    /**
     * Hitung dan bangun data rekapitulasi log harian bulanan pegawai.
     */
    public function getRekapData(User $currentUser, Request $request): array
    {
        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        $targetUserId = $currentUser->id;
        $canManage = $currentUser->canManagePekerjaan() || $currentUser->isAdmin();

        if ($canManage && $request->filled('user_id')) {
            $targetUserId = (int) $request->user_id;
        }

        $targetUser = User::with(['pokja.katim'])->findOrFail($targetUserId);

        // Ambil seluruh log bulan ini
        $logs = LogHarian::with(['pekerjaan', 'skpTahunan', 'buktiDukung', 'verifikatorPokja', 'katim'])
            ->where('user_id', $targetUserId)
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Hitung statistik
        $totalLogs = $logs->count();
        $totalHariAktif = $logs->groupBy(fn ($i) => Carbon::parse($i->tanggal_kegiatan)->format('Y-m-d'))->count();
        $totalVolume = $logs->sum('volume_capaian');

        $totalDurasiMenit = $logs->reduce(function ($carry, $log) {
            if ($log->jam_mulai && $log->jam_selesai) {
                $start = Carbon::parse($log->jam_mulai);
                $end = Carbon::parse($log->jam_selesai);

                return $carry + max(0, $start->diffInMinutes($end));
            }

            return $carry;
        }, 0);
        $totalJamKerja = round($totalDurasiMenit / 60, 1);

        $namaBulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        // Pejabat Penilai / Katim
        $pejabatPenilai = null;
        if ($targetUser->pokja && $targetUser->pokja->katim) {
            $pejabatPenilai = $targetUser->pokja->katim;
        } else {
            // Kasubag atau Kabal
            $pejabatPenilai = User::whereIn('role', ['kasubag', 'kabal'])->first();
        }

        return [
            'targetUser' => $targetUser,
            'month' => $month,
            'year' => $year,
            'namaBulanIndo' => $namaBulanIndo,
            'logs' => $logs,
            'totalLogs' => $totalLogs,
            'totalHariAktif' => $totalHariAktif,
            'totalVolume' => $totalVolume,
            'totalJamKerja' => $totalJamKerja,
            'pejabatPenilai' => $pejabatPenilai,
        ];
    }

    /**
     * Generate file export CSV untuk rekapitulasi log harian bulanan.
     */
    public function exportCsv(User $currentUser, Request $request): StreamedResponse
    {
        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        $targetUserId = $currentUser->id;
        $canManage = $currentUser->canManagePekerjaan() || $currentUser->isAdmin();

        if ($canManage && $request->filled('user_id')) {
            $targetUserId = (int) $request->user_id;
        }

        $targetUser = User::with('pokja')->findOrFail($targetUserId);

        $logs = LogHarian::with(['pekerjaan', 'skpTahunan', 'buktiDukung'])
            ->where('user_id', $targetUserId)
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->orderBy('tanggal_kegiatan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $filename = 'Log_Harian_'.str_replace(' ', '_', $targetUser->name).'_'.sprintf('%02d', $month).'_'.$year.'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs, $targetUser, $month, $year) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM untuk Excel Indonesia
            fwrite($file, "\xEF\xBB\xBF");

            // Header Informasi Pegawai
            fputcsv($file, ['REKAPITULASI LOG HARIAN KINERJA PEGAWAI']);
            fputcsv($file, ['BGTK PROVINSI DKI JAKARTA']);
            fputcsv($file, ['Periode', sprintf('%02d/%d', $month, $year)]);
            fputcsv($file, ['Nama Pegawai', $targetUser->name]);
            fputcsv($file, ['NIP', $targetUser->nip ?? '-']);
            fputcsv($file, ['Jabatan', $targetUser->jabatan ?? 'Pegawai']);
            fputcsv($file, ['Unit Kerja / Pokja', $targetUser->pokja?->nama_pokja ?? '-']);
            fputcsv($file, []);

            // Header Tabel
            fputcsv($file, [
                'No',
                'Tanggal',
                'Jam Mulai',
                'Jam Selesai',
                'Durasi',
                'Uraian Kegiatan',
                'Output / Hasil',
                'Volume',
                'Satuan',
                'Rujukan Pekerjaan',
                'Rujukan SKP',
                'Status',
                'Bukti Dukung',
                'Catatan Katim',
            ]);

            $no = 1;
            foreach ($logs as $log) {
                $durasi = '-';
                if ($log->jam_mulai && $log->jam_selesai) {
                    $start = Carbon::parse($log->jam_mulai);
                    $end = Carbon::parse($log->jam_selesai);
                    $durasi = $start->diffInMinutes($end).' Menit';
                }

                $buktiText = '-';
                if ($log->buktiDukung->isNotEmpty()) {
                    $buktiText = $log->buktiDukung->pluck('nama_dokumen')->implode('; ');
                }

                fputcsv($file, [
                    $no++,
                    Carbon::parse($log->tanggal_kegiatan)->format('d/m/Y'),
                    $log->jam_mulai ? Carbon::parse($log->jam_mulai)->format('H:i') : '-',
                    $log->jam_selesai ? Carbon::parse($log->jam_selesai)->format('H:i') : '-',
                    $durasi,
                    $log->uraian_kegiatan,
                    $log->output_kegiatan,
                    $log->volume_capaian,
                    $log->satuan,
                    $log->pekerjaan?->judul ?? '-',
                    $log->skpTahunan?->rencana_hasil_kerja ?? '-',
                    $log->status_label,
                    $buktiText,
                    $log->catatan_katim ?? ($log->catatan_pokja ?? '-'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
