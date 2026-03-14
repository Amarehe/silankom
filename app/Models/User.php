<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'name',
        'password',
        'jabatan_id',
        'unitkerja_id',
        'role_id',
        'last_login',
    ];

    // Relasi ke model Jabatan
    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'jabatan_id', 'id');
    }

    // Relasi ke model UnitKerja
    public function unitkerja()
    {
        return $this->belongsTo(UnitKerjaModel::class, 'unitkerja_id', 'id');
    }

    // Relasi ke model Role
    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id', 'id');
    }

    // Agar user bisa mengakses Filament Admin Panel
    public function canAccessFilament(): bool
    {
        // Contoh: return $this->role_id === 1;
        return true; // Sementara return true agar semua user di tabel bisa login
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
