<?php

namespace App\Filament\Resources\RiwayatDukunganUsers;

use App\Filament\Resources\RiwayatDukunganUsers\Pages\ListRiwayatDukunganUsers;
use App\Filament\Resources\RiwayatDukunganUsers\Tables\RiwayatDukunganUserTable;
use App\Models\ReqDukunganModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RiwayatDukunganUserResource extends Resource
{
    protected static ?string $model = ReqDukunganModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Riwayat Dukungan';

    protected static ?string $navigationLabel = 'Riwayat Dukungan';

    protected static ?string $slug = 'riwayat-dukungan-user';

    protected static string|UnitEnum|null $navigationGroup = 'Dukungan';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Riwayat Pengajuan Dukungan';

    public static function table(Table $table): Table
    {
        return RiwayatDukunganUserTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('pemohon_id', Auth::id());
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
            'index' => ListRiwayatDukunganUsers::route('/'),
        ];
    }
}
