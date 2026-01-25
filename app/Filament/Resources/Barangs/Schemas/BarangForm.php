<?php

namespace App\Filament\Resources\Barangs\Schemas;

use App\Models\KategoriModel;
use App\Models\MerekModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_barang')
                    ->label('Nama Barang')
                    ->required()
                    ->validationMessages([
                        'required' => 'Nama Barang wajib diisi.',
                    ])
                    ->placeholder('Contoh: Laptop Asus ROG Strix G15')
                    ->columnSpanFull(),

                Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(KategoriModel::all()->pluck('nama_kategori', 'id'))
                    ->required()
                    ->searchable()
                    ->validationMessages([
                        'required' => 'Kategori wajib dipilih.',
                    ])
                    ->placeholder('Pilih kategori'),

                Select::make('merek_id')
                    ->label('Merek')
                    ->options(MerekModel::all()->pluck('nama_merek', 'id'))
                    ->required()
                    ->searchable()
                    ->validationMessages([
                        'required' => 'Merek wajib dipilih.',
                    ])
                    ->placeholder('Pilih merek'),

                TextInput::make('serial_number')
                    ->label('Serial Number')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Serial Number sudah terdaftar.',
                    ])
                    ->placeholder('Contoh: ASU-2024-001 atau SN123456789'),

                TextInput::make('label')
                    ->label('Label')
                    ->placeholder('Contoh: INV-LP-001 atau LABEL-2024-001'),

                Select::make('kondisi')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        'perlu_perbaikan' => 'Perlu Perbaikan',
                    ])
                    ->default('baik')
                    ->required()
                    ->placeholder('Pilih kondisi'),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y') + 1)
                    ->validationMessages([
                        'numeric' => 'Tahun harus berupa angka.',
                        'min' => 'Tahun minimal 1900.',
                        'max' => 'Tahun maksimal ' . (date('Y') + 1) . '.',
                    ])
                    ->placeholder('Contoh: 2024'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'dipinjam' => 'Dipinjam',
                    ])
                    ->default('tersedia')
                    ->required()
                    ->placeholder('Pilih status'),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->placeholder('Contoh: Laptop gaming dengan spesifikasi tinggi, RAM 16GB, SSD 512GB')
                    ->columnSpanFull(),

                Hidden::make('user_id')
                    ->default(Auth::id()),
            ]);
    }
}
