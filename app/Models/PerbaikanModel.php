<?php

namespace App\Models;

use App\Notifications\PerbaikanBaruNotification;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerbaikanModel extends Model
{
    protected $table = 'perbaikan';

    protected $guarded = [];

    protected static function booted(): void
    {
        // Kirim notifikasi ke admin & teknisi saat ada pengajuan perbaikan baru
        static::created(function (PerbaikanModel $perbaikan) {
            PerbaikanBaruNotification::send($perbaikan);
        });

        // Kirim notifikasi ke pemohon saat status berubah
        static::updated(function (PerbaikanModel $perbaikan) {
            if ($perbaikan->isDirty('status_perbaikan') && $perbaikan->pemohon) {
                StatusUpdateNotification::send(
                    $perbaikan->pemohon,
                    'Perbaikan',
                    $perbaikan->status_perbaikan,
                    $perbaikan->id,
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
            'tgl_pengajuan' => 'date',
            'tgl_perbaikan' => 'date',
        ];
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id');
    }

    public function merek(): BelongsTo
    {
        return $this->belongsTo(MerekModel::class, 'merek_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
}
