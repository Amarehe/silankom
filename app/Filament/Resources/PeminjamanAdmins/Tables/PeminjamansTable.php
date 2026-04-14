<?php

namespace App\Filament\Resources\PeminjamanAdmins\Tables;

use App\Models\BarangModel;
use App\Models\PeminjamanModel;
use App\Services\NomorSuratService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PeminjamansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with([
                'reqPinjam.user.unitkerja',
                'reqPinjam.user.jabatan',
                'barang.kategori',
                'barang.merek',
                'admin',
            ]))
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (PeminjamanModel $record): string {
                        $html = '<div class="space-y-1">';
                        // Nomor Peminjaman (Biru)
                        $html .= '<div class="flex items-center gap-1">';
                        $html .= '<span class="text-xs">📄</span>';
                        $html .= '<span class="font-semibold text-info-600 dark:text-info-400">'.$record->nomor_surat.'</span>';
                        $html .= '</div>';

                        // Nomor Pengembalian (Hijau) - hanya jika sudah dikembalikan
                        if ($record->status_peminjaman === 'dikembalikan' && $record->nomor_surat_pengembalian) {
                            $html .= '<div class="flex items-center gap-1">';
                            $html .= '<span class="text-xs">📄</span>';
                            $html .= '<span class="font-semibold text-success-600 dark:text-success-400">'.$record->nomor_surat_pengembalian.'</span>';
                            $html .= '</div>';
                        }

                        $html .= '</div>';

                        return $html;
                    })
                    ->html()
                    ->copyable()
                    ->copyableState(fn (PeminjamanModel $record): string => $record->nomor_surat.($record->nomor_surat_pengembalian ? ' | '.$record->nomor_surat_pengembalian : ''))
                    ->copyMessage('Nomor surat berhasil disalin'),

                TextColumn::make('reqPinjam.user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn (PeminjamanModel $record): string => ($record->reqPinjam->user->nip ?? '-').' • '.($record->reqPinjam->user->unitkerja->nm_unitkerja ?? '-')
                    )
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('barang.nama_barang')
                    ->label('Barang')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn (PeminjamanModel $record): string => ($record->barang->kategori->nama_kategori ?? '-').' • '.($record->barang->merek->nama_merek ?? '-')
                    )
                    ->icon('heroicon-o-cube')
                    ->wrap(),

                TextColumn::make('tanggal_serah_terima')
                    ->label('Tanggal Pinjam')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->sortable()
                    ->description(
                        fn (PeminjamanModel $record): string => $record->created_at->diffForHumans()
                    )
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('kondisi_barang')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak ringan' => 'warning',
                        'rusak berat' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'baik' => 'heroicon-o-check-circle',
                        'rusak ringan' => 'heroicon-o-exclamation-triangle',
                        'rusak berat' => 'heroicon-o-x-circle',
                    }),

                TextColumn::make('tanggal_kembali')
                    ->label('Tgl Kembali')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y') : '-')
                    ->icon('heroicon-o-calendar-days')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('kondisi_kembali')
                    ->label('Kondisi Kembali')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak ringan' => 'warning',
                        'rusak berat' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match ($state) {
                        'baik' => 'heroicon-o-check-circle',
                        'rusak ringan' => 'heroicon-o-exclamation-triangle',
                        'rusak berat' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-minus-circle',
                    })
                    ->placeholder('Belum Kembali')
                    ->alignCenter(),

                TextColumn::make('status_peminjaman')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dipinjam' => 'info',
                        'dikembalikan' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'dipinjam' => 'heroicon-o-arrow-right-circle',
                        'dikembalikan' => 'heroicon-o-check-badge',
                    })
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    // View Detail Action
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Peminjaman')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Informasi Peminjam')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('reqPinjam.user.name')
                                            ->label('Nama Lengkap')
                                            ->icon('heroicon-m-user')
                                            ->weight('bold'),
                                        TextEntry::make('reqPinjam.user.nip')
                                            ->label('NIP')
                                            ->icon('heroicon-m-identification')
                                            ->placeholder('-'),
                                        TextEntry::make('reqPinjam.user.jabatan.nm_jabatan')
                                            ->label('Jabatan')
                                            ->icon('heroicon-m-briefcase')
                                            ->placeholder('-'),
                                        TextEntry::make('reqPinjam.user.unitkerja.nm_unitkerja')
                                            ->label('Unit Kerja')
                                            ->icon('heroicon-m-building-office'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Data Peminjaman')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('nomor_surat')
                                            ->label('Nomor Surat Peminjaman')
                                            ->icon('heroicon-m-document-text')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->copyable(),
                                        TextEntry::make('barang.nama_barang')
                                            ->label('Nama Barang')
                                            ->icon('heroicon-m-computer-desktop'),
                                        TextEntry::make('barang.kategori.nama_kategori')
                                            ->label('Kategori')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('barang.merek.nama_merek')
                                            ->label('Merek')
                                            ->icon('heroicon-m-tag')
                                            ->placeholder('-'),
                                        TextEntry::make('tanggal_serah_terima')
                                            ->label('Tanggal Serah Terima')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                        TextEntry::make('kondisi_barang')
                                            ->label('Kondisi Barang')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'baik' => 'success',
                                                'rusak ringan' => 'warning',
                                                'rusak berat' => 'danger',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('status_peminjaman')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'dipinjam' => 'info',
                                                'dikembalikan' => 'success',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'dipinjam' => 'Dipinjam',
                                                'dikembalikan' => 'Dikembalikan',
                                                default => ucfirst($state),
                                            }),
                                    ]),
                                ])->collapsible(),

                            Section::make('Kelengkapan & Catatan')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('kelengkapan')
                                            ->label('Kelengkapan')
                                            ->prose()
                                            ->placeholder('-'),
                                        TextEntry::make('catatan_admin')
                                            ->label('Catatan Admin')
                                            ->prose()
                                            ->placeholder('-'),
                                    ]),
                                ])
                                ->visible(fn (PeminjamanModel $record) => ! empty($record->kelengkapan) || ! empty($record->catatan_admin)),

                            Section::make('Info Pengembalian')
                                ->icon('heroicon-o-arrow-uturn-left')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('nomor_surat_pengembalian')
                                            ->label('Nomor Surat Pengembalian')
                                            ->icon('heroicon-m-document-text')
                                            ->weight('bold')
                                            ->color('success')
                                            ->copyable()
                                            ->placeholder('-'),
                                        TextEntry::make('tanggal_kembali')
                                            ->label('Tanggal Kembali')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar-days'),
                                        TextEntry::make('kondisi_kembali')
                                            ->label('Kondisi Kembali')
                                            ->badge()
                                            ->color(fn (?string $state): string => match ($state) {
                                                'baik' => 'success',
                                                'rusak ringan' => 'warning',
                                                'rusak berat' => 'danger',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('catatan_pengembalian')
                                            ->label('Catatan Pengembalian')
                                            ->prose()
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ]),
                                ])
                                ->visible(fn (PeminjamanModel $record) => $record->status_peminjaman === 'dikembalikan'),
                        ])
                        ->extraModalFooterActions([
                            Action::make('download_peminjaman_from_detail')
                                ->label('📄 Peminjaman')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('info')
                                ->url(fn (PeminjamanModel $record) => route('download.tanda-terima', $record))
                                ->openUrlInNewTab(),
                            Action::make('download_pengembalian_from_detail')
                                ->label('📄 Pengembalian')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->visible(fn (PeminjamanModel $record) => $record->status_peminjaman === 'dikembalikan' && $record->nomor_surat_pengembalian)
                                ->url(fn (PeminjamanModel $record) => route('download.tanda-terima-pengembalian', $record))
                                ->openUrlInNewTab(),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),

                    // Return Action (Modal dengan Form)
                    Action::make('return')
                        ->label('Proses Pengembalian')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->visible(fn (PeminjamanModel $record) => $record->status_peminjaman === 'dipinjam')
                        ->modalHeading('Proses Pengembalian Barang')
                        ->modalDescription('Silakan isi form pengembalian barang di bawah ini')
                        ->modalWidth('xl')
                        ->form([
                            DatePicker::make('tanggal_kembali')
                                ->label('Tanggal Pengembalian')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->maxDate(now()),

                            Select::make('kondisi_kembali')
                                ->label('Kondisi Barang Saat Dikembalikan')
                                ->options([
                                    'baik' => 'Baik',
                                    'rusak ringan' => 'Rusak Ringan',
                                    'rusak berat' => 'Rusak Berat',
                                ])
                                ->required()
                                ->default('baik')
                                ->native(false),

                            Textarea::make('catatan_pengembalian')
                                ->label('Catatan Pengembalian')
                                ->placeholder('Catatan kondisi atau kejadian saat pengembalian (opsional)')
                                ->rows(3),
                        ])
                        ->action(function (PeminjamanModel $record, array $data) {
                            // Generate nomor surat pengembalian
                            $nomorSuratPengembalian = NomorSuratService::generatePengembalian();

                            $record->update([
                                'status_peminjaman' => 'dikembalikan',
                                'tanggal_kembali' => $data['tanggal_kembali'],
                                'kondisi_kembali' => $data['kondisi_kembali'],
                                'catatan_pengembalian' => $data['catatan_pengembalian'] ?? null,
                                'admin_penerima_id' => Auth::id(),
                                'nomor_surat_pengembalian' => $nomorSuratPengembalian,
                            ]);

                            // Update kondisi barang - mapping dari kondisi_kembali ke kondisi barang
                            if ($data['kondisi_kembali'] !== 'baik') {
                                // Map kondisi_kembali to barang kondisi enum values
                                $kondisiBarang = match ($data['kondisi_kembali']) {
                                    'rusak ringan' => 'perlu_perbaikan',
                                    'rusak berat' => 'rusak',
                                    default => 'baik',
                                };

                                BarangModel::where('id_barang', $record->barang_id)
                                    ->update(['kondisi' => $kondisiBarang]);
                            }
                        })
                        ->successNotificationTitle('Pengembalian berhasil diproses')
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Pengembalian Berhasil')
                                ->body('Barang telah berhasil dikembalikan dan status telah diperbarui.')
                        )
                        ->modalSubmitActionLabel('Proses Pengembalian')
                        ->modalCancelActionLabel('Batal'),

                    // Download PDF Tanda Terima Peminjaman
                    Action::make('download_tanda_terima')
                        ->label('📄 Peminjaman')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (PeminjamanModel $record) => route('download.tanda-terima', $record))
                        ->openUrlInNewTab(),

                    // Download PDF Tanda Terima Pengembalian
                    Action::make('download_tanda_terima_pengembalian')
                        ->label('📄 Pengembalian')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn (PeminjamanModel $record) => $record->status_peminjaman === 'dikembalikan' && $record->nomor_surat_pengembalian)
                        ->url(fn (PeminjamanModel $record) => route('download.tanda-terima-pengembalian', $record))
                        ->openUrlInNewTab(),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size('sm')
                    ->color('warning')
                    ->button(),
            ])
            ->filters([
                SelectFilter::make('status_peminjaman')
                    ->label('Status Peminjaman')
                    ->options([
                        'dipinjam' => 'Sedang Dipinjam',
                        'dikembalikan' => 'Sudah Dikembalikan',
                    ])
                    ->placeholder('Semua Status'),

                SelectFilter::make('kondisi_barang')
                    ->label('Kondisi Barang')
                    ->options([
                        'baik' => 'Baik',
                        'rusak ringan' => 'Rusak Ringan',
                        'rusak berat' => 'Rusak Berat',
                    ])
                    ->placeholder('Semua Kondisi'),

                SelectFilter::make('kategori')
                    ->label('Kategori Barang')
                    ->relationship('barang.kategori', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kategori'),

                SelectFilter::make('unit_kerja')
                    ->label('Unit Kerja Peminjam')
                    ->relationship('reqPinjam.user.unitkerja', 'nm_unitkerja')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Unit Kerja'),

                Filter::make('tanggal_serah_terima')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->placeholder('Pilih tanggal awal')
                            ->native(false),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->placeholder('Pilih tanggal akhir')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_serah_terima', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_serah_terima', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: '.\Carbon\Carbon::parse($data['dari'])->format('d M Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: '.\Carbon\Carbon::parse($data['sampai'])->format('d M Y');
                        }

                        return $indicators;
                    }),
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
