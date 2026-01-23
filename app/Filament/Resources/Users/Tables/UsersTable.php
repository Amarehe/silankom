<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use BladeUI\Icons\Components\Icon;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jabatan.nm_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unitkerja.nm_unitkerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role.nm_role')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_login')
                    ->label('Login Terakhir')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                // TextColumn::make('email')
                //     ->label('Email address')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('email_verified_at')
                //     dateTime('d M Y, H:i')
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->label('Lihat')
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Data User') // Judul Modal
                    ->modalWidth('3xl')
                    ->modalCancelAction(
                        fn($action) =>
                        $action->label('Tutup')    // Ganti Tulisan
                            ->color('danger')
                            ->icon('heroicon-o-x-circle')
                    ),

                EditAction::make()
                    ->button()
                    ->label('Ubah')
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading('Edit Data User') // Judul Modal
                    ->modalWidth('3xl')
                    ->successNotificationTitle('Data User berhasil diubah')
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
                    ->modalHeading('Hapus Data User')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
