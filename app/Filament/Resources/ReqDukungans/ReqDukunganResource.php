<?php

namespace App\Filament\Resources\ReqDukungans;

use App\Filament\Resources\ReqDukungans\Pages\ListReqDukungans;
use App\Filament\Resources\ReqDukungans\Schemas\ReqDukunganForm;
use App\Filament\Resources\ReqDukungans\Tables\ReqDukungansTable;
use App\Models\ReqDukunganModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ReqDukunganResource extends Resource
{
    protected static ?string $model = ReqDukunganModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::HandRaised;

    protected static ?string $recordTitleAttribute = 'Pengajuan Dukungan';

    protected static ?string $navigationLabel = 'Ajukan Dukungan';

    protected static ?string $slug = 'req-dukungan';

    protected static string|UnitEnum|null $navigationGroup = 'Dukungan';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Dukungan';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isKaryawan() ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isKaryawan() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return ReqDukunganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReqDukungansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('pemohon_id', Auth::id())
            ->where('status_dukungan', 'belum_didukung');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReqDukungans::route('/'),
        ];
    }
}
