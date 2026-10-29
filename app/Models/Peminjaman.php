<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'tanggal_pinjam',
        'jam_mulai',
        'jam_selesai',
        'keperluan',
        'dokumen_pendukung',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailRuangan()
    {
        return $this->hasMany(DetailRuangan::class, 'peminjaman_id');
    }

    public function detailAlat()
    {
        return $this->hasMany(DetailAlat::class, 'peminjaman_id');
    }

    public function serahTerima()
    {
        return $this->hasOne(SerahTerima::class, 'peminjaman_id');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

    public function persetujuan()
    {
        return $this->hasMany(Persetujuan::class, 'peminjaman_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'peminjaman_id');
    }

    // Shortcut accessor to get the main/first ruangan
    public function getRuanganAttribute()
    {
        return $this->detailRuangan->first()?->ruangan;
    }

    // Shortcut accessor to get title / title alias
    public function getTitleAttribute()
    {
        return $this->keperluan;
    }
}
