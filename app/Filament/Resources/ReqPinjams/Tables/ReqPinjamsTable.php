<?php

namespace App\Filament\Resources\ReqPinjams\Tables;

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
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
