<?php

namespace App\Filament\Resources\Mereks\Tables;

use Dom\Text;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class MereksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('nama_merek')
                    ->label('Merek')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->modalHeading('Edit Data Merek')
                    ->modalWidth('xl')
                    ->successNotificationTitle('Data Merek berhasil diubah')
                    ->modalSubmitAction(
                        fn(Action $action) =>
                        $action->label('Ubah Data') // Ganti Tulisan
                            ->color('success')     // Ganti Warna (success, danger, info, warning)
                            ->icon('heroicon-o-pencil-square') // Tambah Icon (Opsional)
                    )
                    ->modalCancelAction(
                        fn(Action $action) =>
                        $action->label('Batal')    // Ganti Tulisan
                            ->color('danger')
                            ->icon(Heroicon::XCircle)

                    ),
                DeleteAction::make()
                    ->modalHeading('Hapus Data Merek')
                    ->button()
                    ,
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
