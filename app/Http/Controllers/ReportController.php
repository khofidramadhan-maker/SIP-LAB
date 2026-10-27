<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\LabRoom;
use App\Models\Asset;

class ReportController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('user', 'labRoom')->latest()->limit(20)->get();
        $rooms = LabRoom::count();
        $assets = Asset::count();

        return view('reports.index', compact('bookings', 'rooms', 'assets'));
    }
}
