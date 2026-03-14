<?php

namespace App\Filament\Resources\PeminjamanAdmins\Pages;

use App\Filament\Resources\PeminjamanAdmins\PeminjamanAdminResource;
use Filament\Resources\Pages\ListRecords;

class ListPeminjamans extends ListRecords
{
    protected static string $resource = PeminjamanAdminResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Tidak ada action create
    }
}
