<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Tests\TestCase;

class SipLabEndpointsTest extends TestCase
{
    public function test_public_pages_load_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_student_pages_load_successfully(): void
    {
        $student = User::where('email', 'mahasiswa@siplab.test')->first();

        $this->actingAs($student)->get('/mahasiswa/dashboard')->assertStatus(200);
        $this->actingAs($student)->get('/mahasiswa/inventaris')->assertStatus(200);
        $this->actingAs($student)->get('/mahasiswa/riwayat')->assertStatus(200);
        $this->actingAs($student)->get('/mahasiswa/peminjaman/create')->assertStatus(200);
        $this->actingAs($student)->get('/mahasiswa/peminjaman/1')->assertStatus(200);
        $this->actingAs($student)->get('/mahasiswa/notifikasi')->assertStatus(200);
    }

    public function test_staff_pages_load_successfully(): void
    {
        $staff = User::where('email', 'staff@siplab.test')->first();

        $this->actingAs($staff)->get('/staff/dashboard')->assertStatus(200);
        $this->actingAs($staff)->get('/staff/verifikasi')->assertStatus(200);
        $this->actingAs($staff)->get('/staff/inventaris')->assertStatus(200);
    }

    public function test_lab_head_pages_load_successfully(): void
    {
        $head = User::where('email', 'kepala@siplab.test')->first();

        $this->actingAs($head)->get('/kepala-lab/dashboard')->assertStatus(200);
        $this->actingAs($head)->get('/kepala-lab/approval')->assertStatus(200);
        $this->actingAs($head)->get('/kepala-lab/laporan')->assertStatus(200);
    }

    public function test_full_peminjaman_workflow(): void
    {
        $student = User::where('email', 'mahasiswa@siplab.test')->first();
        $staff = User::where('email', 'staff@siplab.test')->first();
        $head = User::where('email', 'kepala@siplab.test')->first();
        $room = Ruangan::first();
        $alat = Alat::first();

        // 1. Student creates booking
        $response = $this->actingAs($student)->post('/mahasiswa/peminjaman', [
            'keperluan' => 'Uji Coba Otomasi Alur Pengujian Sistem',
            'ruangan_id' => $room->id,
            'tanggal_pinjam' => today()->addDays(2)->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'alat_id' => $alat->id,
            'jumlah_alat' => 1,
        ]);
        $response->assertRedirect('/mahasiswa/dashboard');

        $latestBooking = Peminjaman::latest()->first();
        $this->assertEquals('Menunggu Verifikasi', $latestBooking->status);
        $this->assertNotNull($latestBooking->detailRuangan->first());
        $this->assertNotNull($latestBooking->detailAlat->first());

        // 2. Staff verifies and forwards to Lab Head
        $response = $this->actingAs($staff)->post("/staff/verifikasi/{$latestBooking->id}", [
            'status' => 'approved',
        ]);
        $response->assertSessionHas('success');
        $latestBooking->refresh();
        $this->assertEquals('Menunggu Persetujuan Kepala Lab', $latestBooking->status);

        // 3. Lab Head approves
        $response = $this->actingAs($head)->post("/kepala-lab/approval/{$latestBooking->id}", [
            'status' => 'approved',
        ]);
        $response->assertSessionHas('success');
        $latestBooking->refresh();
        $this->assertEquals('Disetujui', $latestBooking->status);
    }
}
