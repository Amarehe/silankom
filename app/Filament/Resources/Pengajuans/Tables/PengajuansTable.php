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
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                    ->description(fn (ReqPinjamModel $record): string => $record->user->nip ?? '-')
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
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->sortable()
                    ->description(fn (ReqPinjamModel $record): string => $record->created_at->diffForHumans()),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->tooltip(fn (ReqPinjamModel $record): ?string => $record->keterangan)
                    ->wrap()
                    ->placeholder('Tidak ada keterangan'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['user.jabatan', 'user.unitkerja', 'kategori']))
            ->actions([
                ActionGroup::make([
                    // View Detail Action
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Peminjaman')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Informasi Pemohon')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('user.name')
                                            ->label('Nama Lengkap')
                                            ->icon('heroicon-m-user')
                                            ->weight('bold'),
                                        TextEntry::make('user.nip')
                                            ->label('NIP')
                                            ->icon('heroicon-m-identification')
                                            ->placeholder('-'),
                                        TextEntry::make('user.jabatan.nm_jabatan')
                                            ->label('Jabatan')
                                            ->icon('heroicon-m-briefcase')
                                            ->placeholder('-'),
                                        TextEntry::make('user.unitkerja.nm_unitkerja')
                                            ->label('Unit Kerja')
                                            ->icon('heroicon-m-building-office'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Rincian Pengajuan')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('kategori.nama_kategori')
                                            ->label('Kategori Barang')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('jumlah')
                                            ->label('Jumlah')
                                            ->suffix(' Unit')
                                            ->icon('heroicon-m-cube')
                                            ->weight('bold'),
                                        TextEntry::make('tanggal_request')
                                            ->label('Tanggal Pengajuan')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'diproses' => 'warning',
                                                'disetujui' => 'success',
                                                'ditolak' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'diproses' => 'Diproses',
                                                'disetujui' => 'Disetujui',
                                                'ditolak' => 'Ditolak',
                                                default => ucfirst($state),
                                            }),
                                    ]),
                                ])->collapsible(),

                            Section::make('Keperluan / Keterangan')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    TextEntry::make('keterangan')
                                        ->label('Keterangan')
                                        ->prose()
                                        ->placeholder('-'),
                                ])
                                ->visible(fn (ReqPinjamModel $record) => ! empty($record->keterangan)),

                            Section::make('Alasan Penolakan')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->schema([
                                    TextEntry::make('alasan_penolakan')
                                        ->label('Alasan')
                                        ->prose()
                                        ->color('danger'),
                                ])
                                ->visible(fn (ReqPinjamModel $record) => $record->status === 'ditolak' && ! empty($record->alasan_penolakan)),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),

                    // Approve Action
                    Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (ReqPinjamModel $record) => $record->status === 'diproses')
                        ->modalHeading('Setujui Pengajuan Peminjaman')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Informasi Pemohon')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('user.name')
                                            ->label('Nama Lengkap')
                                            ->icon('heroicon-m-user')
                                            ->weight('bold'),
                                        TextEntry::make('user.nip')
                                            ->label('NIP')
                                            ->icon('heroicon-m-identification')
                                            ->placeholder('-'),
                                        TextEntry::make('user.unitkerja.nm_unitkerja')
                                            ->label('Unit Kerja')
                                            ->icon('heroicon-m-building-office'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Rincian Pengajuan')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('kategori.nama_kategori')
                                            ->label('Kategori Barang')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('jumlah')
                                            ->label('Jumlah')
                                            ->suffix(' Unit')
                                            ->weight('bold')
                                            ->color('success'),
                                        TextEntry::make('tanggal_request')
                                            ->label('Tanggal Pengajuan')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Keperluan / Keterangan')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    TextEntry::make('keterangan')
                                        ->label('Keterangan')
                                        ->prose(),
                                ])
                                ->visible(fn (ReqPinjamModel $record) => ! empty($record->keterangan)),
                        ])
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
                        ->visible(fn (ReqPinjamModel $record) => $record->status === 'diproses')
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
                    ->button()
                    ->label('Aksi')
                    ->icon('heroicon-m-chevron-down')
                    ->color('primary'),
            ])
            ->filters([
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_request', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_request', '<=', $date),
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
                                fn (Builder $query, $jumlah): Builder => $query->where('jumlah', '>=', $jumlah),
                            )
                            ->when(
                                $data['max'],
                                fn (Builder $query, $jumlah): Builder => $query->where('jumlah', '<=', $jumlah),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min'] ?? null) {
                            $indicators[] = 'Min: '.$data['min'].' unit';
                        }
                        if ($data['max'] ?? null) {
                            $indicators[] = 'Max: '.$data['max'].' unit';
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
