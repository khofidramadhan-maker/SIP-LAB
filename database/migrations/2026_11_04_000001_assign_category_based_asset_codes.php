<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $assets = DB::table('alat')->orderBy('id')->get();
            $prefixCounters = [];
            $codeMap = [];

            foreach ($assets as $asset) {
                $categoryCode = preg_replace('/[^A-Z0-9]/', '', Str::upper(Str::ascii($asset->kategori ?? '')));
                $prefix = substr($categoryCode, 0, 3) ?: 'ALT';
                $prefixCounters[$prefix] = ($prefixCounters[$prefix] ?? 0) + 1;
                $codeMap[$asset->id] = [
                    'old' => $asset->kode_inventaris,
                    'new' => $prefix.'-'.str_pad((string) $prefixCounters[$prefix], 3, '0', STR_PAD_LEFT),
                ];
            }

            foreach (array_keys($codeMap) as $assetId) {
                DB::table('alat')->where('id', $assetId)->update([
                    'kode_inventaris' => "__TMP_ASSET_{$assetId}__",
                ]);
                DB::table('unit_alat')->where('alat_id', $assetId)->get()->each(function ($unit) {
                    DB::table('unit_alat')->where('id', $unit->id)->update([
                        'kode_unit' => "__TMP_UNIT_{$unit->id}__",
                    ]);
                });
            }

            foreach ($codeMap as $assetId => $codes) {
                DB::table('alat')->where('id', $assetId)->update([
                    'kode_inventaris' => $codes['new'],
                ]);
                DB::table('log_aktivitas')
                    ->where('jenis_data', 'alat')
                    ->where('kode_identitas', $codes['old'])
                    ->update(['kode_identitas' => $codes['new']]);

                DB::table('unit_alat')->where('alat_id', $assetId)->orderBy('id')->get()
                    ->each(function ($unit, $index) use ($codes) {
                        DB::table('unit_alat')->where('id', $unit->id)->update([
                            'kode_unit' => $codes['new'].'-U'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                        ]);
                    });
            }
        });
    }

    public function down(): void
    {
        // Existing codes cannot be restored without storing a separate mapping.
    }
};
