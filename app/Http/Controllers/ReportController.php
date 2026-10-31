<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateToRules = ['nullable', 'date_format:Y-m-d'];
        if ($request->filled('date_from')) {
            $dateToRules[] = 'after_or_equal:date_from';
        }

        $filters = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => $dateToRules,
        ]);

        $bookingsQuery = Peminjaman::query()
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pinjam', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pinjam', '<=', $date));

        $bookings = (clone $bookingsQuery)
            ->with(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'persetujuan.user'])
            ->latest()
            ->get();
        $rooms = Ruangan::count();
        $assets = Alat::count();
        $approvedCount = (clone $bookingsQuery)->where('status', 'Disetujui')->count();
        $totalBookings = $bookingsQuery->count();

        return view('reports.index', compact('bookings', 'rooms', 'assets', 'approvedCount', 'totalBookings', 'filters'));
    }
}
