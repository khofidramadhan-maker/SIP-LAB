<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyHandoverSchemaMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_the_obsolete_required_legacy_unit_column(): void
    {
        Schema::table('detail_alat_unit', function (Blueprint $table) {
            $table->unsignedBigInteger('alat_unit_id')->nullable(false);
            $table->unique(['detail_alat_id', 'alat_unit_id'], 'legacy_detail_alat_unit_unique');
        });

        $migration = require database_path('migrations/2026_11_04_000002_remove_legacy_alat_unit_id.php');
        $migration->up();

        $this->assertFalse(Schema::hasColumn('detail_alat_unit', 'alat_unit_id'));
        $this->assertTrue(Schema::hasColumn('detail_alat_unit', 'unit_alat_id'));
        $this->assertFalse(Schema::hasIndex('detail_alat_unit', 'legacy_detail_alat_unit_unique'));
    }
}
