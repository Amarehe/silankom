<?php

namespace App\Filament\Resources\RoleUsers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nm_role')
                    ->label('Role Name')
                    ->required()
                    ->validationMessages([
                        'required' => 'Role User wajib diisi.',
                        'unique' => 'Role User sudah terdaftar.',
                    ])
                    ->placeholder('Masukkan nama role user'),
            ]);
    }
}
