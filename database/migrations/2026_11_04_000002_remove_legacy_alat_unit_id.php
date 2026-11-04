<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('detail_alat_unit') || ! Schema::hasColumn('detail_alat_unit', 'alat_unit_id')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            foreach (Schema::getForeignKeys('detail_alat_unit') as $foreignKey) {
                if (in_array('alat_unit_id', $foreignKey['columns'], true)) {
                    Schema::table('detail_alat_unit', function (Blueprint $table) use ($foreignKey) {
                        $table->dropForeign($foreignKey['name']);
                    });
                }
            }
        }

        foreach (Schema::getIndexes('detail_alat_unit') as $index) {
            if (! $index['primary'] && in_array('alat_unit_id', $index['columns'], true)) {
                Schema::table('detail_alat_unit', function (Blueprint $table) use ($index) {
                    $table->dropIndex($index['name']);
                });
            }
        }

        Schema::table('detail_alat_unit', function (Blueprint $table) {
            $table->dropColumn('alat_unit_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('detail_alat_unit') || Schema::hasColumn('detail_alat_unit', 'alat_unit_id')) {
            return;
        }

        Schema::table('detail_alat_unit', function (Blueprint $table) {
            $table->unsignedBigInteger('alat_unit_id')->nullable();
        });
    }
};
