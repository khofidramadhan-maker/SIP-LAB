<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('labRoom')->where('user_id', Auth::id())->latest()->get();
        return view('history.index', compact('bookings'));
    }
}
