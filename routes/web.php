<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ManajemenController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [AuthController::class, 'home'])->name('legacy.home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // Role: Mahasiswa
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'studentDashboard'])->name('student.dashboard');
        Route::get('/mahasiswa/inventaris', [InventoryController::class, 'index'])->name('student.inventory');
        Route::get('/mahasiswa/riwayat', [HistoryController::class, 'index'])->name('student.history');
        Route::get('/mahasiswa/peminjaman/create', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/mahasiswa/peminjaman', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/mahasiswa/notifikasi', [NotificationController::class, 'index'])->name('student.notifications');
        Route::post('/mahasiswa/notifikasi/{notification}/dibaca', [NotificationController::class, 'markAsRead'])
            ->name('student.notifications.read');
        // Calendar: AJAX endpoint to get approved bookings for a given month
        Route::get('/mahasiswa/kalender-data', [DashboardController::class, 'calendarData'])->name('student.calendar.data');
    });
    Route::get('/mahasiswa/peminjaman/{booking}', [BookingController::class, 'show'])->name('booking.show');

    // Role: Staf Sarpras
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');
        Route::get('/staff/verifikasi', [StaffController::class, 'index'])->name('staff.verification');
        Route::post('/staff/verifikasi/{booking}', [StaffController::class, 'verify'])->name('staff.verify');
        Route::get('/staff/serah-terima', [StaffController::class, 'handoverIndex'])->name('staff.handover.index');
        Route::get('/staff/serah-terima/{booking}', [StaffController::class, 'handoverForm'])->name('staff.handover.create');
        Route::post('/staff/serah-terima/{booking}', [StaffController::class, 'handover'])->name('staff.handover.store');
        Route::get('/staff/pengembalian', [StaffController::class, 'returnIndex'])->name('staff.returns.index');
        Route::get('/staff/pengembalian/{booking}', [StaffController::class, 'returnForm'])->name('staff.returns.create');
        Route::post('/staff/pengembalian/{booking}', [StaffController::class, 'returnBooking'])->name('staff.returns.store');
        Route::get('/staff/inventaris', [InventoryController::class, 'index'])->name('staff.inventory');

        // ── Manajemen Ruangan ──────────────────────────────────────────────
        Route::get('/staff/ruangan',                [ManajemenController::class, 'ruanganIndex'])->name('staff.ruangan.index');
        Route::get('/staff/ruangan/tambah',         [ManajemenController::class, 'ruanganCreate'])->name('staff.ruangan.create');
        Route::post('/staff/ruangan',               [ManajemenController::class, 'ruanganStore'])->name('staff.ruangan.store');
        Route::get('/staff/ruangan/{ruangan}',      [ManajemenController::class, 'ruanganShow'])->name('staff.ruangan.show');
        Route::get('/staff/ruangan/{ruangan}/ubah', [ManajemenController::class, 'ruanganEdit'])->name('staff.ruangan.edit');
        Route::put('/staff/ruangan/{ruangan}',      [ManajemenController::class, 'ruanganUpdate'])->name('staff.ruangan.update');

        // ── Manajemen Pengguna ─────────────────────────────────────────────
        Route::get('/staff/pengguna',               [ManajemenController::class, 'penggunaIndex'])->name('staff.pengguna.index');
        Route::get('/staff/pengguna/tambah',        [ManajemenController::class, 'penggunaCreate'])->name('staff.pengguna.create');
        Route::post('/staff/pengguna',              [ManajemenController::class, 'penggunaStore'])->name('staff.pengguna.store');
        Route::get('/staff/pengguna/{user}',        [ManajemenController::class, 'penggunaShow'])->name('staff.pengguna.show');
        Route::get('/staff/pengguna/{user}/ubah',   [ManajemenController::class, 'penggunaEdit'])->name('staff.pengguna.edit');
        Route::put('/staff/pengguna/{user}',        [ManajemenController::class, 'penggunaUpdate'])->name('staff.pengguna.update');

        // ── Manajemen Alat ─────────────────────────────────────────────────
        Route::get('/staff/alat',                   [ManajemenController::class, 'alatIndex'])->name('staff.alat.index');
        Route::get('/staff/alat/tambah',            [ManajemenController::class, 'alatCreate'])->name('staff.alat.create');
        Route::post('/staff/alat',                  [ManajemenController::class, 'alatStore'])->name('staff.alat.store');
        Route::get('/staff/alat/{alat}',            [ManajemenController::class, 'alatShow'])->name('staff.alat.show');
        Route::get('/staff/alat/{alat}/ubah',       [ManajemenController::class, 'alatEdit'])->name('staff.alat.edit');
        Route::put('/staff/alat/{alat}',            [ManajemenController::class, 'alatUpdate'])->name('staff.alat.update');

        // ── Riwayat Perubahan Data ─────────────────────────────────────────
        Route::get('/staff/riwayat-data',           [ManajemenController::class, 'riwayat'])->name('staff.riwayat');
    });

    // Role: Kepala Laboratorium
    Route::middleware('role:kepala_lab')->group(function () {
        Route::get('/kepala-lab/dashboard', [DashboardController::class, 'labHeadDashboard'])->name('lab-head.dashboard');
        Route::get('/kepala-lab/laporan', [ReportController::class, 'index'])->name('lab-head.reports');
    });
});
