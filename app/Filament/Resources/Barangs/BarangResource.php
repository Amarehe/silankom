<?php

namespace App\Filament\Resources\Barangs;

use App\Filament\Resources\Barangs\Pages\ListBarangs;
use App\Filament\Resources\Barangs\Schemas\BarangForm;
use App\Filament\Resources\Barangs\Schemas\Baranginfolist;
use App\Filament\Resources\Barangs\Tables\BarangsTable;
use App\Models\BarangModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class BarangResource extends Resource
{
    protected static ?string $model = BarangModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;

    protected static ?string $recordTitleAttribute = 'Barang';

    protected static ?string $navigationLabel = 'Barang / Inventaris';

    protected static ?string $slug = 'barang';

    protected static string|UnitEnum|null $navigationGroup = 'Master Inventaris';

    // Urutan Navigation
    protected static ?int $navigationSort = 1;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Barang';

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(Auth::user()?->role_id, [1, 2, 3]);
    }

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role_id, [1, 2, 3]);
    }

    /** Teknisi hanya bisa melihat, tidak bisa tambah/ubah/hapus. */
    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return BarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return Baranginfolist::configure($schema);
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
            'index' => ListBarangs::route('/'),
        ];
    }
}
