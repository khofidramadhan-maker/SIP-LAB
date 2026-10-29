<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id',
        'petugas_id',
        'waktu_kembali',
        'hari_terlambat',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function kerusakanAlat()
    {
        return $this->hasMany(KerusakanAlat::class, 'pengembalian_id');
    }

    public function sanksi()
    {
        return $this->hasOne(Sanksi::class, 'pengembalian_id');
    }
}
