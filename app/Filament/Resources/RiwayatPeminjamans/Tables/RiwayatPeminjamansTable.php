<?php

namespace App\Filament\Resources\RiwayatPeminjamans\Tables;

use App\Models\PeminjamanModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RiwayatPeminjamansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with([
                'reqPinjam.user.jabatan',
                'reqPinjam.user.unitkerja',
                'reqPinjam.kategori',
                'barang.merek',
                'barang.kategori',
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
                        $html .= '<span class="font-semibold text-info-600 dark:text-info-400">' . $record->nomor_surat . '</span>';
                        $html .= '</div>';

                        // Nomor Pengembalian (Hijau) - hanya jika sudah dikembalikan
                        if ($record->status_peminjaman === 'dikembalikan' && $record->nomor_surat_pengembalian) {
                            $html .= '<div class="flex items-center gap-1">';
                            $html .= '<span class="text-xs">📄</span>';
                            $html .= '<span class="font-semibold text-success-600 dark:text-success-400">' . $record->nomor_surat_pengembalian . '</span>';
                            $html .= '</div>';
                        }

                        $html .= '</div>';

                        return $html;
                    })
                    ->html()
                    ->copyable()
                    ->copyableState(fn(PeminjamanModel $record): string => $record->nomor_surat . ($record->nomor_surat_pengembalian ? ' | ' . $record->nomor_surat_pengembalian : ''))
                    ->copyMessage('Nomor surat berhasil disalin'),

                TextColumn::make('reqPinjam.user.name')
                    ->label('Peminjam')
                    ->description(
                        fn(PeminjamanModel $record): string => $record->reqPinjam?->user?->nip .
                            ' • ' .
                            $record->reqPinjam?->user?->unitkerja?->nm_unitkerja
                    )
                    ->icon('heroicon-o-user')
                    ->searchable(['name', 'nip'])
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('barang.nama_barang')
                    ->label('Barang')
                    ->description(
                        fn(PeminjamanModel $record): string => ($record->barang?->merek?->nama_merek ?? '-') .
                            ' • ' .
                            ($record->barang?->kategori?->nama_kategori ?? '-')
                    )
                    ->icon('heroicon-o-cube')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight(FontWeight::Medium),

                TextColumn::make('tanggal_serah_terima')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->icon('heroicon-o-calendar')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('kondisi_barang')
                    ->label('Kondisi Pinjam')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak ringan' => 'warning',
                        'rusak berat' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'baik' => 'heroicon-o-check-circle',
                        'rusak ringan' => 'heroicon-o-exclamation-triangle',
                        'rusak berat' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->alignCenter(),

                TextColumn::make('tanggal_kembali')
                    ->label('Tgl Kembali')
                    ->date('d M Y')
                    ->icon('heroicon-o-calendar-days')
                    ->placeholder('-')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('kondisi_kembali')
                    ->label('Kondisi Kembali')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak ringan' => 'warning',
                        'rusak berat' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(?string $state): string => match ($state) {
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
                    ->color(fn(string $state): string => match ($state) {
                        'dipinjam' => 'info',
                        'dikembalikan' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'dipinjam' => 'heroicon-o-arrow-right-circle',
                        'dikembalikan' => 'heroicon-o-check-badge',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable()
                    ->alignCenter(),
            ])
            ->actions([
                ActionGroup::make([
                    // Download Tanda Terima Peminjaman
                    Action::make('download_tanda_terima')
                        ->label('📄 Peminjaman')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn(PeminjamanModel $record) => route('download.tanda-terima', $record))
                        ->openUrlInNewTab(),

                    // Download Tanda Terima Pengembalian
                    Action::make('download_tanda_terima_pengembalian')
                        ->label('📄 Pengembalian')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(fn(PeminjamanModel $record) => route('download.tanda-terima-pengembalian', $record))
                        ->openUrlInNewTab()
                        ->visible(
                            fn(PeminjamanModel $record) => $record->status_peminjaman === 'dikembalikan' &&
                                $record->nomor_surat_pengembalian !== null
                        ),
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
                    ->label('Kondisi Saat Pinjam')
                    ->options([
                        'baik' => 'Baik',
                        'rusak ringan' => 'Rusak Ringan',
                        'rusak berat' => 'Rusak Berat',
                    ])
                    ->placeholder('Semua Kondisi'),

                SelectFilter::make('kondisi_kembali')
                    ->label('Kondisi Saat Kembali')
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

                Filter::make('tanggal_pinjam')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_serah_terima', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_serah_terima', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Pinjam dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d M Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Pinjam sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d M Y');
                        }

                        return $indicators;
                    }),

                Filter::make('tanggal_kembali')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_kembali', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_kembali', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Kembali dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d M Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Kembali sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d M Y');
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
