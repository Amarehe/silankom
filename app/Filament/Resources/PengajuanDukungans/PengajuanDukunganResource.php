<?php

namespace App\Filament\Resources\PengajuanDukungans;

use App\Filament\Resources\PengajuanDukungans\Pages\ListPengajuanDukungans;
use App\Filament\Resources\PengajuanDukungans\Tables\PengajuanDukungansTable;
use App\Models\ReqDukunganModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PengajuanDukunganResource extends Resource
{
    protected static ?string $model = ReqDukunganModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'Pengajuan Dukungan';

    protected static ?string $navigationLabel = 'Pengajuan Dukungan';

    protected static ?string $slug = 'pengajuan-dukungan';

    protected static string|UnitEnum|null $navigationGroup = 'Kelola Dukungan';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Dukungan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_dukungan', 'belum_didukung')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() || Auth::user()?->isTeknisiKomlek() ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() || Auth::user()?->isTeknisiKomlek() ?? false;
    }

    public static function table(Table $table): Table
    {
        return PengajuanDukungansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status_dukungan', 'belum_didukung');
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
            'index' => ListPengajuanDukungans::route('/'),
        ];
    }
}
