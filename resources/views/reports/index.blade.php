@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Ruangan</div><h3 class="fw-bold mt-2 mb-0">{{ $rooms }}</h3></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Aset</div><h3 class="fw-bold mt-2 mb-0">{{ $assets }}</h3></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Peminjaman</div><h3 class="fw-bold mt-2 mb-0">{{ \App\Models\Booking::count() }}</h3></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Status</div><h3 class="fw-bold mt-2 mb-0">Aktif</h3></div></div>
</div>

<div class="card p-4">
    <h3 class="fw-bold mb-4">Laporan Operasional</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Judul</th>
                    <th>Ruang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->title }}</td>
                    <td>{{ $booking->labRoom?->name ?? '-' }}</td>
                    <td><span class="badge-status badge-info">{{ $booking->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
