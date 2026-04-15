<?php

namespace App\Filament\Resources\RiwayatDukungans\Tables;

use App\Models\ReqDukunganModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RiwayatDukunganTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['pemohon.unitkerja', 'pemohon.jabatan', 'picDukungan']))
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('nomor_nodis')
                    ->label('Nomor Nodis')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor nodis disalin!')
                    ->icon('heroicon-o-document-duplicate')
                    ->tooltip('Klik untuk menyalin'),

                TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tgl_kegiatan')
                    ->label('Tgl Kegiatan')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('req_barang')
                    ->label('Barang Dibutuhkan')
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

                TextColumn::make('barang_diberikan')
                    ->label('Barang Diberikan')
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
                        'didukung' => 'success',
                        'tidak_didukung' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'didukung' => 'Didukung',
                        'tidak_didukung' => 'Tidak Didukung',
                    })
                    ->sortable(),

                TextColumn::make('pemohon.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tgl_disetujui')
                    ->label('Tgl Disetujui')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y') : '-')
                    ->sortable(),

                TextColumn::make('picDukungan.name')
                    ->label('PIC Admin')
                    ->placeholder('-'),

                TextColumn::make('alasan_ditolak')
                    ->label('Alasan Ditolak')
                    ->placeholder('-')
                    ->wrap(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Riwayat Dukungan')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Informasi Pemohon')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('pemohon.name')
                                            ->label('Nama Pemohon')
                                            ->icon('heroicon-m-user')
                                            ->weight('bold'),
                                        TextEntry::make('pemohon.unitkerja.nm_unitkerja')
                                            ->label('Unit Kerja')
                                            ->icon('heroicon-m-building-office')
                                            ->placeholder('-'),
                                        TextEntry::make('pemohon.jabatan.nm_jabatan')
                                            ->label('Jabatan')
                                            ->icon('heroicon-m-briefcase')
                                            ->placeholder('-'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Detail Kegiatan')
                                ->icon('heroicon-o-calendar-days')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('nomor_nodis')
                                            ->label('Nomor Nodis')
                                            ->icon('heroicon-m-document-text')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->copyable()
                                            ->copyMessage('Nomor nodis disalin!')
                                            ->placeholder('-'),
                                        TextEntry::make('nama_kegiatan')
                                            ->label('Nama Kegiatan')
                                            ->icon('heroicon-m-tag')
                                            ->weight('bold')
                                            ->placeholder('-'),
                                    ]),
                                    Grid::make(3)->schema([
                                        TextEntry::make('ruangan')
                                            ->label('Ruangan')
                                            ->icon('heroicon-m-map-pin'),
                                        TextEntry::make('tgl_kegiatan')
                                            ->label('Tanggal Kegiatan')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                        TextEntry::make('created_at')
                                            ->label('Tanggal Pengajuan')
                                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                                            ->icon('heroicon-m-clock'),
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
                                        ->label('Daftar Barang Diminta')
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

                            Section::make('Barang Diberikan')
                                ->icon('heroicon-o-gift')
                                ->schema([
                                    TextEntry::make('barang_diberikan')
                                        ->label('Daftar Barang Diberikan')
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
                                ])
                                ->visible(fn (ReqDukunganModel $record) => ! empty($record->barang_diberikan)),

                            Section::make('Keputusan')
                                ->icon('heroicon-o-shield-check')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('status_dukungan')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'didukung' => 'success',
                                                'tidak_didukung' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'didukung' => 'Didukung',
                                                'tidak_didukung' => 'Tidak Didukung',
                                                default => ucfirst($state),
                                            }),
                                        TextEntry::make('tgl_disetujui')
                                            ->label('Tanggal Disetujui')
                                            ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y') : '-')
                                            ->icon('heroicon-m-check-badge')
                                            ->placeholder('-'),
                                        TextEntry::make('picDukungan.name')
                                            ->label('PIC Admin')
                                            ->icon('heroicon-m-user-circle')
                                            ->placeholder('-'),
                                    ]),
                                    TextEntry::make('catatan_admin')
                                        ->label('Catatan Admin')
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
            ->filters([
                SelectFilter::make('status_dukungan')
                    ->label('Status Dukungan')
                    ->options([
                        'didukung' => 'Didukung',
                        'tidak_didukung' => 'Tidak Didukung',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->extremePaginationLinks();
    }
}
