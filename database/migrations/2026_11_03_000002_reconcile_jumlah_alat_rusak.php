<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rekonsiliasi satu kali untuk unit yang sudah terlanjur rusak SEBELUM
     * aturan "jumlah berkurang jika rusak" dipasang.
     *
     * Unit rusak dari tes lama itu statusnya sudah "rusak", tapi
     * jumlah_total-nya belum pernah dikurangi — makanya di Manajemen Alat
     * masih tampil 15 unit padahal sudah ada 1 unit rusak. Migrasi ini
     * menyamakan jumlah_total dengan jumlah unit yang masih bisa dipakai
     * (tersedia + dipinjam) untuk alat yang jumlahnya masih lebih besar
     * dari itu. Berjalan sekali saja per database (dicatat di tabel
     * migrations), jadi pengembalian rusak sesudah ini tidak akan
     * terpotong dua kali: yang sesudah ini dikurangi oleh StaffController,
     * bukan oleh migrasi ini.
     */
    public function up(): void
    {
        foreach (DB::table('alat')->get() as $alat) {
            $masihBisaDipakai = DB::table('unit_alat')
                ->where('alat_id', $alat->id)
                ->where('status', '!=', 'rusak')
                ->count();

            // Hanya alat yang punya unit (sudah migrasi unit) dan jumlahnya
            // masih lebih besar dari unit yang bisa dipakai yang disesuaikan.
            // Alat tanpa unit (belum migrasi unit) tidak disentuh.
            $totalUnit = DB::table('unit_alat')->where('alat_id', $alat->id)->count();
            if ($totalUnit > 0 && (int) $alat->jumlah_total > $masihBisaDipakai) {
                DB::table('alat')->where('id', $alat->id)->update([
                    'jumlah_total' => $masihBisaDipakai,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Tidak bisa mengembalikan angka lama secara andal (angka lama
        // tidak disimpan di tempat lain), jadi down() sengaja kosong.
    }
};
