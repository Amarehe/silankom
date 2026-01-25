<?php

namespace App\Filament\Resources\Barangs;

use App\Filament\Resources\Barangs\Pages\CreateBarang;
use App\Filament\Resources\Barangs\Pages\EditBarang;
use App\Filament\Resources\Barangs\Pages\ListBarangs;
use App\Filament\Resources\Barangs\Schemas\BarangForm;
use App\Filament\Resources\Barangs\Tables\BarangsTable;
use App\Models\BarangModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BarangResource extends Resource
{
    protected static ?string $model = BarangModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;

    protected static ?string $recordTitleAttribute = 'Barang';

    protected static ?string $navigationLabel = 'Barang / Inventaris';

    protected static ?string $slug = 'barang';

    protected static string|UnitEnum|null $navigationGroup = 'Master User';

    // Urutan Navigation
    protected static ?int $navigationSort = 4;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Barang';

    public static function form(Schema $schema): Schema
    {
        return BarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangsTable::configure($table);
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
