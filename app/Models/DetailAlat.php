<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailAlat extends Model
{
    use HasFactory;

    protected $table = 'detail_alat';

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
        'kondisi_awal',
        'kondisi_akhir',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    public function kerusakanAlat()
    {
        return $this->hasMany(KerusakanAlat::class, 'detail_alat_id');
    }
}
