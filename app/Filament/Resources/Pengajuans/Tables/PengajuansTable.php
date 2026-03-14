<?php

namespace App\Filament\Resources\Pengajuans\Tables;

use App\Models\BarangModel;
use App\Models\PeminjamanModel;
use App\Models\ReqPinjamModel;
use App\Services\NomorSuratService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class PengajuansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                TextColumn::make('user.name')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable()
                    ->description(fn(ReqPinjamModel $record): string => $record->user->nip ?? '-')
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('user.unitkerja.nm_unitkerja')
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

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->alignCenter()
                    ->suffix(' Unit')
                    ->weight('bold'),

                TextColumn::make('tanggal_request')
                    ->label('Tanggal Pengajuan')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn(ReqPinjamModel $record): string => $record->created_at->diffForHumans()),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diproses' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'diproses' => 'heroicon-o-clock',
                        'disetujui' => 'heroicon-o-check-circle',
                        'ditolak' => 'heroicon-o-x-circle',
                    })
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->tooltip(fn(ReqPinjamModel $record): ?string => $record->keterangan)
                    ->wrap()
                    ->placeholder('Tidak ada keterangan'),
            ])
            ->modifyQueryUsing(fn($query) => $query->with(['user.jabatan', 'user.unitkerja', 'kategori']))
            ->actions([
                ActionGroup::make([
                    // View Detail Action
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Peminjaman')
                        ->modalWidth('3xl')
                        ->modalDescription(fn(ReqPinjamModel $record) => new HtmlString('
                            <div style="margin: -24px; padding: 20px 16px 16px 16px;">
                                
                                <!-- Grid Container untuk 2 Card Utama -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    
                                    <!-- DATA PEMOHON Card (Kiri) -->
                                    <div style="border-left: 4px solid #3B82F6; background: #F3F4F6; padding: 18px; width: 100%; box-sizing: border-box;">
                                        <h3 style="margin: 0 0 14px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;">DATA PEMOHON</h3>
                                        <div style="display: flex; flex-direction: column; gap: 14px;">
                                            <div>
                                                <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">Nama Lengkap</div>
                                                <div style="font-size: 14px; font-weight: 600; color: #111827;">' . ($record->user->name ?? '-') . '</div>
                                            </div>
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                                                <div>
                                                    <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">NIP</div>
                                                    <div style="font-size: 14px; font-weight: 600; color: #111827;">' . ($record->user->nip ?? '-') . '</div>
                                                </div>
                                                <div>
                                                    <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">Jabatan</div>
                                                    <div style="font-size: 14px; font-weight: 600; color: #111827;">' . ($record->user->jabatan->nm_jabatan ?? '-') . '</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">Unit Kerja</div>
                                                <div style="font-size: 14px; font-weight: 600; color: #111827;">' . ($record->user->unitkerja->nm_unitkerja ?? '-') . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- RINCIAN PENGAJUAN Card (Kanan) -->
                                    <div style="border-left: 4px solid #10B981; background: #F3F4F6; padding: 18px; width: 100%; box-sizing: border-box;">
                                        <h3 style="margin: 0 0 14px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;">RINCIAN PENGAJUAN</h3>
                                        <div style="display: flex; flex-direction: column; gap: 14px;">
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                                                <div>
                                                    <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">Kategori Barang</div>
                                                    <div style="font-size: 14px; font-weight: 600; color: #111827;">' . ($record->kategori->nama_kategori ?? '-') . '</div>
                                                </div>
                                                <div>
                                                    <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">Jumlah</div>
                                                    <div style="font-size: 14px; font-weight: 600; color: #111827;">' . $record->jumlah . ' unit</div>
                                                </div>
                                            </div>
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                                                <div>
                                                    <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">Tanggal Pengajuan</div>
                                                    <div style="font-size: 14px; font-weight: 600; color: #111827;">' . date('d F Y', strtotime($record->tanggal_request)) . '</div>
                                                </div>
                                                <div>
                                                    <div style="font-size: 11px; color: #6B7280; margin-bottom: 4px;">Status</div>
                                                    <div>
                                                        ' . match ($record->status) {
                            'diproses' => '<span style="display: inline-block; padding: 4px 12px; background: #FEF3C7; color: #92400E; border-radius: 4px; font-size: 12px; font-weight: 600;">Diproses</span>',
                            'disetujui' => '<span style="display: inline-block; padding: 4px 12px; background: #D1FAE5; color: #065F46; border-radius: 4px; font-size: 12px; font-weight: 600;">Disetujui</span>',
                            'ditolak' => '<span style="display: inline-block; padding: 4px 12px; background: #FEE2E2; color: #991B1B; border-radius: 4px; font-size: 12px; font-weight: 600;">Ditolak</span>',
                            default => '<span style="font-size: 14px; font-weight: 600; color: #6B7280;">' . ucfirst($record->status) . '</span>'
                        } . '
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                ' . ($record->keterangan ? '
                                <!-- KEPERLUAN / KETERANGAN Card (Full Width) -->
                                <div style="border-left: 4px solid #8B5CF6; background: #F3F4F6; padding: 18px; margin-bottom: 10px; width: 100%; box-sizing: border-box;">
                                    <h3 style="margin: 0 0 10px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #374151;">KEPERLUAN / KETERANGAN</h3>
                                    <div style="font-size: 14px; font-weight: 500; color: #111827; line-height: 1.5;">' . nl2br(e($record->keterangan)) . '</div>
                                </div>
                                ' : '') . '
                                
                                ' . ($record->status === 'ditolak' && $record->alasan_penolakan ? '
                                <!-- ALASAN PENOLAKAN Card (Full Width) -->
                                <div style="border-left: 4px solid #EF4444; background: #FEE2E2; padding: 18px; width: 100%; box-sizing: border-box;">
                                    <h3 style="margin: 0 0 10px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #991B1B;">⚠ ALASAN PENOLAKAN</h3>
                                    <div style="font-size: 14px; font-weight: 500; color: #DC2626; line-height: 1.5;">' . nl2br(e($record->alasan_penolakan)) . '</div>
                                </div>
                                ' : '') . '
                                
                            </div>
                        '))
                        ->modalFooterActions([])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),

                    // Approve Action
                    Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(ReqPinjamModel $record) => $record->status === 'diproses')
                        ->modalHeading('Setujui Pengajuan Peminjaman')
                        ->modalDescription(fn(ReqPinjamModel $record) => new HtmlString('
                            <div style="margin: -24px -24px 20px -24px; padding: 16px 20px; background: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                    
                                    <!-- DATA PEMOHON -->
                                    <div style="background: white; border-left: 3px solid #3B82F6; padding: 12px; border-radius: 6px;">
                                        <h4 style="margin: 0 0 10px 0; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6B7280;">DATA PEMOHON</h4>
                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                            <div>
                                                <div style="font-size: 10px; color: #9CA3AF; margin-bottom: 2px;">Nama Lengkap</div>
                                                <div style="font-size: 13px; font-weight: 600; color: #111827;">' . ($record->user->name ?? '-') . '</div>
                                            </div>
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                                <div>
                                                    <div style="font-size: 10px; color: #9CA3AF; margin-bottom: 2px;">NIP</div>
                                                    <div style="font-size: 12px; font-weight: 600; color: #111827;">' . ($record->user->nip ?? '-') . '</div>
                                                </div>
                                                <div>
                                                    <div style="font-size: 10px; color: #9CA3AF; margin-bottom: 2px;">Unit Kerja</div>
                                                    <div style="font-size: 12px; font-weight: 500; color: #111827;">' . ($record->user->unitkerja->nm_unitkerja ?? '-') . '</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- RINCIAN PENGAJUAN -->
                                    <div style="background: white; border-left: 3px solid #10B981; padding: 12px; border-radius: 6px;">
                                        <h4 style="margin: 0 0 10px 0; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6B7280;">RINCIAN PENGAJUAN</h4>
                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                            <div>
                                                <div style="font-size: 10px; color: #9CA3AF; margin-bottom: 2px;">Kategori Barang</div>
                                                <div style="font-size: 13px; font-weight: 600; color: #111827;">' . ($record->kategori->nama_kategori ?? '-') . '</div>
                                            </div>
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                                <div>
                                                    <div style="font-size: 10px; color: #9CA3AF; margin-bottom: 2px;">Jumlah</div>
                                                    <div style="font-size: 13px; font-weight: 700; color: #059669;">' . $record->jumlah . ' Unit</div>
                                                </div>
                                                <div>
                                                    <div style="font-size: 10px; color: #9CA3AF; margin-bottom: 2px;">Tanggal Pengajuan</div>
                                                    <div style="font-size: 12px; font-weight: 500; color: #111827;">' . \Carbon\Carbon::parse($record->tanggal_request)->format('d M Y') . '</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                ' . ($record->keterangan ? '
                                <div style="background: white; border-left: 3px solid #8B5CF6; padding: 10px; border-radius: 6px; margin-top: 12px;">
                                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6B7280; margin-bottom: 6px;">KEPERLUAN</div>
                                    <div style="font-size: 12px; color: #374151; line-height: 1.4;">' . nl2br(e($record->keterangan)) . '</div>
                                </div>
                                ' : '') . '
                            </div>
                            <div style="margin-bottom: 16px; padding: 12px; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 4px;">
                                <p style="margin: 0; font-size: 13px; color: #92400E; font-weight: 500;">
                                    📋 Lengkapi form di bawah untuk menyetujui pengajuan ini
                                </p>
                            </div>
                        '))
                        ->modalWidth('3xl')
                        ->form([
                            Select::make('barang_id')
                                ->label('Pilih Barang')
                                ->options(function (ReqPinjamModel $record) {
                                    return BarangModel::where('kategori_id', $record->kategori_id)
                                        ->where('status', 'tersedia')
                                        ->pluck('nama_barang', 'id_barang');
                                })
                                ->required()
                                ->placeholder('Pilih barang yang akan dipinjamkan')
                                ->searchable()
                                ->preload()
                                ->helperText('Hanya menampilkan barang dengan status tersedia'),

                            Select::make('kondisi_barang')
                                ->label('Kondisi Barang')
                                ->options([
                                    'baik' => 'Baik',
                                    'rusak ringan' => 'Rusak Ringan',
                                    'rusak berat' => 'Rusak Berat',
                                ])
                                ->required()
                                ->default('baik')
                                ->native(false),

                            DatePicker::make('tanggal_serah_terima')
                                ->label('Tanggal Serah Terima')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->maxDate(now()),

                            Textarea::make('kelengkapan')
                                ->label('Kelengkapan')
                                ->placeholder('Contoh: Charger, Tas, Mouse, dll.')
                                ->rows(3),

                            Textarea::make('catatan_admin')
                                ->label('Catatan Admin')
                                ->placeholder('Catatan tambahan untuk peminjam (opsional)')
                                ->rows(3),
                        ])
                        ->action(function (ReqPinjamModel $record, array $data) {
                            DB::transaction(function () use ($record, $data) {
                                $record->update(['status' => 'disetujui']);
                                $nomorSurat = NomorSuratService::generate();
                                PeminjamanModel::create([
                                    'nomor_surat' => $nomorSurat,
                                    'req_pinjam_id' => $record->id,
                                    'barang_id' => $data['barang_id'],
                                    'admin_id' => Auth::id(),
                                    'tanggal_serah_terima' => $data['tanggal_serah_terima'],
                                    'kondisi_barang' => $data['kondisi_barang'],
                                    'kelengkapan' => $data['kelengkapan'] ?? null,
                                    'catatan_admin' => $data['catatan_admin'] ?? null,
                                    'status_peminjaman' => 'dipinjam',
                                ]);
                            });
                        })
                        ->successNotificationTitle('Pengajuan berhasil disetujui')
                        ->modalSubmitActionLabel('Setujui & Buat Peminjaman')
                        ->modalCancelActionLabel('Batal')
                        ->requiresConfirmation(),

                    // Reject Action
                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(ReqPinjamModel $record) => $record->status === 'diproses')
                        ->modalHeading('Tolak Pengajuan Peminjaman')
                        ->modalDescription('Berikan alasan penolakan yang jelas kepada pemohon')
                        ->modalWidth('xl')
                        ->form([
                            Textarea::make('alasan_penolakan')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->placeholder('Jelaskan alasan penolakan pengajuan ini')
                                ->rows(3),
                        ])
                        ->action(function (ReqPinjamModel $record, array $data) {
                            $record->update([
                                'status' => 'ditolak',
                                'alasan_penolakan' => $data['alasan_penolakan'],
                            ]);
                        })
                        ->successNotificationTitle('Pengajuan ditolak')
                        ->modalSubmitActionLabel('Tolak Pengajuan')
                        ->modalCancelActionLabel('Batal')
                        ->requiresConfirmation(),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('primary')
                    ->button(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pengajuan')
                    ->options([
                        'diproses' => 'Sedang Diproses',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->placeholder('Semua Status'),

                SelectFilter::make('kategori')
                    ->label('Kategori Barang')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kategori'),

                SelectFilter::make('unit_kerja')
                    ->label('Unit Kerja Pemohon')
                    ->relationship('user.unitkerja', 'nm_unitkerja')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Unit Kerja'),

                Filter::make('tanggal_request')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_request', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_request', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d M Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d M Y');
                        }

                        return $indicators;
                    }),

                Filter::make('jumlah')
                    ->form([
                        Select::make('min')
                            ->label('Jumlah Minimal')
                            ->options([
                                1 => '1 Unit',
                                5 => '5 Unit',
                                10 => '10 Unit',
                                20 => '20 Unit',
                            ])
                            ->placeholder('Tidak ada batasan'),
                        Select::make('max')
                            ->label('Jumlah Maksimal')
                            ->options([
                                5 => '5 Unit',
                                10 => '10 Unit',
                                20 => '20 Unit',
                                50 => '50 Unit',
                            ])
                            ->placeholder('Tidak ada batasan'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min'],
                                fn(Builder $query, $jumlah): Builder => $query->where('jumlah', '>=', $jumlah),
                            )
                            ->when(
                                $data['max'],
                                fn(Builder $query, $jumlah): Builder => $query->where('jumlah', '<=', $jumlah),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min'] ?? null) {
                            $indicators[] = 'Min: ' . $data['min'] . ' unit';
                        }
                        if ($data['max'] ?? null) {
                            $indicators[] = 'Max: ' . $data['max'] . ' unit';
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
