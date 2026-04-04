<?php

namespace App\Filament\Resources\ReqPerbaikans\Tables;

use App\Models\PerbaikanModel;
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

                TextColumn::make('nm_barang')
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
                        'diajukan' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'tidak_bisa_diperbaiki' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                    }),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi')
                    ->placeholder('-'),
            ])
            ->modifyQueryUsing(fn($query) => $query->with(['kategori', 'merek', 'teknisi']))
            ->defaultSort('created_at', 'desc');
    }
}
