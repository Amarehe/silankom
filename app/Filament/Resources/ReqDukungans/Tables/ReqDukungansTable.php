<?php

namespace App\Filament\Resources\ReqDukungans\Tables;

use App\Models\ReqDukunganModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReqDukungansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['picDukungan']))
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('id')
                    ->label('ID Dukungan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nomor_nodis')
                    ->label('Nomor Nodis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deskripsi_kegiatan')
                    ->label('Deskripsi')
                    ->limit(30)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tgl_kegiatan')
                    ->label('Tanggal Kegiatan')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('req_barang')
                    ->label('Barang Diminta')
                    ->formatStateUsing(function ($state) {
                        $item = is_string($state) ? json_decode($state, true) : $state;
                        if (! is_array($item)) {
                            return $state;
                        }

                        $nama = $item['nama'] ?? $item['nama'] ?? '-';
                        $jumlah = $item['jumlah'] ?? $item['jumlah'] ?? 0;

                        return "{$nama} ({$jumlah})";
                    })
                    ->bulleted(),

                TextColumn::make('status_dukungan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_didukung' => 'warning',
                        'didukung' => 'success',
                        'tidak_didukung' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_didukung' => 'Belum Didukung',
                        'didukung' => 'Didukung',
                        'tidak_didukung' => 'Tidak Didukung',
                    }),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('picDukungan.name')
                    ->label('PIC Admin')
                    ->placeholder('-'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Dukungan')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Detail Kegiatan')
                                ->icon('heroicon-o-calendar-days')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('nomor_nodis')
                                            ->label('Nomor Nodis')
                                            ->icon('heroicon-m-document-text')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->placeholder('-'),
                                        TextEntry::make('ruangan')
                                            ->label('Ruangan')
                                            ->icon('heroicon-m-map-pin'),
                                        TextEntry::make('tgl_kegiatan')
                                            ->label('Tanggal Kegiatan')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                    ]),
                                    TextEntry::make('deskripsi_kegiatan')
                                        ->label('Deskripsi Kegiatan')
                                        ->prose()
                                        ->placeholder('-'),
                                ])->collapsible(),

                            Section::make('Barang Diminta')
                                ->icon('heroicon-o-cube')
                                ->schema([
                                    TextEntry::make('req_barang')
                                        ->label('Daftar Barang')
                                        ->formatStateUsing(function ($state) {
                                            $item = is_string($state) ? json_decode($state, true) : $state;
                                            if (! is_array($item)) {
                                                return $state;
                                            }

                                            $nama = $item['nama'] ?? '-';
                                            $jumlah = $item['jumlah'] ?? 0;

                                            return "{$nama} ({$jumlah} unit)";
                                        })
                                        ->bulleted(),
                                ]),

                            Section::make('Status & Keterangan')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('status_dukungan')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'belum_didukung' => 'warning',
                                                'didukung' => 'success',
                                                'tidak_didukung' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'belum_didukung' => 'Belum Didukung',
                                                'didukung' => 'Didukung',
                                                'tidak_didukung' => 'Tidak Didukung',
                                                default => ucfirst($state),
                                            }),
                                        TextEntry::make('picDukungan.name')
                                            ->label('PIC Admin')
                                            ->icon('heroicon-m-user-circle')
                                            ->placeholder('-'),
                                    ]),
                                    TextEntry::make('keterangan')
                                        ->label('Keterangan')
                                        ->prose()
                                        ->placeholder('-'),
                                ]),

                            Section::make('Alasan Penolakan')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->schema([
                                    TextEntry::make('alasan_ditolak')
                                        ->label('Alasan')
                                        ->prose()
                                        ->color('danger'),
                                ])
                                ->visible(fn (ReqDukunganModel $record) => $record->status_dukungan === 'tidak_didukung' && ! empty($record->alasan_ditolak)),
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
