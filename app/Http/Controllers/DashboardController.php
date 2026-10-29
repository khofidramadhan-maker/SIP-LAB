<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\KerusakanAlat;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function studentDashboard()
    {
        $bookings = Peminjaman::with(['detailRuangan.ruangan', 'detailAlat.alat'])
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();
        $rooms = Ruangan::latest()->limit(6)->get();
        $assets = Alat::latest()->limit(6)->get();

        return view('dashboard.student', compact('bookings', 'rooms', 'assets'));
    }

    public function staffDashboard()
    {
        $pending = Peminjaman::where('status', 'Menunggu Verifikasi')->count();
        $approved = Peminjaman::where('status', 'Disetujui')->count();
        $rooms = Ruangan::count();
        $todayBookings = Peminjaman::whereDate('tanggal_pinjam', today())->count();

        return view('dashboard.staff', compact('pending', 'approved', 'rooms', 'todayBookings'));
    }

    public function labHeadDashboard()
    {
        $pending = Peminjaman::where('status', 'Menunggu Persetujuan Kepala Lab')->count();
        $approved = Peminjaman::where('status', 'Disetujui')->count();
        $incidents = KerusakanAlat::count();
        $rooms = Ruangan::count();
        $assets = Alat::count();
        $totalBookings = Peminjaman::count();

        return view('dashboard.lab-head', compact('pending', 'approved', 'incidents', 'rooms', 'assets', 'totalBookings'));
    }
}
