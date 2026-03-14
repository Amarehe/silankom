<?php

namespace App\Filament\Resources\RiwayatPeminjamans\Pages;

use App\Filament\Resources\RiwayatPeminjamans\RiwayatPeminjamanResource;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPeminjamans extends ListRecords
{
    protected static string $resource = RiwayatPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Tidak ada action create
    }
}
