<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KerusakanAlat extends Model
{
    use HasFactory;

    protected $table = 'kerusakan_alat';

    protected $fillable = [
        'pengembalian_id',
        'detail_alat_id',
        'jenis_kondisi',
        'deskripsi',
    ];

    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class, 'pengembalian_id');
    }

    public function detailAlat()
    {
        return $this->belongsTo(DetailAlat::class, 'detail_alat_id');
    }
}
