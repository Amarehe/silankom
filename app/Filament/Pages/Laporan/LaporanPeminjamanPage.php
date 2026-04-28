<?php

namespace App\Filament\Pages\Laporan;

use App\Models\KategoriModel;
use App\Models\UnitKerjaModel;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaporanPeminjamanPage extends BaseLaporanPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $navigationLabel = 'Rekap Peminjaman';

    protected static ?string $title = 'Rekap Peminjaman & Pengembalian';

    protected static ?string $slug = 'laporan-peminjaman';

    protected static string|UnitEnum|null $navigationGroup = 'Rekap';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.laporan.peminjaman';

    protected function jenisLaporan(): string
    {
        return 'peminjaman';
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function auditFilters(): array
    {
        return [
            Select::make('status_peminjaman')
                ->label('Status Peminjaman')
                ->options([
                    'dipinjam' => 'Sedang Dipinjam',
                    'dikembalikan' => 'Sudah Dikembalikan',
                ])
                ->placeholder('Semua Status')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('kondisi_barang')
                ->label('Kondisi Barang')
                ->options([
                    'baik' => 'Baik',
                    'rusak ringan' => 'Rusak Ringan',
                    'rusak berat' => 'Rusak Berat',
                ])
                ->placeholder('Semua Kondisi')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('kategori_id')
                ->label('Kategori Barang')
                ->options(KategoriModel::query()->pluck('nama_kategori', 'id'))
                ->searchable()
                ->placeholder('Semua Kategori')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('unit_kerja_id')
                ->label('Unit Kerja')
                ->options(UnitKerjaModel::query()->pluck('nm_unitkerja', 'id'))
                ->searchable()
                ->placeholder('Semua Unit Kerja')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),
        ];
    }
}
