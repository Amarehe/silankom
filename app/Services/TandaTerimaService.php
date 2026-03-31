<?php

namespace App\Services;

use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use Barryvdh\DomPDF\Facade\Pdf;

class TandaTerimaService
{
    /**
     * Generate PDF tanda terima peminjaman on-demand (stream langsung ke browser)
     */
    public static function generate(PeminjamanModel $peminjaman)
    {
        // Eager load semua relasi yang dibutuhkan
        $peminjaman->load([
            'barang.merek',
            'barang.kategori',
            'reqPinjam.user.jabatan',
            'reqPinjam.user.unitkerja',
            'admin.jabatan',
            'admin.unitkerja',
        ]);

        $pdf = PDF::loadView('pdf.tanda_terima_template', [
            'peminjaman' => $peminjaman,
            'peminjam' => $peminjaman->peminjam(),
            'barang' => $peminjaman->barang,
            'admin' => $peminjaman->admin,
        ]);

        // Stream PDF langsung ke browser (tidak disimpan di storage)
        $filename = str_replace('/', '-', $peminjaman->nomor_surat);

        return $pdf->stream("Tanda_Terima_Peminjaman_{$filename}.pdf");
    }

    /**
     * Generate PDF tanda terima pengembalian on-demand (stream langsung ke browser)
     */
    public static function generatePengembalian(PeminjamanModel $peminjaman)
    {
        // Eager load semua relasi yang dibutuhkan
        $peminjaman->load([
            'barang.merek',
            'barang.kategori',
            'reqPinjam.user.jabatan',
            'reqPinjam.user.unitkerja',
            'adminPenerima.jabatan',
            'adminPenerima.unitkerja',
        ]);

        $pdf = PDF::loadView('pdf.tanda_terima_pengembalian_template', [
            'peminjaman' => $peminjaman,
            'peminjam' => $peminjaman->peminjam(),
            'barang' => $peminjaman->barang,
            'adminPenerima' => $peminjaman->adminPenerima,
            'nomorSuratPengembalian' => $peminjaman->nomor_surat_pengembalian,
        ]);

        // Stream PDF langsung ke browser (tidak disimpan di storage)
        $filename = str_replace('/', '-', $peminjaman->nomor_surat_pengembalian);

        return $pdf->stream("Tanda_Terima_Pengembalian_{$filename}.pdf");
    }

    /**
     * Generate PDF surat perbaikan on-demand (stream langsung ke browser)
     */
    public static function generatePerbaikan(PerbaikanModel $perbaikan)
    {
        // Eager load semua relasi yang dibutuhkan
        $perbaikan->load([
            'pemohon.jabatan',
            'pemohon.unitkerja',
            'kategori',
            'merek',
            'teknisi.jabatan',
            'teknisi.unitkerja',
        ]);

        $pdf = PDF::loadView('pdf.surat_perbaikan_template', [
            'perbaikan' => $perbaikan,
        ]);

        // Stream PDF langsung ke browser (tidak disimpan di storage)
        $filename = str_replace('/', '-', $perbaikan->no_surat);

        return $pdf->stream("Surat_Perbaikan_{$filename}.pdf");
    }
}
