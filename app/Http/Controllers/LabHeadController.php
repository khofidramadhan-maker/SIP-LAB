<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Persetujuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabHeadController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'persetujuan'])
            ->where('status', 'Menunggu Persetujuan Kepala Lab')
            ->latest()
            ->get();

        return view('lab-head.index', compact('bookings'));
    }

    public function decide(Request $request, Peminjaman $booking)
    {
        $statusInput = $request->input('status');
        $notes = $request->input('notes');

        $isApproved = ($statusInput === 'approved');
        $booking->status = $isApproved ? 'Disetujui' : 'Ditolak';
        $booking->save();

        $keputusan = $isApproved ? 'disetujui' : 'ditolak';

        // Rekam riwayat persetujuan tingkat 2 (Kepala Lab)
        Persetujuan::create([
            'peminjaman_id' => $booking->id,
            'user_id' => Auth::id(),
            'tingkat' => '2',
            'keputusan' => $keputusan,
            'catatan' => $notes,
            'tanggal' => now(),
        ]);

        // Rekam log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => "Kepala Laboratorium menetapkan putusan tingkat II untuk {$booking->kode_peminjaman} ({$keputusan})",
            'waktu' => now(),
        ]);

        // Kirim notifikasi ke pemohon
        $pesanNotif = $isApproved
            ? "Selamat! Permohonan peminjaman {$booking->kode_peminjaman} telah disetujui resmi oleh Kepala Laboratorium."
            : "Permohonan peminjaman {$booking->kode_peminjaman} ditolak oleh Kepala Lab. Catatan: " . ($notes ?? 'Tidak disetujui');

        Notifikasi::create([
            'user_id' => $booking->user_id,
            'peminjaman_id' => $booking->id,
            'pesan' => $pesanNotif,
            'sudah_dibaca' => false,
        ]);

        return back()->with('success', "Keputusan kepala laboratorium untuk {$booking->kode_peminjaman} berhasil disimpan.");
    }
}
