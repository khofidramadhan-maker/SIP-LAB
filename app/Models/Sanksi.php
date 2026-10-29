<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanksi extends Model
{
    use HasFactory;

    protected $table = 'sanksi';

    protected $fillable = [
        'pengembalian_id',
        'jenis_sanksi',
        'nominal',
        'status_lunas',
        'ditandai_oleh',
        'tanggal_lunas',
    ];

    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class, 'pengembalian_id');
    }

    public function penandatangan()
    {
        return $this->belongsTo(User::class, 'ditandai_oleh');
    }
}
