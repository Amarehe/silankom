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

class PerbaikanExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithTitle
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

        return $service->getQueryPerbaikan($this->filters)->get();
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function headings(): array
    {
        $service = new LaporanService;
        $periode = $service->getLabelPeriode($this->filters);

        return [
            ['LAPORAN PERBAIKAN PERALATAN ELEKTRONIK'],
            ['Periode: '.$periode],
            ['Dicetak Oleh: '.$this->dicetakOleh],
            ['Waktu Cetak: '.Carbon::now()->locale('id')->translatedFormat('d F Y H:i').' WIB'],
            [],
            [
                'No',
                'No Surat Perbaikan',
                'No Nota Dinas',
                'Nama Pemohon',
                'NIP',
                'Unit Kerja',
                'Jabatan',
                'Nama Barang',
                'Kategori',
                'Merek',
                'Serial Number',
                'Jumlah',
                'Tgl Pengajuan',
                'Keluhan',
                'Status',
                'Teknisi',
                'Keterangan',
                'Catatan Barang',
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
            $row->no_surat_perbaikan ?? '-',
            $row->nodis ?? '-',
            $row->pemohon?->name ?? '-',
            $row->pemohon?->nip ?? '-',
            $row->pemohon?->unitkerja?->nm_unitkerja ?? '-',
            $row->pemohon?->jabatan?->nm_jabatan ?? '-',
            $row->nm_barang ?? '-',
            $row->kategori?->nama_kategori ?? '-',
            $row->merek?->nama_merek ?? '-',
            $row->serial_number ?? '-',
            $row->jumlah ?? 1,
            $row->tgl_pengajuan ? Carbon::parse($row->tgl_pengajuan)->translatedFormat('d F Y') : '-',
            $row->keluhan ?? '-',
            ucfirst(str_replace('_', ' ', $row->status_perbaikan ?? '-')),
            $row->teknisi?->name ?? '-',
            $row->keterangan ?? '-',
            $row->catatan_barang ?? '-',
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

                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2:A4')->getFont()->setBold(true)->setSize(11);

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

                $sheet->freezePane('A7');

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

                    $sheet->getColumnDimension('N')->setWidth(40); // Keluhan
                    $sheet->getColumnDimension('Q')->setWidth(40); // Keterangan
                    $sheet->getColumnDimension('R')->setWidth(40); // Catatan Barang
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Perbaikan';
    }
}
