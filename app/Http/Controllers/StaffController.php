<?php

namespace App\Http\Controllers;

use App\Models\DetailAlatUnit;
use App\Models\KerusakanAlat;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\Persetujuan;
use App\Models\SerahTerima;
use App\Models\UnitAlat;
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
        $booking->load(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'detailAlat.alat.availableUnits']);
        $this->ensureReadyForHandover($booking);

        return view('staff.handover.create', compact('booking'));
    }

    public function handover(Request $request, Peminjaman $booking)
    {
        $booking->load('detailAlat.alat');
        $this->ensureReadyForHandover($booking);

        // Staf memilih unit fisik persis yang diserahkan (tepat sejumlah
        // yang dipinjam) dan menulis kondisi awal tiap unit — bukan satu
        // kondisi untuk satu jenis alat.
        $rules = [
            'units' => ['nullable', 'array'],
            'unit_conditions' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
        foreach ($booking->detailAlat as $detail) {
            $rules["units.{$detail->id}"] = ['required', 'array', 'size:' . $detail->jumlah];
            $rules["units.{$detail->id}.*"] = ['required', 'integer', 'distinct', 'exists:unit_alat,id'];
            $rules["unit_conditions.{$detail->id}"] = ['nullable', 'array'];
            // Kondisi hanya wajib untuk unit yang dicentang — dicek di
            // transaksi di bawah, karena form mengirim kolom kondisi untuk
            // semua unit yang tampil, termasuk yang tidak dicentang.
            $rules["unit_conditions.{$detail->id}.*"] = ['nullable', 'string', 'max:100'];
        }
        $validated = $request->validate($rules);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Peminjaman::query()->lockForUpdate()->findOrFail($booking->id);
            $this->ensureReadyForHandover($booking);
            $booking->load('detailAlat.alat');

            foreach ($booking->detailAlat as $detail) {
                $summary = [];
                foreach ($validated['units'][$detail->id] as $unitId) {
                    $unit = UnitAlat::query()->lockForUpdate()->findOrFail($unitId);

                    if ($unit->alat_id !== $detail->alat_id || $unit->status !== UnitAlat::STATUS_TERSEDIA) {
                        throw ValidationException::withMessages([
                            "units.{$detail->id}" => "Unit {$unit->kode_unit} sudah tidak tersedia. Pilih unit lain.",
                        ]);
                    }

                    $condition = trim($validated['unit_conditions'][$detail->id][$unitId] ?? '');
                    if ($condition === '') {
                        throw ValidationException::withMessages([
                            "unit_conditions.{$detail->id}.{$unitId}" => "Tulis kondisi awal untuk unit {$unit->kode_unit}.",
                        ]);
                    }
                    DetailAlatUnit::create([
                        'detail_alat_id' => $detail->id,
                        'unit_alat_id' => $unit->id,
                        'kondisi_awal' => $condition,
                    ]);
                    $unit->update(['status' => UnitAlat::STATUS_DIPINJAM]);
                    $summary[] = "{$unit->kode_unit}: {$condition}";
                }

                // Ringkasan per jenis tetap disimpan untuk tampilan lama.
                $detail->update(['kondisi_awal' => implode('; ', $summary)]);
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
        $bookings = Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'detailAlat.detailAlatUnits.unitAlat', 'serahTerima'])
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
        $booking->load(['user', 'detailRuangan.ruangan', 'detailAlat.alat', 'detailAlat.detailAlatUnits.unitAlat', 'serahTerima']);
        $this->ensureReadyForReturn($booking);

        return view('staff.returns.create', compact('booking'));
    }

    public function returnBooking(Request $request, Peminjaman $booking)
    {
        $booking->load('detailAlat.detailAlatUnits.unitAlat');
        $this->ensureReadyForReturn($booking);

        // Per unit yang dulu diserahkan: kondisi akhir + centang rusak +
        // deskripsi kerusakan. Kalau peminjaman lama belum punya alokasi
        // unit (diserahkan sebelum fitur ini), pakai jalur lama per baris.
        $rules = [
            'conditions' => ['nullable', 'array'],
            'damage_descriptions' => ['nullable', 'array'],
            'unit_conditions' => ['nullable', 'array'],
            'unit_damaged' => ['nullable', 'array'],
            'unit_damage_descriptions' => ['nullable', 'array'],
        ];
        foreach ($booking->detailAlat as $detail) {
            if ($detail->detailAlatUnits->isNotEmpty()) {
                foreach ($detail->detailAlatUnits as $row) {
                    $rules["unit_conditions.{$detail->id}.{$row->unit_alat_id}"] = ['required', 'string', 'max:100'];
                    $rules["unit_damage_descriptions.{$detail->id}.{$row->unit_alat_id}"] = ['nullable', 'string', 'max:2000'];
                }
            } else {
                $rules["conditions.{$detail->id}"] = ['required', 'string', 'max:100'];
                $rules["damage_descriptions.{$detail->id}"] = ['nullable', 'string', 'max:2000'];
            }
        }
        $validated = $request->validate($rules);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Peminjaman::query()->lockForUpdate()->findOrFail($booking->id);
            $this->ensureReadyForReturn($booking);
            $booking->load('detailAlat.detailAlatUnits.unitAlat');

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
                if ($detail->detailAlatUnits->isNotEmpty()) {
                    $summary = [];
                    foreach ($detail->detailAlatUnits as $row) {
                        $unitId = $row->unit_alat_id;
                        $condition = $validated['unit_conditions'][$detail->id][$unitId];
                        $damaged = !empty($validated['unit_damaged'][$detail->id][$unitId] ?? null);
                        $description = trim($validated['unit_damage_descriptions'][$detail->id][$unitId] ?? '');

                        if ($damaged && $description === '') {
                            throw ValidationException::withMessages([
                                "unit_damage_descriptions.{$detail->id}.{$unitId}" => "Tulis deskripsi kerusakan untuk unit {$row->unitAlat->kode_unit}.",
                            ]);
                        }

                        $row->update(['kondisi_akhir' => $condition]);
                        $row->unitAlat->update(['status' => $damaged ? UnitAlat::STATUS_RUSAK : UnitAlat::STATUS_TERSEDIA]);
                        $summary[] = "{$row->unitAlat->kode_unit}: {$condition}";

                        if ($damaged) {
                            KerusakanAlat::create([
                                'pengembalian_id' => $return->id,
                                'detail_alat_id' => $detail->id,
                                'unit_alat_id' => $unitId,
                                'jenis_kondisi' => $condition,
                                'deskripsi' => $description,
                            ]);

                            // Unit rusak keluar dari stok yang bisa dipinjam:
                            // jumlah di Manajemen Alat berkurang sejumlah
                            // unit yang rusak pada pengembalian ini.
                            $alat = \App\Models\Alat::query()->lockForUpdate()->find($detail->alat_id);
                            if ($alat && $alat->jumlah_total > 0) {
                                $alat->decrement('jumlah_total');
                            }
                        }
                    }
                    $detail->update(['kondisi_akhir' => implode('; ', $summary)]);
                } else {
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
            ->with('success', "Pengembalian peminjaman {$booking->kode_peminjaman} berhasil dicatat.");
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
