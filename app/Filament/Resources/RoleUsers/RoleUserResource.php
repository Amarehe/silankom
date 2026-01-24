<?php

namespace App\Filament\Resources\RoleUsers;

use App\Filament\Resources\RoleUsers\Pages\CreateRoleUser;
use App\Filament\Resources\RoleUsers\Pages\EditRoleUser;
use App\Filament\Resources\RoleUsers\Pages\ListRoleUsers;
use App\Filament\Resources\RoleUsers\Schemas\RoleUserForm;
use App\Filament\Resources\RoleUsers\Tables\RoleUsersTable;
use App\Models\RoleModel;
use App\Models\RoleUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RoleUserResource extends Resource
{
    protected static ?string $model = RoleModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog;

    protected static ?string $recordTitleAttribute = 'Role User';

    protected static ?string $navigationLabel = 'Role User';

    protected static ?string $slug = 'role';

    protected static string|UnitEnum|null $navigationGroup = 'Master User';

    // Urutan Navigation
    protected static ?int $navigationSort = 4;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Role User';

    public static function form(Schema $schema): Schema
    {
        return RoleUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoleUsersTable::configure($table);
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
            'index' => ListRoleUsers::route('/'),
            // 'create' => CreateRoleUser::route('/create'),
            // 'edit' => EditRoleUser::route('/{record}/edit'),
        ];
    }
}
