<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\Ruangan;
use Illuminate\Validation\ValidationException;

class BookingAvailability
{
    public const ACTIVE_STATUSES = [
        'Menunggu Verifikasi',
        'Menunggu Pemeriksaan Staf Sarpras',
        'Disetujui',
        'Sedang Dipinjam',
    ];

    public function lockAndAssertAvailable(
        int $roomId,
        string $date,
        string $start,
        string $end,
        ?int $assetId = null,
        int $assetQuantity = 0,
        ?int $exceptBookingId = null
    ): array {
        $room = Ruangan::query()->whereKey($roomId)->lockForUpdate()->firstOrFail();

        if ($room->status !== 'tersedia') {
            throw ValidationException::withMessages([
                'ruangan_id' => 'Ruang laboratorium sedang tidak tersedia.',
            ]);
        }

        $roomConflict = $room->peminjaman()
            ->whereDate('tanggal_pinjam', $date)
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->where('jam_mulai', '<', $end)
            ->where('jam_selesai', '>', $start)
            ->when($exceptBookingId, fn ($query) => $query->whereKeyNot($exceptBookingId))
            ->exists();

        if ($roomConflict) {
            throw ValidationException::withMessages([
                'ruangan_id' => 'Ruangan sudah memiliki peminjaman pada rentang waktu tersebut.',
            ]);
        }

        $asset = null;
        if ($assetId !== null) {
            $asset = Alat::query()->whereKey($assetId)->lockForUpdate()->firstOrFail();

            if ($asset->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'alat_id' => 'Alat yang dipilih sedang tidak tersedia.',
                ]);
            }

            $borrowedQuantity = $asset->detailAlat()
                ->whereHas('peminjaman', function ($query) use ($date, $start, $end, $exceptBookingId) {
                    $query->whereDate('tanggal_pinjam', $date)
                        ->whereIn('status', self::ACTIVE_STATUSES)
                        ->where('jam_mulai', '<', $end)
                        ->where('jam_selesai', '>', $start)
                        ->when($exceptBookingId, fn ($bookingQuery) => $bookingQuery->whereKeyNot($exceptBookingId));
                })
                ->sum('jumlah');

            if ($borrowedQuantity + $assetQuantity > $asset->jumlah_total) {
                throw ValidationException::withMessages([
                    'jumlah_alat' => "Stok {$asset->nama_alat} tidak mencukupi pada jadwal tersebut.",
                ]);
            }
        }

        return [$room, $asset];
    }
}
