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
            ->whereIn('status', ['Menunggu Verifikasi', 'Perlu Revisi'])
            ->latest()
            ->get();

        return view('staff.index', compact('bookings'));
    }

    public function verify(Request $request, Peminjaman $booking)
    {
        $statusInput = $request->input('status');
        $notes = $request->input('notes');

        $keputusan = ($statusInput === 'approved') ? 'disetujui' : 'perlu_revisi';

        if ($statusInput === 'approved') {
            $booking->status = 'Menunggu Persetujuan Kepala Lab';
            $pesanNotif = "Permohonan {$booking->kode_peminjaman} telah lolos verifikasi staf dan diteruskan ke Kepala Laboratorium.";
        } else {
            $booking->status = 'Perlu Revisi';
            $pesanNotif = "Permohonan {$booking->kode_peminjaman} memerlukan perbaikan catatan: " . ($notes ?? 'Periksa kembali kelengkapan');
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
            'aktivitas' => "Staf sarpras memproses verifikasi tingkat I untuk {$booking->kode_peminjaman} ({$keputusan})",
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
