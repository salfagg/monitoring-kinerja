<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Services\RekapPenugasanService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapPenugasanController extends Controller
{
    public function __construct(
        protected RekapPenugasanService $rekapService
    ) {}

    /**
     * Tampilkan halaman utama Rekapitulasi Penugasan & Surat Tugas per Orang.
     */
    public function index(Request $request)
    {
        $pokjas = Pokja::orderBy('id')->get();
        $dataRekap = $this->rekapService->buildRekapData($request);

        return view('rekap_penugasan.index', [
            'pegawaiList' => $dataRekap['pegawaiList'],
            'kpi' => $dataRekap['kpi'],
            'pokjas' => $pokjas,
            'filters' => [
                'q' => $request->input('q', ''),
                'pokja_id' => $request->input('pokja_id', ''),
                'status_pegawai' => $request->input('status_pegawai', ''),
                'filter_tugas' => $request->input('filter_tugas', 'all'),
            ],
        ]);
    }

    /**
     * Ekspor seluruh data rekap dan rincian tugas ke file Excel (.xlsx).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $dataRekap = $this->rekapService->buildRekapData($request);
        $spreadsheet = $this->rekapService->generateExcelSpreadsheet(
            $dataRekap['pegawaiList'],
            $dataRekap['kpi']
        );

        $filename = 'Rekap_Penugasan_ST_BGTK_Jakarta_'.date('Ymd_His').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }
}
