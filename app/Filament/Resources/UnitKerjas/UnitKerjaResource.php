<?php

namespace App\Filament\Resources\UnitKerjas;

use App\Filament\Resources\UnitKerjas\Pages\ListUnitKerjas;
use App\Filament\Resources\UnitKerjas\Schemas\UnitKerjaForm;
use App\Filament\Resources\UnitKerjas\Tables\UnitKerjasTable;
use App\Models\UnitKerja;
use App\Models\UnitKerjaModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UnitKerjaResource extends Resource
{
    protected static ?string $model = UnitKerjaModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice;

    protected static ?string $recordTitleAttribute = 'Unit Kerja';

    protected static ?string $navigationLabel = 'Unit Kerja';

    protected static ?string $slug = 'unit-kerja';

    protected static string|UnitEnum|null $navigationGroup = 'Master User';

    // Urutan Navigation
    protected static ?int $navigationSort = 3;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Unit Kerja';

    public static function form(Schema $schema): Schema
    {
        return UnitKerjaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitKerjasTable::configure($table);
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
            'index' => ListUnitKerjas::route('/'),
        ];
    }
}
