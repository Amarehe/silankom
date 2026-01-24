<?php

namespace App\Filament\Resources\Kategoris\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class KategorisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->modalHeading('Edit Data Kategori')
                    ->modalWidth('xl')
                    ->successNotificationTitle('Data Kategori berhasil diubah')
                    ->modalSubmitAction(
                        fn($action) =>
                        $action->label('Ubah Data') // Ganti Tulisan
                            ->color('success')     // Ganti Warna (success, danger, info, warning)
                            ->icon('heroicon-o-pencil-square') // Tambah Icon (Opsional)
                    )
                    ->modalCancelAction(
                        fn($action) =>
                        $action->label('Batal')    // Ganti Tulisan
                            ->color('danger')
                            ->icon('heroicon-o-x-circle')
                    ),

            DeleteAction::make()
                    ->modalHeading('Hapus Data Kategori')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
