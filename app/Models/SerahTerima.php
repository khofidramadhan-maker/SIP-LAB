<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerahTerima extends Model
{
    use HasFactory;

    protected $table = 'serah_terima';

    protected $fillable = [
        'peminjaman_id',
        'petugas_id',
        'waktu_serah',
        'catatan',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
