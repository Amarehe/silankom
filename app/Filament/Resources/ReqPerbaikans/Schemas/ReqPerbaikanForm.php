<?php

namespace App\Filament\Resources\ReqPerbaikans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ReqPerbaikanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('kategori_id')
                ->label('Kategori Barang')
                ->relationship('kategori', 'nama_kategori')
                ->required()
                ->searchable()
                ->preload()
                ->columnSpanFull(),

            Select::make('merek_id')
                ->label('Merek')
                ->relationship('merek', 'nama_merek')
                ->required()
                ->searchable()
                ->preload()
                ->columnSpanFull(),

            TextInput::make('nama_barang')
                ->label('Nama Barang')
                ->required()
                ->placeholder('Masukkan nama barang')
                ->columnSpanFull(),

            DatePicker::make('tgl_pengajuan')
                ->label('Tanggal Pengajuan')
                ->required()
                ->native(false)
                ->default(now())
                ->columnSpanFull(),

            Textarea::make('keluhan')
                ->label('Keluhan / Deskripsi Masalah')
                ->required()
                ->placeholder('Jelaskan permasalahan barang yang diajukan')
                ->rows(3)
                ->columnSpanFull(),

            TextInput::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->required()
                ->minValue(1)
                ->default(1)
                ->columnSpanFull(),

            TextInput::make('nodis')
                ->label('Nomor Nota Dinas')
                ->required()
                ->placeholder('Masukkan nomor nota dinas')
                ->columnSpanFull(),

            Hidden::make('user_id')
                ->default(Auth::id()),

            Hidden::make('status_perbaikan')
                ->default('pending'),
        ]);
    }
}
