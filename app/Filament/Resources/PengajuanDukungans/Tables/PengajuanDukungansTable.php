<?php

namespace App\Filament\Resources\PengajuanDukungans\Tables;

use App\Models\ReqDukunganModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PengajuanDukungansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['pemohon.unitkerja', 'pemohon.jabatan', 'picDukungan']))
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('nomor_nodis')
                    ->label('Nomor Nodis')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor nodis disalin!')
                    ->icon('heroicon-o-document-duplicate')
                    ->tooltip('Klik untuk menyalin')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tgl_kegiatan')
                    ->label('Tgl Kegiatan')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                    ->sortable()
                    ->icon('heroicon-o-clock'),

                TextColumn::make('req_barang')
                    ->label('Barang Dibutuhkan')
                    ->formatStateUsing(function ($state) {
                        $item = is_string($state) ? json_decode($state, true) : $state;
                        if (! is_array($item)) {
                            return $state;
                        }

                        $nama = $item['nama'] ?? $item['nama'] ?? '-';
                        $jumlah = $item['jumlah'] ?? $item['jumlah'] ?? 0;

                        return "{$nama} ({$jumlah})";
                    })
                    ->bulleted(),

                TextColumn::make('pemohon.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    // View Detail Action
                    Action::make('view_detail')
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail Pengajuan Dukungan')
                        ->modalWidth('3xl')
                        ->infolist([
                            Section::make('Informasi Pemohon')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('pemohon.name')
                                            ->label('Nama Pemohon')
                                            ->icon('heroicon-m-user')
                                            ->weight('bold'),
                                        TextEntry::make('pemohon.unitkerja.nm_unitkerja')
                                            ->label('Unit Kerja')
                                            ->icon('heroicon-m-building-office')
                                            ->placeholder('-'),
                                        TextEntry::make('pemohon.jabatan.nm_jabatan')
                                            ->label('Jabatan')
                                            ->icon('heroicon-m-briefcase')
                                            ->placeholder('-'),
                                    ]),
                                ])->collapsible(),

                            Section::make('Detail Kegiatan')
                                ->icon('heroicon-o-calendar-days')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('nomor_nodis')
                                            ->label('Nomor Nodis')
                                            ->icon('heroicon-m-document-text')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->copyable()
                                            ->copyMessage('Nomor nodis disalin!')
                                            ->placeholder('-'),
                                        TextEntry::make('nama_kegiatan')
                                            ->label('Nama Kegiatan')
                                            ->icon('heroicon-m-tag')
                                            ->weight('bold')
                                            ->placeholder('-'),
                                    ]),
                                    Grid::make(3)->schema([
                                        TextEntry::make('ruangan')
                                            ->label('Ruangan')
                                            ->icon('heroicon-m-map-pin'),
                                        TextEntry::make('tgl_kegiatan')
                                            ->label('Tanggal Kegiatan')
                                            ->date('l, d F Y')
                                            ->icon('heroicon-m-calendar'),
                                        TextEntry::make('created_at')
                                            ->label('Tanggal Pengajuan')
                                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y'))
                                            ->icon('heroicon-m-clock'),
                                    ]),
                                    TextEntry::make('deskripsi_kegiatan')
                                        ->label('Deskripsi Kegiatan')
                                        ->prose()
                                        ->placeholder('-'),
                                ])->collapsible(),

                            Section::make('Barang Dibutuhkan')
                                ->icon('heroicon-o-cube')
                                ->schema([
                                    TextEntry::make('req_barang')
                                        ->label('Daftar Barang')
                                        ->formatStateUsing(function ($state) {
                                            $item = is_string($state) ? json_decode($state, true) : $state;
                                            if (! is_array($item)) {
                                                return $state;
                                            }

                                            $nama = $item['nama'] ?? '-';
                                            $jumlah = $item['jumlah'] ?? 0;

                                            return "{$nama} ({$jumlah} unit)";
                                        })
                                        ->bulleted(),
                                ]),

                            Section::make('Keterangan')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    TextEntry::make('keterangan')
                                        ->label('Keterangan Tambahan')
                                        ->prose()
                                        ->placeholder('-'),
                                ])
                                ->visible(fn (ReqDukunganModel $record) => ! empty($record->keterangan)),
                        ])
                        ->modalSubmitAction(false)
                        ->extraModalFooterActions(fn (ReqDukunganModel $record): array => $record->status_dukungan === 'belum_didukung'
                            ? [
                                Action::make('approve_from_detail')
                                    ->label('Setujui / Dukung')
                                    ->icon('heroicon-o-check-circle')
                                    ->color('success')
                                    ->requiresConfirmation()
                                    ->modalHeading('Setujui Dukungan Kegiatan')
                                    ->modalDescription('Isi jumlah barang yang akan diberikan untuk kegiatan ini')
                                    ->modalWidth('xl')
                                    ->fillForm(fn (): array => [
                                        'tgl_disetujui' => now()->format('Y-m-d'),
                                        'barang_diberikan' => collect($record->req_barang ?? [])
                                            ->map(fn ($item) => [
                                                'nama' => $item['nama'] ?? '',
                                                'jumlah_diminta' => $item['jumlah'] ?? 0,
                                                'jumlah' => $item['jumlah'] ?? 0,
                                            ])
                                            ->toArray(),
                                    ])
                                    ->form([
                                        DatePicker::make('tgl_disetujui')
                                            ->label('Tanggal Disetujui')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('l, d F Y'),

                                        Repeater::make('barang_diberikan')
                                            ->label('Barang yang Diberikan')
                                            ->schema([
                                                TextInput::make('nama')
                                                    ->label('Nama Barang')
                                                    ->required(),
                                                TextInput::make('jumlah_diminta')
                                                    ->label('Jumlah Diminta')
                                                    ->numeric()
                                                    ->disabled()
                                                    ->dehydrated(false),
                                                TextInput::make('jumlah')
                                                    ->label('Jumlah Diberikan')
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(0),
                                            ])
                                            ->columns(3)
                                            ->addable(true)
                                            ->addActionLabel('Tambah Barang')
                                            ->deletable(true)
                                            ->reorderable(false),

                                        Textarea::make('catatan_admin')
                                            ->label('Catatan Admin')
                                            ->placeholder('Catatan tambahan (opsional)')
                                            ->rows(3),
                                    ])
                                    ->action(function (array $data) use ($record) {
                                        $barangDiberikan = collect($data['barang_diberikan'])
                                            ->map(fn ($item) => [
                                                'nama' => $item['nama'],
                                                'jumlah' => $item['jumlah'],
                                            ])
                                            ->toArray();

                                        $record->update([
                                            'status_dukungan' => 'didukung',
                                            'tgl_disetujui' => $data['tgl_disetujui'],
                                            'barang_diberikan' => $barangDiberikan,
                                            'catatan_admin' => $data['catatan_admin'] ?? null,
                                            'pic_dukungan_id' => Auth::id(),
                                        ]);

                                        Notification::make()
                                            ->success()
                                            ->title('Dukungan Disetujui')
                                            ->body('Request dukungan telah berhasil disetujui.')
                                            ->send();
                                    })
                                    ->modalSubmitActionLabel('Setujui Dukungan')
                                    ->modalCancelActionLabel('Batal'),
                            ]
                            : []
                        )
                        ->modalCancelActionLabel('Tutup')
                        ->modalCancelAction(fn ($action) => $action->color('gray')),

                    // Approve Action
                    Action::make('approve')
                        ->label('Setujui / Dukung')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Dukungan Kegiatan')
                        ->modalDescription('Isi jumlah barang yang akan diberikan untuk kegiatan ini')
                        ->modalWidth('xl')
                        ->fillForm(fn (ReqDukunganModel $record): array => [
                            'tgl_disetujui' => now()->format('Y-m-d'),
                            'barang_diberikan' => collect($record->req_barang ?? [])
                                ->map(fn ($item) => [
                                    'nama' => $item['nama'] ?? '',
                                    'jumlah_diminta' => $item['jumlah'] ?? 0,
                                    'jumlah' => $item['jumlah'] ?? 0,
                                ])
                                ->toArray(),
                        ])
                        ->form([
                            DatePicker::make('tgl_disetujui')
                                ->label('Tanggal Disetujui')
                                ->required()
                                ->native(false)
                                ->displayFormat('l, d F Y'),

                            Repeater::make('barang_diberikan')
                                ->label('Barang yang Diberikan')
                                ->schema([
                                    TextInput::make('nama')
                                        ->label('Nama Barang')
                                        ->required(),
                                    TextInput::make('jumlah_diminta')
                                        ->label('Jumlah Diminta')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(false),
                                    TextInput::make('jumlah')
                                        ->label('Jumlah Diberikan')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0),
                                ])
                                ->columns(3)
                                ->addable(true)
                                ->addActionLabel('Tambah Barang')
                                ->deletable(true)
                                ->reorderable(false),

                            Textarea::make('catatan_admin')
                                ->label('Catatan Admin')
                                ->placeholder('Catatan tambahan (opsional)')
                                ->rows(3),
                        ])
                        ->action(function (ReqDukunganModel $record, array $data) {
                            $barangDiberikan = collect($data['barang_diberikan'])
                                ->map(fn ($item) => [
                                    'nama' => $item['nama'],
                                    'jumlah' => $item['jumlah'],
                                ])
                                ->toArray();

                            $record->update([
                                'status_dukungan' => 'didukung',
                                'tgl_disetujui' => $data['tgl_disetujui'],
                                'barang_diberikan' => $barangDiberikan,
                                'catatan_admin' => $data['catatan_admin'] ?? null,
                                'pic_dukungan_id' => Auth::id(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Dukungan Disetujui')
                                ->body('Request dukungan telah berhasil disetujui.')
                                ->send();
                        })
                        ->modalSubmitActionLabel('Setujui Dukungan')
                        ->modalCancelActionLabel('Batal'),

                    // Reject Action
                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->modalHeading('Tolak Dukungan Kegiatan')
                        ->modalDescription('Berikan alasan penolakan dukungan')
                        ->modalWidth('lg')
                        ->form([
                            Textarea::make('alasan_ditolak')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->placeholder('Jelaskan alasan penolakan')
                                ->rows(3),
                        ])
                        ->action(function (ReqDukunganModel $record, array $data) {
                            $record->update([
                                'status_dukungan' => 'tidak_didukung',
                                'alasan_ditolak' => $data['alasan_ditolak'],
                                'pic_dukungan_id' => Auth::id(),
                            ]);
                        })
                        ->successNotification(
                            Notification::make()
                                ->warning()
                                ->title('Dukungan Ditolak')
                                ->body('Request dukungan telah ditolak.')
                        )
                        ->modalSubmitActionLabel('Tolak Dukungan')
                        ->modalCancelActionLabel('Batal'),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-chevron-down')
                    ->size('sm')
                    ->color('warning')
                    ->button(),
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
