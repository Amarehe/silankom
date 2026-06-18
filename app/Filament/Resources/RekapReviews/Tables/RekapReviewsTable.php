<?php

namespace App\Filament\Resources\RekapReviews\Tables;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Models\ReviewLayananModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RekapReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user.unitkerja', 'user.jabatan', 'reviewable']))
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->description(fn (ReviewLayananModel $record): string => $record->user?->nip ?? '-')
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.unitkerja.nm_unitkerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('reviewable_type')
                    ->label('Jenis Layanan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PeminjamanModel::class => 'Peminjaman',
                        PerbaikanModel::class => 'Perbaikan',
                        ReqDukunganModel::class => 'Dukungan',
                        default => 'Lainnya',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        PeminjamanModel::class => 'success',
                        PerbaikanModel::class => 'danger',
                        ReqDukunganModel::class => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        PeminjamanModel::class => 'heroicon-o-document-plus',
                        PerbaikanModel::class => 'heroicon-o-wrench',
                        ReqDukunganModel::class => 'heroicon-o-hand-raised',
                    })
                    ->sortable(),

                TextColumn::make('picName')
                    ->label('Ditangani Oleh')
                    ->state(fn (ReviewLayananModel $record): string => match($record->reviewable_type) {
                        PeminjamanModel::class => $record->reviewable?->admin?->name ?? '-',
                        PerbaikanModel::class => $record->reviewable?->teknisi?->name ?? '-',
                        ReqDukunganModel::class => $record->reviewable?->picDukungan?->name ?? '-',
                        default => '-',
                    })
                    ->icon('heroicon-o-user-circle')
                    ->toggleable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)." ({$state}/5)")
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('aspek_kecepatan')
                    ->label('Kecepatan')
                    ->formatStateUsing(fn (int $state): string => "{$state}/5")
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('aspek_kualitas')
                    ->label('Kualitas')
                    ->formatStateUsing(fn (int $state): string => "{$state}/5")
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('aspek_pelayanan')
                    ->label('Pelayanan')
                    ->formatStateUsing(fn (int $state): string => "{$state}/5")
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('komentar')
                    ->label('Komentar')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tgl Review')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Review Layanan')
                        ->modalWidth('xl')
                        ->infolist([
                            Section::make('Info Karyawan')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('user.name')
                                            ->label('Nama')
                                            ->icon('heroicon-m-user')
                                            ->weight('bold'),
                                        TextEntry::make('user.nip')
                                            ->label('NIP')
                                            ->icon('heroicon-m-identification'),
                                        TextEntry::make('user.unitkerja.nm_unitkerja')
                                            ->label('Unit Kerja')
                                            ->icon('heroicon-m-building-office'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Penilaian')
                                ->icon('heroicon-o-star')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('reviewable_type')
                                            ->label('Jenis Layanan')
                                            ->badge()
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                PeminjamanModel::class => 'Peminjaman',
                                                PerbaikanModel::class => 'Perbaikan',
                                                ReqDukunganModel::class => 'Dukungan',
                                                default => 'Lainnya',
                                            })
                                            ->color(fn (string $state): string => match ($state) {
                                                PeminjamanModel::class => 'success',
                                                PerbaikanModel::class => 'danger',
                                                ReqDukunganModel::class => 'info',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('created_at')
                                            ->label('Tanggal Review')
                                            ->dateTime('l, d F Y H:i')
                                            ->icon('heroicon-m-calendar'),
                                        TextEntry::make('rating')
                                            ->label('⭐ Rating Keseluruhan')
                                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)." ({$state}/5)")
                                            ->weight('bold'),
                                        TextEntry::make('aspek_kecepatan')
                                            ->label('⏱️ Kecepatan Layanan')
                                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)." ({$state}/5)"),
                                        TextEntry::make('aspek_kualitas')
                                            ->label('✅ Kualitas Hasil')
                                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)." ({$state}/5)"),
                                        TextEntry::make('aspek_pelayanan')
                                            ->label('🤝 Pelayanan Petugas')
                                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)." ({$state}/5)"),
                                    ]),
                                    TextEntry::make('komentar')
                                        ->label('Komentar')
                                        ->prose()
                                        ->placeholder('Tidak ada komentar'),
                                ]),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),
                ])
                    ->button()
                    ->label('Aksi')
                    ->icon('heroicon-m-chevron-down')
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('reviewable_type')
                    ->label('Jenis Layanan')
                    ->options([
                        PeminjamanModel::class => 'Peminjaman',
                        PerbaikanModel::class => 'Perbaikan',
                        ReqDukunganModel::class => 'Dukungan',
                    ])
                    ->placeholder('Semua Layanan'),

                SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '⭐ (1)',
                        2 => '⭐⭐ (2)',
                        3 => '⭐⭐⭐ (3)',
                        4 => '⭐⭐⭐⭐ (4)',
                        5 => '⭐⭐⭐⭐⭐ (5)',
                    ])
                    ->placeholder('Semua Rating'),

                SelectFilter::make('unit_kerja')
                    ->label('Unit Kerja')
                    ->relationship('user.unitkerja', 'nm_unitkerja')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Unit Kerja'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->extremePaginationLinks();
    }
}
