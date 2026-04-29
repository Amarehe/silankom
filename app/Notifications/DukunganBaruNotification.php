<?php

namespace App\Notifications;

use App\Filament\Resources\PengajuanDukungans\PengajuanDukunganResource;
use App\Models\ReqDukunganModel;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DukunganBaruNotification
{
    /**
     * Kirim notifikasi pengajuan dukungan baru ke Super Admin, Admin Komlek & Teknisi Komlek.
     */
    public static function send(ReqDukunganModel $dukungan): void
    {
        $pemohon = $dukungan->pemohon?->name ?? 'User';
        $kegiatan = $dukungan->nama_kegiatan ?? 'Kegiatan';
        $targetUrl = PengajuanDukunganResource::getUrl('index');

        $penerima = User::whereIn('role_id', [1, 2, 3])->get();

        $notification = Notification::make()
            ->title('Pengajuan Dukungan Baru')
            ->body("Pengajuan dukungan dari <strong>{$pemohon}</strong> untuk kegiatan <strong>{$kegiatan}</strong>.")
            ->icon('heroicon-o-hand-raised')
            ->iconColor('success')
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Pengajuan')
                    ->url($targetUrl)
                    ->markAsRead(),
            ]);

        FilamentNotificationSender::send($notification, $penerima, $targetUrl, $dukungan->id);
    }
}
