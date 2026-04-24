<?php

namespace App\Filament\Widgets;

use App\Models\PerbaikanModel;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewTeknisi extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '15s';

    protected static ?int $sort = 1;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            $this->buildPerbaikanDiambilStat($userId, $startOfMonth),
            $this->buildPerbaikanSelesaiStat($userId, $startOfMonth),
            $this->buildDukunganDiambilStat($userId, $startOfMonth),
            $this->buildDukunganSelesaiStat($userId, $startOfMonth),
        ];
    }

    private function buildPerbaikanDiambilStat(int $userId, Carbon $startOfMonth): Stat
    {
        $pending = PerbaikanModel::query()
            ->where('teknisi_id', $userId)
            ->whereIn('status_perbaikan', ['diproses'])
            ->count();

        $total = PerbaikanModel::query()->where('teknisi_id', $userId)->count();

        return Stat::make('Perbaikan Diambil', number_format($pending))
            ->description("{$total} total perbaikan diambil")
            ->descriptionIcon('heroicon-m-wrench-screwdriver')
            ->color('warning');
    }

    private function buildPerbaikanSelesaiStat(int $userId, Carbon $startOfMonth): Stat
    {
        $selesai = PerbaikanModel::query()
            ->where('teknisi_id', $userId)
            ->whereIn('status_perbaikan', ['selesai', 'tidak_bisa_diperbaiki'])
            ->count();

        $bulanIni = PerbaikanModel::query()
            ->where('teknisi_id', $userId)
            ->whereIn('status_perbaikan', ['selesai', 'tidak_bisa_diperbaiki'])
            ->where('updated_at', '>=', $startOfMonth)
            ->count();

        return Stat::make('Perbaikan Selesai', number_format($selesai))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Tidak ada bulan ini')
            ->descriptionIcon('heroicon-m-check-badge')
            ->color('success');
    }

    private function buildDukunganDiambilStat(int $userId, Carbon $startOfMonth): Stat
    {
        $pending = \App\Models\ReqDukunganModel::query()
            ->where('pic_dukungan_id', $userId)
            ->whereIn('status_dukungan', ['sedang_diproses'])
            ->count();

        $total = \App\Models\ReqDukunganModel::query()->where('pic_dukungan_id', $userId)->count();

        return Stat::make('Dukungan Diambil', number_format($pending))
            ->description("{$total} total dukungan diambil")
            ->descriptionIcon('heroicon-m-ticket')
            ->color('warning');
    }

    private function buildDukunganSelesaiStat(int $userId, Carbon $startOfMonth): Stat
    {
        $selesai = \App\Models\ReqDukunganModel::query()
            ->where('pic_dukungan_id', $userId)
            ->whereIn('status_dukungan', ['didukung', 'tidak_didukung'])
            ->count();

        $bulanIni = \App\Models\ReqDukunganModel::query()
            ->where('pic_dukungan_id', $userId)
            ->whereIn('status_dukungan', ['didukung', 'tidak_didukung'])
            ->where('updated_at', '>=', $startOfMonth)
            ->count();

        return Stat::make('Dukungan Selesai', number_format($selesai))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Tidak ada bulan ini')
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('success');
    }
}
