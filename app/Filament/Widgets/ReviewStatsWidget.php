<?php

namespace App\Filament\Widgets;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Models\ReviewLayananModel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReviewStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $totalReview = ReviewLayananModel::query()->count();
        $avgRating = ReviewLayananModel::query()->avg('rating');
        $avgKecepatan = ReviewLayananModel::query()->avg('aspek_kecepatan');
        $avgKualitas = ReviewLayananModel::query()->avg('aspek_kualitas');
        $avgPelayanan = ReviewLayananModel::query()->avg('aspek_pelayanan');

        $reviewPeminjaman = ReviewLayananModel::query()
            ->where('reviewable_type', PeminjamanModel::class)
            ->count();

        $reviewPerbaikan = ReviewLayananModel::query()
            ->where('reviewable_type', PerbaikanModel::class)
            ->count();

        $reviewDukungan = ReviewLayananModel::query()
            ->where('reviewable_type', ReqDukunganModel::class)
            ->count();

        return [
            Stat::make('Total Review', number_format($totalReview))
                ->description("Peminjaman: {$reviewPeminjaman} | Perbaikan: {$reviewPerbaikan} | Dukungan: {$reviewDukungan}")
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),

            Stat::make('Rata-rata Rating', $avgRating ? number_format($avgRating, 1).'/5 ⭐' : '-')
                ->description('Rating keseluruhan')
                ->descriptionIcon('heroicon-m-star')
                ->color($this->getRatingColor($avgRating)),

            Stat::make('Rata-rata Kecepatan', $avgKecepatan ? number_format($avgKecepatan, 1).'/5' : '-')
                ->description('Kualitas: '.($avgKualitas ? number_format($avgKualitas, 1) : '-').'/5')
                ->descriptionIcon('heroicon-m-clock')
                ->color($this->getRatingColor($avgKecepatan)),

            Stat::make('Rata-rata Pelayanan', $avgPelayanan ? number_format($avgPelayanan, 1).'/5' : '-')
                ->description('Keramahan petugas')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color($this->getRatingColor($avgPelayanan)),
        ];
    }

    private function getRatingColor(?float $rating): string
    {
        if ($rating === null) {
            return 'gray';
        }

        return match (true) {
            $rating >= 4.0 => 'success',
            $rating >= 3.0 => 'warning',
            default => 'danger',
        };
    }
}
