<?php

namespace App\Notifications;

use App\Models\ReqPinjamModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanBaruNotification extends Notification
{
    use Queueable;

    public function __construct(
        private ReqPinjamModel $reqPinjam
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Pengajuan Peminjaman Baru',
            'message' => "Pengajuan peminjaman dari {$this->reqPinjam->user->name} untuk kategori {$this->reqPinjam->kategori->nama_kategori}",
            'req_pinjam_id' => $this->reqPinjam->id,
            'url' => route('filament.admin.resources.pengajuan.index'),
        ];
    }
}
