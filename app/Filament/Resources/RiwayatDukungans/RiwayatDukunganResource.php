<?php

namespace App\Filament\Resources\RiwayatDukungans;

use App\Filament\Resources\RiwayatDukungans\Pages\ListRiwayatDukungans;
use App\Filament\Resources\RiwayatDukungans\Tables\RiwayatDukunganTable;
use App\Models\ReqDukunganModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RiwayatDukunganResource extends Resource
{
    protected static ?string $model = ReqDukunganModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Riwayat Dukungan';

    protected static ?string $navigationLabel = 'Riwayat Dukungan';

    protected static ?string $slug = 'riwayat-dukungan';

    protected static string|UnitEnum|null $navigationGroup = 'Kelola Dukungan';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Riwayat Dukungan';

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
        return RiwayatDukunganTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status_dukungan', ['didukung', 'tidak_didukung']);
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
            'index' => ListRiwayatDukungans::route('/'),
        ];
    }
}
