<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\LabRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function studentDashboard()
    {
        $bookings = Booking::with('labRoom')->where('user_id', Auth::id())->latest()->limit(5)->get();
        $rooms = LabRoom::latest()->limit(6)->get();
        $assets = Asset::latest()->limit(6)->get();

        return view('dashboard.student', compact('bookings', 'rooms', 'assets'));
    }

    public function staffDashboard()
    {
        $pending = Booking::where('status', 'Menunggu Verifikasi')->count();
        $approved = Booking::where('status', 'Disetujui')->count();
        $rooms = LabRoom::count();
        $todayBookings = Booking::whereDate('start_date', today())->count();

        return view('dashboard.staff', compact('pending', 'approved', 'rooms', 'todayBookings'));
    }

    public function labHeadDashboard()
    {
        $pending = Booking::where('status', 'Menunggu Persetujuan Kepala Lab')->count();
        $approved = Booking::where('status', 'Disetujui')->count();
        $incidents = Booking::whereNotNull('rejection_reason')->count();

        return view('dashboard.lab-head', compact('pending', 'approved', 'incidents'));
    }

    public function adminDashboard()
    {
        $users = \App\Models\User::count();
        $rooms = LabRoom::count();
        $assets = Asset::count();

        return view('dashboard.admin', compact('users', 'rooms', 'assets'));
    }
}
