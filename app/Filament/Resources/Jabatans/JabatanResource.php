<?php

namespace App\Filament\Resources\Jabatans;

use App\Filament\Resources\Jabatans\Pages\ListJabatans;
use App\Filament\Resources\Jabatans\Schemas\JabatanForm;
use App\Filament\Resources\Jabatans\Tables\JabatansTable;
use App\Models\Jabatan;
use App\Models\JabatanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JabatanResource extends Resource
{
    protected static ?string $model = JabatanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static ?string $recordTitleAttribute = 'Jabatan';

    protected static ?string $navigationLabel = 'Jabatan';

    protected static ?string $slug = 'jabatan';

    protected static string|UnitEnum|null $navigationGroup = 'Master User';

    // Urutan Navigation
    protected static ?int $navigationSort = 2;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Jabatan';


    public static function form(Schema $schema): Schema
    {
        return JabatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JabatansTable::configure($table);
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
            'index' => ListJabatans::route('/'),
        ];
    }
}
