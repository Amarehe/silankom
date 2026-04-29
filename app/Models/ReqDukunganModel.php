<?php

namespace App\Models;

use App\Notifications\DukunganBaruNotification;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReqDukunganModel extends Model
{
    protected $table = 'req_dukungan';

    protected $guarded = [];

    protected static function booted(): void
    {
        // Kirim notifikasi ke admin & teknisi saat ada pengajuan dukungan baru
        static::created(function (ReqDukunganModel $dukungan) {
            DukunganBaruNotification::send($dukungan);
        });

        // Kirim notifikasi ke pemohon saat status berubah
        static::updated(function (ReqDukunganModel $dukungan) {
            if ($dukungan->isDirty('status_dukungan') && $dukungan->pemohon) {
                StatusUpdateNotification::send(
                    $dukungan->pemohon,
                    'Dukungan',
                    $dukungan->status_dukungan,
                    $dukungan->id,
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'req_barang' => 'array',
            'barang_diberikan' => 'array',
            'tgl_kegiatan' => 'date',
            'tgl_disetujui' => 'date',
        ];
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function picDukungan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_dukungan_id');
    }
}
