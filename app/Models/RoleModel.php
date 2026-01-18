<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleModel extends Model
{
    use HasFactory;
    protected $table = 'role';
    protected $guarded = [];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
