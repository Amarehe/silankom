<?php

namespace App\Filament\Resources\ReqPinjams\Tables;

use App\Models\ReqPinjamModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReqPinjamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('tanggal_request')
                    ->label('Tanggal Pengajuan')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'diproses' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    }),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('alasan_penolakan')
                    ->label('Alasan Penolakan')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-')
                    ->visible(fn ($record) => $record?->status === 'ditolak'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['kategori']))
            ->actions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Peminjaman')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Rincian Pengajuan')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('kategori.nama_kategori')
                                            ->label('Kategori Barang')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('jumlah')
                                            ->label('Jumlah')
                                            ->suffix(' Unit')
                                            ->icon('heroicon-m-cube')
                                            ->weight('bold'),
                                        TextEntry::make('tanggal_request')
                                            ->label('Tanggal Pengajuan')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'diproses' => 'warning',
                                                'disetujui' => 'success',
                                                'ditolak' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'diproses' => 'Diproses',
                                                'disetujui' => 'Disetujui',
                                                'ditolak' => 'Ditolak',
                                                default => ucfirst($state),
                                            }),
                                    ]),
                                ])->collapsible(),

                            Section::make('Keterangan')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    TextEntry::make('keterangan')
                                        ->label('Keperluan / Keterangan')
                                        ->prose()
                                        ->placeholder('-'),
                                ])
                                ->visible(fn (ReqPinjamModel $record) => ! empty($record->keterangan)),

                            Section::make('Alasan Penolakan')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->schema([
                                    TextEntry::make('alasan_penolakan')
                                        ->label('Alasan')
                                        ->prose()
                                        ->color('danger'),
                                ])
                                ->visible(fn (ReqPinjamModel $record) => $record->status === 'ditolak' && ! empty($record->alasan_penolakan)),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),
                ])
                    ->button()
                    ->label('Aksi')
                    ->icon('heroicon-m-chevron-down')
                    ->color('primary'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
