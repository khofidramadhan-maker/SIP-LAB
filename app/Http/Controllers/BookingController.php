<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailAlat;
use App\Models\DetailRuangan;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan'])->latest()->get();
        return view('history.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        // Retrieve filter parameters
        $date = $request->query('date');
        $start = $request->query('start');
        $end = $request->query('end');

        // Base query for rooms
        $roomsQuery = Ruangan::query();

        if ($date && $start && $end) {
            // Exclude rooms that have an approved booking overlapping the requested time range
            $roomsQuery->whereDoesntHave('peminjamen', function ($q) use ($date, $start, $end) {
                $q->where('tanggal_pinjam', $date)
                  ->where('status', 'Disetujui')
                  ->where(function ($sub) use ($start, $end) {
                      $sub->where('jam_mulai', '<', $end)
                          ->where('jam_selesai', '>', $start);
                  });
            });
        }

        $rooms = $roomsQuery->get();
        $assets = Alat::all();
        return view('bookings.create', compact('rooms', 'assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'keperluan' => ['required', 'string'],
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'tanggal_pinjam' => ['required', 'date'],
            'jam_mulai' => ['required'],
            'jam_selesai' => ['required'],
            'alat_id' => ['nullable', 'exists:alat,id'],
            'jumlah_alat' => ['nullable', 'integer', 'min:1'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $dokumenPath = null;
        if ($request->hasFile('document')) {
            $dokumenPath = $request->file('document')->store('dokumen_peminjaman', 'public');
        }

        // Generate unique kode peminjaman
        $kode = 'PINJAM-' . date('Ymd') . '-' . rand(100, 999);

        $booking = Peminjaman::create([
            'kode_peminjaman' => $kode,
            'user_id' => Auth::id(),
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'keperluan' => $validated['keperluan'],
            'dokumen_pendukung' => $dokumenPath,
                        'status' => 'Menunggu Pemeriksaan Staf Sarpras',
        ]);

        // Simpan relasi detail_ruangan
        DetailRuangan::create([
            'peminjaman_id' => $booking->id,
            'ruangan_id' => $validated['ruangan_id'],
        ]);

        // Simpan relasi detail_alat jika ada alat yang dipilih
        if (!empty($validated['alat_id'])) {
            DetailAlat::create([
                'peminjaman_id' => $booking->id,
                'alat_id' => $validated['alat_id'],
                'jumlah' => $validated['jumlah_alat'] ?? 1,
                'kondisi_awal' => 'baik',
            ]);
        }

        // Catat ke log_aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => "Mengajukan permohonan peminjaman laboratorium {$kode}",
            'waktu' => now(),
        ]);

        // Buat notifikasi
        Notifikasi::create([
            'user_id' => Auth::id(),
            'peminjaman_id' => $booking->id,
            'pesan' => "Permohonan peminjaman {$kode} berhasil dibuat dan saat ini dalam antrean verifikasi staf.",
            'sudah_dibaca' => false,
        ]);

        return redirect()->route('student.dashboard')->with('success', "Pengajuan peminjaman {$kode} berhasil dikirim.");
    }

    public function show(Peminjaman $booking)
    {
        $booking->load(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'persetujuan.user', 'serahTerima', 'pengembalian.sanksi']);
        return view('bookings.show', compact('booking'));
    }
}
