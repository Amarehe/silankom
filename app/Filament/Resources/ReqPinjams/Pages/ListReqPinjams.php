<?php

namespace App\Filament\Resources\ReqPinjams\Pages;

use App\Filament\Resources\Concerns\ChecksPendingReview;
use App\Filament\Resources\ReqPinjams\ReqPinjamResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListReqPinjams extends ListRecords
{
    use ChecksPendingReview;

    protected static string $resource = ReqPinjamResource::class;

    protected function getHeaderActions(): array
    {
        $hasPending = self::hasPendingReview();
        $pendingCount = $hasPending ? self::getPendingReviewCount() : 0;
        $pendingDetails = $hasPending ? self::getPendingReviewDetails() : collect();

        return array_filter([
            $hasPending
                ? Action::make('review_warning')
                    ->label("{$pendingCount} Review Belum Diisi")
                    ->icon('heroicon-m-exclamation-triangle')
                    ->color('danger')
                    ->extraAttributes(['class' => 'animate-pulse'])
                    ->modalHeading('⚠️ Review Pelayanan Belum Diisi')
                    ->modalDescription('Anda harus mengisi review untuk semua layanan yang sudah selesai sebelum mengajukan request baru. Silakan klik tombol di bawah untuk mengisi review.')
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->extraModalFooterActions(
                        $pendingDetails->map(fn (array $detail) => Action::make('go_'.strtolower($detail['jenis']))
                            ->label("📝 Isi Review {$detail['jenis']} ({$detail['count']})")
                            ->icon($detail['icon'])
                            ->color($detail['color'])
                            ->url($detail['url'])
                        )->all()
                    )
                : null,

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
                )
                ->disabled($hasPending)
                ->tooltip($hasPending ? 'Isi review layanan yang sudah selesai terlebih dahulu' : null),
        ]);
    }
}
