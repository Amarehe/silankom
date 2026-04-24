<?php

use App\Http\Controllers\LaporanController;
use App\Models\PeminjamanModel;
use App\Models\PerbaikanModel;
use App\Services\TandaTerimaService;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route untuk download PDF tanda terima
Route::get('/download-tanda-terima/{peminjaman}', function (PeminjamanModel $peminjaman) {
    // Pastikan user hanya bisa download PDF miliknya sendiri (kecuali admin)
    if (auth()->user()->role_id != 1 && $peminjaman->reqPinjam->user_id != auth()->id()) {
        abort(403, 'Unauthorized');
    }

    return TandaTerimaService::generate($peminjaman);
})->middleware('auth')->name('download.tanda-terima');

// Route untuk download PDF tanda terima pengembalian
Route::get('/download-tanda-terima-pengembalian/{peminjaman}', function (PeminjamanModel $peminjaman) {
    // Pastikan user hanya bisa download PDF miliknya sendiri (kecuali admin)
    if (auth()->user()->role_id != 1 && $peminjaman->reqPinjam->user_id != auth()->id()) {
        abort(403, 'Unauthorized');
    }

    // Pastikan peminjaman sudah dikembalikan dan memiliki nomor surat pengembalian
    if ($peminjaman->status_peminjaman !== 'dikembalikan' || $peminjaman->nomor_surat_pengembalian === null) {
        abort(404, 'Tanda terima pengembalian tidak tersedia');
    }

    return TandaTerimaService::generatePengembalian($peminjaman);
})->middleware('auth')->name('download.tanda-terima-pengembalian');

// Route untuk download PDF surat perbaikan
Route::get('/download-surat-perbaikan/{perbaikan}', function (PerbaikanModel $perbaikan) {
    // Pastikan user hanya bisa download PDF miliknya sendiri (kecuali admin)
    if (auth()->user()->role_id != 1 && $perbaikan->pemohon_id != auth()->id()) {
        abort(403, 'Unauthorized');
    }

    // Pastikan perbaikan sudah memiliki nomor surat
    if ($perbaikan->no_surat_perbaikan === null) {
        abort(404, 'Surat perbaikan tidak tersedia');
    }

    return TandaTerimaService::generatePerbaikan($perbaikan);
})->middleware('auth')->name('download.surat-perbaikan');

// Routes untuk Laporan (PDF & Excel)
Route::middleware('auth')->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/pdf/{jenis}', [LaporanController::class, 'exportPdf'])->name('pdf');
    Route::get('/excel/{jenis}', [LaporanController::class, 'exportExcel'])->name('excel');
    Route::get('/print/{jenis}', [LaporanController::class, 'exportPrint'])->name('print');
});
