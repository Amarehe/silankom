<?php

namespace App\Filament\Resources\Mereks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MerekForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_merek')
                    ->label('Merek')
                    ->required()
                    ->validationMessages([
                        'required' => 'Nama Merek wajib diisi.',
                        'unique' => 'Merek sudah terdaftar.',
                    ])
                    ->placeholder('Masukkan nama merek'),
            ]);
    }
}
