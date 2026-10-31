<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailAlat;
use App\Models\DetailRuangan;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Services\BookingAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan'])->latest()->get();

        return view('history.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start' => ['nullable', 'date_format:H:i'],
            'end' => ['nullable', 'date_format:H:i'],
        ]);

        if (!empty($filters['start']) && !empty($filters['end']) && $filters['end'] <= $filters['start']) {
            return back()->withErrors([
                'end' => 'Jam selesai harus lebih akhir daripada jam mulai.',
            ])->withInput();
        }

        $roomsQuery = Ruangan::query()->where('status', 'tersedia');

        if (!empty($filters['date']) && !empty($filters['start']) && !empty($filters['end'])) {
            $roomsQuery->whereDoesntHave('peminjaman', function ($query) use ($filters) {
                $query->whereDate('tanggal_pinjam', $filters['date'])
                    ->whereIn('status', BookingAvailability::ACTIVE_STATUSES)
                    ->where('jam_mulai', '<', $filters['end'])
                    ->where('jam_selesai', '>', $filters['start']);
            });
        }

        $rooms = $roomsQuery->get();
        $assets = Alat::query()->where('status', 'tersedia')->get();

        return view('bookings.create', compact('rooms', 'assets'));
    }

    public function store(Request $request, BookingAvailability $availability)
    {
        $validated = $request->validate([
            'keperluan' => ['required', 'string', 'max:2000'],
            'ruangan_id' => ['required', 'integer', 'exists:ruangan,id'],
            'tanggal_pinjam' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'alat_id' => ['nullable', 'integer', 'exists:alat,id'],
            'jumlah_alat' => ['nullable', 'integer', 'min:1'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $assetId = !empty($validated['alat_id']) ? (int) $validated['alat_id'] : null;
        $assetQuantity = $assetId === null ? 0 : (int) ($validated['jumlah_alat'] ?? 1);

        $booking = DB::transaction(function () use ($request, $validated, $availability, $assetId, $assetQuantity) {
            $availability->lockAndAssertAvailable(
                (int) $validated['ruangan_id'],
                $validated['tanggal_pinjam'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $assetId,
                $assetQuantity
            );

            $documentPath = $request->hasFile('document')
                ? $request->file('document')->store('dokumen_peminjaman', 'public')
                : null;

            $code = 'PINJAM-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(6));

            $booking = Peminjaman::create([
                'kode_peminjaman' => $code,
                'user_id' => Auth::id(),
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'keperluan' => $validated['keperluan'],
                'dokumen_pendukung' => $documentPath,
                'status' => 'Menunggu Pemeriksaan Staf Sarpras',
            ]);

            DetailRuangan::create([
                'peminjaman_id' => $booking->id,
                'ruangan_id' => $validated['ruangan_id'],
            ]);

            if ($assetId !== null) {
                DetailAlat::create([
                    'peminjaman_id' => $booking->id,
                    'alat_id' => $assetId,
                    'jumlah' => $assetQuantity,
                    'kondisi_awal' => 'Belum diperiksa',
                ]);
            }

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => "Mengajukan permohonan peminjaman laboratorium {$code}",
                'waktu' => now(),
            ]);

            Notifikasi::create([
                'user_id' => Auth::id(),
                'peminjaman_id' => $booking->id,
                'pesan' => "Permohonan peminjaman {$code} berhasil dibuat dan menunggu pemeriksaan Staf Sarpras.",
                'sudah_dibaca' => false,
            ]);

            return $booking;
        });

        return redirect()->route('student.dashboard')
            ->with('success', "Pengajuan peminjaman {$booking->kode_peminjaman} berhasil dikirim.");
    }

    public function show(Peminjaman $booking)
    {
        if (Auth::user()->role === 'mahasiswa' && $booking->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        $booking->load([
            'user',
            'detailRuangan.ruangan',
            'detailAlat.alat',
            'persetujuan.user',
            'serahTerima.petugas',
            'pengembalian.petugas',
            'pengembalian.kerusakanAlat.detailAlat.alat',
            'pengembalian.sanksi',
        ]);

        return view('bookings.show', compact('booking'));
    }
}
