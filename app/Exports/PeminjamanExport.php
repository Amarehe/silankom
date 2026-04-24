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

class PeminjamanExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithTitle
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

        return $service->getQueryPeminjaman($this->filters)->get();
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function headings(): array
    {
        $service = new LaporanService;
        $periode = $service->getLabelPeriode($this->filters);

        return [
            ['LAPORAN PEMINJAMAN BARANG'],
            ['Periode: '.$periode],
            ['Dicetak Oleh: '.$this->dicetakOleh],
            ['Waktu Cetak: '.Carbon::now()->locale('id')->translatedFormat('d F Y H:i').' WIB'],
            [],
            [
                'No',
                'Nomor Surat Peminjaman',
                'Nomor Surat Pengembalian',
                'Nama Peminjam',
                'NIP',
                'Unit Kerja',
                'Jabatan',
                'Nama Barang',
                'Kategori',
                'Merek',
                'Tgl Pinjam',
                'Kondisi Pinjam',
                'Tgl Kembali',
                'Kondisi Kembali',
                'Status',
                'Catatan Admin',
                'Catatan Pengembalian',
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
            $row->nomor_surat ?? '-',
            $row->nomor_surat_pengembalian ?? '-',
            $row->reqPinjam?->user?->name ?? '-',
            $row->reqPinjam?->user?->nip ?? '-',
            $row->reqPinjam?->user?->unitkerja?->nm_unitkerja ?? '-',
            $row->reqPinjam?->user?->jabatan?->nm_jabatan ?? '-',
            $row->barang?->nama_barang ?? '-',
            $row->barang?->kategori?->nama_kategori ?? '-',
            $row->barang?->merek?->nama_merek ?? '-',
            $row->tanggal_serah_terima ? Carbon::parse($row->tanggal_serah_terima)->translatedFormat('d F Y') : '-',
            ucfirst($row->kondisi_barang ?? '-'),
            $row->tanggal_kembali ? Carbon::parse($row->tanggal_kembali)->translatedFormat('d F Y') : '-',
            ucfirst($row->kondisi_kembali ?? '-'),
            ucfirst(str_replace('_', ' ', $row->status_peminjaman ?? '-')),
            $row->catatan_admin ?? '-',
            $row->catatan_pengembalian ?? '-',
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
                    $sheet->getColumnDimension('P')->setWidth(40); // Catatan Admin
                    $sheet->getColumnDimension('Q')->setWidth(40); // Catatan Pengembalian
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Peminjaman';
    }
}
