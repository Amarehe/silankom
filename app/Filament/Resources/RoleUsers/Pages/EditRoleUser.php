<?php

namespace App\Filament\Resources\RoleUsers\Pages;

use App\Filament\Resources\RoleUsers\RoleUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRoleUser extends EditRecord
{
    protected static string $resource = RoleUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
