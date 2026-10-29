<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Ruangan;

class ReportController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'persetujuan.user'])->latest()->get();
        $rooms = Ruangan::count();
        $assets = Alat::count();

        return view('reports.index', compact('bookings', 'rooms', 'assets'));
    }
}
