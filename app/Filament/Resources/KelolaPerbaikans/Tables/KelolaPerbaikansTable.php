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
use Illuminate\Support\HtmlString;

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

                TextColumn::make('nm_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->placeholder('-'),

                BadgeColumn::make('status_perbaikan')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'selesai' => 'success',
                        'tidak_bisa_diperbaiki' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'selesai' => 'Selesai',
                        'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'selesai' => 'heroicon-o-check-circle',
                        'tidak_bisa_diperbaiki' => 'heroicon-o-x-circle',
                    }),

                TextColumn::make('no_surat_perbaikan')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi')
                    ->placeholder('-'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('updated_at')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn($query) => $query->with(['pemohon.jabatan', 'pemohon.unitkerja', 'kategori', 'merek', 'teknisi']))
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
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    // Lihat Detail
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Perbaikan')
                        ->modalWidth('3xl')
                        ->modalDescription(fn(PerbaikanModel $record) => new HtmlString("
                            <div style=\"margin: -24px; padding: 20px 16px 16px 16px;\">
                                <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;\">
                                    <div style=\"border-left: 4px solid #3B82F6; background: #F3F4F6; padding: 18px; width: 100%; box-sizing: border-box;\">
                                        <h3 style=\"margin: 0 0 14px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;\">DATA PEMOHON</h3>
                                        <div style=\"display: flex; flex-direction: column; gap: 14px;\">
                                            <div>
                                                <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Nama Lengkap</div>
                                                <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->pemohon->name ?? '-') . "</div>
                                            </div>
                                            <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 14px;\">
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">NIP</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->pemohon->nip ?? '-') . "</div>
                                                </div>
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Jabatan</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->pemohon->jabatan->nm_jabatan ?? '-') . "</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Unit Kerja</div>
                                                <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->pemohon->unitkerja->nm_unitkerja ?? '-') . "</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style=\"border-left: 4px solid #10B981; background: #F3F4F6; padding: 18px; width: 100%; box-sizing: border-box;\">
                                        <h3 style=\"margin: 0 0 14px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;\">RINCIAN PERBAIKAN</h3>
                                        <div style=\"display: flex; flex-direction: column; gap: 14px;\">
                                            <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 14px;\">
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Kategori Barang</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->kategori->nama_kategori ?? '-') . "</div>
                                                </div>
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Merek</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->merek->nama_merek ?? '-') . "</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Nama Barang</div>
                                                <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->nm_barang ?? '-') . "</div>
                                            </div>
                                            <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 14px;\">
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Serial Number</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->serial_number ?? '-') . "</div>
                                                </div>
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Jumlah</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . $record->jumlah . " unit</div>
                                                </div>
                                            </div>
                                            <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 14px;\">
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Nomor Nota Dinas</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->nodis ?? '-') . "</div>
                                                </div>
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Nomor Surat Perbaikan</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->no_surat_perbaikan ?? '-') . "</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style=\"border-left: 4px solid #F59E0B; background: #F3F4F6; padding: 18px; margin-bottom: 10px;\">
                                    <h3 style=\"margin: 0 0 14px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;\">KELUHAN & HASIL</h3>
                                    <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 14px;\">
                                        <div>
                                            <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Keluhan / Kerusakan</div>
                                            <div style=\"font-size: 14px; color: #111827; line-height: 1.6;\">" . nl2br(e($record->keluhan ?? '-')) . "</div>
                                        </div>
                                        <div>
                                            <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Keterangan / Hasil</div>
                                            <div style=\"font-size: 14px; color: #111827; line-height: 1.6;\">" . nl2br(e($record->keterangan ?? '-')) . "</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        "))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),

                    // Download PDF
                    Action::make('download_surat')
                        ->label('Download PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->visible(fn(PerbaikanModel $record): bool => !empty($record->no_surat_perbaikan))
                        ->url(fn(PerbaikanModel $record): string => route('download.surat-perbaikan', $record))
                        ->openUrlInNewTab(),
                ]),
            ]);
    }
}
