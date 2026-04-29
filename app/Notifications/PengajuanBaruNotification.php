<?php

namespace App\Notifications;

use App\Filament\Resources\Pengajuans\PengajuanResource;
use App\Models\ReqPinjamModel;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PengajuanBaruNotification
{
    /**
     * Kirim notifikasi pengajuan peminjaman baru ke Super Admin & Admin Komlek.
     */
    public static function send(ReqPinjamModel $reqPinjam): void
    {
        $pemohon = $reqPinjam->user?->name ?? 'User';
        $kategori = $reqPinjam->kategori?->nama_kategori ?? 'Barang';
        $targetUrl = PengajuanResource::getUrl('index');

        $admins = User::whereIn('role_id', [1, 2])->get();

        $notification = Notification::make()
            ->title('Pengajuan Peminjaman Baru')
            ->body("Pengajuan peminjaman dari <strong>{$pemohon}</strong> untuk kategori <strong>{$kategori}</strong> ({$reqPinjam->jumlah} unit).")
            ->icon('heroicon-o-inbox-stack')
            ->iconColor('primary')
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Pengajuan')
                    ->url($targetUrl)
                    ->markAsRead(),
            ]);

        FilamentNotificationSender::send($notification, $admins, $targetUrl, $reqPinjam->id);
    }
}
