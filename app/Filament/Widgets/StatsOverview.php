<?php

namespace App\Filament\Widgets;

use App\Models\BarangModel;
use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Models\ReqPinjamModel;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '15s';

    protected static ?int $sort = 1;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            $this->buildTotalBarangStat($startOfMonth),
            $this->buildBarangTersediaStat(),
            $this->buildBarangDipinjamStat(),
            $this->buildPengajuanPendingStat($startOfMonth),
            $this->buildPeminjamanAktifStat($startOfMonth),
            $this->buildPerbaikanStat($startOfMonth),
            $this->buildDukunganPendingStat($startOfMonth),
            $this->buildTotalUserStat($startOfMonth),
        ];
    }

    private function buildTotalBarangStat(Carbon $startOfMonth): Stat
    {
        $total = BarangModel::query()->count();
        $bulanIni = BarangModel::query()->where('created_at', '>=', $startOfMonth)->count();

        return Stat::make('Total Barang', number_format($total))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Tidak ada perubahan')
            ->descriptionIcon($bulanIni > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
            ->color('primary')
            ->chart($this->getSparkline(BarangModel::class));
    }

    private function buildBarangTersediaStat(): Stat
    {
        $tersedia = BarangModel::query()->where('status', 'tersedia')->count();
        $total = BarangModel::query()->count();
        $persen = $total > 0 ? round(($tersedia / $total) * 100) : 0;

        return Stat::make('Barang Tersedia', number_format($tersedia))
            ->description("{$persen}% dari total")
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

    private function buildPengajuanPendingStat(Carbon $startOfMonth): Stat
    {
        $pending = ReqPinjamModel::query()->where('status', 'diproses')->count();
        $bulanIni = ReqPinjamModel::query()
            ->where('status', 'diproses')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        return Stat::make('Pengajuan Pinjam', number_format($pending))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Tidak ada pending')
            ->descriptionIcon('heroicon-m-clock')
            ->color('warning')
            ->chart($this->getSparkline(ReqPinjamModel::class));
    }

    private function buildPeminjamanAktifStat(Carbon $startOfMonth): Stat
    {
        $aktif = PeminjamanModel::query()->where('status_peminjaman', 'dipinjam')->count();
        $bulanIni = PeminjamanModel::query()
            ->where('status_peminjaman', 'dipinjam')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        return Stat::make('Peminjaman Aktif', number_format($aktif))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Stabil')
            ->descriptionIcon('heroicon-m-document-text')
            ->color('info')
            ->chart($this->getSparkline(PeminjamanModel::class));
    }

    private function buildPerbaikanStat(Carbon $startOfMonth): Stat
    {
        $pending = PerbaikanModel::query()
            ->whereIn('status_perbaikan', ['diajukan', 'diproses'])
            ->count();
        $bulanIni = PerbaikanModel::query()
            ->whereIn('status_perbaikan', ['diajukan', 'diproses'])
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        return Stat::make('Pengajuan Perbaikan', number_format($pending))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Tidak ada pending')
            ->descriptionIcon('heroicon-m-wrench-screwdriver')
            ->color('danger')
            ->chart($this->getSparkline(PerbaikanModel::class));
    }

    private function buildDukunganPendingStat(Carbon $startOfMonth): Stat
    {
        $pending = ReqDukunganModel::query()->where('status_dukungan', 'belum_didukung')->count();
        $bulanIni = ReqDukunganModel::query()
            ->where('status_dukungan', 'belum_didukung')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        return Stat::make('Dukungan Pending', number_format($pending))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Tidak ada pending')
            ->descriptionIcon('heroicon-m-hand-raised')
            ->color('warning')
            ->chart($this->getSparkline(ReqDukunganModel::class));
    }

    private function buildTotalUserStat(Carbon $startOfMonth): Stat
    {
        $total = User::query()->count();
        $bulanIni = User::query()->where('created_at', '>=', $startOfMonth)->count();

        return Stat::make('Total User', number_format($total))
            ->description($bulanIni > 0 ? "+{$bulanIni} bulan ini" : 'Tidak ada perubahan')
            ->descriptionIcon('heroicon-m-users')
            ->color('primary')
            ->chart($this->getSparkline(User::class));
    }

    /**
     * Generate sparkline data (last 7 days).
     *
     * @param  class-string  $modelClass
     * @return array<int, int>
     */
    private function getSparkline(string $modelClass): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data[] = $modelClass::query()->whereDate('created_at', $date)->count();
        }

        return $data;
    }
}
