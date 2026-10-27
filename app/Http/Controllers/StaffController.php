<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('user')->whereIn('status', ['Menunggu Verifikasi', 'Perlu Revisi'])->latest()->get();
        return view('staff.index', compact('bookings'));
    }

    public function verify(Request $request, Booking $booking)
    {
        $status = $request->input('status');
        $notes = $request->input('notes');

        if ($status === 'approved') {
            $booking->status = 'Menunggu Persetujuan Kepala Lab';
        } else {
            $booking->status = 'Perlu Revisi';
            $booking->rejection_reason = $notes;
        }

        $booking->save();

        return back()->with('success', 'Verifikasi berhasil diproses.');
    }
}
