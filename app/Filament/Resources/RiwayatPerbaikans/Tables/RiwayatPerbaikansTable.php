<?php

namespace App\Filament\Resources\RiwayatPerbaikans\Tables;

use App\Models\PerbaikanModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RiwayatPerbaikansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                TextColumn::make('nodis')
                    ->label('No. Nota Dinas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->weight('bold')
                    ->color('primary')
                    ->placeholder('-')
                    ->icon('heroicon-o-document-text')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tgl. Pengajuan')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('d M Y'))
                    ->sortable(),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('nm_barang')
                    ->name('nm_barang')
                    ->label('Nama Barang')
                    ->searchable(['nm_barang', 'serial_number'])
                    ->sortable()
                    ->description(fn (PerbaikanModel $record): string => 'SN: '.($record->serial_number ?? '-')),

                BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'selesai' => 'success',
                        'tidak_bisa_diperbaiki' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'selesai' => 'Selesai',
                        'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'selesai' => 'heroicon-o-check-circle',
                        'tidak_bisa_diperbaiki' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('catatan_barang')
                    ->label('Info Pengambilan')
                    ->limit(45)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('no_surat_perbaikan')
                    ->label('No. Surat')
                    ->placeholder('-')
                    ->copyable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['kategori', 'merek', 'teknisi']))
            ->defaultSort('created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Riwayat Perbaikan')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Rincian Barang')
                                ->icon('heroicon-o-computer-desktop')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('kategori.nama_kategori')->label('Kategori'),
                                        TextEntry::make('merek.nama_merek')->label('Merek'),
                                        TextEntry::make('nm_barang')->label('Nama Barang'),
                                        TextEntry::make('serial_number')->label('Serial Number')->placeholder('-'),
                                        TextEntry::make('jumlah')->label('Jumlah Unit')->suffix(' unit'),
                                        TextEntry::make('tgl_pengajuan')->label('Tgl. Pengajuan')->date('l, d F Y'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Ringkasan Pengajuan')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('pemohon.name')
                                            ->label('Nama Pemohon')
                                            ->icon('heroicon-m-user'),
                                        TextEntry::make('pemohon.jabatan.nm_jabatan')
                                            ->label('Jabatan')
                                            ->icon('heroicon-m-briefcase')
                                            ->placeholder('-'),
                                        TextEntry::make('nodis')
                                            ->label('No. Nota Dinas')
                                            ->placeholder('Tidak ada')
                                            ->color('primary')
                                            ->weight('bold')
                                            ->copyable()
                                            ->icon('heroicon-m-document-text'),
                                        TextEntry::make('tgl_pengajuan')
                                            ->label('Tanggal Pengajuan')
                                            ->formatStateUsing(fn ($state) => $state?->translatedFormat('l, d F Y'))
                                            ->icon('heroicon-m-calendar-days'),
                                        TextEntry::make('status_perbaikan')
                                            ->label('Status Perbaikan')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'selesai' => 'success',
                                                'tidak_bisa_diperbaiki' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'selesai' => '✅ Selesai',
                                                'tidak_bisa_diperbaiki' => '❌ Tidak Bisa Diperbaiki',
                                                default => $state,
                                            })
                                            ->icon(fn (string $state): string => match ($state) {
                                                'selesai' => 'heroicon-o-check-circle',
                                                'tidak_bisa_diperbaiki' => 'heroicon-o-x-circle',
                                                default => 'heroicon-o-clock',
                                            }),
                                        TextEntry::make('no_surat_perbaikan')
                                            ->label('No. Surat Perbaikan')
                                            ->placeholder('Belum diterbitkan')
                                            ->color('primary')
                                            ->weight('bold')
                                            ->copyable()
                                            ->icon('heroicon-m-document-check'),
                                    ]),
                                ]),

                            Section::make('Data Barang')
                                ->icon('heroicon-o-computer-desktop')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('kategori.nama_kategori')
                                            ->label('Kategori')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('merek.nama_merek')
                                            ->label('Merek')
                                            ->icon('heroicon-m-tag'),
                                        TextEntry::make('nm_barang')
                                            ->label('Nama Barang')
                                            ->weight('bold')
                                            ->icon('heroicon-m-computer-desktop'),
                                        TextEntry::make('serial_number')
                                            ->label('Serial Number')
                                            ->placeholder('-')
                                            ->copyable()
                                            ->icon('heroicon-m-hashtag'),
                                        TextEntry::make('jumlah')
                                            ->label('Jumlah')
                                            ->suffix(' Unit')
                                            ->icon('heroicon-m-cube'),
                                        TextEntry::make('keluhan')
                                            ->label('Keluhan / Kerusakan')
                                            ->prose()
                                            ->columnSpanFull(),
                                    ]),
                                ])->collapsible(),

                            Section::make('Hasil Perbaikan')
                                ->icon('heroicon-o-wrench-screwdriver')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('teknisi.name')
                                            ->label('Teknisi Penanggung Jawab')
                                            ->placeholder('Belum ditentukan')
                                            ->icon('heroicon-m-user-circle'),
                                        TextEntry::make('updated_at')
                                            ->label('Tanggal Diselesaikan')
                                            ->formatStateUsing(fn ($state) => $state?->translatedFormat('l, d F Y'))
                                            ->icon('heroicon-m-check-badge'),
                                        TextEntry::make('keterangan')
                                            ->label('Tindakan / Hasil Perbaikan')
                                            ->prose()
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ]),
                                ])->collapsible(),

                            Section::make('Info Pengambilan Barang')
                                ->icon('heroicon-o-archive-box-arrow-down')
                                ->description('Silakan datang sesuai instruksi berikut untuk mengambil barang Anda.')
                                ->schema([
                                    TextEntry::make('catatan_barang')
                                        ->label('Instruksi dari Teknisi')
                                        ->prose()
                                        ->placeholder('-'),
                                ])
                                ->visible(fn (PerbaikanModel $record) => ! empty($record->catatan_barang)),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),
                ])
                    ->button()
                    ->label('Aksi')
                    ->icon('heroicon-m-chevron-down')
                    ->color('primary'),
            ]);
    }
}
