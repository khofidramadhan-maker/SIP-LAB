<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'name',
        'nim',
        'email',
        'username',
        'password',
        'role',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // Accessor so $user->name always returns $user->nama if name is not set
    public function getNameAttribute($value)
    {
        return $value ?: $this->attributes['nama'] ?? '';
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }

    public function persetujuan()
    {
        return $this->hasMany(Persetujuan::class, 'user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    // Alias for compatibility
    public function notifications()
    {
        return $this->notifikasi();
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'user_id');
    }

    public function serahTerima()
    {
        return $this->hasMany(SerahTerima::class, 'petugas_id');
    }

    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class, 'petugas_id');
    }
}
