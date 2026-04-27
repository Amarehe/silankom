<?php

namespace App\Http\Controllers;

use App\Exports\DukunganExport;
use App\Exports\PeminjamanExport;
use App\Exports\PerbaikanExport;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function __construct(
        private LaporanService $laporanService,
    ) {}

    /**
     * Export rekap as PDF.
     */
    public function exportPdf(Request $request, string $jenis): Response
    {
        $this->authorizeAccess();

        $filters = $this->extractFilters($request);
        $data = $this->getData($jenis, $filters);
        $statistik = $this->laporanService->getStatistik($jenis, $filters);
        $periode = $this->laporanService->getLabelPeriode($filters);

        $viewMap = [
            'peminjaman' => 'pdf.laporan_peminjaman',
            'perbaikan' => 'pdf.laporan_perbaikan',
            'dukungan' => 'pdf.laporan_dukungan',
        ];

        $judulMap = [
            'peminjaman' => 'Rekap_Peminjaman',
            'perbaikan' => 'Rekap_Perbaikan',
            'dukungan' => 'Rekap_Dukungan',
        ];

        $view = $viewMap[$jenis] ?? abort(404, 'Jenis rekap tidak valid.');
        $judul = $judulMap[$jenis] ?? 'Rekap';

        $pdf = PDF::loadView($view, [
            'data' => $data,
            'statistik' => $statistik,
            'periode' => $periode,
            'filters' => $filters,
            'dicetak_oleh' => auth()->user()?->name ?? 'Administrator',
        ])->setPaper('a4', 'landscape');

        $filename = $judul.'_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export rekap as Excel.
     */
    public function exportExcel(Request $request, string $jenis): BinaryFileResponse
    {
        $this->authorizeAccess();

        $filters = $this->extractFilters($request);
        $dicetakOleh = auth()->user()?->name ?? 'Administrator';

        $exportMap = [
            'peminjaman' => new PeminjamanExport($filters, $dicetakOleh),
            'perbaikan' => new PerbaikanExport($filters, $dicetakOleh),
            'dukungan' => new DukunganExport($filters, $dicetakOleh),
        ];

        $judulMap = [
            'peminjaman' => 'Rekap_Peminjaman',
            'perbaikan' => 'Rekap_Perbaikan',
            'dukungan' => 'Rekap_Dukungan',
        ];

        $export = $exportMap[$jenis] ?? abort(404, 'Jenis rekap tidak valid.');
        $judul = $judulMap[$jenis] ?? 'Rekap';

        $filename = $judul.'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download($exportMap[$jenis], $filename);
    }

    /**
     * View rekap in browser for printing.
     */
    public function exportPrint(Request $request, string $jenis): Response
    {
        $this->authorizeAccess();

        $filters = $this->extractFilters($request);
        $data = $this->getData($jenis, $filters);
        $statistik = $this->laporanService->getStatistik($jenis, $filters);
        $periode = $this->laporanService->getLabelPeriode($filters);

        $viewMap = [
            'peminjaman' => 'pdf.laporan_peminjaman',
            'perbaikan' => 'pdf.laporan_perbaikan',
            'dukungan' => 'pdf.laporan_dukungan',
        ];

        $view = $viewMap[$jenis] ?? abort(404, 'Jenis rekap tidak valid.');

        return response()->view($view, [
            'data' => $data,
            'statistik' => $statistik,
            'periode' => $periode,
            'filters' => $filters,
            'dicetak_oleh' => auth()->user()?->name ?? 'Administrator',
            'is_print' => true,
        ]);
    }

    /**
     * Ensure the current user has Admin access.
     */
    private function authorizeAccess(): void
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Extract filter parameters from the request.
     *
     * @return array<string, mixed>
     */
    private function extractFilters(Request $request): array
    {
        return [
            'tipe_periode' => $request->query('tipe_periode'),
            'tahun' => $request->query('tahun'),
            'bulan' => $request->query('bulan'),
            'tanggal_dari' => $request->query('tanggal_dari'),
            'tanggal_sampai' => $request->query('tanggal_sampai'),
            'status_peminjaman' => $request->query('status_peminjaman'),
            'kondisi_barang' => $request->query('kondisi_barang'),
            'kategori_id' => $request->query('kategori_id'),
            'unit_kerja_id' => $request->query('unit_kerja_id'),
            'merek_id' => $request->query('merek_id'),
            'status_perbaikan' => $request->query('status_perbaikan'),
            'teknisi_id' => $request->query('teknisi_id'),
            'status_dukungan' => $request->query('status_dukungan'),
            'pic_dukungan_id' => $request->query('pic_dukungan_id'),
        ];
    }

    /**
     * Get the data collection based on report type and filters.
     *
     * @param  array<string, mixed>  $filters
     */
    private function getData(string $jenis, array $filters): \Illuminate\Support\Collection
    {
        return match ($jenis) {
            'peminjaman' => $this->laporanService->getQueryPeminjaman($filters)->get(),
            'perbaikan' => $this->laporanService->getQueryPerbaikan($filters)->get(),
            'dukungan' => $this->laporanService->getQueryDukungan($filters)->get(),
            default => collect(),
        };
    }
}
