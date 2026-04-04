<?php

namespace App\Filament\Resources\RiwayatPerbaikans;

use App\Filament\Resources\RiwayatPerbaikans\Pages\ListRiwayatPerbaikans;
use App\Filament\Resources\RiwayatPerbaikans\Tables\RiwayatPerbaikansTable;
use App\Models\PerbaikanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RiwayatPerbaikanResource extends Resource
{
    protected static ?string $model = PerbaikanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Riwayat Perbaikan';

    protected static ?string $navigationLabel = 'Riwayat Perbaikan';

    protected static ?string $slug = 'riwayat-perbaikan';

    protected static string|UnitEnum|null $navigationGroup = 'Perbaikan';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Riwayat Perbaikan';

    public static function table(Table $table): Table
    {
        return RiwayatPerbaikansTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('pemohon_id', Auth::id())
            ->whereIn('status_perbaikan', ['selesai', 'tidak_bisa_diperbaiki']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRiwayatPerbaikans::route('/'),
        ];
    }
}
