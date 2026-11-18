<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekapPenugasanController extends Controller
{
    /**
     * Tampilkan halaman utama Rekapitulasi Penugasan & Surat Tugas per Orang.
     */
    public function index(Request $request)
    {
        $pokjas = Pokja::orderBy('id')->get();

        $dataRekap = $this->buildRekapData($request);

        return view('rekap_penugasan.index', [
            'pegawaiList'   => $dataRekap['pegawaiList'],
            'kpi'           => $dataRekap['kpi'],
            'pokjas'        => $pokjas,
            'filters'       => [
                'q'              => $request->input('q', ''),
                'pokja_id'       => $request->input('pokja_id', ''),
                'status_pegawai' => $request->input('status_pegawai', ''),
                'filter_tugas'   => $request->input('filter_tugas', 'all'),
            ],
        ]);
    }

    /**
     * Ekspor seluruh data rekap dan rincian tugas ke file Excel (.xlsx).
     */
    public function exportExcel(Request $request)
    {
        $dataRekap = $this->buildRekapData($request);
        $pegawaiList = $dataRekap['pegawaiList'];
        $kpi = $dataRekap['kpi'];

        $spreadsheet = new Spreadsheet();

        // ==========================================
        // SHEET 1: REKAP BEBAN PENUGASAN PEGAWAI
        // ==========================================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Rekap Beban Pegawai');

        // Judul Laporan
        $sheet1->setCellValue('A1', 'REKAPITULASI BEBAN PENUGASAN & SURAT TUGAS PEGAWAI');
        $sheet1->setCellValue('A2', 'BALAI GURU PENGGERAK (BGTK) PROVINSI DKI JAKARTA - TANJIDOR');
        $sheet1->setCellValue('A3', 'Waktu Pengunduhan: ' . Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm') . ' WIB');

        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0B4A82'));
        $sheet1->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('334155'));
        $sheet1->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        // Header Kolom Tabel Sheet 1
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
            'M5' => 'Rata-rata Progres (%)',
        ];

        foreach ($headersSheet1 as $cell => $text) {
            $sheet1->setCellValue($cell, $text);
        }

        // Styling Header Sheet 1
        $headerStyle1 = [
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0B4A82'], // TANJIDOR Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet1->getStyle('A5:M5')->applyFromArray($headerStyle1);
        $sheet1->getRowDimension(5)->setRowHeight(32);

        // Isi Data Sheet 1
        $rowNum1 = 6;
        $no1 = 1;

        foreach ($pegawaiList as $item) {
            $isPns = (!empty($item['nip']) && $item['nip'] !== '-' && strtolower($item['jabatan']) !== 'tenaga alih daya');
            $statusPegawai = $isPns ? 'PNS' : 'Tenaga Alih Daya';

            $sheet1->setCellValue("A{$rowNum1}", $no1);
            $sheet1->setCellValue("B{$rowNum1}", $item['name']);
            $sheet1->setCellValueExplicit("C{$rowNum1}", (string) ($item['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet1->setCellValue("D{$rowNum1}", $statusPegawai);
            $sheet1->setCellValue("E{$rowNum1}", $item['jabatan'] ?? '-');
            $sheet1->setCellValue("F{$rowNum1}", $item['pokja_nama'] ?? 'Balai Guru Penggerak');
            $sheet1->setCellValue("G{$rowNum1}", $item['total_st']);
            $sheet1->setCellValue("H{$rowNum1}", $item['total_tugas']);
            $sheet1->setCellValue("I{$rowNum1}", $item['total_pj']);
            $sheet1->setCellValue("J{$rowNum1}", $item['total_anggota']);
            $sheet1->setCellValue("K{$rowNum1}", $item['tugas_selesai']);
            $sheet1->setCellValue("L{$rowNum1}", $item['tugas_berjalan']);
            $sheet1->setCellValue("M{$rowNum1}", round($item['avg_progres'], 1) . '%');

            // Alignments
            $sheet1->getStyle("A{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("C{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("D{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("G{$rowNum1}:M{$rowNum1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra striping
            if ($no1 % 2 === 0) {
                $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            // Border per cell
            $sheet1->getStyle("A{$rowNum1}:M{$rowNum1}")->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

            $rowNum1++;
            $no1++;
        }

        // Summary Total Row
        $summaryRow = $rowNum1;
        $sheet1->setCellValue("A{$summaryRow}", 'TOTAL');
        $sheet1->mergeCells("A{$summaryRow}:F{$summaryRow}");
        $sheet1->setCellValue("G{$summaryRow}", "=SUM(G6:G" . ($summaryRow - 1) . ")");
        $sheet1->setCellValue("H{$summaryRow}", "=SUM(H6:H" . ($summaryRow - 1) . ")");
        $sheet1->setCellValue("I{$summaryRow}", "=SUM(I6:I" . ($summaryRow - 1) . ")");
        $sheet1->setCellValue("J{$summaryRow}", "=SUM(J6:J" . ($summaryRow - 1) . ")");
        $sheet1->setCellValue("K{$summaryRow}", "=SUM(K6:K" . ($summaryRow - 1) . ")");
        $sheet1->setCellValue("L{$summaryRow}", "=SUM(L6:L" . ($summaryRow - 1) . ")");
        $sheet1->setCellValue("M{$summaryRow}", "=AVERAGE(G6:G" . ($summaryRow - 1) . ")");

        $sheet1->getStyle("A{$summaryRow}:M{$summaryRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '64748B']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
            ],
        ]);
        $sheet1->getStyle("A{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto column widths Sheet 1
        foreach (range('A', 'M') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // ==========================================
        // SHEET 2: RINCIAN TUGAS & SURAT TUGAS
        // ==========================================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Rincian ST & Tugas');

        // Judul Laporan Sheet 2
        $sheet2->setCellValue('A1', 'RINCIAN SELURUH PENUGASAN & SURAT TUGAS PER PEGAWAI');
        $sheet2->setCellValue('A2', 'BALAI GURU PENGGERAK (BGTK) PROVINSI DKI JAKARTA - TANJIDOR');
        $sheet2->setCellValue('A3', 'Waktu Pengunduhan: ' . Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm') . ' WIB');

        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0A6FB5'));
        $sheet2->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('334155'));
        $sheet2->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        // Header Kolom Tabel Sheet 2
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
            'L5' => 'Progres (%)',
            'M5' => 'Status Pelaksanaan',
        ];

        foreach ($headersSheet2 as $cell => $text) {
            $sheet2->setCellValue($cell, $text);
        }

        // Styling Header Sheet 2
        $headerStyle2 = [
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A6FB5'], // Primary Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet2->getStyle('A5:M5')->applyFromArray($headerStyle2);
        $sheet2->getRowDimension(5)->setRowHeight(32);

        // Isi Data Sheet 2
        $rowNum2 = 6;
        $no2 = 1;

        foreach ($pegawaiList as $item) {
            if (empty($item['daftar_tugas']) || count($item['daftar_tugas']) === 0) {
                // Pegawai belum memiliki penugasan
                $sheet2->setCellValue("A{$rowNum2}", $no2);
                $sheet2->setCellValue("B{$rowNum2}", $item['name']);
                $sheet2->setCellValueExplicit("C{$rowNum2}", (string) ($item['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValue("D{$rowNum2}", $item['jabatan'] ?? '-');
                $sheet2->setCellValue("E{$rowNum2}", $item['pokja_nama'] ?? 'Balai Guru Penggerak');
                $sheet2->setCellValue("F{$rowNum2}", '(Belum ada penugasan)');
                $sheet2->setCellValue("G{$rowNum2}", '-');
                $sheet2->setCellValue("H{$rowNum2}", '-');
                $sheet2->setCellValue("I{$rowNum2}", '-');
                $sheet2->setCellValue("J{$rowNum2}", '-');
                $sheet2->setCellValue("K{$rowNum2}", '-');
                $sheet2->setCellValue("L{$rowNum2}", '0%');
                $sheet2->setCellValue("M{$rowNum2}", 'Belum Ada Tugas');

                $sheet2->getStyle("A{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("C{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("F{$rowNum2}:M{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("A{$rowNum2}:M{$rowNum2}")->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('94A3B8'));
                $sheet2->getStyle("A{$rowNum2}:M{$rowNum2}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

                $rowNum2++;
                $no2++;
                continue;
            }

            foreach ($item['daftar_tugas'] as $tgs) {
                $sheet2->setCellValue("A{$rowNum2}", $no2);
                $sheet2->setCellValue("B{$rowNum2}", $item['name']);
                $sheet2->setCellValueExplicit("C{$rowNum2}", (string) ($item['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValue("D{$rowNum2}", $item['jabatan'] ?? '-');
                $sheet2->setCellValue("E{$rowNum2}", $tgs['pokja_nama'] ?? $item['pokja_nama'] ?? '-');
                $sheet2->setCellValue("F{$rowNum2}", !empty($tgs['nomor_st']) ? $tgs['nomor_st'] : 'Penugasan Internal (Tanpa No. ST)');
                $sheet2->setCellValue("G{$rowNum2}", !empty($tgs['tanggal_st']) ? Carbon::parse($tgs['tanggal_st'])->format('d/m/Y') : '-');
                $sheet2->setCellValue("H{$rowNum2}", $tgs['kode_tampilan'] ?? '-');
                $sheet2->setCellValue("I{$rowNum2}", $tgs['judul']);
                $sheet2->setCellValue("J{$rowNum2}", $tgs['peran']);
                $sheet2->setCellValue("K{$rowNum2}", !empty($tgs['tenggat']) ? Carbon::parse($tgs['tenggat'])->format('d/m/Y') : '-');
                $sheet2->setCellValue("L{$rowNum2}", round($tgs['progres'], 1) . '%');
                $sheet2->setCellValue("M{$rowNum2}", $tgs['status_label']);

                // Alignment
                $sheet2->getStyle("A{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("C{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("G{$rowNum2}:H{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle("J{$rowNum2}:M{$rowNum2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Zebra striping
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

        // Auto column widths Sheet 2
        foreach (range('A', 'M') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet to 1
        $spreadsheet->setActiveSheetIndex(0);

        // Download Response via Laravel Streamed Response
        $filename = 'Rekap_Penugasan_ST_BGTK_Jakarta_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma'              => 'public',
        ]);
    }

    /**
     * Membangun dataset rekapitulasi pegawai beserta surat tugas & pekerjaannya.
     */
    protected function buildRekapData(Request $request): array
    {
        $qSearch       = trim((string) $request->input('q', ''));
        $pokjaId       = $request->input('pokja_id');
        $statusPegawai = $request->input('status_pegawai');
        $filterTugas   = $request->input('filter_tugas', 'all');

        $userQuery = User::with([
            'pokja',
            'pekerjaanDitugaskan' => function ($q) {
                $q->with(['pokja', 'penanggungJawab'])->orderBy('tenggat', 'asc');
            },
            'pekerjaanSebagaiPj' => function ($q) {
                $q->with(['pokja', 'pelaksana'])->orderBy('tenggat', 'asc');
            },
        ]);

        // Filter Pokja
        if (!empty($pokjaId)) {
            $userQuery->where(function ($uq) use ($pokjaId) {
                $uq->where('pokja_id', $pokjaId)
                    ->orWhereHas('pekerjaanDitugaskan', fn ($sub) => $sub->where('pokja_id', $pokjaId))
                    ->orWhereHas('pekerjaanSebagaiPj', fn ($sub) => $sub->where('pokja_id', $pokjaId));
            });
        }

        // Filter Status Kepegawaian (PNS vs Tenaga Alih Daya)
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
            // Gabungkan pekerjaan di mana user adalah Pelaksana atau Penanggung Jawab
            $combinedJobs = collect();

            foreach ($user->pekerjaanDitugaskan as $job) {
                $combinedJobs->put($job->id, $job);
            }
            foreach ($user->pekerjaanSebagaiPj as $job) {
                $combinedJobs->put($job->id, $job);
            }

            // Filter jika ada pencarian keyword pada judul atau nomor ST
            if ($qSearch !== '') {
                $nameMatches = (stripos($user->name, $qSearch) !== false) ||
                               (stripos((string) $user->nip, $qSearch) !== false) ||
                               (stripos((string) $user->jabatan, $qSearch) !== false);

                if (!$nameMatches) {
                    // Cari di judul pekerjaan atau nomor ST
                    $matchedJobs = $combinedJobs->filter(function ($job) use ($qSearch) {
                        return (stripos($job->judul, $qSearch) !== false) ||
                               (stripos((string) $job->nomor_st, $qSearch) !== false) ||
                               (stripos((string) $job->kode_tampilan, $qSearch) !== false);
                    });

                    if ($matchedJobs->isEmpty()) {
                        // User tidak cocok dengan pencarian
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
            $sumProgres = 0;
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

                $progresVal = (float) ($job->progres ?? 0);
                $sumProgres += $progresVal;

                // Status label format
                $statusLabel = match ($job->status_pelaksanaan) {
                    'selesai_terverifikasi'  => 'Selesai',
                    'hasil_diajukan'         => 'Menunggu Verifikasi',
                    'dikerjakan'             => 'Sedang Dikerjakan',
                    'perlu_perbaikan_hasil'  => 'Perlu Revisi',
                    default                  => 'Belum Dimulai',
                };

                $statusBadgeClass = match ($job->status_pelaksanaan) {
                    'selesai_terverifikasi'  => 'badge-selesai',
                    'hasil_diajukan'         => 'badge-verifikasi',
                    'dikerjakan'             => 'badge-dikerjakan',
                    'perlu_perbaikan_hasil'  => 'badge-revisi',
                    default                  => 'badge-belum',
                };

                $daftarTugas[] = [
                    'id'                 => $job->id,
                    'kode_tampilan'      => $job->kode_tampilan ?? 'TGS-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
                    'judul'              => $job->judul,
                    'nomor_st'           => $nomorSt !== '' ? $nomorSt : null,
                    'tanggal_st'         => $job->tanggal_st,
                    'pokja_nama'         => $job->pokja?->nama_pokja ?? 'BGTK Provinsi DKI Jakarta',
                    'pokja_kode'         => $job->pokja?->kode_pokja ?? 'BGTK',
                    'peran'              => $peranLabel,
                    'is_pj'              => $isPj,
                    'tenggat'            => $job->tenggat,
                    'progres'            => $progresVal,
                    'status_pelaksanaan' => $job->status_pelaksanaan,
                    'status_label'       => $statusLabel,
                    'status_badge_class' => $statusBadgeClass,
                ];
            }

            $totalTugas = $combinedJobs->count();
            if ($totalTugas > 0) {
                $totalPegawaiBertugas++;
            }

            // Filter status penugasan (ada_tugas vs tanpa_tugas)
            if ($filterTugas === 'ada_tugas' && $totalTugas === 0) {
                continue;
            } elseif ($filterTugas === 'tanpa_tugas' && $totalTugas > 0) {
                continue;
            }

            $avgProgres = $totalTugas > 0 ? ($sumProgres / $totalTugas) : 0;

            $pegawaiList[] = [
                'id'            => $user->id,
                'name'          => $user->name,
                'nip'           => $user->nip,
                'jabatan'       => $user->jabatan,
                'role'          => $user->role,
                'role_label'    => $user->role_label,
                'pokja_id'      => $user->pokja_id,
                'pokja_nama'    => $user->pokja?->nama_pokja ?? '-',
                'pokja_kode'    => $user->pokja?->kode_pokja ?? '-',
                'total_st'      => count($userStList),
                'st_list'       => array_keys($userStList),
                'total_tugas'   => $totalTugas,
                'total_pj'      => $totalPj,
                'total_anggota' => $totalAnggota,
                'tugas_selesai' => $tugasSelesai,
                'tugas_berjalan'=> $tugasBerjalan,
                'avg_progres'   => $avgProgres,
                'daftar_tugas'  => $daftarTugas,
            ];
        }

        // Urutkan: pegawai dengan tugas terbanyak di atas
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
            'kpi'         => [
                'total_pegawai'          => $totalPegawai,
                'total_pegawai_bertugas' => $totalPegawaiBertugas,
                'total_st_terbit'        => $totalStTerbit,
                'total_penugasan_balai'  => $totalPenugasanBalai,
                'rata_beban'             => $rataBeban,
            ],
        ];
    }
}
