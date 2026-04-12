<?php

namespace App\Filament\Resources\KelolaPerbaikans\Pages;

use App\Filament\Resources\KelolaPerbaikans\KelolaPerbaikanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKelolaPerbaikan extends EditRecord
{
    protected static string $resource = KelolaPerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
