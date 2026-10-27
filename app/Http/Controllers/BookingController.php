<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\LabRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['labRoom', 'user'])->latest()->get();
        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms = LabRoom::all();
        $assets = Asset::all();
        return view('bookings.create', compact('rooms', 'assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string'],
            'lab_room_id' => ['nullable', 'exists:lab_rooms,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'supervisor_name' => ['nullable', 'string'],
            'supervisor_nip' => ['nullable', 'string'],
        ]);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'lab_room_id' => $validated['lab_room_id'] ?? null,
            'title' => $validated['title'],
            'purpose' => $validated['purpose'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'request_type' => 'lab_usage',
            'supervisor_name' => $validated['supervisor_name'] ?? null,
            'supervisor_nip' => $validated['supervisor_nip'] ?? null,
            'status' => 'Menunggu Verifikasi',
        ]);

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('documents', 'public');
            $booking->documents()->create([
                'document_type' => 'rekomendasi',
                'file_name' => $request->file('document')->getClientOriginalName(),
                'file_path' => $path,
            ]);
        }

        return redirect()->route('student.dashboard')->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function show(Booking $booking)
    {
        return view('bookings.show', compact('booking'));
    }
}
