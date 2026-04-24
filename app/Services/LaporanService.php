<?php

namespace App\Services;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LaporanService
{
    /**
     * Build filtered query for Peminjaman reports.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getQueryPeminjaman(array $filters): Builder
    {
        $query = PeminjamanModel::query()
            ->with([
                'reqPinjam.user.jabatan',
                'reqPinjam.user.unitkerja',
                'reqPinjam.kategori',
                'barang.merek',
                'barang.kategori',
                'admin',
                'adminPenerima',
            ]);

        $this->applyPeriodFilter($query, 'tanggal_serah_terima', $filters);

        if (! empty($filters['status_peminjaman'])) {
            $query->where('status_peminjaman', $filters['status_peminjaman']);
        }

        if (! empty($filters['kondisi_barang'])) {
            $query->where('kondisi_barang', $filters['kondisi_barang']);
        }

        if (! empty($filters['kategori_id'])) {
            $query->whereHas('barang', fn (Builder $q) => $q->where('kategori_id', $filters['kategori_id']));
        }

        if (! empty($filters['unit_kerja_id'])) {
            $query->whereHas('reqPinjam.user', fn (Builder $q) => $q->where('unitkerja_id', $filters['unit_kerja_id']));
        }

        return $query->orderBy('tanggal_serah_terima', 'desc');
    }

    /**
     * Build filtered query for Perbaikan reports.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getQueryPerbaikan(array $filters): Builder
    {
        $query = PerbaikanModel::query()
            ->with([
                'pemohon.jabatan',
                'pemohon.unitkerja',
                'kategori',
                'merek',
                'teknisi',
            ]);

        $this->applyPeriodFilter($query, 'tgl_pengajuan', $filters);

        if (! empty($filters['status_perbaikan'])) {
            $query->where('status_perbaikan', $filters['status_perbaikan']);
        }

        if (! empty($filters['kategori_id'])) {
            $query->where('kategori_id', $filters['kategori_id']);
        }

        if (! empty($filters['merek_id'])) {
            $query->where('merek_id', $filters['merek_id']);
        }

        if (! empty($filters['teknisi_id'])) {
            $query->where('teknisi_id', $filters['teknisi_id']);
        }

        return $query->orderBy('tgl_pengajuan', 'desc');
    }

    /**
     * Build filtered query for Dukungan Kegiatan reports.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getQueryDukungan(array $filters): Builder
    {
        $query = ReqDukunganModel::query()
            ->with([
                'pemohon.jabatan',
                'pemohon.unitkerja',
                'picDukungan',
            ]);

        $this->applyPeriodFilter($query, 'tgl_kegiatan', $filters);

        if (! empty($filters['status_dukungan'])) {
            $query->where('status_dukungan', $filters['status_dukungan']);
        }

        if (! empty($filters['pic_dukungan_id'])) {
            $query->where('pic_dukungan_id', $filters['pic_dukungan_id']);
        }

        return $query->orderBy('tgl_kegiatan', 'desc');
    }

    /**
     * Apply period-based date filters to the query.
     *
     * @param  array<string, mixed>  $filters
     */
    public function applyPeriodFilter(Builder $query, string $dateColumn, array $filters): Builder
    {
        $tipePeriode = $filters['tipe_periode'] ?? null;
        $tahun = $filters['tahun'] ?? null;
        $bulan = $filters['bulan'] ?? null;
        $triwulan = $filters['triwulan'] ?? null;
        $semester = $filters['semester'] ?? null;
        $tanggalDari = $filters['tanggal_dari'] ?? null;
        $tanggalSampai = $filters['tanggal_sampai'] ?? null;

        return match ($tipePeriode) {
            'bulanan' => $query->when($tahun && $bulan, fn (Builder $q) => $q
                ->whereYear($dateColumn, $tahun)
                ->whereMonth($dateColumn, $bulan)),

            'triwulan' => $query->when($tahun && $triwulan, function (Builder $q) use ($dateColumn, $tahun, $triwulan) {
                $startMonth = (($triwulan - 1) * 3) + 1;
                $start = Carbon::create($tahun, $startMonth, 1)->startOfDay();
                $end = $start->copy()->addMonths(2)->endOfMonth();
                $q->whereBetween($dateColumn, [$start, $end]);
            }),

            'semester' => $query->when($tahun && $semester, function (Builder $q) use ($dateColumn, $tahun, $semester) {
                $startMonth = $semester === 1 ? 1 : 7;
                $start = Carbon::create($tahun, $startMonth, 1)->startOfDay();
                $end = $start->copy()->addMonths(5)->endOfMonth();
                $q->whereBetween($dateColumn, [$start, $end]);
            }),

            'tahunan' => $query->when($tahun, fn (Builder $q) => $q->whereYear($dateColumn, $tahun)),

            'custom' => $query
                ->when($tanggalDari, fn (Builder $q) => $q->whereDate($dateColumn, '>=', $tanggalDari))
                ->when($tanggalSampai, fn (Builder $q) => $q->whereDate($dateColumn, '<=', $tanggalSampai)),

            default => $query,
        };
    }

    /**
     * Compute summary statistics for a given report type.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getStatistik(string $jenis, array $filters): array
    {
        return match ($jenis) {
            'peminjaman' => $this->getStatistikPeminjaman($filters),
            'perbaikan' => $this->getStatistikPerbaikan($filters),
            'dukungan' => $this->getStatistikDukungan($filters),
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function getStatistikPeminjaman(array $filters): array
    {
        $query = $this->getQueryPeminjaman($filters);
        $data = $query->get();

        return [
            'total' => $data->count(),
            'dipinjam' => $data->where('status_peminjaman', 'dipinjam')->count(),
            'dikembalikan' => $data->where('status_peminjaman', 'dikembalikan')->count(),
            'kondisi_baik' => $data->where('kondisi_barang', 'baik')->count(),
            'kondisi_rusak_ringan' => $data->where('kondisi_barang', 'rusak ringan')->count(),
            'kondisi_rusak_berat' => $data->where('kondisi_barang', 'rusak berat')->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function getStatistikPerbaikan(array $filters): array
    {
        $query = $this->getQueryPerbaikan($filters);
        $data = $query->get();

        return [
            'total' => $data->count(),
            'diajukan' => $data->where('status_perbaikan', 'diajukan')->count(),
            'diproses' => $data->where('status_perbaikan', 'diproses')->count(),
            'selesai' => $data->where('status_perbaikan', 'selesai')->count(),
            'tidak_bisa_diperbaiki' => $data->where('status_perbaikan', 'tidak_bisa_diperbaiki')->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function getStatistikDukungan(array $filters): array
    {
        $query = $this->getQueryDukungan($filters);
        $data = $query->get();

        return [
            'total' => $data->count(),
            'belum_didukung' => $data->where('status_dukungan', 'belum_didukung')->count(),
            'didukung' => $data->where('status_dukungan', 'didukung')->count(),
            'tidak_didukung' => $data->where('status_dukungan', 'tidak_didukung')->count(),
        ];
    }

    /**
     * Generate human-readable period label for the report header.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getLabelPeriode(array $filters): string
    {
        $tipePeriode = $filters['tipe_periode'] ?? null;
        $tahun = $filters['tahun'] ?? null;
        $bulan = $filters['bulan'] ?? null;

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return match ($tipePeriode) {
            'bulanan' => 'Bulan '.($namaBulan[(int) $bulan] ?? '').' '.$tahun,
            'triwulan' => 'Triwulan '.($filters['triwulan'] ?? '').' Tahun '.$tahun,
            'semester' => 'Semester '.($filters['semester'] ?? '').' Tahun '.$tahun,
            'tahunan' => 'Tahun '.$tahun,
            'custom' => 'Periode '.($filters['tanggal_dari'] ? Carbon::parse($filters['tanggal_dari'])->translatedFormat('d F Y') : '...').' s/d '.($filters['tanggal_sampai'] ? Carbon::parse($filters['tanggal_sampai'])->translatedFormat('d F Y') : '...'),
            default => 'Semua Periode',
        };
    }
}
