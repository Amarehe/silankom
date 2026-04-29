<?php

namespace App\Notifications;

use App\Filament\Resources\ReqDukungans\ReqDukunganResource;
use App\Filament\Resources\ReqPerbaikans\ReqPerbaikanResource;
use App\Filament\Resources\ReqPinjams\ReqPinjamResource;
use App\Filament\Resources\RiwayatDukunganUsers\RiwayatDukunganUserResource;
use App\Filament\Resources\RiwayatPeminjamans\RiwayatPeminjamanResource;
use App\Filament\Resources\RiwayatPerbaikans\RiwayatPerbaikanResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class StatusUpdateNotification
{
    /**
     * Status yang menandakan record sudah selesai dan masuk ke riwayat.
     *
     * @var array<string, array<string>>
     */
    private static array $riwayatStatuses = [
        'Peminjaman' => ['disetujui'],
        'Perbaikan' => ['selesai', 'tidak_bisa_diperbaiki'],
        'Dukungan' => ['didukung', 'tidak_didukung'],
    ];

    /**
     * Kirim notifikasi perubahan status ke pemohon (karyawan).
     *
     * @param  string  $layanan  Jenis layanan: 'Peminjaman', 'Perbaikan', 'Dukungan'
     * @param  string  $statusBaru  Status terbaru
     * @param  int|string  $recordId  ID record yang terkait
     * @param  int|string|null  $riwayatRecordId  ID record di tabel riwayat (opsional, untuk Peminjaman yang membuat record baru)
     */
    public static function send(
        User $pemohon,
        string $layanan,
        string $statusBaru,
        int|string $recordId,
        int|string|null $riwayatRecordId = null,
    ): void {
        $statusLabel = str_replace('_', ' ', $statusBaru);
        $isRiwayat = in_array($statusBaru, self::$riwayatStatuses[$layanan] ?? []);

        $iconMap = [
            'Peminjaman' => 'heroicon-o-inbox-stack',
            'Perbaikan' => 'heroicon-o-wrench',
            'Dukungan' => 'heroicon-o-hand-raised',
        ];

        $colorMap = [
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'selesai' => 'success',
            'tidak_bisa_diperbaiki' => 'danger',
            'dikerjakan' => 'info',
            'diproses' => 'warning',
            'didukung' => 'success',
            'tidak_didukung' => 'danger',
        ];

        // URL untuk daftar pengajuan aktif (belum selesai)
        $activeUrlMap = [
            'Peminjaman' => ReqPinjamResource::getUrl('index'),
            'Perbaikan' => ReqPerbaikanResource::getUrl('index'),
            'Dukungan' => ReqDukunganResource::getUrl('index'),
        ];

        // URL untuk riwayat (sudah selesai) — dari sisi karyawan
        $riwayatUrlMap = [
            'Peminjaman' => RiwayatPeminjamanResource::getUrl('index'),
            'Perbaikan' => RiwayatPerbaikanResource::getUrl('index'),
            'Dukungan' => RiwayatDukunganUserResource::getUrl('index'),
        ];

        $targetUrl = $isRiwayat
            ? ($riwayatUrlMap[$layanan] ?? '/')
            : ($activeUrlMap[$layanan] ?? '/');

        // Tentukan highlight ID yang sesuai
        $highlightId = $isRiwayat && $riwayatRecordId
            ? $riwayatRecordId
            : $recordId;

        $notification = Notification::make()
            ->title("Status {$layanan} Diperbarui")
            ->body("Pengajuan {$layanan} Anda sekarang berstatus <strong>{$statusLabel}</strong>.")
            ->icon($iconMap[$layanan] ?? 'heroicon-o-bell')
            ->iconColor($colorMap[$statusBaru] ?? 'primary')
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Detail')
                    ->url($targetUrl)
                    ->markAsRead(),
            ]);

        FilamentNotificationSender::send($notification, $pemohon, $targetUrl, $highlightId);
    }
}
