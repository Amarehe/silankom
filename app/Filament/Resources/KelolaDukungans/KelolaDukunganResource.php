<?php

namespace App\Filament\Resources\KelolaDukungans;

use App\Filament\Resources\KelolaDukungans\Pages\ListKelolaDukungans;
use App\Filament\Resources\KelolaDukungans\Tables\KelolaDukungansTable;
use App\Models\ReqDukunganModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KelolaDukunganResource extends Resource
{
    protected static ?string $model = ReqDukunganModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'Kelola Dukungan';

    protected static ?string $navigationLabel = 'Kelola Dukungan';

    protected static ?string $slug = 'kelola-dukungan';

    protected static string|UnitEnum|null $navigationGroup = 'Admin Dukungan';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Daftar Request Dukungan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_dukungan', 'belum_didukung')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return KelolaDukungansTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKelolaDukungans::route('/'),
        ];
    }
}
