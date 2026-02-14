<?php

namespace App\Filament\Resources\Pengajuans;

use App\Filament\Resources\Pengajuans\Pages\ListPengajuans;
use App\Filament\Resources\Pengajuans\Tables\PengajuansTable;
use App\Models\ReqPinjamModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PengajuanResource extends Resource
{
    protected static ?string $model = ReqPinjamModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::InboxStack;

    protected static ?string $recordTitleAttribute = 'Pengajuan Peminjaman';

    protected static ?string $navigationLabel = 'Pengajuan Peminjaman';

    protected static ?string $slug = 'pengajuan';

    protected static string|UnitEnum|null $navigationGroup = 'Admin Peminjaman';

    // Urutan Navigation
    protected static ?int $navigationSort = 1;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Peminjaman';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'diproses')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return PengajuansTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false; // Admin tidak bisa create pengajuan
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengajuans::route('/'),
        ];
    }
}
