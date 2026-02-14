<?php

namespace App\Filament\Resources\PeminjamanAdmins;

use App\Filament\Resources\PeminjamanAdmins\Pages\ListPeminjamans;
use App\Filament\Resources\PeminjamanAdmins\Tables\PeminjamansTable;
use App\Models\PeminjamanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PeminjamanAdminResource extends Resource
{
    protected static ?string $model = PeminjamanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'Kelola Peminjaman';

    protected static ?string $navigationLabel = 'Kelola Peminjaman';

    protected static ?string $slug = 'peminjaman-admin';

    protected static string|UnitEnum|null $navigationGroup = 'Admin Peminjaman';

    // Urutan Navigation
    protected static ?int $navigationSort = 2;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Peminjaman Aktif & Riwayat';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_peminjaman', 'dipinjam')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function table(Table $table): Table
    {
        return PeminjamansTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false; // Peminjaman dibuat via approval
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
            'index' => ListPeminjamans::route('/'),
        ];
    }
}
