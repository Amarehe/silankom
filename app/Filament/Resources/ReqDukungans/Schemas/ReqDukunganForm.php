<?php

namespace App\Filament\Resources\ReqDukungans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ReqDukunganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('nomor_nodis')
                ->label('Nomor Nodis')
                ->required()
                ->placeholder('Masukkan nomor nodis')
                ->columnSpanFull(),

            TextInput::make('nama_kegiatan')
                ->label('Nama Kegiatan')
                ->required()
                ->placeholder('Nama kegiatan yang akan dilaksanakan')
                ->columnSpanFull(),

            Textarea::make('deskripsi_kegiatan')
                ->label('Deskripsi Kegiatan')
                ->required()
                ->placeholder('Deskripsi lengkap kegiatan')
                ->rows(4)
                ->columnSpanFull(),

            TextInput::make('ruangan')
                ->label('Ruangan / Lokasi')
                ->required()
                ->placeholder('Lokasi kegiatan')
                ->columnSpanFull(),

            DatePicker::make('tgl_kegiatan')
                ->label('Tanggal Kegiatan')
                ->required()
                ->native(false)
                ->displayFormat('l, d F Y')
                ->columnSpanFull(),

            TextInput::make('waktu')
                ->label('Waktu Kegiatan')
                ->required()
                ->placeholder('Contoh: 08:00 - 12:00')
                ->columnSpanFull(),

            Repeater::make('req_barang')
                ->label('Barang yang Diminta')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Barang')
                        ->required(),
                    TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1),
                ])
                ->columns(2)
                ->minItems(1)
                ->defaultItems(1)
                ->addActionLabel('Tambah Barang')
                ->columnSpanFull(),

            Textarea::make('keterangan')
                ->label('Keterangan Tambahan')
                ->placeholder('Keterangan tambahan (opsional)')
                ->rows(3)
                ->columnSpanFull(),

            Hidden::make('pemohon_id')
                ->default(Auth::id()),

            Hidden::make('status_dukungan')
                ->default('belum_didukung'),
        ]);
    }
}
