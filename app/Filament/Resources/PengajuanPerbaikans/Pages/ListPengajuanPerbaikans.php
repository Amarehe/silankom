<?php

namespace App\Filament\Resources\PengajuanPerbaikans\Pages;

use App\Filament\Resources\PengajuanPerbaikans\PengajuanPerbaikanResource;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanPerbaikans extends ListRecords
{
    protected static string $resource = PengajuanPerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
