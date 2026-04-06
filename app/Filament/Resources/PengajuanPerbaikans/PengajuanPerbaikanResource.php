<?php

namespace App\Filament\Resources\PengajuanPerbaikans;

use App\Filament\Resources\PengajuanPerbaikans\Pages\EditPengajuanPerbaikan;
use App\Filament\Resources\PengajuanPerbaikans\Pages\ListPengajuanPerbaikans;
use App\Filament\Resources\PengajuanPerbaikans\Schemas\PengajuanPerbaikanEditForm;
use App\Filament\Resources\PengajuanPerbaikans\Tables\PengajuanPerbaikansTable;
use App\Models\PerbaikanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PengajuanPerbaikanResource extends Resource
{
    protected static ?string $model = PerbaikanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::InboxStack;

    protected static ?string $recordTitleAttribute = 'Pengajuan Perbaikan';

    protected static ?string $navigationLabel = 'Pengajuan Perbaikan';

    protected static ?string $slug = 'pengajuan-perbaikan';

    protected static string|UnitEnum|null $navigationGroup = 'Admin Perbaikan';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Perbaikan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status_perbaikan', ['diajukan', 'diproses'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return PengajuanPerbaikanEditForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengajuanPerbaikansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status_perbaikan', ['diajukan', 'diproses']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->role_id == 1;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->role_id == 1;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengajuanPerbaikans::route('/'),
            'edit' => EditPengajuanPerbaikan::route('/{record}/edit'),
        ];
    }
}
