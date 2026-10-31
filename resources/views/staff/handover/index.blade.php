@extends('layouts.app')

@section('title', 'Antrean Serah Terima - SIP-LAB')
@section('header-title', 'Serah Terima Peminjaman')
@section('header-sub', 'Catat penyerahan ruang dan kondisi awal alat kepada mahasiswa')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Peminjaman Siap Diserahterimakan</h5>
            <p class="text-muted small mb-0">{{ $bookings->count() }} peminjaman menunggu serah terima</p>
        </div>
        <a href="{{ route('staff.verification') }}" class="btn btn-sm btn-brand-outline">
            <i class="bi bi-arrow-left"></i> Antrean Verifikasi
        </a>
    </div>

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Kode & Pemohon</th>
                    <th>Ruang dan Alat</th>
                    <th>Jadwal</th>
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
                        <td class="text-end">
                            <a href="{{ route('staff.handover.create', $booking) }}" class="btn btn-sm btn-brand-primary">
                                Catat Serah Terima
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Tidak ada peminjaman yang menunggu serah terima.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
