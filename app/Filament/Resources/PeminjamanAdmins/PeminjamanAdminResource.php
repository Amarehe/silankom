<?php

namespace App\Filament\Resources\PeminjamanAdmins;

use App\Filament\Resources\PeminjamanAdmins\Pages\ListPeminjamans;
use App\Filament\Resources\PeminjamanAdmins\Tables\PeminjamansTable;
use App\Models\PeminjamanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PeminjamanAdminResource extends Resource
{
    protected static ?string $model = PeminjamanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Riwayat Peminjaman';

    protected static ?string $navigationLabel = 'Riwayat Peminjaman';

    protected static ?string $slug = 'peminjaman-admin';

    protected static string|UnitEnum|null $navigationGroup = 'Kelola Peminjaman';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Riwayat Peminjaman';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPeminjamans::route('/'),
        ];
    }
}
