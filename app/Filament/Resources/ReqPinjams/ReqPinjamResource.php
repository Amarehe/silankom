<?php

namespace App\Filament\Resources\ReqPinjams;

use App\Filament\Resources\ReqPinjams\Pages\ListReqPinjams;
use App\Filament\Resources\ReqPinjams\Schemas\ReqPinjamForm;
use App\Filament\Resources\ReqPinjams\Tables\ReqPinjamsTable;
use App\Models\ReqPinjamModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ReqPinjamResource extends Resource
{
    protected static ?string $model = ReqPinjamModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentPlus;

    protected static ?string $recordTitleAttribute = 'Pengajuan Peminjaman';

    protected static ?string $navigationLabel = 'Ajukan Peminjaman';

    protected static ?string $slug = 'req-pinjam';

    protected static string|UnitEnum|null $navigationGroup = 'Peminjaman';

    // Urutan Navigation
    protected static ?int $navigationSort = 1;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Peminjaman';

    public static function form(Schema $schema): Schema
    {
        return ReqPinjamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReqPinjamsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        // User hanya melihat pengajuan mereka sendiri
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
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
            'index' => ListReqPinjams::route('/'),
        ];
    }
}
