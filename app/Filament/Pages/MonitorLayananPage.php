<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ReqPinjamModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use UnitEnum;
use BackedEnum;
use Filament\Support\Enums\Width;

class MonitorLayananPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Monitor Layanan';

    protected static ?string $title = 'Live Monitor Layanan';

    protected static string|UnitEnum|null $navigationGroup = 'Rekap & Monitor';

    protected static ?int $navigationSort = 99;

    protected static ?string $slug = 'monitor-layanan';

    protected string $view = 'filament.pages.monitor-layanan-page';

    protected Width|string|null $maxContentWidth = 'full';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isTeknisiKomlek();
    }

    protected function getViewData(): array
    {
        return [
            'peminjamanBaru' => ReqPinjamModel::with(['user', 'kategori'])
                ->where('status', 'diproses')
                ->latest('created_at')
                ->get(),

            'perbaikanBaru' => PerbaikanModel::with(['pemohon', 'kategori'])
                ->whereIn('status_perbaikan', ['diajukan', 'diproses'])
                ->latest('created_at')
                ->get(),

            'dukunganBaru' => ReqDukunganModel::with(['pemohon'])
                ->where('status_dukungan', 'belum_didukung')
                ->latest('created_at')
                ->get(),
        ];
    }
}
