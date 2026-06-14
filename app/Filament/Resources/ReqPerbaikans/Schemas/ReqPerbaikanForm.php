<?php

namespace App\Filament\Resources\ReqPerbaikans\Schemas;

use App\Models\JabatanModel;
use App\Models\UnitKerjaModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ReqPerbaikanForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdminTU = Auth::user()?->isAdminTU() ?? false;

        return $schema->schema(array_filter([
            $isAdminTU ? self::dataPemohonSection() : null,

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
                ->placeholder('Masukkan nama barang')
                ->columnSpanFull(),

            TextInput::make('tgl_pengajuan_display')
                ->label('Tanggal Pengajuan')
                ->default(fn () => now()->translatedFormat('l, d F Y'))
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),

            Hidden::make('tgl_pengajuan')
                ->default(fn () => now()->toDateString()),

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
                ->placeholder('Masukkan nomor nota dinas (jika ada)')
                ->helperText('Opsional. Pengisian nomor Nota Dinas akan membantu percepatan prioritas penanganan pengajuan.')
                ->columnSpanFull(),

            Hidden::make('pemohon_id')
                ->default(fn () => Auth::id()),

            Hidden::make('status_perbaikan')
                ->default('diajukan'),

            $isAdminTU ? Hidden::make('submitted_by')->default(Auth::id()) : null,
        ]));
    }

    private static function dataPemohonSection(): Section
    {
        return Section::make('Data Pemohon')
            ->description('Isi data pemohon yang mengajukan permintaan ini')
            ->icon('heroicon-o-user')
            ->schema([
                TextInput::make('nama_pemohon')
                    ->label('Nama Pemohon')
                    ->required()
                    ->placeholder('Masukkan nama lengkap pemohon'),

                TextInput::make('nip_pemohon')
                    ->label('NIP Pemohon')
                    ->required()
                    ->placeholder('Masukkan NIP pemohon'),

                Select::make('jabatan_pemohon')
                    ->label('Jabatan Pemohon')
                    ->options(JabatanModel::query()->pluck('nm_jabatan', 'nm_jabatan'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih jabatan pemohon'),

                Select::make('unitkerja_pemohon')
                    ->label('Unit Kerja Pemohon')
                    ->options(UnitKerjaModel::query()->pluck('nm_unitkerja', 'nm_unitkerja'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih unit kerja pemohon'),
            ])
            ->columns(['default' => 1, 'md' => 2])
            ->collapsible()
            ->columnSpanFull();
    }
}
