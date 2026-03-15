<?php

namespace App\Filament\Resources\ReqDukungans\Pages;

use App\Filament\Resources\ReqDukungans\ReqDukunganResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListReqDukungans extends ListRecords
{
    protected static string $resource = ReqDukunganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->button()
                ->icon('heroicon-o-plus')
                ->color('cyan')
                ->label('Ajukan Dukungan Baru')
                ->modalHeading('Form Pengajuan Dukungan Kegiatan')
                ->modalWidth('xl')
                ->createAnother(false)
                ->successNotificationTitle('Pengajuan dukungan berhasil dikirim')
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
