<?php

namespace App\Filament\Widgets;

use App\Models\ReqDukunganModel;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class DukunganChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Tren Dukungan';

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public ?string $filter = '6';

    protected function getFilters(): ?array
    {
        return [
            '6' => '6 Bulan Terakhir',
            '12' => '1 Tahun Terakhir',
        ];
    }

    protected function getData(): array
    {
        $months = (int) $this->filter;
        $labels = [];
        $masukData = [];
        $didukungData = [];
        $ditolakData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $masukData[] = ReqDukunganModel::query()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $didukungData[] = ReqDukunganModel::query()
                ->where('status_dukungan', 'didukung')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->count();

            $ditolakData[] = ReqDukunganModel::query()
                ->where('status_dukungan', 'tidak_didukung')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan Masuk',
                    'data' => $masukData,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.5)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Didukung',
                    'data' => $didukungData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Ditolak',
                    'data' => $ditolakData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
