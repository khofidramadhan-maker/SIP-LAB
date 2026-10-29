<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('name')->nullable(); // alias for Laravel Auth
            $table->string('nim')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('username')->nullable()->unique();
            $table->string('password');
            $table->enum('role', ['mahasiswa', 'staff', 'kepala_lab'])->default('mahasiswa');
            $table->enum('status_aktif', ['aktif', 'nonaktif'])->default('aktif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
