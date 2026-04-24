<?php

namespace App\Filament\Widgets;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Models\ReqDukunganModel;
use App\Models\ReqPinjamModel;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AktivitasTerbaru extends Widget
{
    protected static bool $isDiscovered = false;

    protected string $view = 'filament.widgets.aktivitas-terbaru';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    /**
     * Mengambil 10 aktivitas terbaru dari semua modul.
     *
     * @return Collection<int, array{waktu: \Carbon\Carbon, ikon: string, warna: string, deskripsi: string, user: string, modul: string}>
     */
    public function getAktivitas(): Collection
    {
        $aktivitas = collect();

        // Pengajuan Peminjaman
        ReqPinjamModel::query()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->each(function ($item) use ($aktivitas): void {
                $statusLabel = match ($item->status) {
                    'diproses' => 'mengajukan peminjaman baru',
                    'disetujui' => 'pengajuan peminjaman disetujui',
                    'ditolak' => 'pengajuan peminjaman ditolak',
                    default => 'peminjaman diperbarui',
                };

                $aktivitas->push([
                    'waktu' => $item->created_at,
                    'ikon' => $this->getIkonByStatus($item->status, 'pinjam'),
                    'warna' => $this->getWarnaByStatus($item->status, 'pinjam'),
                    'deskripsi' => ucfirst($statusLabel),
                    'user' => $item->user?->name ?? '-',
                    'modul' => 'Peminjaman',
                ]);
            });

        // Peminjaman aktif / dikembalikan
        PeminjamanModel::query()
            ->with('reqPinjam.user')
            ->latest()
            ->limit(10)
            ->get()
            ->each(function ($item) use ($aktivitas): void {
                $statusLabel = match ($item->status_peminjaman) {
                    'dipinjam' => 'barang diserahterimakan',
                    'dikembalikan' => 'barang dikembalikan',
                    default => 'peminjaman diperbarui',
                };

                $aktivitas->push([
                    'waktu' => $item->updated_at,
                    'ikon' => $item->status_peminjaman === 'dikembalikan' ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-clipboard-document-check',
                    'warna' => $item->status_peminjaman === 'dikembalikan' ? 'success' : 'info',
                    'deskripsi' => ucfirst($statusLabel),
                    'user' => $item->reqPinjam?->user?->name ?? '-',
                    'modul' => 'Peminjaman',
                ]);
            });

        // Perbaikan
        PerbaikanModel::query()
            ->with('pemohon')
            ->latest()
            ->limit(10)
            ->get()
            ->each(function ($item) use ($aktivitas): void {
                $statusLabel = match ($item->status_perbaikan) {
                    'diajukan' => 'mengajukan perbaikan baru',
                    'diproses' => 'perbaikan sedang diproses',
                    'selesai' => 'perbaikan selesai',
                    'tidak_bisa_diperbaiki' => 'barang tidak bisa diperbaiki',
                    default => 'perbaikan diperbarui',
                };

                $aktivitas->push([
                    'waktu' => $item->updated_at,
                    'ikon' => $this->getIkonByStatus($item->status_perbaikan, 'perbaikan'),
                    'warna' => $this->getWarnaByStatus($item->status_perbaikan, 'perbaikan'),
                    'deskripsi' => ucfirst($statusLabel),
                    'user' => $item->pemohon?->name ?? '-',
                    'modul' => 'Perbaikan',
                ]);
            });

        // Dukungan
        ReqDukunganModel::query()
            ->with('pemohon')
            ->latest()
            ->limit(10)
            ->get()
            ->each(function ($item) use ($aktivitas): void {
                $statusLabel = match ($item->status_dukungan) {
                    'belum_didukung' => 'mengajukan dukungan baru',
                    'didukung' => 'dukungan disetujui',
                    'tidak_didukung' => 'dukungan ditolak',
                    default => 'dukungan diperbarui',
                };

                $aktivitas->push([
                    'waktu' => $item->updated_at,
                    'ikon' => $this->getIkonByStatus($item->status_dukungan, 'dukungan'),
                    'warna' => $this->getWarnaByStatus($item->status_dukungan, 'dukungan'),
                    'deskripsi' => ucfirst($statusLabel),
                    'user' => $item->pemohon?->name ?? '-',
                    'modul' => 'Dukungan',
                ]);
            });

        return $aktivitas->sortByDesc('waktu')->take(10)->values();
    }

    private function getIkonByStatus(string $status, string $modul): string
    {
        return match ($modul) {
            'pinjam' => match ($status) {
                'diproses' => 'heroicon-o-clock',
                'disetujui' => 'heroicon-o-check-circle',
                'ditolak' => 'heroicon-o-x-circle',
                default => 'heroicon-o-document',
            },
            'perbaikan' => match ($status) {
                'diajukan' => 'heroicon-o-wrench-screwdriver',
                'diproses' => 'heroicon-o-cog-6-tooth',
                'selesai' => 'heroicon-o-check-badge',
                'tidak_bisa_diperbaiki' => 'heroicon-o-x-circle',
                default => 'heroicon-o-wrench',
            },
            'dukungan' => match ($status) {
                'belum_didukung' => 'heroicon-o-hand-raised',
                'didukung' => 'heroicon-o-check-circle',
                'tidak_didukung' => 'heroicon-o-x-circle',
                default => 'heroicon-o-hand-raised',
            },
            default => 'heroicon-o-bell',
        };
    }

    private function getWarnaByStatus(string $status, string $modul): string
    {
        return match ($modul) {
            'pinjam' => match ($status) {
                'diproses' => 'warning',
                'disetujui' => 'success',
                'ditolak' => 'danger',
                default => 'gray',
            },
            'perbaikan' => match ($status) {
                'diajukan' => 'warning',
                'diproses' => 'info',
                'selesai' => 'success',
                'tidak_bisa_diperbaiki' => 'danger',
                default => 'gray',
            },
            'dukungan' => match ($status) {
                'belum_didukung' => 'warning',
                'didukung' => 'success',
                'tidak_didukung' => 'danger',
                default => 'gray',
            },
            default => 'gray',
        };
    }
}
