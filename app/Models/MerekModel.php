<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerekModel extends Model
{
    use HasFactory;
    protected $table = 'merek';
    protected $guarded = [];

    public function produk(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
