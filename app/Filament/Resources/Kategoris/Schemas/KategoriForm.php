<?php

namespace App\Filament\Resources\Kategoris\Schemas;

use Dom\Text;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kategori')
                    ->label('Kategori')
                    ->required()
                    ->validationMessages([
                        'required' => 'Nama Kategori wajib diisi.',
                        'unique' => 'Kategori sudah terdaftar.',
                    ])
                    ->placeholder('Masukkan nama kategori'),
            ]);
    }
}
