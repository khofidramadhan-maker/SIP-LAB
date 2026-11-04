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
        if (! Schema::hasTable('unit_alat')) {
            Schema::create('unit_alat', function (Blueprint $table) {
                $table->id();
                $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelete();
                $table->string('kode_unit')->unique();
                $table->string('status')->default('tersedia');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('alat_unit')) {
            foreach (DB::table('alat_unit')->orderBy('id')->get() as $legacyUnit) {
                $unit = DB::table('unit_alat')->where('kode_unit', $legacyUnit->kode_unit)->first();

                if ($unit && (int) $unit->alat_id !== (int) $legacyUnit->alat_id) {
                    throw new RuntimeException("Kode unit {$legacyUnit->kode_unit} sudah dipakai oleh alat lain.");
                }

                if (! $unit) {
                    $values = [
                        'alat_id' => $legacyUnit->alat_id,
                        'kode_unit' => $legacyUnit->kode_unit,
                        'status' => $legacyUnit->status,
                        'created_at' => $legacyUnit->created_at,
                        'updated_at' => $legacyUnit->updated_at,
                    ];

                    if (! DB::table('unit_alat')->where('id', $legacyUnit->id)->exists()) {
                        DB::table('unit_alat')->insert(['id' => $legacyUnit->id] + $values);
                    } else {
                        DB::table('unit_alat')->insert($values);
                    }
                }
            }
        }

        if (! Schema::hasTable('detail_alat_unit')) {
            Schema::create('detail_alat_unit', function (Blueprint $table) {
                $table->id();
                $table->foreignId('detail_alat_id')->constrained('detail_alat')->cascadeOnDelete();
                $table->foreignId('unit_alat_id')->constrained('unit_alat')->cascadeOnDelete();
                $table->string('kondisi_awal')->nullable();
                $table->string('kondisi_akhir')->nullable();
                $table->timestamps();

                $table->unique(['detail_alat_id', 'unit_alat_id']);
            });
        } else {
            if (! Schema::hasColumn('detail_alat_unit', 'unit_alat_id')) {
                Schema::table('detail_alat_unit', function (Blueprint $table) {
                    $table->foreignId('unit_alat_id')->nullable()->after('detail_alat_id');
                });
            }

            if (Schema::hasColumn('detail_alat_unit', 'alat_unit_id') && Schema::hasTable('alat_unit')) {
                foreach (DB::table('detail_alat_unit')->whereNull('unit_alat_id')->get() as $link) {
                    $legacyUnit = DB::table('alat_unit')->where('id', $link->alat_unit_id)->first();
                    $unitId = $legacyUnit
                        ? DB::table('unit_alat')->where('kode_unit', $legacyUnit->kode_unit)->value('id')
                        : null;

                    if (! $unitId) {
                        throw new RuntimeException("Unit lama {$link->alat_unit_id} tidak dapat dipetakan.");
                    }

                    DB::table('detail_alat_unit')
                        ->where('id', $link->id)
                        ->update(['unit_alat_id' => $unitId]);
                }
            }

            if (! Schema::hasIndex('detail_alat_unit', ['detail_alat_id', 'unit_alat_id'], 'unique')) {
                Schema::table('detail_alat_unit', function (Blueprint $table) {
                    $table->unique(['detail_alat_id', 'unit_alat_id'], 'detail_alat_unit_detail_unit_unique');
                });
            }

            $hasUnitForeignKey = collect(Schema::getForeignKeys('detail_alat_unit'))->contains(
                fn (array $foreignKey) => $foreignKey['columns'] === ['unit_alat_id']
                    && $foreignKey['foreign_table'] === 'unit_alat'
            );
            if (! $hasUnitForeignKey) {
                Schema::table('detail_alat_unit', function (Blueprint $table) {
                    $table->foreign('unit_alat_id')->references('id')->on('unit_alat')->cascadeOnDelete();
                });
            }
        }

        if (! Schema::hasColumn('kerusakan_alat', 'unit_alat_id')) {
            Schema::table('kerusakan_alat', function (Blueprint $table) {
                $table->foreignId('unit_alat_id')->nullable()->after('detail_alat_id')
                    ->constrained('unit_alat')->nullOnDelete();
            });
        }

        foreach (DB::table('alat')->get() as $alat) {
            $availableCount = DB::table('unit_alat')
                ->where('alat_id', $alat->id)
                ->where('status', '!=', 'rusak')
                ->count();
            $number = 1;

            while ($availableCount < (int) $alat->jumlah_total) {
                $code = $alat->kode_inventaris.'-'.str_pad((string) $number++, 2, '0', STR_PAD_LEFT);
                $existingUnit = DB::table('unit_alat')->where('kode_unit', $code)->first();

                if ($existingUnit) {
                    if ((int) $existingUnit->alat_id === (int) $alat->id && $existingUnit->status !== 'rusak') {
                        $availableCount++;
                    }

                    continue;
                }

                DB::table('unit_alat')->insert([
                    'alat_id' => $alat->id,
                    'kode_unit' => $code,
                    'status' => 'tersedia',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $availableCount++;
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
