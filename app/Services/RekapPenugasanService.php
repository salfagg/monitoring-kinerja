<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapPenugasanService
{
    /**
     * Membangun dataset rekapitulasi penugasan & surat tugas per pegawai.
     */
    public function buildRekapData(Request $request): array
    {
        $qSearch = trim((string) $request->input('q', ''));
        $pokjaId = $request->input('pokja_id');
        $statusPegawai = $request->input('status_pegawai');
        $filterTugas = $request->input('filter_tugas', 'all');

        $userQuery = User::with([
            'pokja',
            'pekerjaanDitugaskan' => function ($q) {
                $q->with(['pokja', 'penanggungJawab'])->orderBy('tenggat', 'asc');
            },
            'pekerjaanSebagaiPj' => function ($q) {
                $q->with(['pokja', 'pelaksana'])->orderBy('tenggat', 'asc');
            },
        ]);

        if (! empty($pokjaId)) {
            $userQuery->where(function ($uq) use ($pokjaId) {
                $uq->where('pokja_id', $pokjaId)
                    ->orWhereHas('pekerjaanDitugaskan', fn ($sub) => $sub->where('pokja_id', $pokjaId))
                    ->orWhereHas('pekerjaanSebagaiPj', fn ($sub) => $sub->where('pokja_id', $pokjaId));
            });
        }

        if ($statusPegawai === 'pns') {
            $userQuery->whereNotNull('nip')
                ->where('nip', '!=', '')
                ->where('nip', '!=', '-')
                ->where('jabatan', '!=', 'Tenaga Alih Daya');
        } elseif ($statusPegawai === 'tad') {
            $userQuery->where(function ($uq) {
                $uq->where('jabatan', 'like', '%Tenaga Alih Daya%')
                    ->orWhereNull('nip')
                    ->orWhere('nip', '')
                    ->orWhere('nip', '-');
            });
        }

        $users = $userQuery->orderBy('name', 'asc')->get();

        $pegawaiList = [];
        $totalStSet = [];
        $totalTugasBalaiSet = [];
        $totalPegawaiBertugas = 0;

        foreach ($users as $user) {
            $combinedJobs = collect();

            foreach ($user->pekerjaanDitugaskan as $job) {
                $combinedJobs->put($job->id, $job);
            }
            foreach ($user->pekerjaanSebagaiPj as $job) {
                $combinedJobs->put($job->id, $job);
            }

            if ($qSearch !== '') {
                $nameMatches = (stripos($user->name, $qSearch) !== false) ||
                               (stripos((string) $user->nip, $qSearch) !== false) ||
                               (stripos((string) $user->jabatan, $qSearch) !== false);

                if (! $nameMatches) {
                    $matchedJobs = $combinedJobs->filter(function ($job) use ($qSearch) {
                        return (stripos($job->judul, $qSearch) !== false) ||
                               (stripos((string) $job->nomor_st, $qSearch) !== false) ||
                               (stripos((string) $job->kode_tampilan, $qSearch) !== false);
                    });

                    if ($matchedJobs->isEmpty()) {
                        continue;
                    }
                    $combinedJobs = $matchedJobs;
                }
            }

            $userStList = [];
            $totalPj = 0;
            $totalAnggota = 0;
            $tugasSelesai = 0;
            $tugasBerjalan = 0;
            $daftarTugas = [];

            foreach ($combinedJobs as $job) {
                $isPj = ($job->penanggung_jawab_id === $user->id);
                if ($isPj) {
                    $totalPj++;
                    $peranLabel = 'Penanggung Jawab (PIC)';
                } else {
                    $totalAnggota++;
                    $peranLabel = 'Anggota Tim';
                }

                $nomorSt = trim((string) $job->nomor_st);
                if ($nomorSt !== '' && $nomorSt !== '-') {
                    $userStList[$nomorSt] = true;
                    $totalStSet[$nomorSt] = true;
                }

                $totalTugasBalaiSet[$job->id] = true;

                $isSelesai = ($job->status_pelaksanaan === 'selesai_terverifikasi');
                if ($isSelesai) {
                    $tugasSelesai++;
                } else {
                    $tugasBerjalan++;
                }

                $statusLabel = match ($job->status_pelaksanaan) {
                    'selesai_terverifikasi' => 'Selesai',
                    'hasil_diajukan' => 'Menunggu Verifikasi',
                    'dikerjakan' => 'Sedang Dikerjakan',
                    'perlu_perbaikan_hasil' => 'Perlu Revisi',
                    default => 'Belum Dimulai',
                };

                $statusBadgeClass = match ($job->status_pelaksanaan) {
                    'selesai_terverifikasi' => 'badge-selesai',
                    'hasil_diajukan' => 'badge-verifikasi',
                    'dikerjakan' => 'badge-dikerjakan',
                    'perlu_perbaikan_hasil' => 'badge-revisi',
                    default => 'badge-belum',
                };

                $daftarTugas[] = [
                    'id' => $job->id,
                    'kode_tampilan' => $job->kode_tampilan ?? 'TGS-'.str_pad($job->id, 3, '0', STR_PAD_LEFT),
                    'judul' => $job->judul,
                    'nomor_st' => $nomorSt !== '' ? $nomorSt : null,
                    'tanggal_st' => $job->tanggal_st,
                    'pokja_nama' => $job->pokja?->nama_pokja ?? 'BGTK Provinsi DKI Jakarta',
                    'pokja_kode' => $job->pokja?->kode_pokja ?? 'BGTK',
                    'peran' => $peranLabel,
                    'is_pj' => $isPj,
                    'tenggat' => $job->tenggat,
                    'laporan_count' => $job->realisasi->where('status_approval', 'disetujui')->count(),
                    'status_pelaksanaan' => $job->status_pelaksanaan,
                    'status_label' => $statusLabel,
                    'status_badge_class' => $statusBadgeClass,
                ];
            }

            $totalTugas = $combinedJobs->count();
            if ($totalTugas > 0) {
                $totalPegawaiBertugas++;
            }

            if ($filterTugas === 'ada_tugas' && $totalTugas === 0) {
                continue;
            } elseif ($filterTugas === 'tanpa_tugas' && $totalTugas > 0) {
                continue;
            }

            $tingkatKetuntasan = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100, 1) : 0;

            $pegawaiList[] = [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip,
                'jabatan' => $user->jabatan,
                'role' => $user->role,
                'role_label' => $user->role_label,
                'pokja_id' => $user->pokja_id,
                'pokja_nama' => $user->pokja?->nama_pokja ?? '-',
                'pokja_kode' => $user->pokja?->kode_pokja ?? '-',
                'total_st' => count($userStList),
                'st_list' => array_keys($userStList),
                'total_tugas' => $totalTugas,
                'total_pj' => $totalPj,
                'total_anggota' => $totalAnggota,
                'tugas_selesai' => $tugasSelesai,
                'tugas_berjalan' => $tugasBerjalan,
                'tingkat_ketuntasan' => $tingkatKetuntasan,
                'avg_progres' => $tingkatKetuntasan,
                'daftar_tugas' => $daftarTugas,
            ];
        }

        usort($pegawaiList, function ($a, $b) {
            if ($a['total_tugas'] === $b['total_tugas']) {
                if ($a['total_st'] === $b['total_st']) {
                    return strcasecmp($a['name'], $b['name']);
                }

                return $b['total_st'] <=> $a['total_st'];
            }

            return $b['total_tugas'] <=> $a['total_tugas'];
        });

        $totalPegawai = count($pegawaiList);
        $totalPenugasanBalai = count($totalTugasBalaiSet);
        $totalStTerbit = count($totalStSet);
        $rataBeban = $totalPegawai > 0 ? round($totalPenugasanBalai / $totalPegawai, 1) : 0;

        return [
            'pegawaiList' => $pegawaiList,
            'kpi' => [
                'total_pegawai' => $totalPegawai,
                'total_pegawai_bertugas' => $totalPegawaiBertugas,
                'total_st_terbit' => $totalStTerbit,
                'total_penugasan_balai' => $totalPenugasanBalai,
                'rata_beban' => $rataBeban,
            ],
        ];
    }

    /**
     * Membuat dokumen Spreadsheet lengkap dengan 2 sheet rapi.
     */
    public function generateExcelSpreadsheet(array $pegawaiList, array $kpi): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;

        // SHEET 1: REKAP BEBAN PENUGASAN PEGAWAI
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Rekap Beban Pegawai');

        $sheet1->setCellValue('A1', 'REKAPITULASI BEBAN PENUGASAN & SURAT TUGAS PEGAWAI');
        $sheet1->setCellValue('A2', 'BALAI GURU PENGGERAK (BGTK) PROVINSI DKI JAKARTA - TANJIDOR');
        $sheet1->setCellValue('A3', 'Waktu Pengunduhan: '.Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm').' WIB');

        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('0B4A82'));
        $sheet1->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('334155'));
        $sheet1->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('64748B'));

        $headersSheet1 = [
            'A5' => 'No',
            'B5' => 'Nama Pegawai',
            'C5' => 'NIP',
            'D5' => 'Status Kepegawaian',
            'E5' => 'Jabatan',
            'F5' => 'Unit Kerja / Pokja',
            'G5' => 'Jumlah ST Unik',
            'H5' => 'Total Penugasan',
            'I5' => 'Sebagai PIC / PJ',
            'J5' => 'Sebagai Anggota',
            'K5' => 'Tugas Selesai',
            'L5' => 'Tugas Berjalan',
            'M5' => 'Ketuntasan Tugas (%)',
        ];

        foreach ($headersSheet1 as $cell => $text) {
            $sheet1->setCellValue($cell, $text);
        }

        $headerStyle1 = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B4A82']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet1->getStyle('A5:M5')->applyFromArray($headerStyle1);
        $sheet1->getRowDimension(5)->setRowHeight(32);

        $rowNum1 = 6;
        $no1 = 1;

        foreach ($pegawaiList as $item) {
            $isPns = (! empty($item['nip']) && $item['nip'] !== '-' && strtolower($item['jabatan']) !== 'tenaga alih daya');
            $statusPegawai = $isPns ? 'PNS' : 'Tenaga Alih Daya';

            $sheet1->setCellValue("A{$rowNum1}", $no1);
            $sheet1->setCellValue("B{$rowNum1}", $item['name']);
            $sheet1->setCellValueExplicit("C{$rowNum1}", (string) ($item['nip'] ?? '-'), DataType::TYPE_STRING);
            $sheet1->setCellValue("D{$rowNum1}", $statusPegawai);
            $sheet1->setCellValue("E{$rowNum1}", $item['jabatan'] ?? '-');
            $sheet1->setCellValue("F{$rowNum1}", $item['pokja_nama'] ?? 'Balai Guru Penggerak');
            $sheet1->setCellValue("G{$rowNum1}", $item['total_st']);
            $sheet1->setCellValue("H{$rowNum1}", $item['total_tugas']);
            $sheet1->setCellValue("I{$rowNum1}", $item['total_pj']);
            $sheet1->setCellValue("J{$rowNum1}", $item['total_anggota']);
            $sheet1->setCellValue("K{$rowNum1}", $item['tugas_selesai']);
            $sheet1->setCellValue("L{$rowNum1}", $item['tugas_berjalan']);
            $sheet1->setCellValue("M{$rowNum1}", round($item['avg_progres'], 1).'%');

            $sheet1->getStyle("A{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("C{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("D{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("G{$rowNum1}:M{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($no1 % 2 === 0) {
                $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

            $rowNum1++;
            $no1++;
        }

        $summaryRow = $rowNum1;
        $sheet1->setCellValue("A{$summaryRow}", 'TOTAL');
        $sheet1->mergeCells("A{$summaryRow}:F{$summaryRow}");
        $sheet1->setCellValue("G{$summaryRow}", '=SUM(G6:G'.($summaryRow - 1).')');
        $sheet1->setCellValue("H{$summaryRow}", '=SUM(H6:H'.($summaryRow - 1).')');
        $sheet1->setCellValue("I{$summaryRow}", '=SUM(I6:I'.($summaryRow - 1).')');
        $sheet1->setCellValue("J{$summaryRow}", '=SUM(J6:J'.($summaryRow - 1).')');
        $sheet1->setCellValue("K{$summaryRow}", '=SUM(K6:K'.($summaryRow - 1).')');
        $sheet1->setCellValue("L{$summaryRow}", '=SUM(L6:L'.($summaryRow - 1).')');
        $sheet1->setCellValue("M{$summaryRow}", '=AVERAGE(G6:G'.($summaryRow - 1).')');

        $sheet1->getStyle("A{$summaryRow}:M{$summaryRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '64748B']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
            ],
        ]);

        foreach (range('A', 'M') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // SHEET 2: RINCIAN TUGAS & SURAT TUGAS
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Rincian ST & Tugas');

        $sheet2->setCellValue('A1', 'RINCIAN SELURUH PENUGASAN & SURAT TUGAS PER PEGAWAI');
        $sheet2->setCellValue('A2', 'BALAI GURU PENGGERAK (BGTK) PROVINSI DKI JAKARTA - TANJIDOR');
        $sheet2->setCellValue('A3', 'Waktu Pengunduhan: '.Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm').' WIB');

        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('0A6FB5'));
        $sheet2->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('334155'));
        $sheet2->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('64748B'));

        $headersSheet2 = [
            'A5' => 'No',
            'B5' => 'Nama Pegawai',
            'C5' => 'NIP',
            'D5' => 'Jabatan',
            'E5' => 'Unit Kerja / Pokja',
            'F5' => 'Nomor Surat Tugas (ST)',
            'G5' => 'Tanggal ST',
            'H5' => 'Kode Tugas',
            'I5' => 'Judul Pekerjaan / Penugasan',
            'J5' => 'Peran dalam Tugas',
            'K5' => 'Tenggat Waktu',
            'L5' => 'Laporan Disetujui',
            'M5' => 'Status Pelaksanaan',
        ];

        foreach ($headersSheet2 as $cell => $text) {
            $sheet2->setCellValue($cell, $text);
        }

        $headerStyle2 = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A6FB5']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet2->getStyle('A5:M5')->applyFromArray($headerStyle2);
        $sheet2->getRowDimension(5)->setRowHeight(32);

        $rowNum2 = 6;
        $no2 = 1;

        foreach ($pegawaiList as $item) {
            if (empty($item['daftar_tugas']) || count($item['daftar_tugas']) === 0) {
                $sheet2->setCellValue("A{$rowNum2}", $no2);
                $sheet2->setCellValue("B{$rowNum2}", $item['name']);
                $sheet2->setCellValueExplicit("C{$rowNum2}", (string) ($item['nip'] ?? '-'), DataType::TYPE_STRING);
                $sheet2->setCellValue("D{$rowNum2}", $item['jabatan'] ?? '-');
                $sheet2->setCellValue("E{$rowNum2}", $item['pokja_nama'] ?? 'Balai Guru Penggerak');
                $sheet2->setCellValue("F{$rowNum2}", '(Belum ada penugasan)');
                $sheet2->setCellValue("G{$rowNum2}", '-');
                $sheet2->setCellValue("H{$rowNum2}", '-');
                $sheet2->setCellValue("I{$rowNum2}", '-');
                $sheet2->setCellValue("J{$rowNum2}", '-');
                $sheet2->setCellValue("K{$rowNum2}", '-');
                $sheet2->setCellValue("L{$rowNum2}", '-');
                $sheet2->setCellValue("M{$rowNum2}", 'Belum Ada Tugas');

                $sheet2->getStyle("A{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("C{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("F{$rowNum2}:M{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("A{$rowNum2}:M{$rowNum2}")->getFont()->setItalic(true)->setColor(new Color('94A3B8'));
                $sheet2->getStyle("A{$rowNum2}:M{$rowNum2}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

                $rowNum2++;
                $no2++;

                continue;
            }

            foreach ($item['daftar_tugas'] as $tgs) {
                $sheet2->setCellValue("A{$rowNum2}", $no2);
                $sheet2->setCellValue("B{$rowNum2}", $item['name']);
                $sheet2->setCellValueExplicit("C{$rowNum2}", (string) ($item['nip'] ?? '-'), DataType::TYPE_STRING);
                $sheet2->setCellValue("D{$rowNum2}", $item['jabatan'] ?? '-');
                $sheet2->setCellValue("E{$rowNum2}", $tgs['pokja_nama'] ?? $item['pokja_nama'] ?? '-');
                $sheet2->setCellValue("F{$rowNum2}", ! empty($tgs['nomor_st']) ? $tgs['nomor_st'] : 'Penugasan Internal (Tanpa No. ST)');
                $sheet2->setCellValue("G{$rowNum2}", ! empty($tgs['tanggal_st']) ? Carbon::parse($tgs['tanggal_st'])->format('d/m/Y') : '-');
                $sheet2->setCellValue("H{$rowNum2}", $tgs['kode_tampilan'] ?? '-');
                $sheet2->setCellValue("I{$rowNum2}", $tgs['judul']);
                $sheet2->setCellValue("J{$rowNum2}", $tgs['peran']);
                $sheet2->setCellValue("K{$rowNum2}", ! empty($tgs['tenggat']) ? Carbon::parse($tgs['tenggat'])->format('d/m/Y') : '-');
                $sheet2->setCellValue("L{$rowNum2}", $tgs['laporan_count'].' Laporan');
                $sheet2->setCellValue("M{$rowNum2}", $tgs['status_label']);

                $sheet2->getStyle("A{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("C{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("G{$rowNum2}:H{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("J{$rowNum2}:M{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                if ($no2 % 2 === 0) {
                    $sheet2->getStyle("A{$rowNum2}:M{$rowNum2}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F8FAFC');
                }

                $sheet2->getStyle("A{$rowNum2}:M{$rowNum2}")->getBorders()
                    ->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

                $rowNum2++;
                $no2++;
            }
        }

        foreach (range('A', 'M') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }
}
