<?php

namespace App\Filament\Resources\PengajuanPerbaikans\Pages;

use App\Filament\Resources\PengajuanPerbaikans\PengajuanPerbaikanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanPerbaikan extends EditRecord
{
    protected static string $resource = PengajuanPerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
