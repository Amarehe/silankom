<?php

namespace App\Filament\Resources\RiwayatPeminjamans;

use App\Filament\Resources\RiwayatPeminjamans\Pages\ListRiwayatPeminjamans;
use App\Filament\Resources\RiwayatPeminjamans\Tables\RiwayatPeminjamansTable;
use App\Models\PeminjamanModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RiwayatPeminjamanResource extends Resource
{
    protected static ?string $model = PeminjamanModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Riwayat Peminjaman';

    protected static ?string $navigationLabel = 'Riwayat Peminjaman';

    protected static ?string $slug = 'riwayat-peminjaman';

    protected static string|UnitEnum|null $navigationGroup = 'Peminjaman';

    // Urutan Navigation
    protected static ?int $navigationSort = 2;

    // Label untuk banyak item (Plural) - Ini yang muncul di Judul Tabel List
    protected static ?string $pluralModelLabel = 'Riwayat Peminjaman Saya';

    public static function table(Table $table): Table
    {
        return RiwayatPeminjamansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        // User hanya melihat peminjaman mereka sendiri
        return parent::getEloquentQuery()
            ->whereHas('reqPinjam', function ($query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function canCreate(): bool
    {
        return false; // Tidak ada create, karena dibuat oleh admin
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
            'index' => ListRiwayatPeminjamans::route('/'),
        ];
    }
}
