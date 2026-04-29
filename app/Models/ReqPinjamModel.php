<?php

namespace App\Models;

use App\Notifications\PengajuanBaruNotification;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReqPinjamModel extends Model
{
    protected $table = 'req_pinjam';

    protected $guarded = [];

    protected static function booted(): void
    {
        // Kirim notifikasi ke admin saat ada pengajuan baru
        static::created(function (ReqPinjamModel $reqPinjam) {
            PengajuanBaruNotification::send($reqPinjam);
        });

        // Kirim notifikasi ke pemohon saat status berubah
        static::updated(function (ReqPinjamModel $reqPinjam) {
            if ($reqPinjam->isDirty('status') && $reqPinjam->user) {
                // Untuk status disetujui, notifikasi dikirim manual setelah PeminjamanModel dibuat
                if ($reqPinjam->status === 'disetujui') {
                    return;
                }

                StatusUpdateNotification::send(
                    $reqPinjam->user,
                    'Peminjaman',
                    $reqPinjam->status,
                    $reqPinjam->id,
                );
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
