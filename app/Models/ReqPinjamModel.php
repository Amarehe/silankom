<?php

namespace App\Models;

use App\Notifications\PengajuanBaruNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReqPinjamModel extends Model
{
    protected $table = 'req_pinjam';

    protected $guarded = [];

    protected static function booted()
    {
        // Kirim notifikasi ke admin saat ada pengajuan baru
        static::created(function ($reqPinjam) {
            // Ambil user pertama sebagai admin (atau bisa disesuaikan dengan logic role Anda)
            $admin = User::first();
            if ($admin) {
                $admin->notify(new PengajuanBaruNotification($reqPinjam));
            }
        });
    }

    // Relasi ke user yang mengajukan
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi ke kategori barang
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'id');
    }

    // Relasi ke peminjaman (jika disetujui)
    public function peminjaman(): HasOne
    {
        return $this->hasOne(PeminjamanModel::class, 'req_pinjam_id', 'id');
    }
}
