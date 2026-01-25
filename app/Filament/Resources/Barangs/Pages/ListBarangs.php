<?php

namespace App\Filament\Resources\Barangs\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Barangs\BarangResource;
use Filament\Support\Icons\Heroicon;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->button()
                ->icon('heroicon-o-plus')
                ->color('cyan')
                ->label('Tambah Data')
                ->modalHeading('Tambah Barang Baru')
                ->modalWidth('2xl')
                ->createAnother(true)
                ->successNotificationTitle('Data Barang berhasil disimpan')
                ->modalSubmitAction(
                    fn(Action $action) =>
                    $action->label('Simpan Data')
                        ->color('success')
                        ->icon(Heroicon::CheckCircle)
                )
                ->modalCancelAction(
                    fn(Action $action) =>
                    $action->label('Batal')
                        ->color('danger')
                        ->icon(Heroicon::XCircle)
                ),
        ];
    }
}
