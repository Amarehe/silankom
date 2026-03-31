<?php

namespace App\Filament\Resources\KelolaPerbaikans\Tables;

use App\Models\PerbaikanModel;
use Filament\Actions\Action;
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

                TextColumn::make('pemohon.name')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable()
                    ->description(fn(PerbaikanModel $record): string => $record->pemohon->nip ?? '-')
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('pemohon.unitkerja.nm_unitkerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->wrap()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('merek.nama_merek')
                    ->label('Merek')
                    ->searchable(),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->alignCenter()
                    ->suffix(' unit'),

                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'proses' => 'info',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        'rusak' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'proses' => 'Proses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        'rusak' => 'Rusak',
                    }),

                TextColumn::make('no_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi')
                    ->placeholder('-'),

                TextColumn::make('catatan_teknisi')
                    ->label('Keterangan')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->modifyQueryUsing(fn($query) => $query->with(['pemohon.unitkerja', 'kategori', 'merek', 'teknisi']))
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status_perbaikan')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'proses' => 'Proses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        'rusak' => 'Rusak',
                    ]),

                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_pengajuan', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_pengajuan', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('download_surat')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn(PerbaikanModel $record): bool => !empty($record->no_surat))
                    ->url(fn(PerbaikanModel $record): string => route('download.surat-perbaikan', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
