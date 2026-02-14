<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanModel extends Model
{
    protected $table = 'peminjaman';

    protected $guarded = [];

    protected static function booted()
    {
        // Update status barang saat peminjaman dibuat
        static::created(function ($peminjaman) {
            BarangModel::where('id_barang', $peminjaman->barang_id)
                ->update(['status' => 'dipinjam']);
        });

        // Update status barang saat peminjaman dikembalikan
        static::updated(function ($peminjaman) {
            if ($peminjaman->status_peminjaman === 'dikembalikan') {
                BarangModel::where('id_barang', $peminjaman->barang_id)
                    ->update(['status' => 'tersedia']);
            }
        });
    }

    // Relasi ke pengajuan
    public function reqPinjam(): BelongsTo
    {
        return $this->belongsTo(ReqPinjamModel::class, 'req_pinjam_id', 'id');
    }

    // Relasi ke barang
    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'id_barang');
    }

    // Relasi ke admin yang menyetujui
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    // Relasi ke admin yang menerima pengembalian
    public function adminPenerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_penerima_id', 'id');
    }

    // Accessor untuk mendapatkan peminjam via reqPinjam
    public function peminjam()
    {
        return $this->reqPinjam?->user;
    }
}
