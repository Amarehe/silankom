<?php

namespace App\Filament\Resources\Kategoris;

use App\Filament\Resources\Kategoris\Pages\ListKategoris;
use App\Filament\Resources\Kategoris\Schemas\KategoriForm;
use App\Filament\Resources\Kategoris\Tables\KategorisTable;
use App\Models\Kategori;
use App\Models\KategoriModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KategoriResource extends Resource
{
    protected static ?string $model =  KategoriModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static ?string $recordTitleAttribute = 'Kategori';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $slug = 'kategori';

    protected static string|UnitEnum|null $navigationGroup = 'Master User';

    // Urutan Navigation
    protected static ?int $navigationSort = 3;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Kategori';

    public static function form(Schema $schema): Schema
    {
        return KategoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategorisTable::configure($table);
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
            'index' => ListKategoris::route('/'),
        ];
    }
}
