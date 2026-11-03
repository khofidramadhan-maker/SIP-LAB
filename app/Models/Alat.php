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

    /** Satu unit fisik per baris, mis. AST-004-01 s.d. AST-004-15. */
    public function units()
    {
        return $this->hasMany(UnitAlat::class, 'alat_id')->orderBy('kode_unit');
    }

    public function availableUnits()
    {
        return $this->units()->where('status', UnitAlat::STATUS_TERSEDIA);
    }

    /**
     * Alias yang dipakai dosen/pengguna: kode barang dan kode alat
     * sama-sama merujuk ke kode inventaris yang sudah ada di DB.
     * Tidak ada kolom baru, jadi tidak perlu migrasi.
     */
    public function getKodeBarangAttribute(): ?string
    {
        return $this->kode_inventaris;
    }

    public function getKodeAlatAttribute(): ?string
    {
        return $this->kode_inventaris;
    }
}
