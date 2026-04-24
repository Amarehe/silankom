<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AktivitasTerbaruTeknisi;
use App\Filament\Widgets\PerbaikanChart;
use App\Filament\Widgets\StatsOverviewTeknisi;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class TeknisiKomlekDashboard extends BaseDashboard
{
    protected static string $routePath = '/teknisi';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Teknisi Komlek';

    protected static ?int $navigationSort = -2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isTeknisiKomlek() ?? false;
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverviewTeknisi::class,
            PerbaikanChart::class,
            \App\Filament\Widgets\DukunganChart::class,
            AktivitasTerbaruTeknisi::class,
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
