<?php

namespace App\Filament\Resources\Mereks;

use App\Filament\Resources\Mereks\Pages\ListMereks;
use App\Filament\Resources\Mereks\Schemas\MerekForm;
use App\Filament\Resources\Mereks\Tables\MereksTable;
use App\Models\Merek;
use App\Models\MerekModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MerekResource extends Resource
{
    protected static ?string $model = MerekModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Star;

    protected static ?string $recordTitleAttribute = 'Merek';

    protected static ?string $navigationLabel = 'Merek';

    protected static ?string $slug = 'Merek';

    protected static string|UnitEnum|null $navigationGroup = 'Master Inventaris';

    // Urutan Navigation
    protected static ?int $navigationSort = 3;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Merek';


    public static function form(Schema $schema): Schema
    {
        return MerekForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MereksTable::configure($table);
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
            'index' => ListMereks::route('/'),
        ];
    }
}
