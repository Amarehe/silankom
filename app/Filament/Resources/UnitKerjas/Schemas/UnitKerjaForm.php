<?php

namespace App\Filament\Resources\UnitKerjas\Schemas;

use Dom\Text;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitKerjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nm_unitkerja')
                    ->label('Unit Kerja')
                    ->required()
                    ->validationMessages([
                        'required' => 'Nama Unit Kerja wajib diisi.',
                        'unique' => 'Unit Kerja sudah terdaftar.',
                    ])
                    ->placeholder('Masukkan nama unit kerja'),
            ]);
    }
}
