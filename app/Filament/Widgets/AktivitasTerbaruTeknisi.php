<?php

namespace App\Filament\Widgets;

use App\Models\PerbaikanModel;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AktivitasTerbaruTeknisi extends Widget
{
    protected static bool $isDiscovered = false;

    // Kita bisa memakai blade view yang sama karena data strukturnya sama
    protected string $view = 'filament.widgets.aktivitas-terbaru';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    /**
     * Mengambil 10 aktivitas terbaru khusus untuk teknisi yang sedang login.
     *
     * @return Collection<int, array{waktu: \Carbon\Carbon, ikon: string, warna: string, deskripsi: string, user: string, modul: string}>
     */
    public function getAktivitas(): Collection
    {
        $aktivitas = collect();
        $userId = Auth::id();

        if (! $userId) {
            return $aktivitas;
        }

        // Teknisi Komlek fokus pada Perbaikan dan Dukungan

        // Perbaikan yang ditugaskan kepada saya
        PerbaikanModel::query()
            ->with('pemohon')
            ->where('teknisi_id', $userId)
            ->latest()
            ->limit(10)
            ->get()
            ->each(function ($item) use ($aktivitas): void {
                $statusLabel = match ($item->status_perbaikan) {
                    'diajukan' => 'menunggu tindak lanjut',
                    'diproses' => 'sedang dikerjakan (Tugas Anda)',
                    'selesai' => 'tugas perbaikan diselesaikan',
                    'tidak_bisa_diperbaiki' => 'barang tidak bisa diperbaiki',
                    default => 'status perbaikan diperbarui',
                };

                $aktivitas->push([
                    'waktu' => $item->updated_at,
                    'ikon' => $this->getIkonByStatus($item->status_perbaikan, 'perbaikan'),
                    'warna' => $this->getWarnaByStatus($item->status_perbaikan, 'perbaikan'),
                    'deskripsi' => ucfirst($statusLabel),
                    'user' => $item->pemohon?->name ?? 'User',
                    'modul' => 'Perbaikan',
                ]);
            });

        return $aktivitas->sortByDesc('waktu')->take(10)->values();
    }

    private function getIkonByStatus(string $status, string $modul): string
    {
        return match ($modul) {
            'perbaikan' => match ($status) {
                'diajukan' => 'heroicon-o-wrench-screwdriver',
                'diproses' => 'heroicon-o-cog-6-tooth',
                'selesai' => 'heroicon-o-check-badge',
                'tidak_bisa_diperbaiki' => 'heroicon-o-x-circle',
                default => 'heroicon-o-wrench',
            },
            default => 'heroicon-o-bell',
        };
    }

    private function getWarnaByStatus(string $status, string $modul): string
    {
        return match ($modul) {
            'perbaikan' => match ($status) {
                'diajukan' => 'warning',
                'diproses' => 'info',
                'selesai' => 'success',
                'tidak_bisa_diperbaiki' => 'danger',
                default => 'gray',
            },
            default => 'gray',
        };
    }
}
