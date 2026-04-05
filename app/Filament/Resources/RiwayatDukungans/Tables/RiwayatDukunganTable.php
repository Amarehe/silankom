<?php

namespace App\Filament\Resources\RiwayatDukungans\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RiwayatDukunganTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['pemohon', 'picDukungan']))
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('nomor_nodis')
                    ->label('Nomor Nodis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tgl_kegiatan')
                    ->label('Tgl Kegiatan')
                    ->date('d M Y')
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
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('picDukungan.name')
                    ->label('PIC Admin')
                    ->placeholder('-'),

                TextColumn::make('alasan_ditolak')
                    ->label('Alasan Ditolak')
                    ->placeholder('-')
                    ->wrap(),
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
