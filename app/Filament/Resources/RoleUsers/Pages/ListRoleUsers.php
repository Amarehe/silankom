<?php

namespace App\Filament\Resources\RoleUsers\Pages;

use App\Filament\Resources\RoleUsers\RoleUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoleUsers extends ListRecords
{
    protected static string $resource = RoleUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->button()
                ->icon('heroicon-o-plus')
                ->color('cyan')
                ->label('Tambah Data') // Opsional: Mengganti label tombol
                ->modalHeading('Tambah Role User Baru') // Opsional: Judul Modal
                ->modalWidth('xl') // Pilihan: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl', 'full'
                ->createAnother(true)
                ->successNotificationTitle('Data Role User berhasil disimpan')
                ->modalSubmitAction(
                    fn($action) =>
                    $action->label('Simpan Data') // Ganti Tulisan
                        ->color('success')     // Ganti Warna (success, danger, info, warning)
                        ->icon('heroicon-o-check-circle') // Tambah Icon (Opsional)
                )
                ->modalCancelAction(
                    fn($action) =>
                    $action->label('Batal')    // Ganti Tulisan
                        ->color('danger') 
                        ->icon('heroicon-o-x-circle')
                        
                ),
        ];
    }
}
