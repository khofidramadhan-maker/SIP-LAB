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
        // 1. Ruangan
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan')->unique();
            $table->string('nama_ruangan');
            $table->integer('kapasitas')->default(30);
            $table->string('lokasi');
            $table->text('fasilitas')->nullable();
            $table->string('status')->default('tersedia'); // tersedia, terpakai, perbaikan
            $table->timestamps();
        });

        // 2. Alat
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_inventaris')->unique();
            $table->string('nama_alat');
            $table->string('kategori')->default('Umum');
            $table->integer('jumlah_total')->default(1);
            $table->string('kondisi')->default('baik');
            $table->string('status')->default('tersedia');
            $table->timestamps();
        });

        // 3. Peminjaman
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_pinjam');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('keperluan');
            $table->string('dokumen_pendukung')->nullable();
            $table->string('status')->default('Menunggu Verifikasi'); 
            // Menunggu Verifikasi, Menunggu Persetujuan Kepala Lab, Disetujui, Perlu Revisi, Ditolak, Selesai
            $table->timestamps();
        });

        // 4. Detail Ruangan
        Schema::create('detail_ruangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->cascadeOnDelete();
            $table->timestamps();
        });

        // 5. Detail Alat
        Schema::create('detail_alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->string('kondisi_awal')->default('baik');
            $table->string('kondisi_akhir')->nullable();
            $table->timestamps();
        });

        // 6. Serah Terima
        Schema::create('serah_terima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('waktu_serah');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 7. Pengembalian
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('waktu_kembali');
            $table->integer('hari_terlambat')->default(0);
            $table->timestamps();
        });

        // 8. Kerusakan Alat
        Schema::create('kerusakan_alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')->constrained('pengembalian')->cascadeOnDelete();
            $table->foreignId('detail_alat_id')->constrained('detail_alat')->cascadeOnDelete();
            $table->string('jenis_kondisi');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 9. Sanksi
        Schema::create('sanksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')->constrained('pengembalian')->cascadeOnDelete();
            $table->string('jenis_sanksi');
            $table->decimal('nominal', 12, 2)->default(0);
            $table->enum('status_lunas', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->foreignId('ditandai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_lunas')->nullable();
            $table->timestamps();
        });

        // 10. Persetujuan
        Schema::create('persetujuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tingkat'); // '1' = Staf, '2' = Kepala Lab
            $table->string('keputusan'); // 'disetujui', 'ditolak', 'perlu_revisi'
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal');
            $table->timestamps();
        });

        // 11. Notifikasi
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('peminjaman_id')->nullable()->constrained('peminjaman')->nullOnDelete();
            $table->text('pesan');
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamps();
        });

        // 12. Log Aktivitas
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('aktivitas');
            $table->dateTime('waktu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('persetujuan');
        Schema::dropIfExists('sanksi');
        Schema::dropIfExists('kerusakan_alat');
        Schema::dropIfExists('pengembalian');
        Schema::dropIfExists('serah_terima');
        Schema::dropIfExists('detail_alat');
        Schema::dropIfExists('detail_ruangan');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('alat');
        Schema::dropIfExists('ruangan');
    }
};
