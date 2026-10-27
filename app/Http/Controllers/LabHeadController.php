<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class LabHeadController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('user')->where('status', 'Menunggu Persetujuan Kepala Lab')->latest()->get();
        return view('lab-head.index', compact('bookings'));
    }

    public function decide(Request $request, Booking $booking)
    {
        $status = $request->input('status');
        $notes = $request->input('notes');

        $booking->status = $status === 'approved' ? 'Disetujui' : 'Ditolak';
        $booking->notes = $notes;
        $booking->save();

        return back()->with('success', 'Keputusan kepala lab berhasil disimpan.');
    }
}
