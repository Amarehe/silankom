<?php

namespace App\Filament\Resources\KelolaPerbaikans\Pages;

use App\Filament\Resources\KelolaPerbaikans\KelolaPerbaikanResource;
use Filament\Resources\Pages\ListRecords;

class ListKelolaPerbaikans extends ListRecords
{
    protected static string $resource = KelolaPerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
