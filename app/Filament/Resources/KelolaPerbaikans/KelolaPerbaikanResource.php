<?php

namespace App\Filament\Resources\KelolaPerbaikans;

use App\Filament\Resources\KelolaPerbaikans\Pages\ListKelolaPerbaikans;
use App\Filament\Resources\KelolaPerbaikans\Tables\KelolaPerbaikansTable;
use App\Models\PerbaikanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KelolaPerbaikanResource extends Resource
{
    protected static ?string $model = PerbaikanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'Kelola Perbaikan';

    protected static ?string $navigationLabel = 'Kelola Perbaikan';

    protected static ?string $slug = 'kelola-perbaikan';

    protected static string|UnitEnum|null $navigationGroup = 'Admin Perbaikan';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Kelola Perbaikan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_perbaikan', 'proses')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function table(Table $table): Table
    {
        return KelolaPerbaikansTable::configure($table);
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
            'index' => ListKelolaPerbaikans::route('/'),
        ];
    }
}
