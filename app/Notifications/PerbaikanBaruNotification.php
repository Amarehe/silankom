<?php

namespace App\Notifications;

use App\Filament\Resources\PengajuanPerbaikans\PengajuanPerbaikanResource;
use App\Models\PerbaikanModel;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PerbaikanBaruNotification
{
    /**
     * Kirim notifikasi pengajuan perbaikan baru ke Super Admin, Admin Komlek & Teknisi Komlek.
     */
    public static function send(PerbaikanModel $perbaikan): void
    {
        $pemohon = $perbaikan->pemohon?->name ?? 'User';
        $barang = $perbaikan->nm_barang ?? 'Barang';
        $targetUrl = PengajuanPerbaikanResource::getUrl('index');

        $penerima = User::whereIn('role_id', [1, 2, 3])->get();

        $notification = Notification::make()
            ->title('Pengajuan Perbaikan Baru')
            ->body("Pengajuan perbaikan dari <strong>{$pemohon}</strong> untuk barang <strong>{$barang}</strong>.")
            ->icon('heroicon-o-wrench')
            ->iconColor('danger')
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Pengajuan')
                    ->url($targetUrl)
                    ->markAsRead(),
            ]);

        FilamentNotificationSender::send($notification, $penerima, $targetUrl, $perbaikan->id);
    }
}
