<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitAlat extends Model
{
    use HasFactory;

    public const STATUS_TERSEDIA = 'tersedia';

    public const STATUS_DIPINJAM = 'dipinjam';

    public const STATUS_RUSAK = 'rusak';

    protected $table = 'unit_alat';

    protected $fillable = [
        'alat_id',
        'kode_unit',
        'status',
    ];

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    public function detailAlatUnits()
    {
        return $this->hasMany(DetailAlatUnit::class, 'unit_alat_id');
    }

    public function kerusakanAlat()
    {
        return $this->hasMany(KerusakanAlat::class, 'unit_alat_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_TERSEDIA);
    }

    public static function codeFor(Alat $alat, int $number): string
    {
        return $alat->kode_inventaris.'-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Pastikan sebuah jenis alat punya tepat satu unit per jumlah_total,
     * dengan kode berurutan. Dipakai saat alat dibuat, saat seeder jalan,
     * dan saat jumlah_total diubah. Unit yang sedang dipinjam / rusak
     * tidak pernah dihapus oleh method ini.
     */
    public static function ensureForAlat(Alat $alat): void
    {
        $usableCount = static::query()
            ->where('alat_id', $alat->id)
            ->where('status', '!=', self::STATUS_RUSAK)
            ->count();
        $number = 1;

        while ($usableCount < (int) $alat->jumlah_total) {
            $unit = static::firstOrCreate(
                ['kode_unit' => static::codeFor($alat, $number++)],
                ['alat_id' => $alat->id, 'status' => self::STATUS_TERSEDIA]
            );

            if ((int) $unit->alat_id === (int) $alat->id && $unit->status !== self::STATUS_RUSAK) {
                $usableCount++;
            }
        }
    }
}
