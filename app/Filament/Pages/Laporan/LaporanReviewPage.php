<?php

namespace App\Filament\Pages\Laporan;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Models\UnitKerjaModel;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaporanReviewPage extends BaseLaporanPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Star;

    protected static ?string $navigationLabel = 'Rekap Review';

    protected static ?string $title = 'Rekap Review Layanan';

    protected static ?string $slug = 'laporan-review';

    protected static string|UnitEnum|null $navigationGroup = 'Rekap';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.laporan.review';

    protected function jenisLaporan(): string
    {
        return 'review';
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function auditFilters(): array
    {
        return [
            Select::make('reviewable_type')
                ->label('Jenis Layanan')
                ->options([
                    PeminjamanModel::class => 'Peminjaman',
                    PerbaikanModel::class => 'Perbaikan',
                    ReqDukunganModel::class => 'Dukungan',
                ])
                ->placeholder('Semua Layanan')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('rating')
                ->label('Rating')
                ->options([
                    1 => '⭐ (1)',
                    2 => '⭐⭐ (2)',
                    3 => '⭐⭐⭐ (3)',
                    4 => '⭐⭐⭐⭐ (4)',
                    5 => '⭐⭐⭐⭐⭐ (5)',
                ])
                ->placeholder('Semua Rating')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('unit_kerja_id')
                ->label('Unit Kerja (Karyawan)')
                ->options(UnitKerjaModel::query()->pluck('nm_unitkerja', 'id'))
                ->searchable()
                ->placeholder('Semua Unit Kerja')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('pic_id')
                ->label('Ditangani Oleh (PIC)')
                ->options(\App\Models\User::query()->whereIn('role_id', [1, 2, 3])->pluck('name', 'id'))
                ->searchable()
                ->placeholder('Semua PIC / Teknisi')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),
        ];
    }
}
