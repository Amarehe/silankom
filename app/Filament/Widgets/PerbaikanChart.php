<?php

namespace App\Filament\Widgets;

use App\Models\PerbaikanModel;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PerbaikanChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Tren Perbaikan';

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 3;

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
        $diajukanData = [];
        $selesaiData = [];
        $tidakBisaData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $diajukanData[] = PerbaikanModel::query()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $selesaiData[] = PerbaikanModel::query()
                ->where('status_perbaikan', 'selesai')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->count();

            $tidakBisaData[] = PerbaikanModel::query()
                ->where('status_perbaikan', 'tidak_bisa_diperbaiki')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan Masuk',
                    'data' => $diajukanData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Selesai',
                    'data' => $selesaiData,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Tidak Bisa Diperbaiki',
                    'data' => $tidakBisaData,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
