<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\KerusakanAlat;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function studentDashboard()
    {
        $bookings = Peminjaman::with(['detailRuangan.ruangan', 'detailAlat.alat'])
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();
        $bookingStatusCounts = Peminjaman::query()
            ->where('user_id', Auth::id())
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $rooms = Ruangan::latest()->limit(6)->get();
        $assets = Alat::latest()->limit(6)->get();

        return view('dashboard.student', compact('bookings', 'bookingStatusCounts', 'rooms', 'assets'));
    }

    /**
     * Return approved bookings for a given year-month as JSON (for calendar AJAX).
     */
    public function calendarData(Request $request)
    {
        $year  = (int) $request->query('year',  now()->year);
        $month = (int) $request->query('month', now()->month);

        // All approved bookings in the requested month
        $bookings = Peminjaman::with(['detailRuangan.ruangan'])
            ->whereIn('status', ['Disetujui', 'Sedang Dipinjam'])
            ->whereYear('tanggal_pinjam', $year)
            ->whereMonth('tanggal_pinjam', $month)
            ->get();

        // Build a map: date => [ array of schedule items ]
        $calendar = [];
        foreach ($bookings as $b) {
            $date   = Carbon::parse($b->tanggal_pinjam)->format('Y-m-d');
            $ruang  = $b->detailRuangan->first()?->ruangan;
            $start  = substr($b->jam_mulai,  0, 5);
            $end    = substr($b->jam_selesai, 0, 5);

            // Duration in minutes
            $startCarbon = Carbon::createFromFormat('H:i', $start);
            $endCarbon   = Carbon::createFromFormat('H:i', $end);
            $durMins     = $startCarbon->diffInMinutes($endCarbon);
            $durText     = $durMins < 60
                ? "{$durMins} menit"
                : (($durMins % 60 === 0)
                    ? ($durMins / 60) . " jam"
                    : floor($durMins / 60) . " jam " . ($durMins % 60) . " menit");

            $calendar[$date][] = [
                'kode_peminjaman' => $b->kode_peminjaman,
                'ruangan'         => $ruang?->nama_ruangan ?? 'Ruang Khusus',
                'kode_ruangan'    => $ruang?->kode_ruangan ?? '-',
                'jam_mulai'       => $start,
                'jam_selesai'     => $end,
                'durasi'          => $durText,
                'status'          => $b->status,
            ];
        }

        return response()->json([
            'year'     => $year,
            'month'    => $month,
            'calendar' => $calendar,
        ]);
    }

    public function staffDashboard()
    {
        $pending = Peminjaman::whereIn('status', ['Menunggu Pemeriksaan Staf Sarpras', 'Menunggu Verifikasi'])->count();
        $approved = Peminjaman::where('status', 'Disetujui')->count();
        $rooms = Ruangan::count();
        $todayBookings = Peminjaman::whereDate('tanggal_pinjam', today())->count();
        $recentActivity = LogAktivitas::with('user')->latest('waktu')->limit(5)->get();

        return view('dashboard.staff', compact('pending', 'approved', 'rooms', 'todayBookings', 'recentActivity'));
    }

    public function labHeadDashboard()
    {
        $approved = Peminjaman::where('status', 'Disetujui')->count();
        $decided = Peminjaman::whereIn('status', ['Disetujui', 'Ditolak'])->count();
        $incidents = KerusakanAlat::count();
        $rooms = Ruangan::count();
        $assets = Alat::count();
        $totalBookings = Peminjaman::count();
        $recentActivity = LogAktivitas::with('user')->latest('waktu')->limit(5)->get();

        return view('dashboard.lab-head', compact('decided', 'approved', 'incidents', 'rooms', 'assets', 'totalBookings', 'recentActivity'));
    }
}
