<?php

namespace App\Services;

use App\Models\PeminjamanModel;
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
}
