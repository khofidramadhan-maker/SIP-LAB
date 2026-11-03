<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kode barang per unit fisik, bukan per jenis alat.
     *
     * Satu baris `alat` (mis. AST-004, jumlah_total 15) mendapat 15 baris
     * `unit_alat` dengan kode AST-004-01 s.d. AST-004-15. Peminjaman tidak
     * lagi hanya mencatat "2 unit", tapi unit mana persis yang diserahkan
     * (detail_alat_unit) dan unit mana persis yang rusak saat kembali
     * (kerusakan_alat.unit_alat_id).
     */
    public function up(): void
    {
        Schema::create('unit_alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelete();
            $table->string('kode_unit')->unique();
            // tersedia | dipinjam | rusak
            $table->string('status')->default('tersedia');
            $table->timestamps();
        });

        Schema::create('detail_alat_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_alat_id')->constrained('detail_alat')->cascadeOnDelete();
            $table->foreignId('unit_alat_id')->constrained('unit_alat')->cascadeOnDelete();
            $table->string('kondisi_awal')->nullable();
            $table->string('kondisi_akhir')->nullable();
            $table->timestamps();

            $table->unique(['detail_alat_id', 'unit_alat_id']);
        });

        Schema::table('kerusakan_alat', function (Blueprint $table) {
            $table->foreignId('unit_alat_id')->nullable()->after('detail_alat_id')
                ->constrained('unit_alat')->nullOnDelete();
        });

        // Backfill: unit untuk alat yang sudah ada di database (mis. data demo
        // yang di-seed sebelum migrasi ini). Database fresh yang di-seed
        // sesudah migrasi ini ditangani DemoSeeder + ManajemenController.
        foreach (DB::table('alat')->get() as $alat) {
            for ($i = 1; $i <= (int) $alat->jumlah_total; $i++) {
                DB::table('unit_alat')->updateOrInsert(
                    ['kode_unit' => $alat->kode_inventaris . '-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)],
                    ['alat_id' => $alat->id, 'status' => 'tersedia']
                );
            }
        }
    }

    public function down(): void
    {
        Schema::table('kerusakan_alat', function (Blueprint $table) {
            $table->dropForeign(['unit_alat_id']);
            $table->dropColumn('unit_alat_id');
        });

        Schema::dropIfExists('detail_alat_unit');
        Schema::dropIfExists('unit_alat');
    }
};
