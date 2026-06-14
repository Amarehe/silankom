<?php

namespace App\Filament\Resources\ReqDukungans\Schemas;

use App\Models\JabatanModel;
use App\Models\UnitKerjaModel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ReqDukunganForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdminTU = Auth::user()?->isAdminTU() ?? false;

        return $schema->schema(array_filter([
            $isAdminTU ? self::dataPemohonSection() : null,

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
