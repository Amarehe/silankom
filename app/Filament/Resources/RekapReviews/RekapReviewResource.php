<?php

namespace App\Filament\Resources\RekapReviews;

use App\Filament\Resources\RekapReviews\Pages\ListRekapReviews;
use App\Filament\Resources\RekapReviews\Tables\RekapReviewsTable;
use App\Models\ReviewLayananModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RekapReviewResource extends Resource
{
    protected static ?string $model = ReviewLayananModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Star;

    protected static ?string $recordTitleAttribute = 'komentar';

    protected static ?string $navigationLabel = 'Review Layanan';

    protected static ?string $slug = 'rekap-review';

    protected static string|UnitEnum|null $navigationGroup = 'Review';

    protected static ?int $navigationSort = 4;

    protected static ?string $pluralModelLabel = 'Review Layanan';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return RekapReviewsTable::configure($table);
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
            'index' => ListRekapReviews::route('/'),
        ];
    }
}
