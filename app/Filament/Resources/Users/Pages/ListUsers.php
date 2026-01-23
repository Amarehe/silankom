<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\Console\Color;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->button()
                ->icon('heroicon-o-plus')
                ->color('cyan')
                ->label('Tambah Data') // Opsional: Mengganti label tombol
                ->modalHeading('Tambah User Baru') // Opsional: Judul Modal
                ->modalWidth('3xl') // Pilihan: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl', 'full'
                ->createAnother(true)
                ->successNotificationTitle('Data User berhasil disimpan')
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
