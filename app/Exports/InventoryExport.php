<?php

namespace App\Exports;

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

class InventoryExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithTitle
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

        return $service->getQueryInventory($this->filters)->get();
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function headings(): array
    {
        $service = new LaporanService;
        $periode = $service->getLabelPeriode($this->filters);

        return [
            ['LAPORAN INVENTORY BARANG'],
            ['Periode: '.$periode],
            ['Dicetak Oleh: '.$this->dicetakOleh],
            ['Waktu Cetak: '.Carbon::now()->locale('id')->translatedFormat('d F Y H:i').' WIB'],
            [],
            [
                'No',
                'Nama Barang',
                'Kategori',
                'Merek',
                'Serial Number',
                'Label',
                'Kondisi',
                'Tahun Perolehan',
                'Status',
                'Diinput Oleh',
                'Keterangan',
                'Tgl Input',
            ],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->nama_barang ?? '-',
            $row->kategori?->nama_kategori ?? '-',
            $row->merek?->nama_merek ?? '-',
            $row->serial_number ?? '-',
            $row->label ?? '-',
            ucfirst(str_replace('_', ' ', $row->kondisi ?? '-')),
            $row->tahun ?? '-',
            ucfirst($row->status ?? '-'),
            $row->user?->name ?? '-',
            $row->keterangan ?? '-',
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
                    $sheet->getColumnDimension('K')->setWidth(40); // Keterangan
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Inventory Barang';
    }
}
