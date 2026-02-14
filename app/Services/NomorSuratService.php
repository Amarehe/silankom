<?php

namespace App\Services;

use App\Models\PeminjamanModel;

class NomorSuratService
{
    /**
     * Generate nomor surat peminjaman otomatis
     * Format: Peminjaman/TTPPE/II/002/2026/Komlek
     * Auto reset per bulan
     */
    public static function generate(): string
    {
        $bulan = now()->format('m');
        $tahun = now()->format('Y');
        $bulanRomawi = self::toRoman((int) $bulan);

        // Hitung nomor urut untuk bulan ini (auto reset per bulan)
        $lastNumber = PeminjamanModel::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->count();

        $nomorUrut = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "Peminjaman/TTPPE/{$bulanRomawi}/{$nomorUrut}/{$tahun}/Komlek";
    }

    /**
     * Generate nomor surat pengembalian otomatis
     * Format: Pengembalian/TTPPE/II/002/2026/Komlek
     * Auto reset per bulan, urutan terpisah dari peminjaman
     */
    public static function generatePengembalian(): string
    {
        $bulan = now()->format('m');
        $tahun = now()->format('Y');
        $bulanRomawi = self::toRoman((int) $bulan);

        // Hitung nomor urut pengembalian untuk bulan ini (terpisah dari peminjaman)
        $lastNumber = PeminjamanModel::whereYear('tanggal_kembali', $tahun)
            ->whereMonth('tanggal_kembali', $bulan)
            ->whereNotNull('tanggal_kembali')
            ->count();

        $nomorUrut = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "Pengembalian/TTPPE/{$bulanRomawi}/{$nomorUrut}/{$tahun}/Komlek";
    }

    /**
     * Konversi angka bulan ke romawi
     */
    private static function toRoman(int $number): string
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $map[$number] ?? 'I';
    }
}
