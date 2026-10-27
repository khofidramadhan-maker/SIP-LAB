@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="hero mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <p class="mb-2 text-white-50">Selamat datang,</p>
            <h2 class="fw-bold mb-0">{{ Auth::user()->name }}</h2>
        </div>
        <a href="{{ route('booking.create') }}" class="btn btn-light text-primary fw-semibold">Ajukan Peminjaman</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Pengajuan aktif</div>
            <h3 class="fw-bold mt-2 mb-0">{{ Auth::user()->bookings()->count() }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Laboratorium</div>
            <h3 class="fw-bold mt-2 mb-0">{{ \App\Models\LabRoom::count() }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Inventaris siap</div>
            <h3 class="fw-bold mt-2 mb-0">{{ \App\Models\Asset::where('status', 'available')->count() }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Notifikasi</div>
            <h3 class="fw-bold mt-2 mb-0">{{ Auth::user()->notifications()->count() }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Ketersediaan Laboratorium</h4>
                <a href="{{ route('student.inventory') }}" class="text-primary text-decoration-none">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ruang</th>
                            <th>Lokasi</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                        <tr>
                            <td>{{ $room->name }}</td>
                            <td>{{ $room->location }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td><span class="badge-status {{ $room->status === 'available' ? 'badge-success' : 'badge-warning' }}">{{ $room->status }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4">
            <h4 class="fw-bold mb-3">Riwayat Peminjaman</h4>
            @forelse($bookings as $booking)
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $booking->title }}</strong>
                        <span class="badge-status badge-info">{{ $booking->status }}</span>
                    </div>
                    <small class="text-muted d-block mt-2">{{ $booking->start_date }} - {{ $booking->end_date }}</small>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada peminjaman.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
