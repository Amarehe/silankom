<?php

namespace App\Exports;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Services\LaporanService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReviewExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithTitle
{
    private int $rowNumber = 0;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        private array $filters = [],
        private string $dicetakOleh = 'Administrator'
    ) {}

    public function collection(): Collection
    {
        $service = new LaporanService;

        return $service->getQueryReview($this->filters)->get();
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function headings(): array
    {
        $service = new LaporanService;
        $periode = $service->getLabelPeriode($this->filters);

        return [
            ['LAPORAN REVIEW LAYANAN'],
            ['Periode: '.$periode],
            ['Dicetak Oleh: '.$this->dicetakOleh],
            ['Waktu Cetak: '.Carbon::now()->locale('id')->translatedFormat('d F Y H:i').' WIB'],
            [],
            [
                'No',
                'Nama Karyawan',
                'NIP',
                'Unit Kerja',
                'Jabatan',
                'Jenis Layanan',
                'Ditangani Oleh',
                'Rating Keseluruhan',
                'Kecepatan',
                'Kualitas',
                'Pelayanan',
                'Rata-rata',
                'Komentar',
                'Tanggal Review',
            ],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        $this->rowNumber++;

        $jenisLayanan = match ($row->reviewable_type) {
            PeminjamanModel::class => 'Peminjaman',
            PerbaikanModel::class => 'Perbaikan',
            ReqDukunganModel::class => 'Dukungan',
            default => '-',
        };

        $rataRata = round(($row->rating + $row->aspek_kecepatan + $row->aspek_kualitas + $row->aspek_pelayanan) / 4, 1);

        $picName = match ($row->reviewable_type) {
            PeminjamanModel::class => $row->reviewable?->admin?->name ?? '-',
            PerbaikanModel::class => $row->reviewable?->teknisi?->name ?? '-',
            ReqDukunganModel::class => $row->reviewable?->picDukungan?->name ?? '-',
            default => '-',
        };

        return [
            $this->rowNumber,
            $row->user?->name ?? '-',
            $row->user?->nip ?? '-',
            $row->user?->unitkerja?->nm_unitkerja ?? '-',
            $row->user?->jabatan?->nm_jabatan ?? '-',
            $jenisLayanan,
            $picName,
            $row->rating.'/5',
            $row->aspek_kecepatan.'/5',
            $row->aspek_kualitas.'/5',
            $row->aspek_pelayanan.'/5',
            $rataRata.'/5',
            $row->komentar ?? '-',
            $row->created_at ? Carbon::parse($row->created_at)->translatedFormat('d F Y') : '-',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = $sheet->getHighestColumn();

                // Merge title and metadata cells
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');

                // Style title
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2:A4')->getFont()->setBold(true)->setSize(11);

                // Style header row (Row 6)
                $sheet->getStyle("A6:{$lastCol}6")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E79'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Freeze panes so header is always visible
                $sheet->freezePane('A7');

                // Apply borders and alignment to table data
                if ($lastRow >= 6) {
                    $sheet->getStyle("A6:{$lastCol}{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ]);

                    // Set specific widths for columns that might have long text
                    $sheet->getColumnDimension('L')->setWidth(40); // Komentar
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Review Layanan';
    }
}
