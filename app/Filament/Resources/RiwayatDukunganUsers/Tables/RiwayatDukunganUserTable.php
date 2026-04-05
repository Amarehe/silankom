<?php

namespace App\Filament\Resources\RiwayatDukunganUsers\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RiwayatDukunganUserTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    })
                    ->sortable(),

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
                    ->bulleted()
                    ->placeholder('-'),

                TextColumn::make('tgl_disetujui')
                    ->label('Tgl Disetujui')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('alasan_ditolak')
                    ->label('Alasan Ditolak')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('picDukungan.name')
                    ->label('PIC Admin')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status_dukungan')
                    ->label('Status')
                    ->options([
                        'belum_didukung' => 'Belum Didukung',
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
