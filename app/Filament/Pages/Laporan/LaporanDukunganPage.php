<?php

namespace App\Filament\Pages\Laporan;

use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaporanDukunganPage extends BaseLaporanPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::HandRaised;

    protected static ?string $navigationLabel = 'Rekap Dukungan';

    protected static ?string $title = 'Rekap Dukungan Kegiatan';

    protected static ?string $slug = 'laporan-dukungan';

    protected static string|UnitEnum|null $navigationGroup = 'Rekap';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.laporan.dukungan';

    protected function jenisLaporan(): string
    {
        return 'dukungan';
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function auditFilters(): array
    {
        return [
            Select::make('status_dukungan')
                ->label('Status Dukungan')
                ->options([
                    'belum_didukung' => 'Belum Didukung',
                    'didukung' => 'Didukung',
                    'tidak_didukung' => 'Tidak Didukung',
                ])
                ->placeholder('Semua Status')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('pic_dukungan_id')
                ->label('PIC Dukungan')
                ->options(User::query()->whereIn('role_id', [1, 2, 3])->pluck('name', 'id'))
                ->searchable()
                ->placeholder('Semua PIC')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),
        ];
    }
}
