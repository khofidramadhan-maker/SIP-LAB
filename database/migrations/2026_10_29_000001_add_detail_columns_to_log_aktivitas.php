<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->string('jenis_data')->nullable()->after('aktivitas');   // ruangan, pengguna, alat
            $table->string('kode_identitas')->nullable()->after('jenis_data'); // kode/nama entitas
            $table->string('jenis_aksi')->nullable()->after('kode_identitas'); // tambah, ubah
            $table->json('data_sebelum')->nullable()->after('jenis_aksi');
            $table->json('data_sesudah')->nullable()->after('data_sebelum');
        });
    }

    public function down(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropColumn(['jenis_data', 'kode_identitas', 'jenis_aksi', 'data_sebelum', 'data_sesudah']);
        });
    }
};

