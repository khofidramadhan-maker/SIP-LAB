<?php

namespace App\Http\Controllers;

use App\Models\KerusakanAlat;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\Persetujuan;
use App\Models\SerahTerima;
use App\Services\BookingAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function index()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'persetujuan'])
            ->whereIn('status', ['Menunggu Pemeriksaan Staf Sarpras', 'Menunggu Verifikasi'])
            ->latest()
            ->get();

        return view('staff.index', compact('bookings'));
    }

    public function verify(Request $request, Peminjaman $booking, BookingAvailability $availability)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'notes' => ['nullable', 'string', 'max:2000', 'required_if:status,rejected'],
        ]);

        DB::transaction(function () use ($booking, $validated, $availability) {
            $booking = Peminjaman::query()->lockForUpdate()->findOrFail($booking->id);

            if (!in_array($booking->status, ['Menunggu Pemeriksaan Staf Sarpras', 'Menunggu Verifikasi'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan ini sudah diproses atau tidak lagi menunggu pemeriksaan staf.',
                ]);
            }

            $isApproved = $validated['status'] === 'approved';

            if ($isApproved) {
                $roomId = $booking->detailRuangan()->value('ruangan_id');
                if (!$roomId) {
                    throw ValidationException::withMessages([
                        'ruangan_id' => 'Pengajuan tidak memiliki data ruang laboratorium.',
                    ]);
                }

                $details = $booking->detailAlat()->orderBy('alat_id')->get();
                if ($details->isEmpty()) {
                    $availability->lockAndAssertAvailable(
                        (int) $roomId,
                        $booking->tanggal_pinjam,
                        $booking->jam_mulai,
                        $booking->jam_selesai,
                        exceptBookingId: $booking->id
                    );
                } else {
                    foreach ($details as $detail) {
                        $availability->lockAndAssertAvailable(
                            (int) $roomId,
                            $booking->tanggal_pinjam,
                            $booking->jam_mulai,
                            $booking->jam_selesai,
                            (int) $detail->alat_id,
                            (int) $detail->jumlah,
                            $booking->id
                        );
                    }
                }

                $booking->status = 'Disetujui';
                $decision = 'disetujui';
                $message = "Permohonan peminjaman {$booking->kode_peminjaman} telah disetujui oleh Staf Sarpras.";
            } else {
                $booking->status = 'Ditolak';
                $decision = 'ditolak';
                $message = "Permohonan {$booking->kode_peminjaman} ditolak oleh Staf Sarpras. Catatan: {$validated['notes']}";
            }

            $booking->save();

            Persetujuan::create([
                'peminjaman_id' => $booking->id,
                'user_id' => Auth::id(),
                'tingkat' => '1',
                'keputusan' => $decision,
                'catatan' => $validated['notes'] ?? null,
                'tanggal' => now(),
            ]);

            $this->recordActivity("Staf Sarpras {$decision} pengajuan {$booking->kode_peminjaman}");
            $this->notifyStudent($booking, $message);
        });

        return back()->with('success', "Verifikasi peminjaman {$booking->kode_peminjaman} berhasil diproses.");
    }

    public function handoverIndex()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat'])
            ->where('status', 'Disetujui')
            ->whereDoesntHave('serahTerima')
            ->orderBy('tanggal_pinjam')
            ->orderBy('jam_mulai')
            ->get();

        return view('staff.handover.index', compact('bookings'));
    }

    public function handoverForm(Peminjaman $booking)
    {
        $booking->load(['user', 'detailRuangan.ruangan', 'detailAlat.alat']);
        $this->ensureReadyForHandover($booking);

        return view('staff.handover.create', compact('booking'));
    }

    public function handover(Request $request, Peminjaman $booking)
    {
        $booking->load('detailAlat');
        $this->ensureReadyForHandover($booking);

        $rules = [
            'conditions' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
        foreach ($booking->detailAlat as $detail) {
            $rules["conditions.{$detail->id}"] = ['required', 'string', 'max:100'];
        }
        $validated = $request->validate($rules);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Peminjaman::query()->lockForUpdate()->findOrFail($booking->id);
            $this->ensureReadyForHandover($booking);

            foreach ($booking->detailAlat as $detail) {
                $detail->update([
                    'kondisi_awal' => $validated['conditions'][$detail->id],
                ]);
            }

            SerahTerima::create([
                'peminjaman_id' => $booking->id,
                'petugas_id' => Auth::id(),
                'waktu_serah' => now(),
                'catatan' => $validated['notes'] ?? null,
            ]);

            $booking->status = 'Sedang Dipinjam';
            $booking->save();

            $this->recordActivity("Staf Sarpras menyerahkan peminjaman {$booking->kode_peminjaman}");
            $this->notifyStudent(
                $booking,
                "Peminjaman {$booking->kode_peminjaman} telah diserahterimakan oleh Staf Sarpras."
            );
        });

        return redirect()->route('staff.handover.index')
            ->with('success', "Serah terima {$booking->kode_peminjaman} berhasil dicatat.");
    }

    public function returnIndex()
    {
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'serahTerima'])
            ->where('status', 'Sedang Dipinjam')
            ->whereHas('serahTerima')
            ->whereDoesntHave('pengembalian')
            ->orderBy('tanggal_pinjam')
            ->orderBy('jam_selesai')
            ->get();

        return view('staff.returns.index', compact('bookings'));
    }

    public function returnForm(Peminjaman $booking)
    {
        $booking->load(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'serahTerima']);
        $this->ensureReadyForReturn($booking);

        return view('staff.returns.create', compact('booking'));
    }

    public function returnBooking(Request $request, Peminjaman $booking)
    {
        $booking->load('detailAlat');
        $this->ensureReadyForReturn($booking);

        $rules = [
            'conditions' => ['nullable', 'array'],
            'damage_descriptions' => ['nullable', 'array'],
        ];
        foreach ($booking->detailAlat as $detail) {
            $rules["conditions.{$detail->id}"] = ['required', 'string', 'max:100'];
            $rules["damage_descriptions.{$detail->id}"] = ['nullable', 'string', 'max:2000'];
        }
        $validated = $request->validate($rules);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Peminjaman::query()->lockForUpdate()->findOrFail($booking->id);
            $this->ensureReadyForReturn($booking);

            $returnedAt = now();
            $dueDate = Carbon::parse($booking->tanggal_pinjam)
                ->setTimeFromTimeString($booking->jam_selesai);
            $lateDays = max(
                0,
                (int) $dueDate->copy()->startOfDay()->diffInDays($returnedAt->copy()->startOfDay(), false)
            );

            $return = Pengembalian::create([
                'peminjaman_id' => $booking->id,
                'petugas_id' => Auth::id(),
                'waktu_kembali' => $returnedAt,
                'hari_terlambat' => $lateDays,
            ]);

            foreach ($booking->detailAlat as $detail) {
                $condition = $validated['conditions'][$detail->id];
                $detail->update(['kondisi_akhir' => $condition]);

                $description = trim($validated['damage_descriptions'][$detail->id] ?? '');
                if ($description !== '') {
                    KerusakanAlat::create([
                        'pengembalian_id' => $return->id,
                        'detail_alat_id' => $detail->id,
                        'jenis_kondisi' => $condition,
                        'deskripsi' => $description,
                    ]);
                }
            }

            $booking->status = 'Selesai';
            $booking->save();

            $this->recordActivity("Staf Sarpras mencatat pengembalian peminjaman {$booking->kode_peminjaman}");
            $this->notifyStudent(
                $booking,
                "Pengembalian peminjaman {$booking->kode_peminjaman} telah dicatat oleh Staf Sarpras."
            );
        });

        return redirect()->route('staff.returns.index')
            ->with('success', "Pengembalian {$booking->kode_peminjaman} berhasil dicatat.");
    }

    private function ensureReadyForHandover(Peminjaman $booking): void
    {
        if ($booking->status !== 'Disetujui' || $booking->serahTerima()->exists()) {
            abort(409, 'Peminjaman belum disetujui atau sudah diserahterimakan.');
        }
    }

    private function ensureReadyForReturn(Peminjaman $booking): void
    {
        if ($booking->status !== 'Sedang Dipinjam'
            || !$booking->serahTerima()->exists()
            || $booking->pengembalian()->exists()) {
            abort(409, 'Peminjaman belum diserahterimakan atau sudah dikembalikan.');
        }
    }

    private function recordActivity(string $activity): void
    {
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => $activity,
            'waktu' => now(),
        ]);
    }

    private function notifyStudent(Peminjaman $booking, string $message): void
    {
        Notifikasi::create([
            'user_id' => $booking->user_id,
            'peminjaman_id' => $booking->id,
            'pesan' => $message,
            'sudah_dibaca' => false,
        ]);
    }
}
