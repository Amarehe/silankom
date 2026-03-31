<?php

namespace App\Filament\Resources\RiwayatPerbaikans\Tables;

use App\Models\PerbaikanModel;
use Filament\Actions\Action;
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

                TextColumn::make('tgl_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('merek.nama_merek')
                    ->label('Merek')
                    ->searchable(),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->alignCenter()
                    ->suffix(' unit'),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->limit(30)
                    ->wrap()
                    ->searchable(),

                BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        'rusak' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        'rusak' => 'Rusak',
                    }),

                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->placeholder('-'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('no_surat')
                    ->label('Nomor Surat')
                    ->placeholder('-')
                    ->copyable(),
            ])
            ->modifyQueryUsing(fn($query) => $query->with(['kategori', 'merek', 'teknisi']))
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('download_surat')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn(PerbaikanModel $record): bool => !empty($record->no_surat))
                    ->url(fn(PerbaikanModel $record): string => route('download.surat-perbaikan', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
