<?php

namespace App\Filament\Resources\PengajuanPerbaikans\Tables;

use App\Models\PerbaikanModel;
use App\Services\NomorSuratService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PengajuanPerbaikansTable
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
                    ->searchable()
                    ->sortable()
                    ->description(fn (PerbaikanModel $record): string => $record->merek->nama_merek ?? '-'),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tgl. Pengajuan')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('d M Y'))
                    ->sortable()
                    ->description(fn (PerbaikanModel $record): string => $record->created_at->diffForHumans()),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->limit(35)
                    ->wrap()
                    ->placeholder('-'),

                BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'diproses' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'diajukan' => 'heroicon-o-paper-airplane',
                        'diproses' => 'heroicon-o-wrench',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi')
                    ->placeholder('-'),

                TextColumn::make('catatan_barang')
                    ->label('Instruksi Penempatan')
                    ->limit(35)
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['pemohon.jabatan', 'pemohon.unitkerja', 'kategori', 'merek', 'teknisi']))
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status_perbaikan')
                    ->label('Status')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                    ]),

                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload(),

                Filter::make('tgl_pengajuan')
                    ->label('Tanggal Pengajuan')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_pengajuan', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_pengajuan', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    // Lihat Detail - selalu tampil
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Perbaikan')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Informasi Pemohon')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
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
                                        TextEntry::make('status_perbaikan')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'diajukan' => 'warning',
                                                'diproses' => 'info',
                                                'selesai' => 'success',
                                                'tidak_bisa_diperbaiki' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'diajukan' => 'Diajukan',
                                                'diproses' => 'Diproses',
                                                'selesai' => 'Selesai',
                                                'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                                                default => $state,
                                            }),
                                    ]),
                                ])->collapsible(),

                            Section::make('Rincian Barang & Masalah')
                                ->icon('heroicon-o-computer-desktop')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('nm_barang')
                                            ->label('Nama Barang / Merek')
                                            ->html()
                                            ->formatStateUsing(fn ($record) => "<strong>{$record->nm_barang}</strong><br><small>".($record->merek->nama_merek ?? '-').'</small>'),
                                        TextEntry::make('keluhan')
                                            ->label('Keluhan / Kerusakan')
                                            ->prose()
                                            ->columnSpanFull(),
                                    ]),
                                ])->collapsible(),

                            Section::make('Instruksi Pengantaran Barang')
                                ->icon('heroicon-o-truck')
                                ->schema([
                                    TextEntry::make('catatan_barang')
                                        ->label('Instruksi dari Teknisi')
                                        ->placeholder('Belum ada instruksi')
                                        ->prose(),
                                ])
                                ->visible(fn ($record) => $record->status_perbaikan === 'diproses'),
                        ])
                        ->extraModalFooterActions([
                            Action::make('download_pdf_from_detail')
                                ->label('Download PDF')
                                ->icon('heroicon-o-document-arrow-down')
                                ->color('success')
                                ->visible(fn (PerbaikanModel $record): bool => ! empty($record->no_surat_perbaikan))
                                ->url(fn (PerbaikanModel $record): string => route('download.surat-perbaikan', $record))
                                ->openUrlInNewTab(),

                            Action::make('update_status_proses_from_detail')
                                ->label('Proses / Ambil Alih')
                                ->icon('heroicon-o-wrench-screwdriver')
                                ->color('warning')
                                ->visible(fn (PerbaikanModel $record): bool => $record->status_perbaikan === 'diajukan')
                                ->form([
                                    Textarea::make('catatan_barang')
                                        ->label('Instruksi Penempatan Barang')
                                        ->helperText('Diisi dengan instruksi lokasi peletakan/pengantaran barang rusak. Contoh: "Barang diantarkan ke ruang teknisi Gedung A lantai 2"')
                                        ->placeholder('Barang dapat diantarkan langsung ke ruangan...')
                                        ->rows(4),
                                ])
                                ->action(function (PerbaikanModel $record, array $data): void {
                                    $record->update([
                                        'status_perbaikan' => 'diproses',
                                        'teknisi_id' => auth()->id(),
                                        'catatan_barang' => $data['catatan_barang'] ?? null,
                                    ]);

                                    Notification::make()
                                        ->title('Status Berhasil Diperbarui')
                                        ->body('Perbaikan sedang diproses.')
                                        ->success()
                                        ->send();
                                }),

                            Action::make('update_status_selesai_from_detail')
                                ->label('Selesai')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->visible(fn (PerbaikanModel $record): bool => $record->status_perbaikan === 'diproses')
                                ->form([
                                    TextInput::make('serial_number')
                                        ->label('Serial Number')
                                        ->placeholder('Masukkan serial number barang')
                                        ->required(),
                                    Textarea::make('keterangan')
                                        ->label('Tindakan / Hasil Perbaikan')
                                        ->required()
                                        ->rows(3),
                                    Textarea::make('catatan_barang')
                                        ->label('Info Pengambilan Barang')
                                        ->helperText('Isi dengan informasi jam & lokasi pengambilan barang untuk disampaikan ke pemohon.')
                                        ->placeholder('Contoh: Barang dapat diambil mulai pukul 10.00 WIB di ruang teknisi Gedung A lt.2')
                                        ->rows(3),
                                ])
                                ->action(function (PerbaikanModel $record, array $data): void {
                                    $record->update([
                                        'status_perbaikan' => 'selesai',
                                        'serial_number' => $data['serial_number'],
                                        'keterangan' => $data['keterangan'],
                                        'catatan_barang' => $data['catatan_barang'] ?? $record->catatan_barang,
                                        'no_surat_perbaikan' => NomorSuratService::generatePerbaikan(),
                                    ]);

                                    Notification::make()
                                        ->title('Perbaikan Selesai')
                                        ->body('Data berhasil diperbarui.')
                                        ->success()
                                        ->send();
                                }),

                            Action::make('tidak_bisa_diperbaiki_from_detail')
                                ->label('Tidak Bisa Diperbaiki')
                                ->icon('heroicon-o-x-circle')
                                ->color('danger')
                                ->visible(fn (PerbaikanModel $record): bool => $record->status_perbaikan === 'diproses')
                                ->form([
                                    Textarea::make('keterangan')
                                        ->label('Alasan Tidak Bisa Diperbaiki')
                                        ->required()
                                        ->rows(3),
                                ])
                                ->action(function (PerbaikanModel $record, array $data): void {
                                    $record->update([
                                        'status_perbaikan' => 'tidak_bisa_diperbaiki',
                                        'keterangan' => $data['keterangan'],
                                    ]);

                                    Notification::make()
                                        ->title('Status Diperbarui')
                                        ->body('Item ditandai sebagai tidak bisa diperbaiki.')
                                        ->warning()
                                        ->send();
                                }),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),

                    // Proses / Ambil Alih Action (only for diajukan)
                    Action::make('proses')
                        ->label('Proses / Ambil Alih')
                        ->icon('heroicon-o-wrench')
                        ->color('warning')
                        ->visible(fn (PerbaikanModel $record): bool => $record->status_perbaikan === 'diajukan')
                        ->form([
                            Textarea::make('catatan_barang')
                                ->label('Instruksi Penempatan Barang')
                                ->helperText('Diisi dengan instruksi lokasi peletakan/pengantaran barang rusak. Contoh: "Barang diantarkan ke ruang teknisi Gedung A lantai 2"')
                                ->placeholder('Barang dapat diantarkan langsung ke ruangan...')
                                ->rows(4),
                        ])
                        ->action(function (PerbaikanModel $record, array $data): void {
                            $record->update([
                                'status_perbaikan' => 'diproses',
                                'teknisi_id' => Auth::id(),
                                'catatan_barang' => $data['catatan_barang'] ?? null,
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Perbaikan Diproses')
                                ->body('Item perbaikan berhasil diambil alih dan diproses.')
                        ),

                    // Selesai Action (only for diproses)
                    Action::make('selesai')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (PerbaikanModel $record): bool => $record->status_perbaikan === 'diproses')
                        ->form([
                            TextInput::make('serial_number')
                                ->label('Serial Number')
                                ->placeholder('Masukkan serial number barang')
                                ->required(),
                            Textarea::make('keterangan')
                                ->label('Tindakan / Hasil Perbaikan')
                                ->required()
                                ->rows(3),
                            Textarea::make('catatan_barang')
                                ->label('Info Pengambilan Barang')
                                ->helperText('Isi dengan informasi jam & lokasi pengambilan barang untuk disampaikan ke pemohon.')
                                ->placeholder('Contoh: Barang dapat diambil mulai pukul 10.00 WIB di ruang teknisi Gedung A lt.2')
                                ->rows(3),
                        ])
                        ->action(function (PerbaikanModel $record, array $data): void {
                            $record->update([
                                'status_perbaikan' => 'selesai',
                                'serial_number' => $data['serial_number'],
                                'keterangan' => $data['keterangan'],
                                'catatan_barang' => $data['catatan_barang'] ?? $record->catatan_barang,
                                'no_surat_perbaikan' => NomorSuratService::generatePerbaikan(),
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Perbaikan Selesai')
                                ->body('Item perbaikan berhasil diselesaikan dan surat telah digenerate.')
                        ),

                    // Tidak Bisa Diperbaiki Action (only for diproses)
                    Action::make('tidak_bisa_diperbaiki')
                        ->label('Tidak Bisa Diperbaiki')
                        ->icon('heroicon-o-exclamation-circle')
                        ->color('danger')
                        ->visible(fn (PerbaikanModel $record): bool => $record->status_perbaikan === 'diproses')
                        ->form([
                            Textarea::make('keterangan')
                                ->label('Alasan Tidak Bisa Diperbaiki')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (PerbaikanModel $record, array $data): void {
                            $record->update([
                                'status_perbaikan' => 'tidak_bisa_diperbaiki',
                                'keterangan' => $data['keterangan'],
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->warning()
                                ->title('Status Diperbarui')
                                ->body('Item perbaikan ditandai sebagai tidak bisa diperbaiki.')
                        ),

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
