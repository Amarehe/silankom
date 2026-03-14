<?php

namespace App\Filament\Resources\ReqPinjams\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ReqPinjamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('kategori_id')
                ->label('Kategori Barang')
                ->relationship('kategori', 'nama_kategori')
                ->required()
                ->placeholder('Pilih kategori barang yang ingin dipinjam')
                ->searchable()
                ->preload()
                ->columnSpanFull(),

            TextInput::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->maxValue(1)
                ->required()
                ->disabled() // Disabled karena user hanya boleh request 1 unit
                ->helperText('User hanya dapat mengajukan 1 unit barang per pengajuan')
                ->columnSpanFull(),

            Textarea::make('keterangan')
                ->label('Keterangan / Keperluan')
                ->placeholder('Jelaskan keperluan peminjaman barang ini')
                ->rows(3)
                ->columnSpanFull(),

            Hidden::make('user_id')
                ->default(Auth::id()),

            Hidden::make('tanggal_request')
                ->default(now()->format('Y-m-d')),

            Hidden::make('status')
                ->default('diproses'),
        ]);
    }
}
