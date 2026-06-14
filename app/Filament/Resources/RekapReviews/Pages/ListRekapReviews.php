<?php

namespace App\Filament\Resources\RekapReviews\Pages;

use App\Filament\Resources\RekapReviews\RekapReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListRekapReviews extends ListRecords
{
    protected static string $resource = RekapReviewResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ReviewStatsWidget::class,
        ];
    }
}
