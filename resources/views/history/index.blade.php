@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - SIP-LAB')
@section('header-title', 'Riwayat Pengajuan Peminjaman')
@section('header-sub', 'Daftar seluruh riwayat peminjaman fasilitas laboratorium yang telah Anda ajukan')

@section('content')
<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card p-3">
            <div class="stat-card-label">Total Diajukan</div>
            <div class="stat-card-value" style="font-size: 1.6rem;">{{ $bookings->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3">
            <div class="stat-card-label">Disetujui</div>
            <div class="stat-card-value text-success" style="font-size: 1.6rem;">
                {{ $bookings->where('status', 'Disetujui')->count() }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3">
            <div class="stat-card-label">Proses Verifikasi</div>
            <div class="stat-card-value text-warning" style="font-size: 1.6rem;">
                {{ $bookings->whereIn('status', ['Menunggu Verifikasi', 'Menunggu Persetujuan Kepala Lab'])->count() }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3">
            <div class="stat-card-label">Revisi / Ditolak</div>
            <div class="stat-card-value text-danger" style="font-size: 1.6rem;">
                {{ $bookings->whereIn('status', ['Perlu Revisi', 'Ditolak'])->count() }}
            </div>
        </div>
    </div>
</div>

<!-- History Table Card -->
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Rekam Jejak Peminjaman</h5>
            <p class="text-muted small mb-0">Klik pada tombol detail untuk melihat rincian peminjaman dan catatan persetujuan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('booking.create') }}" class="btn btn-brand-primary">
                <i class="bi bi-calendar-plus"></i> Ajukan Peminjaman Baru
            </a>
        </div>
    </div>

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Kode & Keperluan</th>
                    <th>Ruang Laboratorium</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th>Waktu (WIB)</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $ruangan = $booking->detailRuangan->first()?->ruangan;
                    @endphp
                    <tr>
                        <td>
                            <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small mb-1">
                                {{ $booking->kode_peminjaman }}
                            </span>
                            <div class="fw-bold text-dark">{{ Str::limit($booking->keperluan, 50) }}</div>
                            <small class="text-muted">Diajukan: {{ $booking->created_at->format('d M Y') }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $ruangan?->nama_ruangan ?? 'Ruang Khusus' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $ruangan?->lokasi ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ date('d M Y', strtotime($booking->tanggal_pinjam)) }}</div>
                            <small class="text-muted">1 Hari Pemakaian</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small">
                                <i class="bi bi-clock me-1 text-muted"></i>{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}
                            </span>
                        </td>
                        <td>
                            @if($booking->status === 'Disetujui')
                                <span class="badge-status badge-success">Disetujui</span>
                            @elseif($booking->status === 'Menunggu Verifikasi')
                                <span class="badge-status badge-pending">Verifikasi Staf</span>
                            @elseif($booking->status === 'Menunggu Persetujuan Kepala Lab')
                                <span class="badge-status badge-info">Persetujuan Kalab</span>
                            @elseif($booking->status === 'Perlu Revisi')
                                <span class="badge-status badge-warning">Perlu Revisi</span>
                            @else
                                <span class="badge-status badge-danger">{{ $booking->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('booking.show', $booking) }}" class="btn btn-sm btn-brand-outline">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-light"></i>
                            Anda belum pernah mengajukan peminjaman laboratorium.<br>
                            <a href="{{ route('booking.create') }}" class="btn btn-sm btn-brand-primary mt-3">Ajukan Peminjaman Pertama</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
