<?php

namespace App\Filament\Resources\Barangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['kategori', 'merek']))
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
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'perlu_perbaikan' => 'warning',
                        'rusak' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
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
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'dipinjam' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tersedia' => 'Tersedia',
                        'dipinjam' => 'Dipinjam',
                    }),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kategori'),

                SelectFilter::make('merek_id')
                    ->label('Merek')
                    ->relationship('merek', 'nama_merek')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Merek'),

                SelectFilter::make('kondisi')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'perlu_perbaikan' => 'Perlu Perbaikan',
                        'rusak' => 'Rusak',
                    ])
                    ->placeholder('Semua Kondisi'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'dipinjam' => 'Dipinjam',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading('Lihat')
                        ->color('info')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detail Data Barang')
                        ->modalWidth('3xl')
                        ->modalCancelAction(
                            fn ($action) => $action->label('Tutup')
                                ->color('danger')
                                ->icon('heroicon-o-x-circle')
                        ),

                    EditAction::make()
                        ->modalHeading('Edit Data Barang')
                        ->modalWidth('2xl')
                        ->successNotificationTitle('Data Barang berhasil diubah')
                        ->modalSubmitAction(
                            fn ($action) => $action->label('Ubah Data')
                                ->color('success')
                                ->icon('heroicon-o-pencil-square')
                        )
                        ->modalCancelAction(
                            fn ($action) => $action->label('Batal')
                                ->color('danger')
                                ->icon('heroicon-o-x-circle')
                        ),

                    DeleteAction::make()
                        ->modalHeading('Hapus Data Barang'),
                ])
                    ->button()
                    ->label('Aksi')
                    ->icon('heroicon-m-chevron-down')
                    ->color('primary'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
