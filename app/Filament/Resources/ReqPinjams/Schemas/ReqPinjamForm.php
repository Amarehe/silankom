<?php

namespace App\Filament\Resources\ReqPinjams\Schemas;

use App\Models\BarangModel;
use App\Models\JabatanModel;
use App\Models\KategoriModel;
use App\Models\UnitKerjaModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ReqPinjamForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdminTU = Auth::user()?->isAdminTU() ?? false;

        return $schema->schema(array_filter([
            $isAdminTU ? self::dataPemohonSection() : null,

            Select::make('kategori_id')
                ->label('Kategori Barang')
                ->options(function (): array {
                    return KategoriModel::query()
                        ->get()
                        ->mapWithKeys(function (KategoriModel $kategori): array {
                            $tersedia = BarangModel::query()
                                ->where('kategori_id', $kategori->id)
                                ->where('status', 'tersedia')
                                ->count();

                            return [$kategori->id => "{$kategori->nama_kategori} ({$tersedia} tersedia)"];
                        })
                        ->toArray();
                })
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
