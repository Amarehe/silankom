<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReqDukunganModel extends Model
{
    protected $table = 'req_dukungan';

    protected $guarded = [];

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
