<?php

namespace App\Filament\Resources\UnitKerjas\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class UnitKerjasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('nm_unitkerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->modalHeading('Edit Data Unit Kerja')
                    ->modalWidth('xl')
                    ->successNotificationTitle('Data Unit Kerja berhasil diubah')
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
                    ->modalHeading('Hapus Data Unit Kerja')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
