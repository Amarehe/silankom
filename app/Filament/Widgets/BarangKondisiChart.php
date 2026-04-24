<?php

namespace App\Filament\Widgets;

use App\Models\BarangModel;
use Filament\Widgets\ChartWidget;

class BarangKondisiChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Kondisi Barang';

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $baik = BarangModel::query()->where('kondisi', 'baik')->count();
        $rusak = BarangModel::query()->where('kondisi', 'rusak')->count();
        $perluPerbaikan = BarangModel::query()->where('kondisi', 'perlu_perbaikan')->count();

        return [
            'datasets' => [
                [
                    'data' => [$baik, $rusak, $perluPerbaikan],
                    'backgroundColor' => [
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                    ],
                    'borderColor' => [
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Baik', 'Rusak', 'Perlu Perbaikan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%',
            'maintainAspectRatio' => false,
        ];
    }
}
