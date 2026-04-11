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
                    ->color('primary'),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tgl. Pengajuan')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('l, d F Y'))
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
                                        TextEntry::make('nodis')
                                            ->label('Nomor Nota Dinas')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                        TextEntry::make('kategori.nama_kategori')->label('Kategori'),
                                        TextEntry::make('merek.nama_merek')->label('Merek'),
                                        TextEntry::make('nm_barang')->label('Nama Barang'),
                                        TextEntry::make('serial_number')->label('Serial Number')->placeholder('-'),
                                        TextEntry::make('jumlah')->label('Jumlah Unit')->suffix(' unit'),
                                        TextEntry::make('tgl_pengajuan')->label('Tgl. Pengajuan')->date('l, d F Y'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Hasil Perbaikan')
                                ->icon('heroicon-o-check-badge')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('status_perbaikan')
                                            ->label('Status Akhir')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'selesai' => 'success',
                                                'tidak_bisa_diperbaiki' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'selesai' => 'Selesai',
                                                'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                                                default => $state,
                                            }),
                                        TextEntry::make('no_surat_perbaikan')->label('Nomor Surat')->placeholder('-'),
                                        TextEntry::make('teknisi.name')->label('Teknisi Penanggung Jawab')->placeholder('-'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Keluhan & Hasil')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('keluhan')->label('Keluhan / Kerusakan')->prose(),
                                        TextEntry::make('keterangan')->label('Keterangan Hasil')->prose(),
                                    ]),
                                ]),

                            Section::make('Info Pengambilan Barang')
                                ->icon('heroicon-o-gift')
                                ->schema([
                                    TextEntry::make('catatan_barang')->label('Instruksi')->placeholder('-'),
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
