<?php

namespace App\Filament\Resources\ReqDukungans\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReqDukungansTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                        if (! is_array($item)) return $state;
                        
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
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
