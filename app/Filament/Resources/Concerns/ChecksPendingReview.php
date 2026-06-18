<?php

namespace App\Filament\Resources\Concerns;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait ChecksPendingReview
{
    /**
     * Check if the current user has any completed services without a review (across all types).
     */
    public static function hasPendingReview(): bool
    {
        return self::getPendingReviewCount() > 0;
    }

    /**
     * Get the count of unreviewed services for the current user.
     */
    public static function getPendingReviewCount(): int
    {
        $userId = Auth::id();

        $peminjamanCount = PeminjamanModel::query()
            ->whereHas('reqPinjam', fn ($q) => $q->where('user_id', $userId))
            ->where('status_peminjaman', 'dikembalikan')
            ->whereDoesntHave('review')
            ->count();

        $perbaikanCount = PerbaikanModel::query()
            ->where('pemohon_id', $userId)
            ->whereIn('status_perbaikan', ['selesai', 'tidak_bisa_diperbaiki'])
            ->whereDoesntHave('review')
            ->count();

        $dukunganCount = ReqDukunganModel::query()
            ->where('pemohon_id', $userId)
            ->whereIn('status_dukungan', ['didukung', 'tidak_didukung'])
            ->whereDoesntHave('review')
            ->count();

        return $peminjamanCount + $perbaikanCount + $dukunganCount;
    }

    /**
     * Get detailed list of pending reviews with redirect URLs.
     *
     * @return Collection<int, array{jenis: string, label: string, url: string, icon: string, color: string}>
     */
    public static function getPendingReviewDetails(): Collection
    {
        $userId = Auth::id();
        $items = collect();

        // Peminjaman
        $peminjamanCount = PeminjamanModel::query()
            ->whereHas('reqPinjam', fn ($q) => $q->where('user_id', $userId))
            ->where('status_peminjaman', 'dikembalikan')
            ->whereDoesntHave('review')
            ->count();

        if ($peminjamanCount > 0) {
            $items->push([
                'jenis' => 'Peminjaman',
                'count' => $peminjamanCount,
                'label' => "Isi Review Peminjaman ({$peminjamanCount})",
                'url' => url('/riwayat-peminjaman'),
                'icon' => 'heroicon-o-star',
                'color' => 'warning',
            ]);
        }

        // Perbaikan
        $perbaikanCount = PerbaikanModel::query()
            ->where('pemohon_id', $userId)
            ->whereIn('status_perbaikan', ['selesai', 'tidak_bisa_diperbaiki'])
            ->whereDoesntHave('review')
            ->count();

        if ($perbaikanCount > 0) {
            $items->push([
                'jenis' => 'Perbaikan',
                'count' => $perbaikanCount,
                'label' => "Isi Review Perbaikan ({$perbaikanCount})",
                'url' => url('/riwayat-perbaikan'),
                'icon' => 'heroicon-o-star',
                'color' => 'warning',
            ]);
        }

        // Dukungan
        $dukunganCount = ReqDukunganModel::query()
            ->where('pemohon_id', $userId)
            ->whereIn('status_dukungan', ['didukung', 'tidak_didukung'])
            ->whereDoesntHave('review')
            ->count();

        if ($dukunganCount > 0) {
            $items->push([
                'jenis' => 'Dukungan',
                'count' => $dukunganCount,
                'label' => "Isi Review Dukungan ({$dukunganCount})",
                'url' => url('/riwayat-dukungan-user'),
                'icon' => 'heroicon-o-star',
                'color' => 'warning',
            ]);
        }

        return $items;
    }
}
