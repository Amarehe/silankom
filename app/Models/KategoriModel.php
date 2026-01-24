<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriModel extends Model
{
    use HasFactory;
    protected $table = 'kategori_barang';
    protected $guarded = [];

    public function produk(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
