<?php

namespace App\Notifications;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class FilamentNotificationSender
{
    /**
     * Kirim Filament Notification ke database dan broadcast secara real-time.
     * Menggunakan notification redirect route agar notifikasi otomatis ditandai dibaca saat diklik.
     *
     * @param  User|Collection<int, User>  $users
     * @param  string|null  $targetUrl  URL tujuan saat notifikasi diklik
     * @param  int|string|null  $highlightId  ID record yang akan di-highlight di tabel
     */
    public static function send(
        Notification $notification,
        User|Collection $users,
        ?string $targetUrl = null,
        int|string|null $highlightId = null,
    ): void {
        if ($users instanceof User) {
            $users = collect([$users]);
        }

        foreach ($users as $user) {
            $user->notify($notification->toDatabase());

            // Ambil notification ID yang baru saja disimpan untuk membuat redirect URL
            $dbNotification = $user->notifications()->latest()->first();

            if ($dbNotification && $targetUrl) {
                $redirectUrl = route('notification.redirect', [
                    'notification' => $dbNotification->id,
                    'url' => $targetUrl,
                    'highlight' => $highlightId,
                ]);

                // Update data notifikasi dengan URL redirect yang benar
                $data = $dbNotification->data;
                $data['actions'] = array_map(function (array $action) use ($redirectUrl) {
                    if (($action['name'] ?? '') === 'lihat') {
                        $action['url'] = $redirectUrl;
                    }

                    return $action;
                }, $data['actions'] ?? []);
                $dbNotification->update(['data' => $data]);
            }

            $notification->broadcast($user);
        }
    }
}
