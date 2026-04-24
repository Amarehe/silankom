<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverviewKaryawan;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class KaryawanDashboard extends BaseDashboard
{
    protected static string $routePath = '/karyawan';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Karyawan';

    protected static ?int $navigationSort = -2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isKaryawan() ?? false;
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverviewKaryawan::class,
            \App\Filament\Widgets\AktivitasTerbaruKaryawan::class,
        ];
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getColumns(): int|array
    {
        return 2;
    }
}
