<?php

namespace App\Filament\Resources\Jabatans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JabatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nm_jabatan')
                    ->label('Jabatan')
                    ->required()
                    ->validationMessages([
                        'required' => 'Nama Jabatan wajib diisi.',
                        'unique' => 'Jabatan sudah terdaftar.',
                    ])
                    ->placeholder('Masukkan nama jabatan'),
            ]);
    }
}
