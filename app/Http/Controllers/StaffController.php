<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Persetujuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'persetujuan'])
            ->where('status', 'Menunggu Pemeriksaan Staf Sarpras')
            ->latest()
            ->get();

        return view('staff.index', compact('bookings'));
    }

    public function verify(Request $request, Peminjaman $booking)
    {
        $statusInput = $request->input('status'); // expected values: 'approved' or 'rejected'
        $notes = $request->input('notes');

        if ($statusInput === 'approved') {
            $booking->status = 'Disetujui';
            $pesanNotif = "Permohonan {$booking->kode_peminjaman} telah disetujui oleh Staf Sarpras.";
            $keputusan = 'disetujui';
        } else {
            $booking->status = 'Ditolak';
            $pesanNotif = "Permohonan {$booking->kode_peminjaman} ditolak oleh Staf Sarpras. Catatan: " . ($notes ?? 'Tidak ada catatan');
            $keputusan = 'ditolak';
        }

        $booking->save();

        // Rekam riwayat persetujuan tingkat 1
        Persetujuan::create([
            'peminjaman_id' => $booking->id,
            'user_id' => Auth::id(),
            'tingkat' => '1',
            'keputusan' => $keputusan,
            'catatan' => $notes,
            'tanggal' => now(),
        ]);

        // Rekam log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => "Staf sarpras memproses {$keputusan} untuk {$booking->kode_peminjaman}",
            'waktu' => now(),
        ]);

        // Kirim notifikasi ke pemohon
        Notifikasi::create([
            'user_id' => $booking->user_id,
            'peminjaman_id' => $booking->id,
            'pesan' => $pesanNotif,
            'sudah_dibaca' => false,
        ]);

        return back()->with('success', "Verifikasi peminjaman {$booking->kode_peminjaman} berhasil diproses.");
    }
}
