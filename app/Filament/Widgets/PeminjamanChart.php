<?php

namespace App\Filament\Widgets;

use App\Models\PeminjamanModel;
use App\Models\ReqPinjamModel;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PeminjamanChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Tren Peminjaman';

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 2;

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
        $pengajuanData = [];
        $disetujuiData = [];
        $dikembalikanData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $pengajuanData[] = ReqPinjamModel::query()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $disetujuiData[] = PeminjamanModel::query()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $dikembalikanData[] = PeminjamanModel::query()
                ->whereNotNull('tanggal_kembali')
                ->whereYear('tanggal_kembali', $date->year)
                ->whereMonth('tanggal_kembali', $date->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan Masuk',
                    'data' => $pengajuanData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Disetujui',
                    'data' => $disetujuiData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Dikembalikan',
                    'data' => $dikembalikanData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
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
