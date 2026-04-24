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

class DukunganExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithTitle
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

        return $service->getQueryDukungan($this->filters)->get();
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function headings(): array
    {
        $service = new LaporanService;
        $periode = $service->getLabelPeriode($this->filters);

        return [
            ['LAPORAN DUKUNGAN KEGIATAN'],
            ['Periode: '.$periode],
            ['Dicetak Oleh: '.$this->dicetakOleh],
            ['Waktu Cetak: '.Carbon::now()->locale('id')->translatedFormat('d F Y H:i').' WIB'],
            [],
            [
                'No',
                'Nomor Nota Dinas',
                'Nama Kegiatan',
                'Deskripsi Kegiatan',
                'Nama Pemohon',
                'NIP',
                'Unit Kerja',
                'Ruangan',
                'Tgl Kegiatan',
                'Waktu',
                'Barang Diminta',
                'Status',
                'PIC Dukungan',
                'Tgl Disetujui',
                'Barang Diberikan',
                'Keterangan',
            ],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        $this->rowNumber++;

        $reqBarang = is_array($row->req_barang) ? collect($row->req_barang)->map(fn ($item) => ($item['nama'] ?? $item['barang'] ?? '-').' ('.$item['jumlah'].')'
        )->implode(', ') : '-';

        $barangDiberikan = is_array($row->barang_diberikan) ? collect($row->barang_diberikan)->map(fn ($item) => ($item['nama'] ?? $item['barang'] ?? '-').' ('.$item['jumlah'].')'
        )->implode(', ') : '-';

        return [
            $this->rowNumber,
            $row->nomor_nodis ?? '-',
            $row->nama_kegiatan ?? '-',
            $row->deskripsi_kegiatan ?? '-',
            $row->pemohon?->name ?? '-',
            $row->pemohon?->nip ?? '-',
            $row->pemohon?->unitkerja?->nm_unitkerja ?? '-',
            $row->ruangan ?? '-',
            $row->tgl_kegiatan ? Carbon::parse($row->tgl_kegiatan)->translatedFormat('d F Y') : '-',
            $row->waktu ?? '-',
            $reqBarang,
            ucfirst(str_replace('_', ' ', $row->status_dukungan ?? '-')),
            $row->picDukungan?->name ?? '-',
            $row->tgl_disetujui ? Carbon::parse($row->tgl_disetujui)->translatedFormat('d F Y') : '-',
            $barangDiberikan,
            $row->keterangan ?? '-',
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

                    $sheet->getColumnDimension('C')->setWidth(30); // Nama Kegiatan
                    $sheet->getColumnDimension('D')->setWidth(40); // Deskripsi Kegiatan
                    $sheet->getColumnDimension('K')->setWidth(40); // Barang Diminta
                    $sheet->getColumnDimension('O')->setWidth(40); // Barang Diberikan
                    $sheet->getColumnDimension('P')->setWidth(40); // Keterangan
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Dukungan Kegiatan';
    }
}
