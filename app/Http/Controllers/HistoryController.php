<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::with(['detailRuangan.ruangan', 'detailAlat.alat', 'persetujuan'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('history.index', compact('bookings'));
    }
}
