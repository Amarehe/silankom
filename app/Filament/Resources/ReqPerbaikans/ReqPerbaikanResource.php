<?php

namespace App\Filament\Resources\ReqPerbaikans;

use App\Filament\Resources\ReqPerbaikans\Pages\ListReqPerbaikans;
use App\Filament\Resources\ReqPerbaikans\Schemas\ReqPerbaikanForm;
use App\Filament\Resources\ReqPerbaikans\Tables\ReqPerbaikansTable;
use App\Models\PerbaikanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ReqPerbaikanResource extends Resource
{
    protected static ?string $model = PerbaikanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Wrench;

    protected static ?string $recordTitleAttribute = 'Pengajuan Perbaikan';

    protected static ?string $navigationLabel = 'Ajukan Perbaikan';

    protected static ?string $slug = 'req-perbaikan';

    protected static string|UnitEnum|null $navigationGroup = 'Perbaikan';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Perbaikan';

    public static function form(Schema $schema): Schema
    {
        return ReqPerbaikanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReqPerbaikansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReqPerbaikans::route('/'),
        ];
    }
}
