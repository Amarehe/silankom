<?php

namespace App\Filament\Resources\ReqPerbaikans\Pages;

use App\Filament\Resources\ReqPerbaikans\ReqPerbaikanResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListReqPerbaikans extends ListRecords
{
    protected static string $resource = ReqPerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->button()
                ->icon('heroicon-o-plus')
                ->color('cyan')
                ->label('Ajukan Perbaikan Baru')
                ->modalHeading('Form Pengajuan Perbaikan')
                ->modalWidth('xl')
                ->createAnother(false)
                ->successNotificationTitle('Pengajuan perbaikan berhasil dikirim')
                ->modalSubmitAction(
                    fn(Action $action) => $action->label('Kirim Pengajuan')
                        ->color('success')
                        ->icon(Heroicon::PaperAirplane)
                )
                ->modalCancelAction(
                    fn(Action $action) => $action->label('Batal')
                        ->color('danger')
                        ->icon(Heroicon::XCircle)
                ),
        ];
    }
}
