<?php

namespace App\Filament\Resources\PengajuanPerbaikans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PengajuanPerbaikanEditForm
{
    /**
     * @return array<int, mixed>
     */
    public static function getFormSchema(): array
    {
        return [
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

            TextInput::make('nm_barang')
                ->label('Nama Barang')
                ->required()
                ->columnSpanFull(),

            DatePicker::make('tgl_pengajuan')
                ->label('Tanggal Pengajuan')
                ->required()
                ->native(false)
                ->columnSpanFull(),

            Textarea::make('keluhan')
                ->label('Keluhan / Deskripsi Masalah')
                ->required()
                ->rows(3)
                ->columnSpanFull(),

            TextInput::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->required()
                ->minValue(1)
                ->columnSpanFull(),

            TextInput::make('nodis')
                ->label('Nomor Nota Dinas')
                ->placeholder('Masukkan nomor nota dinas (opsional)')
                ->columnSpanFull(),

            TextInput::make('serial_number')
                ->label('Serial Number')
                ->placeholder('Serial number barang')
                ->columnSpanFull(),

            Textarea::make('keterangan')
                ->label('Keterangan / Hasil Perbaikan')
                ->rows(3)
                ->columnSpanFull(),

            TextInput::make('no_surat_perbaikan')
                ->label('No. Surat Perbaikan')
                ->placeholder('Nomor surat perbaikan')
                ->columnSpanFull(),

            Select::make('status_perbaikan')
                ->label('Status Perbaikan')
                ->options([
                    'diajukan' => 'Diajukan',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                    'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                ])
                ->required()
                ->columnSpanFull(),

            Textarea::make('catatan_barang')
                ->label('Info Pengambilan Barang')
                ->placeholder('Instruksi pengambilan barang')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->schema(self::getFormSchema());
    }
}
