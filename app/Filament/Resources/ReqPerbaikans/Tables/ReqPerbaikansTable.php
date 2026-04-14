<?php

namespace App\Filament\Resources\ReqPerbaikans\Tables;

use App\Models\PerbaikanModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReqPerbaikansTable
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
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PerbaikanModel $record): string => $record->merek->nama_merek ?? '-'),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->limit(35)
                    ->wrap()
                    ->searchable(),

                BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'diproses' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'diajukan' => 'heroicon-o-paper-airplane',
                        'diproses' => 'heroicon-o-wrench',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi')
                    ->placeholder('Belum ditugaskan'),

                TextColumn::make('catatan_barang')
                    ->label('Instruksi Teknisi')
                    ->limit(45)
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['kategori', 'merek', 'teknisi']))
            ->defaultSort('created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Perbaikan')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Rincian Barang & Pengajuan')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('nodis')
                                            ->label('Nomor Nota Dinas')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->placeholder('-')
                                            ->copyable()
                                            ->columnSpanFull(),
                                        TextEntry::make('kategori.nama_kategori')->label('Kategori'),
                                        TextEntry::make('merek.nama_merek')->label('Merek'),
                                        TextEntry::make('nm_barang')->label('Nama Barang'),
                                        TextEntry::make('jumlah')->label('Jumlah Unit')->suffix(' Unit'),
                                        TextEntry::make('tgl_pengajuan')->label('Tanggal Pengajuan')->date('l, d F Y'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Keluhan / Masalah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->schema([
                                    TextEntry::make('keluhan')->label('Deskripsi Masalah')->prose(),
                                ]),

                            Section::make('Status & Instruksi Teknisi')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Grid::make(1)->schema([
                                        TextEntry::make('status_perbaikan')
                                            ->label('Status Saat Ini')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'diajukan' => 'warning',
                                                'diproses' => 'info',
                                                'selesai' => 'success',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'diajukan' => 'Diajukan',
                                                'diproses' => 'Diproses',
                                                'selesai' => 'Selesai',
                                                default => $state,
                                            }),
                                        TextEntry::make('catatan_barang')
                                            ->label('Instruksi / Catatan dari Teknisi')
                                            ->placeholder('Belum ada instruksi'),
                                    ]),
                                ]),
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
