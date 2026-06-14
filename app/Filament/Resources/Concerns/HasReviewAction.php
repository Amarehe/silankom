<?php

namespace App\Filament\Resources\Concerns;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Models\ReviewLayananModel;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

trait HasReviewAction
{
    /**
     * Get PIC/handler name from a reviewable record.
     */
    private static function getPicInfo(Model $record): string
    {
        return match (true) {
            $record instanceof PeminjamanModel => $record->admin?->name ?? '-',
            $record instanceof PerbaikanModel => $record->teknisi?->name ?? '-',
            $record instanceof ReqDukunganModel => $record->picDukungan?->name ?? '-',
            default => '-',
        };
    }

    /**
     * Get the label for the PIC role.
     */
    private static function getPicLabel(Model $record): string
    {
        return match (true) {
            $record instanceof PeminjamanModel => 'Admin Penyetuju',
            $record instanceof PerbaikanModel => 'Teknisi Penanggung Jawab',
            $record instanceof ReqDukunganModel => 'PIC Dukungan',
            default => 'Penanggung Jawab',
        };
    }

    /**
     * Get the service type label.
     */
    private static function getJenisLabel(Model $record): string
    {
        return match (true) {
            $record instanceof PeminjamanModel => 'Peminjaman',
            $record instanceof PerbaikanModel => 'Perbaikan',
            $record instanceof ReqDukunganModel => 'Dukungan',
            default => 'Layanan',
        };
    }

    /**
     * Build the "Isi Review" action for a table row.
     */
    public static function makeReviewAction(): Action
    {
        return Action::make('isi_review')
            ->label('Isi Review')
            ->icon('heroicon-o-star')
            ->color('warning')
            ->modalHeading('Form Review Pelayanan')
            ->modalDescription('Berikan penilaian Anda terhadap pelayanan yang telah diterima.')
            ->modalWidth('xl')
            ->form(fn (Model $record): array => [
                Section::make('Informasi Layanan')
                    ->description('Detail layanan yang akan Anda review')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Placeholder::make('jenis_layanan')
                            ->label('Jenis Layanan')
                            ->content(fn () => new HtmlString(
                                '<span class="fi-badge fi-color-custom px-2 py-1 text-xs font-medium rounded-md" style="background-color: rgba(59, 130, 246, 0.1); color: rgb(59, 130, 246);">'
                                .self::getJenisLabel($record)
                                .'</span>'
                            )),
                        Placeholder::make('pic_info')
                            ->label(self::getPicLabel($record))
                            ->content(fn () => new HtmlString(
                                '<div class="flex items-center gap-2">'
                                .'<span class="text-sm font-semibold text-gray-900 dark:text-white">👤 '.self::getPicInfo($record).'</span>'
                                .'</div>'
                            )),
                    ])
                    ->columns(2),

                Section::make('Penilaian Pelayanan')
                    ->description('Berikan rating bintang 1-5 untuk setiap aspek')
                    ->icon('heroicon-o-star')
                    ->schema([
                        Radio::make('rating')
                            ->label('⭐ Rating Keseluruhan')
                            ->options([
                                1 => '⭐ Sangat Buruk',
                                2 => '⭐⭐ Buruk',
                                3 => '⭐⭐⭐ Cukup',
                                4 => '⭐⭐⭐⭐ Baik',
                                5 => '⭐⭐⭐⭐⭐ Sangat Baik',
                            ])
                            ->required()
                            ->inline()
                            ->inlineLabel(false),

                        Radio::make('aspek_kecepatan')
                            ->label('⏱️ Kecepatan Layanan')
                            ->options([
                                1 => '⭐ Sangat Lambat',
                                2 => '⭐⭐ Lambat',
                                3 => '⭐⭐⭐ Cukup',
                                4 => '⭐⭐⭐⭐ Cepat',
                                5 => '⭐⭐⭐⭐⭐ Sangat Cepat',
                            ])
                            ->required()
                            ->inline()
                            ->inlineLabel(false),

                        Radio::make('aspek_kualitas')
                            ->label('✅ Kualitas Hasil')
                            ->options([
                                1 => '⭐ Sangat Buruk',
                                2 => '⭐⭐ Buruk',
                                3 => '⭐⭐⭐ Cukup',
                                4 => '⭐⭐⭐⭐ Baik',
                                5 => '⭐⭐⭐⭐⭐ Sangat Baik',
                            ])
                            ->required()
                            ->inline()
                            ->inlineLabel(false),

                        Radio::make('aspek_pelayanan')
                            ->label('🤝 Pelayanan Petugas')
                            ->options([
                                1 => '⭐ Sangat Buruk',
                                2 => '⭐⭐ Buruk',
                                3 => '⭐⭐⭐ Cukup',
                                4 => '⭐⭐⭐⭐ Baik',
                                5 => '⭐⭐⭐⭐⭐ Sangat Baik',
                            ])
                            ->required()
                            ->inline()
                            ->inlineLabel(false),
                    ])
                    ->columns(1),

                Textarea::make('komentar')
                    ->label('Komentar / Saran')
                    ->placeholder('Tuliskan komentar atau saran Anda (opsional)')
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ])
            ->action(function (Model $record, array $data): void {
                ReviewLayananModel::create([
                    'user_id' => Auth::id(),
                    'reviewable_type' => get_class($record),
                    'reviewable_id' => $record->getKey(),
                    'rating' => $data['rating'],
                    'aspek_kecepatan' => $data['aspek_kecepatan'],
                    'aspek_kualitas' => $data['aspek_kualitas'],
                    'aspek_pelayanan' => $data['aspek_pelayanan'],
                    'komentar' => $data['komentar'] ?? null,
                ]);

                Notification::make()
                    ->title('Review berhasil disimpan!')
                    ->body('Terima kasih atas penilaian Anda.')
                    ->success()
                    ->send();
            })
            ->modalSubmitActionLabel('Kirim Review')
            ->visible(fn (Model $record): bool => ! $record->relationLoaded('review')
                ? $record->review === null
                : $record->getRelation('review') === null);
    }

    /**
     * Build the "Lihat Review" action to view a previously submitted review.
     */
    public static function makeViewReviewAction(): Action
    {
        return Action::make('lihat_review')
            ->label('Lihat Review')
            ->icon('heroicon-o-star')
            ->color('success')
            ->modalHeading('Review Pelayanan Anda')
            ->modalWidth('lg')
            ->infolist(fn (Model $record): array => [
                Section::make('Informasi Layanan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('jenis_layanan_display')
                                ->label('Jenis Layanan')
                                ->state(fn () => self::getJenisLabel($record))
                                ->badge()
                                ->color('info'),
                            TextEntry::make('pic_display')
                                ->label(self::getPicLabel($record))
                                ->state(fn () => self::getPicInfo($record))
                                ->icon('heroicon-m-user-circle')
                                ->weight('bold'),
                        ]),
                    ]),

                Section::make('Penilaian')
                    ->icon('heroicon-o-star')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('review.rating')
                                ->label('Rating Keseluruhan')
                                ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state)." ({$state}/5)")
                                ->weight('bold'),
                            TextEntry::make('review.aspek_kecepatan')
                                ->label('Kecepatan Layanan')
                                ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state)." ({$state}/5)"),
                            TextEntry::make('review.aspek_kualitas')
                                ->label('Kualitas Hasil')
                                ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state)." ({$state}/5)"),
                            TextEntry::make('review.aspek_pelayanan')
                                ->label('Pelayanan Petugas')
                                ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state)." ({$state}/5)"),
                        ]),
                        TextEntry::make('review.komentar')
                            ->label('Komentar')
                            ->prose()
                            ->placeholder('Tidak ada komentar'),
                        TextEntry::make('review.created_at')
                            ->label('Tanggal Review')
                            ->dateTime('d F Y, H:i'),
                    ]),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalCancelAction(fn ($action) => $action->color('gray'))
            ->visible(fn (Model $record): bool => $record->review !== null);
    }
}
