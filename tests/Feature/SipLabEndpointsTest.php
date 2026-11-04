<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailAlat;
use App\Models\DetailRuangan;
use App\Models\KerusakanAlat;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Persetujuan;
use App\Models\Ruangan;
use App\Models\UnitAlat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SipLabEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private User $otherStudent;

    private User $staff;

    private User $head;

    private Ruangan $room;

    private Ruangan $otherRoom;

    private Alat $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = $this->makeUser('mahasiswa@siplab.test', 'mahasiswa');
        $this->otherStudent = $this->makeUser('mahasiswa2@siplab.test', 'mahasiswa');
        $this->staff = $this->makeUser('staff@siplab.test', 'staff');
        $this->head = $this->makeUser('kepala@siplab.test', 'kepala_lab');

        $this->room = Ruangan::create([
            'kode_ruangan' => 'LAB-001',
            'nama_ruangan' => 'Laboratorium Uji',
            'kapasitas' => 20,
            'lokasi' => 'Gedung A',
            'status' => 'tersedia',
        ]);
        $this->otherRoom = Ruangan::create([
            'kode_ruangan' => 'LAB-002',
            'nama_ruangan' => 'Laboratorium Kedua',
            'kapasitas' => 20,
            'lokasi' => 'Gedung B',
            'status' => 'tersedia',
        ]);
        $this->asset = Alat::create([
            'kode_inventaris' => 'AST-001',
            'nama_alat' => 'Laptop Uji',
            'kategori' => 'Komputer',
            'jumlah_total' => 1,
            'kondisi' => 'baik',
            'status' => 'tersedia',
        ]);
    }

    public function test_public_and_role_pages_load_successfully(): void
    {
        $booking = $this->makeBooking();

        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();

        $this->actingAs($this->student)->get('/mahasiswa/dashboard')->assertOk();
        $this->get('/mahasiswa/dashboard')
            ->assertSee('Inventaris')
            ->assertSee('Riwayat Peminjaman')
            ->assertSee('Profil')
            ->assertDontSee('Manajemen Pengguna');
        $this->get('/mahasiswa/inventaris')->assertOk();
        $this->get('/mahasiswa/riwayat')->assertOk();
        $this->get('/mahasiswa/peminjaman/create')
            ->assertOk()
            ->assertDontSee('Periksa Ketersediaan Ruangan')
            ->assertSee('type="text" id="jam_mulai"', false)
            ->assertSee('type="text" id="jam_selesai"', false)
            ->assertSee('placeholder="HH:MM"', false)
            ->assertSee('pattern="(?:[01][0-9]|2[0-3]):[0-5][0-9]"', false)
            ->assertSee('id="jam_mulai"', false)
            ->assertSee('id="jam_selesai"', false)
            ->assertSee('Permohonan Peminjaman');
        $this->get(route('booking.show', $booking))
            ->assertOk()
            ->assertSee('Bukti Pengajuan Peminjaman Laboratorium')
            ->assertSee('Ruang Laboratorium')
            ->assertSee('Keperluan Peminjaman')
            ->assertSee('Peralatan yang Dipinjam')
            ->assertSee('booking-proof-print', false)
            ->assertSee('Kode Peminjaman')
            ->assertSee('Dokumen Pendukung')
            ->assertSee('visibility: hidden !important', false)
            ->assertSee('booking-approval-history', false)
            ->assertSee('window.print()', false)
            ->assertDontSee('Ruang Laboratorium (detail_ruangan)');
        $this->get('/mahasiswa/notifikasi')->assertOk();
        $this->get('/profil')->assertOk();

        $this->actingAs($this->staff)->get('/staff/dashboard')->assertOk();
        $this->get('/staff/dashboard')
            ->assertSee('Verifikasi Peminjaman')
            ->assertSee('Antrean Verifikasi Staf Sarpras')
            ->assertDontSee('Antrean Verifikasi Tingkat I')
            ->assertSee('Serah Terima')
            ->assertSee('Pengembalian')
            ->assertSee('Manajemen Ruangan')
            ->assertSee('Manajemen Alat')
            ->assertSee('Manajemen Pengguna')
            ->assertDontSee('Ajukan Peminjaman');
        $this->get('/staff/verifikasi')->assertOk();
        $this->get('/staff/serah-terima')->assertOk();
        $this->get('/staff/pengembalian')->assertOk();
        $this->get('/staff/inventaris')->assertOk();
        $this->get('/staff/ruangan')->assertOk();
        $this->get('/staff/alat')->assertOk();
        $this->get(route('staff.ruangan.show', $this->room))
            ->assertOk()
            ->assertSee('room-detail-list room-info-list', false);
        $this->get(route('staff.alat.show', $this->asset))
            ->assertOk()
            ->assertSee('room-detail-list', false);
        $this->get('/staff/pengguna')->assertOk();
        $this->get(route('staff.pengguna.index'))
            ->assertSee('@'.$this->staff->username)
            ->assertDontSee('{{ $user->username }}');
        $this->get(route('staff.pengguna.show', $this->staff))
            ->assertOk()
            ->assertSee('@'.$this->staff->username);
        $this->get('/staff/riwayat-data')->assertOk();
        $this->get('/profil')->assertOk();

        $this->actingAs($this->head)->get('/kepala-lab/dashboard')->assertOk();
        $this->get('/kepala-lab/dashboard')
            ->assertSee('Laporan')
            ->assertSee('Profil')
            ->assertDontSee('Persetujuan Peminjaman')
            ->assertDontSee('Menunggu Persetujuan')
            ->assertDontSee('Manajemen Ruangan');
        $this->get('/kepala-lab/approval')->assertNotFound();
        $this->get('/kepala-lab/laporan')->assertOk();
        $this->get('/profil')->assertOk();
    }

    public function test_login_after_logout_ignores_stale_intended_url_and_uses_role_dashboard(): void
    {
        $loginPage = $this->get(route('login'));
        $loginPage->assertOk()->assertSee('_token')->assertHeader('Pragma', 'no-cache');
        $this->assertStringContainsString('no-store', $loginPage->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $loginPage->headers->get('Cache-Control'));

        $this->withSession(['url.intended' => '/home'])
            ->post(route('login'), [
                'email' => $this->student->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($this->student);

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));
        $this->assertStringContainsString('no-store', $logoutResponse->headers->get('Cache-Control'));

        $this->assertGuest();

        $this->get(route('login'))->assertOk();
        $this->post(route('login'), [
            'email' => $this->student->email,
            'password' => 'password',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($this->student);
        $this->get('/home')->assertRedirect(route('student.dashboard'));
        $this->get(route('student.dashboard'))->assertOk();
    }

    public function test_multiple_students_can_register_and_access_the_student_portal(): void
    {
        $students = [
            [
                'nama' => 'Mahasiswa Pendaftar Satu',
                'email' => 'pendaftar1@siplab.test',
                'nim' => '240101101',
                'program_study' => 'Teknik Informatika',
                'password' => 'password-123',
                'password_confirmation' => 'password-123',
            ],
            [
                'nama' => 'Mahasiswa Pendaftar Dua',
                'email' => 'pendaftar2@siplab.test',
                'nim' => '240101102',
                'program_study' => 'Sistem Informasi',
                'password' => 'password-123',
                'password_confirmation' => 'password-123',
            ],
        ];

        foreach ($students as $studentData) {
            $this->get(route('register'))->assertOk();
            $this->post(route('register'), $studentData)
                ->assertRedirect(route('student.dashboard'));

            $this->assertAuthenticated();
            $this->assertDatabaseHas('users', [
                'nama' => $studentData['nama'],
                'nim' => $studentData['nim'],
                'program_study' => $studentData['program_study'],
                'email' => $studentData['email'],
                'role' => 'mahasiswa',
                'status_aktif' => 'aktif',
            ]);
            $user = User::where('email', $studentData['email'])->firstOrFail();
            $this->assertTrue(Hash::check($studentData['password'], $user->password));
            $this->get(route('student.dashboard'))->assertOk();
            $this->post(route('logout'))->assertRedirect(route('login'));
            $this->post(route('login'), [
                'email' => $studentData['email'],
                'password' => $studentData['password'],
            ])->assertRedirect(route('student.dashboard'));
            $this->get(route('student.dashboard'))->assertOk();
            $this->post(route('logout'))->assertRedirect(route('login'));
        }

        $this->assertSame(4, User::where('role', 'mahasiswa')->count());
    }

    public function test_profile_updates_only_columns_available_for_each_role_and_verifies_password(): void
    {
        $this->actingAs($this->student)
            ->get('/profil')
            ->assertOk()
            ->assertSee('NIM')
            ->assertSee('Program Studi');

        $this->patch('/profil', [
            'nama' => 'Mahasiswa Diperbarui',
            'nim' => '240101999',
            'program_study' => 'Teknik Komputer',
            'email' => $this->student->email,
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'nama' => 'Mahasiswa Diperbarui',
            'name' => 'Mahasiswa Diperbarui',
            'nim' => '240101999',
            'program_study' => 'Teknik Komputer',
        ]);
        $this->assertTrue(Hash::check('new-password-123', $this->student->fresh()->password));

        $this->patch('/profil', [
            'nama' => 'Tidak Tersimpan',
            'nim' => '240101999',
            'email' => $this->student->email,
            'current_password' => 'wrong-password',
            'password' => 'another-password-123',
            'password_confirmation' => 'another-password-123',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($this->staff)
            ->get('/profil')
            ->assertOk()
            ->assertDontSee('name="nim"');
        $this->patch('/profil', [
            'nama' => 'Staf Diperbarui',
            'nim' => 'SHOULD-NOT-SAVE',
            'email' => $this->staff->email,
        ])->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $this->staff->id,
            'nama' => 'Staf Diperbarui',
            'nim' => null,
        ]);
    }

    public function test_student_can_mark_only_owned_notifications_as_read(): void
    {
        $notification = Notifikasi::create([
            'user_id' => $this->student->id,
            'pesan' => 'Status peminjaman diperbarui.',
            'sudah_dibaca' => false,
        ]);
        $otherNotification = Notifikasi::create([
            'user_id' => $this->otherStudent->id,
            'pesan' => 'Notifikasi pribadi.',
            'sudah_dibaca' => false,
        ]);

        $this->actingAs($this->student)
            ->post(route('student.notifications.read', $notification))
            ->assertRedirect();
        $this->assertTrue($notification->fresh()->sudah_dibaca);

        $this->post(route('student.notifications.read', $otherNotification))
            ->assertNotFound();
        $this->assertFalse($otherNotification->fresh()->sudah_dibaca);
    }

    public function test_reports_and_change_history_apply_date_filters(): void
    {
        $included = $this->makeBooking([
            'kode_peminjaman' => 'TEST-REPORT-IN',
            'tanggal_pinjam' => today()->format('Y-m-d'),
            'keperluan' => 'Pengujian laporan dengan keterangan lengkap tanpa pemotongan',
        ]);
        $excluded = $this->makeBooking([
            'kode_peminjaman' => 'TEST-REPORT-OUT',
            'tanggal_pinjam' => today()->subDays(4)->format('Y-m-d'),
        ]);

        $this->actingAs($this->head)
            ->get(route('lab-head.reports', [
                'date_from' => today()->subDay()->format('Y-m-d'),
                'date_to' => today()->addDay()->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertSee($included->kode_peminjaman)
            ->assertSee('report-print-area', false)
            ->assertSee('display: block;', false)
            ->assertSee('body *', false)
            ->assertSee('data-csv=', false)
            ->assertSee('Nama Pemohon')
            ->assertSee('Tanggal Pengajuan')
            ->assertSee('Pengujian laporan dengan keterangan lengkap tanpa pemotongan')
            ->assertDontSee($excluded->kode_peminjaman);

        $this->get(route('lab-head.reports', [
            'date_to' => today()->subDays(2)->format('Y-m-d'),
        ]))->assertOk();

        LogAktivitas::create([
            'user_id' => $this->staff->id,
            'aktivitas' => 'Data ruangan diperbarui.',
            'jenis_data' => 'ruangan',
            'kode_identitas' => 'LAB-001',
            'jenis_aksi' => 'ubah',
            'waktu' => today()->toDateString().' 10:00:00',
        ]);

        $this->actingAs($this->staff)
            ->get(route('staff.riwayat', [
                'jenis_data' => 'ruangan',
                'date_from' => today()->format('Y-m-d'),
                'date_to' => today()->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertSee('Data ruangan diperbarui.');
    }

    public function test_role_middleware_protects_staff_and_head_navigation(): void
    {
        $this->actingAs($this->student)->get('/staff/ruangan')->assertForbidden();
        $this->get('/kepala-lab/approval')->assertNotFound();

        $this->actingAs($this->staff)->get('/kepala-lab/approval')->assertNotFound();
        $this->get('/mahasiswa/notifikasi')->assertForbidden();

        $this->actingAs($this->head)->get('/staff/alat')->assertForbidden();
        $this->get('/mahasiswa/dashboard')->assertForbidden();

        $this->actingAs($this->student)
            ->post('/logout')
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_booking_validates_schedule_room_conflicts_and_asset_stock(): void
    {
        $this->actingAs($this->student)
            ->post('/mahasiswa/peminjaman', $this->bookingPayload([
                'tanggal_pinjam' => today()->subDay()->format('Y-m-d'),
            ]))
            ->assertSessionHasErrors('tanggal_pinjam');

        $this->post('/mahasiswa/peminjaman', $this->bookingPayload([
            'jam_mulai' => '10:00',
            'jam_selesai' => '10:00',
        ]))->assertSessionHasErrors('jam_selesai');

        $this->post('/mahasiswa/peminjaman', $this->bookingPayload([
            'jam_mulai' => '11:00',
            'jam_selesai' => '10:00',
        ]))->assertSessionHasErrors('jam_selesai');

        $date = today()->addDays(3)->format('Y-m-d');
        $this->post('/mahasiswa/peminjaman', $this->bookingPayload([
            'tanggal_pinjam' => $date,
        ]))->assertRedirect(route('student.dashboard'));

        $this->post('/mahasiswa/peminjaman', $this->bookingPayload([
            'tanggal_pinjam' => $date,
            'alat_id' => null,
            'jumlah_alat' => null,
        ]))->assertSessionHasErrors('ruangan_id');

        $this->post('/mahasiswa/peminjaman', $this->bookingPayload([
            'tanggal_pinjam' => $date,
            'ruangan_id' => $this->otherRoom->id,
        ]))->assertSessionHasErrors('jumlah_alat');

        $this->post('/mahasiswa/peminjaman', $this->bookingPayload([
            'tanggal_pinjam' => $date,
            'jam_mulai' => '12:00',
            'jam_selesai' => '14:00',
            'alat_id' => null,
            'jumlah_alat' => null,
        ]))->assertRedirect(route('student.dashboard'));

        $booking = Peminjaman::latest('id')->firstOrFail();
        $this->assertSame('Menunggu Pemeriksaan Staf Sarpras', $booking->status);
    }

    public function test_student_can_submit_and_staff_can_approve_an_asset_only_booking(): void
    {
        $formResponse = $this->actingAs($this->student)->get(route('booking.create'));
        $formResponse->assertOk()->assertSee('aria-describedby="roomHelp"', false);
        $this->assertSame(
            0,
            preg_match('/<select\b(?=[^>]*\bid="ruangan_id")(?=[^>]*\brequired(?:\s|=|>))[^>]*>/s', $formResponse->getContent())
        );

        $this->post('/mahasiswa/peminjaman', $this->bookingPayload([
            'ruangan_id' => null,
            'tanggal_pinjam' => today()->addDays(8)->format('Y-m-d'),
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
        ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        $booking = Peminjaman::latest('id')->firstOrFail();
        $this->assertSame(0, $booking->detailRuangan()->count());
        $this->assertSame(1, $booking->detailAlat()->count());

        $this->actingAs($this->staff)
            ->post(route('staff.verify', $booking), ['status' => 'approved'])
            ->assertSessionHasNoErrors();

        $this->assertSame('Disetujui', $booking->fresh()->status);
        $this->actingAs($this->student)
            ->get(route('booking.show', $booking))
            ->assertOk()
            ->assertSee('Tanpa peminjaman ruang');
    }

    public function test_uploaded_and_legacy_local_documents_can_be_opened(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->actingAs($this->student)
            ->post(route('booking.store'), $this->bookingPayload([
                'document' => UploadedFile::fake()->create('surat.pdf', 10, 'application/pdf'),
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        $uploadedBooking = Peminjaman::latest('id')->firstOrFail();
        Storage::disk('public')->assertExists($uploadedBooking->dokumen_pendukung);

        $this->get(route('booking.document', $uploadedBooking))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $legacyBooking = $this->makeBooking();
        $legacyPath = 'dokumen_peminjaman/legacy-surat.pdf';
        Storage::disk('local')->put($legacyPath, '%PDF-1.4');
        $legacyBooking->update(['dokumen_pendukung' => $legacyPath]);

        $this->get(route('booking.document', $legacyBooking))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_damaged_asset_count_is_shown_on_asset_detail_not_in_list(): void
    {
        UnitAlat::create([
            'alat_id' => $this->asset->id,
            'kode_unit' => 'AST-001-U001',
            'status' => UnitAlat::STATUS_RUSAK,
        ]);

        $this->actingAs($this->staff)
            ->get(route('staff.alat.index'))
            ->assertOk()
            ->assertDontSee('unit rusak');

        $this->get(route('staff.alat.show', $this->asset))
            ->assertOk()
            ->assertSee('Unit Rusak')
            ->assertSee('1 unit');
    }

    public function test_staff_approval_is_final_and_cannot_be_repeated(): void
    {
        $booking = $this->makeBooking();

        $this->actingAs($this->staff)
            ->post(route('staff.verify', $booking), ['status' => 'approved'])
            ->assertSessionHasNoErrors();

        $booking->refresh();
        $this->assertSame('Disetujui', $booking->status);
        $this->assertDatabaseHas('persetujuan', [
            'peminjaman_id' => $booking->id,
            'tingkat' => '1',
            'keputusan' => 'disetujui',
        ]);
        $this->assertSame(1, Persetujuan::where('peminjaman_id', $booking->id)->count());
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->student->id,
            'peminjaman_id' => $booking->id,
            'pesan' => "Permohonan peminjaman {$booking->kode_peminjaman} telah disetujui oleh Staf Sarpras.",
        ]);
        $this->assertDatabaseHas('log_aktivitas', [
            'user_id' => $this->staff->id,
            'aktivitas' => "Staf Sarpras disetujui pengajuan {$booking->kode_peminjaman}",
        ]);

        $this->post(route('staff.verify', $booking), ['status' => 'approved'])
            ->assertSessionHasErrors('status');
        $this->post(route('staff.verify', $booking), ['status' => 'rejected', 'notes' => 'Tolak'])
            ->assertSessionHasErrors('status');
        $this->assertSame('Disetujui', $booking->fresh()->status);
        $this->assertSame(1, Notifikasi::where('peminjaman_id', $booking->id)->count());
        $this->assertSame(1, LogAktivitas::count());
    }

    public function test_staff_rejection_requires_notes_and_notifies_student_once(): void
    {
        $booking = $this->makeBooking();

        $this->actingAs($this->staff)
            ->post(route('staff.verify', $booking), ['status' => 'rejected'])
            ->assertSessionHasErrors('notes');

        $this->post(route('staff.verify', $booking), [
            'status' => 'rejected',
            'notes' => 'Jadwal tidak sesuai.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Ditolak', $booking->fresh()->status);
        $this->assertDatabaseHas('persetujuan', [
            'peminjaman_id' => $booking->id,
            'tingkat' => '1',
            'keputusan' => 'ditolak',
            'catatan' => 'Jadwal tidak sesuai.',
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->student->id,
            'peminjaman_id' => $booking->id,
            'pesan' => "Permohonan {$booking->kode_peminjaman} ditolak oleh Staf Sarpras. Catatan: Jadwal tidak sesuai.",
        ]);
        $this->assertSame(1, Notifikasi::where('peminjaman_id', $booking->id)->count());
        $this->assertSame(1, LogAktivitas::count());

        $this->actingAs($this->head)
            ->get('/kepala-lab/approval')
            ->assertNotFound();
        $this->post('/kepala-lab/approval/'.$booking->id, ['status' => 'approved'])
            ->assertNotFound();
    }

    public function test_approval_decisions_reject_invalid_values_and_pending_items_do_not_reach_lab_head(): void
    {
        $booking = $this->makeBooking();

        $this->actingAs($this->staff)
            ->post(route('staff.verify', $booking), ['status' => 'needs_review'])
            ->assertSessionHasErrors('status');
        $this->assertSame('Menunggu Pemeriksaan Staf Sarpras', $booking->fresh()->status);

        $this->actingAs($this->head)
            ->get('/kepala-lab/dashboard')
            ->assertOk()
            ->assertDontSee('Persetujuan Peminjaman');
        $this->get('/kepala-lab/laporan')->assertOk();
        $this->get('/kepala-lab/approval')->assertNotFound();
    }

    public function test_asset_unit_sync_reuses_existing_units_and_replenishes_damaged_stock(): void
    {
        $unit = UnitAlat::create([
            'alat_id' => $this->asset->id,
            'kode_unit' => 'AST-001-U001',
            'status' => UnitAlat::STATUS_TERSEDIA,
        ]);

        UnitAlat::ensureForAlat($this->asset);
        $this->assertSame(1, UnitAlat::where('alat_id', $this->asset->id)->count());

        $unit->update(['status' => UnitAlat::STATUS_RUSAK]);
        UnitAlat::ensureForAlat($this->asset);

        $this->assertSame(2, UnitAlat::where('alat_id', $this->asset->id)->count());
        $this->assertSame(1, UnitAlat::where('alat_id', $this->asset->id)
            ->where('status', '!=', UnitAlat::STATUS_RUSAK)
            ->count());
        $this->assertDatabaseHas('unit_alat', [
            'alat_id' => $this->asset->id,
            'kode_unit' => 'AST-001-U002',
            'status' => UnitAlat::STATUS_TERSEDIA,
        ]);
    }

    public function test_asset_codes_are_generated_by_category_and_update_with_category(): void
    {
        $this->actingAs($this->staff)
            ->post(route('staff.alat.store'), [
                'nama_alat' => 'Multimeter Baru',
                'kategori' => 'Alat Ukur',
                'jumlah_total' => 1,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ])
            ->assertRedirect(route('staff.alat.index'));

        $newAsset = Alat::where('nama_alat', 'Multimeter Baru')->firstOrFail();
        $this->assertSame('ALA-001', $newAsset->kode_inventaris);
        $this->assertDatabaseHas('unit_alat', [
            'alat_id' => $newAsset->id,
            'kode_unit' => 'ALA-001-U001',
        ]);

        $unit = UnitAlat::create([
            'alat_id' => $this->asset->id,
            'kode_unit' => 'AST-001-U001',
            'status' => UnitAlat::STATUS_TERSEDIA,
        ]);

        $this->put(route('staff.alat.update', $this->asset), [
            'nama_alat' => $this->asset->nama_alat,
            'kategori' => 'Elektronika',
            'jumlah_total' => 1,
            'kondisi' => 'baik',
            'status' => 'tersedia',
        ])->assertRedirect(route('staff.alat.index'));

        $this->assertSame('ELE-001', $this->asset->fresh()->kode_inventaris);
        $this->assertSame('ELE-001-U001', $unit->fresh()->kode_unit);
    }

    public function test_handover_and_return_record_conditions_damage_logs_and_notifications(): void
    {
        $booking = $this->makeBooking(['status' => 'Disetujui']);
        $detail = $booking->detailAlat()->firstOrFail();
        $unit = UnitAlat::create([
            'alat_id' => $this->asset->id,
            'kode_unit' => 'AST-001-01',
            'status' => UnitAlat::STATUS_TERSEDIA,
        ]);
        $this->recordApproval($booking, $this->staff, '1');

        $this->actingAs($this->staff)
            ->get(route('staff.handover.create', $booking))
            ->assertOk()
            ->assertSee('Kondisi awal alat');

        $this->actingAs($this->staff)
            ->post(route('staff.handover.store', $booking), [
                'units' => [$detail->id => [$unit->id]],
                'unit_conditions' => [$detail->id => [$unit->id => 'Baik']],
                'notes' => 'Alat diserahkan lengkap.',
            ])
            ->assertSessionHasNoErrors();

        $booking->refresh();
        $this->assertSame('Sedang Dipinjam', $booking->status);
        $this->assertDatabaseHas('serah_terima', ['peminjaman_id' => $booking->id]);
        $this->assertSame('AST-001-01: Baik', $detail->fresh()->kondisi_awal);
        $this->assertSame(UnitAlat::STATUS_DIPINJAM, $unit->fresh()->status);

        $this->post(route('staff.handover.store', $booking), [
            'units' => [$detail->id => [$unit->id]],
            'unit_conditions' => [$detail->id => [$unit->id => 'Baik']],
        ])->assertStatus(409);

        $this->get(route('staff.returns.create', $booking))
            ->assertOk()
            ->assertSee('Pemeriksaan alat');

        $this->post(route('staff.returns.store', $booking), [
            'unit_conditions' => [$detail->id => [$unit->id => 'Rusak ringan']],
            'unit_damaged' => [$detail->id => [$unit->id => '1']],
            'unit_damage_descriptions' => [$detail->id => [$unit->id => 'Kabel daya longgar.']],
        ])->assertSessionHasNoErrors();

        $this->assertSame('Selesai', $booking->fresh()->status);
        $this->assertSame('AST-001-01: Rusak ringan', $detail->fresh()->kondisi_akhir);
        $this->assertSame(UnitAlat::STATUS_RUSAK, $unit->fresh()->status);
        $this->assertSame(0, $this->asset->fresh()->jumlah_total);
        $this->assertDatabaseHas('pengembalian', ['peminjaman_id' => $booking->id]);
        $this->assertDatabaseHas('kerusakan_alat', [
            'detail_alat_id' => $detail->id,
            'unit_alat_id' => $unit->id,
            'deskripsi' => 'Kabel daya longgar.',
        ]);
        $this->assertSame(1, KerusakanAlat::where('detail_alat_id', $detail->id)->count());
        $this->assertSame(0, \App\Models\Sanksi::whereHas('pengembalian', fn ($query) => $query->where('peminjaman_id', $booking->id))->count());
        $this->assertSame(2, Notifikasi::where('peminjaman_id', $booking->id)->count());
        $this->assertSame(2, LogAktivitas::count());

        $this->post(route('staff.returns.store', $booking), [
            'conditions' => [$detail->id => 'Baik'],
        ])->assertStatus(409);

        $this->actingAs($this->student)
            ->get(route('booking.show', $booking))
            ->assertOk()
            ->assertSee('Informasi Serah Terima')
            ->assertSee('Informasi Pengembalian');
    }

    public function test_handover_is_blocked_until_final_approval_and_student_cannot_access_other_bookings(): void
    {
        $booking = $this->makeBooking();

        $this->actingAs($this->staff)
            ->get(route('staff.handover.create', $booking))
            ->assertStatus(409);
        $this->post(route('staff.returns.store', $booking), [])
            ->assertStatus(409);

        $this->actingAs($this->otherStudent)
            ->get(route('booking.show', $booking))
            ->assertForbidden();

        $this->get('/staff/serah-terima')->assertForbidden();
    }

    private function makeUser(string $email, string $role): User
    {
        static $counter = 0;
        $counter++;

        return User::create([
            'nama' => 'Pengguna '.$counter,
            'name' => 'Pengguna '.$counter,
            'username' => $role.$counter,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'status_aktif' => 'aktif',
        ]);
    }

    private function bookingPayload(array $overrides = []): array
    {
        return array_merge([
            'keperluan' => 'Pengujian fitur laboratorium',
            'ruangan_id' => $this->room->id,
            'tanggal_pinjam' => today()->addDays(3)->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'alat_id' => $this->asset->id,
            'jumlah_alat' => 1,
        ], $overrides);
    }

    private function makeBooking(array $overrides = []): Peminjaman
    {
        $booking = Peminjaman::create(array_merge([
            'kode_peminjaman' => 'TEST-'.uniqid(),
            'user_id' => $this->student->id,
            'tanggal_pinjam' => today()->addDays(3)->format('Y-m-d'),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '12:00:00',
            'keperluan' => 'Pengujian workflow SIP-LAB',
            'status' => 'Menunggu Pemeriksaan Staf Sarpras',
        ], $overrides));

        DetailRuangan::create([
            'peminjaman_id' => $booking->id,
            'ruangan_id' => $this->room->id,
        ]);
        DetailAlat::create([
            'peminjaman_id' => $booking->id,
            'alat_id' => $this->asset->id,
            'jumlah' => 1,
            'kondisi_awal' => 'Belum diperiksa',
        ]);

        return $booking;
    }

    private function recordApproval(Peminjaman $booking, User $user, string $level): void
    {
        Persetujuan::create([
            'peminjaman_id' => $booking->id,
            'user_id' => $user->id,
            'tingkat' => $level,
            'keputusan' => 'disetujui',
            'tanggal' => now(),
        ]);
    }
}
