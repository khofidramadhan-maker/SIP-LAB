<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailAlatUnit extends Model
{
    use HasFactory;

    protected $table = 'detail_alat_unit';

    protected $fillable = [
        'detail_alat_id',
        'unit_alat_id',
        'kondisi_awal',
        'kondisi_akhir',
    ];

    public function detailAlat()
    {
        return $this->belongsTo(DetailAlat::class, 'detail_alat_id');
    }

    public function unitAlat()
    {
        return $this->belongsTo(UnitAlat::class, 'unit_alat_id');
    }
}
