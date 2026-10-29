<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LabHeadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Role: Mahasiswa
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'studentDashboard'])->name('student.dashboard');
        Route::get('/mahasiswa/inventaris', [InventoryController::class, 'index'])->name('student.inventory');
        Route::get('/mahasiswa/riwayat', [HistoryController::class, 'index'])->name('student.history');
        Route::get('/mahasiswa/peminjaman/create', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/mahasiswa/peminjaman', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/mahasiswa/peminjaman/{booking}', [BookingController::class, 'show'])->name('booking.show');
        Route::get('/mahasiswa/notifikasi', [NotificationController::class, 'index'])->name('student.notifications');
    });

    // Role: Staf Sarpras
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');
        Route::get('/staff/verifikasi', [StaffController::class, 'index'])->name('staff.verification');
        Route::post('/staff/verifikasi/{booking}', [StaffController::class, 'verify'])->name('staff.verify');
        Route::get('/staff/inventaris', [InventoryController::class, 'index'])->name('staff.inventory');
    });

    // Role: Kepala Laboratorium (Pimpinan Lab: Approval & Laporan Operasional)
    Route::middleware('role:kepala_lab')->group(function () {
        Route::get('/kepala-lab/dashboard', [DashboardController::class, 'labHeadDashboard'])->name('lab-head.dashboard');
        Route::get('/kepala-lab/approval', [LabHeadController::class, 'index'])->name('lab-head.approval');
        Route::post('/kepala-lab/approval/{booking}', [LabHeadController::class, 'decide'])->name('lab-head.decide');
        Route::get('/kepala-lab/laporan', [ReportController::class, 'index'])->name('lab-head.reports');
    });
});
