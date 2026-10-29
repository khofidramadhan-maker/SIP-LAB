<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';

    protected $fillable = [
        'kode_inventaris',
        'nama_alat',
        'kategori',
        'jumlah_total',
        'kondisi',
        'status',
    ];

    public function detailAlat()
    {
        return $this->hasMany(DetailAlat::class, 'alat_id');
    }
}
