<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\DetailAlat;
use App\Models\DetailRuangan;
use App\Models\KerusakanAlat;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Persetujuan;
use App\Models\Ruangan;
use App\Models\Sanksi;
use App\Models\SerahTerima;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $staff = User::firstOrCreate(
            ['email' => 'staff@siplab.test'],
            [
                'nama' => 'Staf Sarpras',
                'name' => 'Staf Sarpras',
                'username' => 'staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'status_aktif' => 'aktif',
            ]
        );

        $head = User::firstOrCreate(
            ['email' => 'kepala@siplab.test'],
            [
                'nama' => 'Kepala Laboratorium',
                'name' => 'Kepala Laboratorium',
                'username' => 'kepala_lab',
                'password' => Hash::make('password'),
                'role' => 'kepala_lab',
                'status_aktif' => 'aktif',
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'mahasiswa@siplab.test'],
            [
                'nama' => 'Mahasiswa Demo',
                'name' => 'Mahasiswa Demo',
                'nim' => '240101082',
                'username' => 'mahasiswa',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'status_aktif' => 'aktif',
            ]
        );

        // 2. Ruangan
        $room1 = Ruangan::firstOrCreate(
            ['kode_ruangan' => 'LAB-101'],
            [
                'nama_ruangan' => 'Laboratorium Riset AI & Software',
                'kapasitas' => 30,
                'lokasi' => 'Gedung A Lt. 2',
                'fasilitas' => '30 PC Core i7, GPU RTX 4080, Smart Board, AC, WiFi 1Gbps',
                'status' => 'tersedia',
            ]
        );

        $room2 = Ruangan::firstOrCreate(
            ['kode_ruangan' => 'LAB-102'],
            [
                'nama_ruangan' => 'Laboratorium Elektronika & IoT',
                'kapasitas' => 25,
                'lokasi' => 'Gedung B Lt. 1',
                'fasilitas' => 'Osiloskop Digital, Solder Station, Power Supply, ESP32 Kit, AC',
                'status' => 'tersedia',
            ]
        );

        $room3 = Ruangan::firstOrCreate(
            ['kode_ruangan' => 'LAB-103'],
            [
                'nama_ruangan' => 'Laboratorium Jaringan Komputer',
                'kapasitas' => 20,
                'lokasi' => 'Gedung A Lt. 3',
                'fasilitas' => 'Router Cisco, Switch Manageable, Server Blade, AC',
                'status' => 'tersedia',
            ]
        );

        // 3. Alat
        $alat1 = Alat::firstOrCreate(
            ['kode_inventaris' => 'AST-001'],
            [
                'nama_alat' => 'Laptop Riset GPU RTX',
                'kategori' => 'Komputer & IT',
                'jumlah_total' => 5,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ]
        );

        $alat2 = Alat::firstOrCreate(
            ['kode_inventaris' => 'AST-002'],
            [
                'nama_alat' => 'Multimeter Digital Sanwa',
                'kategori' => 'Alat Ukur',
                'jumlah_total' => 10,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ]
        );

        $alat3 = Alat::firstOrCreate(
            ['kode_inventaris' => 'AST-003'],
            [
                'nama_alat' => 'Osiloskop Digital Rigol',
                'kategori' => 'Alat Ukur',
                'jumlah_total' => 4,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ]
        );

        $alat4 = Alat::firstOrCreate(
            ['kode_inventaris' => 'AST-004'],
            [
                'nama_alat' => 'Kit Sensor IoT ESP32',
                'kategori' => 'Elektronika',
                'jumlah_total' => 15,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ]
        );

        $alat5 = Alat::firstOrCreate(
            ['kode_inventaris' => 'AST-005'],
            [
                'nama_alat' => 'Proyektor Portable HDMI',
                'kategori' => 'Multimedia',
                'jumlah_total' => 3,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ]
        );

        // 4. Sample Peminjaman 1: Selesai
        $pinjam1 = Peminjaman::firstOrCreate(
            ['kode_peminjaman' => 'PINJAM-202610-001'],
            [
                'user_id' => $student->id,
                'tanggal_pinjam' => today()->format('Y-m-d'),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'keperluan' => 'Pengujian Model Deep Learning untuk Skripsi Tugas Akhir',
                'status' => 'Selesai',
            ]
        );
        $pinjam1->update(['status' => 'Selesai']);
        DetailRuangan::firstOrCreate(['peminjaman_id' => $pinjam1->id, 'ruangan_id' => $room1->id]);
        $detailAlat1 = DetailAlat::firstOrCreate([
            'peminjaman_id' => $pinjam1->id,
            'alat_id' => $alat1->id,
            'jumlah' => 1,
            'kondisi_awal' => 'baik',
        ]);
        $detailAlat1->update(['kondisi_akhir' => 'kabel_longgar']);
        Persetujuan::firstOrCreate([
            'peminjaman_id' => $pinjam1->id,
            'user_id' => $staff->id,
            'tingkat' => '1',
            'keputusan' => 'disetujui',
            'catatan' => 'Kelengkapan berkas valid dan jadwal tersedia.',
            'tanggal' => now(),
        ]);
        // 5. Sample Peminjaman 2: Menunggu Pemeriksaan Staf Sarpras
        $pinjam2 = Peminjaman::firstOrCreate(
            ['kode_peminjaman' => 'PINJAM-202610-002'],
            [
                'user_id' => $student->id,
                'tanggal_pinjam' => today()->addDay()->format('Y-m-d'),
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
                'keperluan' => 'Praktikum Mandiri Sensor Lingkungan & Kalibrasi IoT',
                'status' => 'Menunggu Pemeriksaan Staf Sarpras',
            ]
        );
        $pinjam2->update(['status' => 'Menunggu Pemeriksaan Staf Sarpras']);
        DetailRuangan::firstOrCreate(['peminjaman_id' => $pinjam2->id, 'ruangan_id' => $room2->id]);
        DetailAlat::firstOrCreate([
            'peminjaman_id' => $pinjam2->id,
            'alat_id' => $alat4->id,
            'jumlah' => 2,
            'kondisi_awal' => 'baik',
        ]);

        // 6. Sample Peminjaman 3: Menunggu Pemeriksaan Staf Sarpras
        $pinjam3 = Peminjaman::firstOrCreate(
            ['kode_peminjaman' => 'PINJAM-202610-003'],
            [
                'user_id' => $student->id,
                'tanggal_pinjam' => today()->addDays(3)->format('Y-m-d'),
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '15:00:00',
                'keperluan' => 'Simulasi Penetrasi Jaringan & Audit Router Cisco',
                'status' => 'Menunggu Pemeriksaan Staf Sarpras',
            ]
        );
        $pinjam3->update(['status' => 'Menunggu Pemeriksaan Staf Sarpras']);
        DetailRuangan::firstOrCreate(['peminjaman_id' => $pinjam3->id, 'ruangan_id' => $room3->id]);

        // 7. Serah Terima
        SerahTerima::firstOrCreate(
            ['peminjaman_id' => $pinjam1->id],
            [
                'petugas_id' => $staff->id,
                'waktu_serah' => now()->subHours(2),
                'catatan' => 'Kunci ruangan LAB-101 dan 1 unit Laptop GPU diserahkan dalam kondisi lengkap dan baik.',
            ]
        );

        // 8. Pengembalian
        $pengembalian = Pengembalian::firstOrCreate(
            ['peminjaman_id' => $pinjam1->id],
            [
                'petugas_id' => $staff->id,
                'waktu_kembali' => now()->subMinutes(30),
                'hari_terlambat' => 0,
            ]
        );

        // 9. Kerusakan Alat
        if ($detailAlat1) {
            KerusakanAlat::firstOrCreate(
                ['pengembalian_id' => $pengembalian->id, 'detail_alat_id' => $detailAlat1->id],
                [
                    'jenis_kondisi' => 'kabel_longgar',
                    'deskripsi' => 'Kabel charger mengalami kelonggaran dan telah diperbaiki staf sarpras.',
                ]
            );
        }

        // 10. Sanksi
        Sanksi::firstOrCreate(
            ['pengembalian_id' => $pengembalian->id, 'jenis_sanksi' => 'Peringatan Ketepatan Waktu'],
            [
                'nominal' => 0,
                'status_lunas' => 'lunas',
                'ditandai_oleh' => $staff->id,
                'tanggal_lunas' => now(),
            ]
        );

        // 11. Notifikasi
        Notifikasi::firstOrCreate([
            'user_id' => $student->id,
            'peminjaman_id' => $pinjam1->id,
            'pesan' => 'Permohonan peminjaman PINJAM-202610-001 di Laboratorium Riset AI & Software telah disetujui Staf Sarpras.',
            'sudah_dibaca' => true,
        ]);
        Notifikasi::firstOrCreate([
            'user_id' => $student->id,
            'peminjaman_id' => $pinjam2->id,
            'pesan' => 'Permohonan peminjaman PINJAM-202610-002 telah dikirim dan sedang dalam antrean verifikasi Staf Sarpras.',
            'sudah_dibaca' => false,
        ]);

        // 12. Log Aktivitas
        LogAktivitas::firstOrCreate([
            'user_id' => $student->id,
            'aktivitas' => 'Mengajukan permohonan peminjaman ruangan PINJAM-202610-001',
            'waktu' => now()->subHours(5),
        ]);
        LogAktivitas::firstOrCreate([
            'user_id' => $staff->id,
            'aktivitas' => 'Melakukan verifikasi berkas tingkat I untuk PINJAM-202610-001',
            'waktu' => now()->subHours(3),
        ]);
        LogAktivitas::firstOrCreate([
            'user_id' => $staff->id,
            'aktivitas' => 'Melakukan serah terima fasilitas dan kunci ruangan PINJAM-202610-001',
            'waktu' => now()->subMinutes(90),
        ]);
    }
}
