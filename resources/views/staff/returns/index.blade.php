@extends('layouts.app')

@section('title', 'Antrean Pengembalian - SIP-LAB')
@section('header-title', 'Pengembalian Peminjaman')
@section('header-sub', 'Catat pengembalian dan kondisi akhir alat')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Peminjaman Belum Dikembalikan</h5>
            <p class="text-muted small mb-0">{{ $bookings->count() }} peminjaman sedang berlangsung</p>
        </div>
        <a href="{{ route('staff.handover.index') }}" class="btn btn-sm btn-brand-outline">
            <i class="bi bi-box-arrow-up-right"></i> Serah Terima
        </a>
    </div>

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Kode & Pemohon</th>
                    <th>Ruang dan Alat</th>
                    <th>Jadwal</th>
                    <th>Waktu Serah Terima</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php($room = $booking->detailRuangan->first()?->ruangan)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $booking->kode_peminjaman }}</div>
                            <small class="text-muted">{{ $booking->user->nama ?? $booking->user->name }}</small>
                        </td>
                        <td>
                            <div>{{ $room?->nama_ruangan ?? 'Ruang tidak terdata' }}</div>
                            <small class="text-muted">
                                {{ $booking->detailAlat->pluck('alat.nama_alat')->filter()->join(', ') ?: 'Tanpa alat tambahan' }}
                            </small>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->format('d/m/Y') }}<br>
                            <small class="text-muted">{{ substr($booking->jam_mulai, 0, 5) }}–{{ substr($booking->jam_selesai, 0, 5) }}</small>
                        </td>
                        <td>{{ $booking->serahTerima?->waktu_serah ? \Carbon\Carbon::parse($booking->serahTerima->waktu_serah)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('staff.returns.create', $booking) }}" class="btn btn-sm btn-brand-primary">
                                Catat Pengembalian
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Tidak ada peminjaman yang menunggu pengembalian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
