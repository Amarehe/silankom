<?php

namespace App\Filament\Resources\KelolaPerbaikans\Tables;

use App\Models\PerbaikanModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KelolaPerbaikansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                TextColumn::make('nodis')
                    ->label('No. Nota Dinas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->weight('bold')
                    ->color('primary')
                    ->placeholder('-')
                    ->icon('heroicon-o-document-text')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('pemohon.name')
                    ->label('Pemohon')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('pemohon', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhereHas('unitkerja', function (Builder $query) use ($search) {
                                    $query->where('nm_unitkerja', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->sortable()
                    ->description(fn (PerbaikanModel $record): string => $record->pemohon->unitkerja->nm_unitkerja ?? '-')
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('nm_barang')
                    ->label('Nama Barang')
                    ->searchable(['nm_barang', 'serial_number'])
                    ->sortable()
                    ->description(fn (PerbaikanModel $record): string => 'SN: '.($record->serial_number ?? '-')),

                BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'selesai' => 'success',
                        'tidak_bisa_diperbaiki' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'selesai' => 'Selesai',
                        'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'selesai' => 'heroicon-o-check-circle',
                        'tidak_bisa_diperbaiki' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi')
                    ->placeholder('-'),

                TextColumn::make('catatan_barang')
                    ->label('Info Pengambilan')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('updated_at')
                    ->label('Tgl. Selesai')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('d M Y'))
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['pemohon.jabatan', 'pemohon.unitkerja', 'kategori', 'merek', 'teknisi']))
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status_perbaikan')
                    ->label('Status')
                    ->options([
                        'selesai' => 'Selesai',
                        'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                    ]),

                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload(),

                Filter::make('updated_at')
                    ->label('Tanggal Selesai')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Dari'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Riwayat Perbaikan')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Informasi Pemohon')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('nodis')
                                            ->label('No. Nota Dinas')
                                            ->icon('heroicon-m-document-text')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->placeholder('-'),
                                        TextEntry::make('pemohon.name')
                                            ->label('Nama Pemohon')
                                            ->icon('heroicon-m-user'),
                                        TextEntry::make('pemohon.unitkerja.nm_unitkerja')
                                            ->label('Unit Kerja')
                                            ->icon('heroicon-m-building-office'),
                                        TextEntry::make('tgl_pengajuan')
                                            ->label('Tanggal Pengajuan')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                        TextEntry::make('nodis')
                                            ->label('No. Nota Dinas')
                                            ->placeholder('-')
                                            ->badge()
                                            ->color('warning')
                                            ->icon('heroicon-m-document-text'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Rincian Perbaikan')
                                ->icon('heroicon-o-wrench-screwdriver')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('kategori.nama_kategori')
                                            ->label('Kategori')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('nm_barang')
                                            ->label('Nama Barang')
                                            ->icon('heroicon-m-computer-desktop'),
                                        TextEntry::make('serial_number')
                                            ->label('Serial Number')
                                            ->icon('heroicon-m-hashtag')
                                            ->placeholder('-'),
                                        TextEntry::make('no_surat_perbaikan')
                                            ->label('No. Surat')
                                            ->icon('heroicon-m-document-text')
                                            ->placeholder('-'),
                                        TextEntry::make('updated_at')
                                            ->label('Tanggal Selesai')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-check-badge'),
                                        TextEntry::make('status_perbaikan')
                                            ->label('Status Akhir')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'selesai' => 'success',
                                                'tidak_bisa_diperbaiki' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'selesai' => 'Selesai',
                                                'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                                                default => $state,
                                            }),
                                    ]),
                                ])->collapsible(),

                            Section::make('Keluhan & Hasil Perbaikan')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('keluhan')
                                            ->label('Keluhan / Kerusakan')
                                            ->prose(),
                                        TextEntry::make('keterangan')
                                            ->label('Tindakan / Hasil Perbaikan')
                                            ->prose(),
                                    ]),
                                ]),

                            Section::make('Info Pengambilan Barang')
                                ->icon('heroicon-o-archive-box-arrow-down')
                                ->schema([
                                    TextEntry::make('catatan_barang')
                                        ->label('Instruksi')
                                        ->placeholder('-')
                                        ->prose(),
                                ])
                                ->visible(fn (PerbaikanModel $record) => ! empty($record->catatan_barang)),
                        ])
                        ->extraModalFooterActions([
                            Action::make('download_pdf_from_detail')
                                ->label('Download PDF')
                                ->icon('heroicon-o-document-arrow-down')
                                ->color('success')
                                ->visible(fn (PerbaikanModel $record): bool => ! empty($record->no_surat_perbaikan))
                                ->url(fn (PerbaikanModel $record): string => route('download.surat-perbaikan', $record))
                                ->openUrlInNewTab(),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),

                    Action::make('download_surat')
                        ->label('Download PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->visible(fn (PerbaikanModel $record): bool => ! empty($record->no_surat_perbaikan))
                        ->url(fn (PerbaikanModel $record): string => route('download.surat-perbaikan', $record))
                        ->openUrlInNewTab(),

                    EditAction::make()
                        ->visible(fn () => auth()?->user()?->role_id === 1),
                    DeleteAction::make()
                        ->visible(fn () => auth()?->user()?->role_id === 1),
                ])
                    ->button()
                    ->label('Aksi')
                    ->icon('heroicon-m-chevron-down')
                    ->color('primary'),
            ]);
    }
}
