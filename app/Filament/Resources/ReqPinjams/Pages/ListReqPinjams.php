<?php

namespace App\Filament\Resources\ReqPinjams\Pages;

use App\Filament\Resources\ReqPinjams\ReqPinjamResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListReqPinjams extends ListRecords
{
    protected static string $resource = ReqPinjamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->button()
                ->icon('heroicon-o-plus')
                ->color('cyan')
                ->label('Ajukan Peminjaman Baru')
                ->modalHeading('Form Pengajuan Peminjaman Barang')
                ->modalWidth('xl')
                ->createAnother(false)
                ->successNotificationTitle('Pengajuan peminjaman berhasil dikirim')
                ->modalSubmitAction(
                    fn (Action $action) => $action->label('Kirim Pengajuan')
                        ->color('success')
                        ->icon(Heroicon::PaperAirplane)
                )
                ->modalCancelAction(
                    fn (Action $action) => $action->label('Batal')
                        ->color('danger')
                        ->icon(Heroicon::XCircle)
                ),
        ];
    }
}
