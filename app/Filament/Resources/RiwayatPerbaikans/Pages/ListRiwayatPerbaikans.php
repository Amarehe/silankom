<?php

namespace App\Filament\Resources\RiwayatPerbaikans\Pages;

use App\Filament\Resources\RiwayatPerbaikans\RiwayatPerbaikanResource;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPerbaikans extends ListRecords
{
    protected static string $resource = RiwayatPerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
