<?php

namespace App\Filament\Pages\Laporan;

use App\Models\KategoriModel;
use App\Models\MerekModel;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaporanInventoryPage extends BaseLaporanPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cube;

    protected static ?string $navigationLabel = 'Rekap Inventory';

    protected static ?string $title = 'Rekap Inventory Barang';

    protected static ?string $slug = 'laporan-inventory';

    protected static string|UnitEnum|null $navigationGroup = 'Rekap';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.laporan.inventory';

    protected function jenisLaporan(): string
    {
        return 'inventory';
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function auditFilters(): array
    {
        return [
            Select::make('kondisi')
                ->label('Kondisi')
                ->options([
                    'baik' => 'Baik',
                    'rusak' => 'Rusak',
                    'perlu_perbaikan' => 'Perlu Perbaikan',
                ])
                ->placeholder('Semua Kondisi')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('status_barang')
                ->label('Status')
                ->options([
                    'tersedia' => 'Tersedia',
                    'dipinjam' => 'Dipinjam',
                ])
                ->placeholder('Semua Status')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('kategori_id')
                ->label('Kategori Barang')
                ->options(KategoriModel::query()->pluck('nama_kategori', 'id'))
                ->searchable()
                ->placeholder('Semua Kategori')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('merek_id')
                ->label('Merek')
                ->options(MerekModel::query()->pluck('nama_merek', 'id'))
                ->searchable()
                ->placeholder('Semua Merek')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('tahun_perolehan')
                ->label('Tahun Perolehan')
                ->options(function () {
                    $years = [];
                    $currentYear = now()->year;
                    for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
                        $years[$y] = (string) $y;
                    }

                    return $years;
                })
                ->placeholder('Semua Tahun')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),
        ];
    }
}
