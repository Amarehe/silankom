<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AktivitasTerbaru;
use App\Filament\Widgets\BarangKondisiChart;
use App\Filament\Widgets\DukunganChart;
use App\Filament\Widgets\PeminjamanChart;
use App\Filament\Widgets\PerbaikanChart;
use App\Filament\Widgets\StatsOverviewAdminKomlek;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class AdminKomlekDashboard extends BaseDashboard
{
    protected static string $routePath = '/admin-komlek';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Admin Komlek';

    protected static ?int $navigationSort = -2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdminKomlek() ?? false;
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverviewAdminKomlek::class,
            PeminjamanChart::class,
            PerbaikanChart::class,
            DukunganChart::class,
            BarangKondisiChart::class,
            AktivitasTerbaru::class,
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
