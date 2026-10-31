<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('peminjaman')
            ->where('status', 'Menunggu Persetujuan Kepala Lab')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('persetujuan')
                    ->whereColumn('persetujuan.peminjaman_id', 'peminjaman.id')
                    ->where('persetujuan.tingkat', '1')
                    ->where('persetujuan.keputusan', 'disetujui');
            })
            ->update([
                'status' => 'Disetujui',
                'updated_at' => now(),
            ]);

        DB::table('peminjaman')
            ->where('status', 'Menunggu Persetujuan Kepala Lab')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('persetujuan')
                    ->whereColumn('persetujuan.peminjaman_id', 'peminjaman.id')
                    ->where('persetujuan.tingkat', '1')
                    ->where('persetujuan.keputusan', 'ditolak');
            })
            ->update([
                'status' => 'Ditolak',
                'updated_at' => now(),
            ]);

        DB::table('peminjaman')
            ->where('status', 'Menunggu Persetujuan Kepala Lab')
            ->update([
                'status' => 'Menunggu Pemeriksaan Staf Sarpras',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Historical approval data is retained; normalized booking statuses are not reversible.
    }
};
