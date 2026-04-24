<?php

namespace App\Filament\Pages\Laporan;

use App\Models\KategoriModel;
use App\Models\MerekModel;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaporanPerbaikanPage extends BaseLaporanPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;

    protected static ?string $navigationLabel = 'Laporan Perbaikan';

    protected static ?string $title = 'Laporan Perbaikan Peralatan';

    protected static ?string $slug = 'laporan-perbaikan';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.laporan.perbaikan';

    protected function jenisLaporan(): string
    {
        return 'perbaikan';
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function auditFilters(): array
    {
        return [
            Select::make('status_perbaikan')
                ->label('Status Perbaikan')
                ->options([
                    'diajukan' => 'Diajukan',
                    'diproses' => 'Sedang Diproses',
                    'selesai' => 'Selesai',
                    'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
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

            Select::make('teknisi_id')
                ->label('Teknisi')
                ->options(User::query()->whereIn('role_id', [1, 2, 3])->pluck('name', 'id'))
                ->searchable()
                ->placeholder('Semua Teknisi')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),
        ];
    }
}
