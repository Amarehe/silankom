<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReviewLayananModel extends Model
{
    protected $table = 'review_layanan';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getJenisLayananAttribute(): string
    {
        return match ($this->reviewable_type) {
            PeminjamanModel::class => 'Peminjaman',
            PerbaikanModel::class => 'Perbaikan',
            ReqDukunganModel::class => 'Dukungan',
            default => 'Lainnya',
        };
    }

    public function getRataRataAttribute(): float
    {
        return round(($this->rating + $this->aspek_kecepatan + $this->aspek_kualitas + $this->aspek_pelayanan) / 4, 1);
    }
}
