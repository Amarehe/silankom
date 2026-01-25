<?php

namespace App\Filament\Resources\Barangs\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('merek.nama_merek')
                    ->label('Merek')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('label')
                    ->label('Label')
                    ->toggleable(),

                TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baik' => 'success',
                        'perlu_perbaikan' => 'warning',
                        'rusak' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'baik' => 'Baik',
                        'perlu_perbaikan' => 'Perlu Perbaikan',
                        'rusak' => 'Rusak',
                    }),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'dipinjam' => 'info',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'tersedia' => 'Tersedia',
                        'dipinjam' => 'Dipinjam',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->modalHeading('Lihat')
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Data Barang')
                    ->modalWidth('3xl')
                    ->modalCancelAction(
                        fn($action) =>
                        $action->label('Tutup')
                            ->color('danger')
                            ->icon('heroicon-o-x-circle')
                    ),

                EditAction::make()
                    ->button()
                    ->modalHeading('Edit Data Barang')
                    ->modalWidth('2xl')
                    ->successNotificationTitle('Data Barang berhasil diubah')
                    ->modalSubmitAction(
                        fn($action) =>
                        $action->label('Ubah Data')
                            ->color('success')
                            ->icon('heroicon-o-pencil-square')
                    )
                    ->modalCancelAction(
                        fn($action) =>
                        $action->label('Batal')
                            ->color('danger')
                            ->icon('heroicon-o-x-circle')
                    ),

                DeleteAction::make()
                    ->modalHeading('Hapus Data Barang')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
