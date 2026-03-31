<?php

namespace App\Filament\Resources\PengajuanPerbaikans\Tables;

use App\Models\PerbaikanModel;
use App\Services\NomorSuratService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

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
                    ->label('Kategori Barang')
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
                    ->suffix(' unit')
                    ->weight('bold'),

                TextColumn::make('nodis')
                    ->label('Nomor Nota Dinas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn(PerbaikanModel $record): string => $record->created_at->diffForHumans()),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-'),

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
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'proses' => 'heroicon-o-wrench',
                        'selesai' => 'heroicon-o-check-circle',
                        'ditolak' => 'heroicon-o-x-circle',
                        'rusak' => 'heroicon-o-x-circle',
                    }),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi')
                    ->placeholder('-'),

                TextColumn::make('catatan_teknisi')
                    ->label('Keterangan')
                    ->limit(30)
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->modifyQueryUsing(fn($query) => $query->with(['pemohon.jabatan', 'pemohon.unitkerja', 'kategori', 'merek', 'teknisi']))
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
                ActionGroup::make([
                    // View Detail Action
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Perbaikan')
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
                                        <h3 style=\"margin: 0 0 14px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;\">RINCIAN PENGAJUAN</h3>
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
                                                <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->nama_barang ?? '-') . "</div>
                                            </div>
                                            <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 14px;\">
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Jumlah</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . $record->jumlah . " unit</div>
                                                </div>
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">NOMOR NOTA DINAS</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->nodis ?? '-') . "</div>
                                                </div>
                                            </div>
                                            <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 14px;\">
                                                <div>
                                                    <div style=\"font-size: 11px; color: #6B7280; margin-bottom: 4px;\">Tanggal Pengajuan</div>
                                                    <div style=\"font-size: 14px; font-weight: 600; color: #111827;\">" . ($record->tgl_pengajuan?->format('d F Y') ?? '-') . "</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style=\"border-left: 4px solid #F59E0B; background: #F3F4F6; padding: 18px; margin-bottom: 10px;\">
                                    <h3 style=\"margin: 0 0 14px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;\">KELUHAN / DESKRIPSI MASALAH</h3>
                                    <div style=\"font-size: 14px; color: #111827; line-height: 1.6;\">" . nl2br(e($record->keluhan ?? '-')) . "</div>
                                </div>
                            </div>
                        "))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),

                    // Proses / Ambil Alih Action (only for pending)
                    Action::make('proses')
                        ->label('Proses / Ambil Alih')
                        ->icon('heroicon-o-wrench')
                        ->color('warning')
                        ->visible(fn(PerbaikanModel $record): bool => $record->status_perbaikan === 'pending')
                        ->form([
                            TextInput::make('serial_number')
                                ->label('Serial Number')
                                ->required(),
                            Textarea::make('catatan_teknisi')
                                ->label('Catatan')
                                ->rows(3),
                        ])
                        ->action(function (PerbaikanModel $record, array $data): void {
                            $record->update([
                                'status_perbaikan' => 'proses',
                                'teknisi_id' => Auth::id(),
                                'serial_number' => $data['serial_number'],
                                'catatan_teknisi' => $data['catatan_teknisi'] ?? $record->catatan_teknisi,
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Perbaikan Diproses')
                                ->body('Item perbaikan berhasil diambil alih dan diproses.')
                        ),

                    // Selesai Action (only for proses)
                    Action::make('selesai')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(PerbaikanModel $record): bool => $record->status_perbaikan === 'proses')
                        ->form([
                            Textarea::make('tindakan')
                                ->label('Tindakan/Hasil Perbaikan')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (PerbaikanModel $record, array $data): void {
                            $record->update([
                                'status_perbaikan' => 'selesai',
                                'tindakan' => $data['tindakan'],
                                'tgl_perbaikan' => today(),
                                'no_surat' => NomorSuratService::generatePerbaikan(),
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Perbaikan Selesai')
                                ->body('Item perbaikan berhasil diselesaikan dan surat telah digenerate.')
                        ),

                    // Tolak Action (only for pending)
                    Action::make('tolak')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(PerbaikanModel $record): bool => $record->status_perbaikan === 'pending')
                        ->form([
                            Textarea::make('alasan_ditolak')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (PerbaikanModel $record, array $data): void {
                            $record->update([
                                'status_perbaikan' => 'ditolak',
                                'alasan_ditolak' => $data['alasan_ditolak'],
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->warning()
                                ->title('Pengajuan Ditolak')
                                ->body('Pengajuan perbaikan berhasil ditolak.')
                        ),

                    // Tidak Bisa Diperbaiki Action (only for proses)
                    Action::make('tidak_bisa_diperbaiki')
                        ->label('Tidak Bisa Diperbaiki')
                        ->icon('heroicon-o-exclamation-circle')
                        ->color('danger')
                        ->visible(fn(PerbaikanModel $record): bool => $record->status_perbaikan === 'proses')
                        ->form([
                            Textarea::make('alasan_ditolak')
                                ->label('Alasan Tidak Bisa Diperbaiki')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (PerbaikanModel $record, array $data): void {
                            $record->update([
                                'status_perbaikan' => 'rusak',
                                'alasan_ditolak' => $data['alasan_ditolak'],
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->warning()
                                ->title('Status Diperbarui')
                                ->body('Item perbaikan ditandai sebagai tidak bisa diperbaiki.')
                        ),
                ]),
            ]);
    }
}
