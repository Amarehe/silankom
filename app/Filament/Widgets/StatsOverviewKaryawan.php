<?php

namespace App\Filament\Widgets;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Models\ReqPinjamModel;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewKaryawan extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '15s';

    protected static ?int $sort = 1;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            $this->buildBarangTersediaStat(),
            $this->buildBarangDipinjamStat(),
            $this->buildPengajuanPinjamStat($userId, $startOfMonth),
            $this->buildPeminjamanAktifStat($userId, $startOfMonth),
            $this->buildPengajuanPerbaikanStat($userId, $startOfMonth),
            $this->buildTiketDukunganStat($userId, $startOfMonth),
        ];
    }

    private function buildBarangTersediaStat(): Stat
    {
        $tersedia = BarangModel::query()->where('status', 'tersedia')->count();

        $kategoriInfo = KategoriModel::query()->get()->map(function (KategoriModel $kategori): string {
            $count = BarangModel::query()
                ->where('kategori_id', $kategori->id)
                ->where('status', 'tersedia')
                ->count();

            return "{$kategori->nama_kategori}: {$count}";
        })->implode(' | ');

        return Stat::make('Barang Tersedia', number_format($tersedia))
            ->description($kategoriInfo ?: 'Tidak ada kategori')
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('success');
    }

    private function buildBarangDipinjamStat(): Stat
    {
        $dipinjam = BarangModel::query()->where('status', 'dipinjam')->count();

        return Stat::make('Barang Dipinjam', number_format($dipinjam))
            ->description('Sedang dipinjam')
            ->descriptionIcon('heroicon-m-arrow-path')
            ->color('warning');
    }

    private function buildPengajuanPinjamStat(int $userId, Carbon $startOfMonth): Stat
    {
        $pending = ReqPinjamModel::query()
            ->where('user_id', $userId)
            ->where('status', 'diproses')
            ->count();

        $total = ReqPinjamModel::query()->where('user_id', $userId)->count();

        return Stat::make('Pengajuan Peminjaman', number_format($pending))
            ->description("{$total} total riwayat pengajuan")
            ->descriptionIcon('heroicon-m-clock')
            ->color('warning');
    }

    private function buildPeminjamanAktifStat(int $userId, Carbon $startOfMonth): Stat
    {
        $aktif = PeminjamanModel::query()
            ->whereHas('reqPinjam', fn ($q) => $q->where('user_id', $userId))
            ->where('status_peminjaman', 'dipinjam')
            ->count();

        return Stat::make('Peminjaman Aktif', number_format($aktif))
            ->description('Barang yang belum dikembalikan')
            ->descriptionIcon('heroicon-m-arrow-path')
            ->color('info');
    }

    private function buildPengajuanPerbaikanStat(int $userId, Carbon $startOfMonth): Stat
    {
        $pending = PerbaikanModel::query()
            ->where('pemohon_id', $userId)
            ->whereIn('status_perbaikan', ['diajukan', 'diproses'])
            ->count();

        $total = PerbaikanModel::query()->where('pemohon_id', $userId)->count();

        return Stat::make('Pengajuan Perbaikan', number_format($pending))
            ->description("{$total} total riwayat perbaikan")
            ->descriptionIcon('heroicon-m-wrench-screwdriver')
            ->color('danger');
    }

    private function buildTiketDukunganStat(int $userId, Carbon $startOfMonth): Stat
    {
        $pending = ReqDukunganModel::query()
            ->where('pemohon_id', $userId)
            ->whereIn('status_dukungan', ['belum_didukung', 'sedang_diproses'])
            ->count();

        $total = ReqDukunganModel::query()->where('pemohon_id', $userId)->count();

        return Stat::make('Tiket Dukungan', number_format($pending))
            ->description("{$total} total riwayat dukungan")
            ->descriptionIcon('heroicon-m-ticket')
            ->color('primary');
    }
}
