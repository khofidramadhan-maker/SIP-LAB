<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'kapasitas',
        'lokasi',
        'fasilitas',
        'status',
    ];

    public function detailRuangan()
    {
        return $this->hasMany(DetailRuangan::class, 'ruangan_id');
    }

    public function peminjaman()
    {
        return $this->hasManyThrough(Peminjaman::class, DetailRuangan::class, 'ruangan_id', 'id', 'id', 'peminjaman_id');
    }
}
