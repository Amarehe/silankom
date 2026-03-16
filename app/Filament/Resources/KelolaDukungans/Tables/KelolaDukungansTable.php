<?php

namespace App\Filament\Resources\KelolaDukungans\Tables;

use App\Models\ReqDukunganModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class KelolaDukungansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['pemohon', 'picDukungan']))
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('id')
                    ->label('ID Dukungan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nomor_nodis')
                    ->label('Nomor Nodis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tgl_kegiatan')
                    ->label('Tgl Kegiatan')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('req_barang')
                    ->label('Barang Dibutuhkan')
                    ->formatStateUsing(function ($state) {
                        $item = is_string($state) ? json_decode($state, true) : $state;
                        if (! is_array($item)) return $state;
                        
                        $nama = $item['nama'] ?? $item['nama'] ?? '-';
                        $jumlah = $item['jumlah'] ?? $item['jumlah'] ?? 0;
                        return "{$nama} ({$jumlah})";
                    })
                    ->bulleted(),

                TextColumn::make('barang_diberikan')
                    ->label('Barang Diberikan')
                    ->formatStateUsing(function ($state) {
                        $item = is_string($state) ? json_decode($state, true) : $state;
                        if (! is_array($item)) return $state;
                        
                        $nama = $item['nama'] ?? $item['nama'] ?? '-';
                        $jumlah = $item['jumlah'] ?? $item['jumlah'] ?? 0;
                        return "{$nama} ({$jumlah})";
                    })
                    ->bulleted(),

                TextColumn::make('status_dukungan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_didukung' => 'warning',
                        'didukung' => 'success',
                        'tidak_didukung' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_didukung' => 'Belum Didukung',
                        'didukung' => 'Didukung',
                        'tidak_didukung' => 'Tidak Didukung',
                    })
                    ->sortable(),

                TextColumn::make('pemohon.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('picDukungan.name')
                    ->label('PIC Admin')
                    ->placeholder('-'),
            ])
            ->actions([
                ActionGroup::make([
                    // Approve Action
                    Action::make('approve')
                        ->label('Setujui / Dukung')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (ReqDukunganModel $record) => $record->status_dukungan === 'belum_didukung')
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
                                ->native(false),

                            Repeater::make('barang_diberikan')
                                ->label('Barang yang Diberikan')
                                ->schema([
                                    TextInput::make('nama')
                                        ->label('Nama Barang')
                                        ->disabled()
                                        ->dehydrated(),
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
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false),

                            Textarea::make('catatan_admin')
                                ->label('Catatan Admin')
                                ->placeholder('Catatan tambahan (opsional)')
                                ->rows(3),
                        ])
                        ->action(function (ReqDukunganModel $record, array $data) {
                            // Simpan hanya nama & jumlah diberikan (buang jumlah_diminta)
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
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Dukungan Disetujui')
                                ->body('Request dukungan telah berhasil disetujui.')
                        )
                        ->modalSubmitActionLabel('Setujui Dukungan')
                        ->modalCancelActionLabel('Batal'),

                    // Reject Action
                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (ReqDukunganModel $record) => $record->status_dukungan === 'belum_didukung')
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
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size('sm')
                    ->color('warning')
                    ->button(),
            ])
            ->filters([
                SelectFilter::make('status_dukungan')
                    ->label('Status Dukungan')
                    ->options([
                        'belum_didukung' => 'Belum Didukung',
                        'didukung' => 'Didukung',
                        'tidak_didukung' => 'Tidak Didukung',
                    ])
                    ->placeholder('Semua Status'),
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
