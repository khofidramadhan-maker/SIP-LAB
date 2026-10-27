@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Menunggu verifikasi</div><h3 class="fw-bold mt-2 mb-0">{{ $pending }}</h3></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Disetujui</div><h3 class="fw-bold mt-2 mb-0">{{ $approved }}</h3></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Ruang lab</div><h3 class="fw-bold mt-2 mb-0">{{ $rooms }}</h3></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="text-muted small">Jadwal hari ini</div><h3 class="fw-bold mt-2 mb-0">{{ $todayBookings }}</h3></div></div>
</div>

<div class="card p-4">
    <h4 class="fw-bold mb-3">Antrean Verifikasi</h4>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Ruang</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Booking::whereIn('status', ['Menunggu Verifikasi', 'Perlu Revisi'])->latest()->get() as $booking)
                    <tr>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->labRoom?->name ?? '-' }}</td>
                        <td>{{ $booking->start_date }} - {{ $booking->end_date }}</td>
                        <td><span class="badge-status badge-pending">{{ $booking->status }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('staff.verify', $booking) }}">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="btn btn-sm btn-primary" type="submit">Setujui</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
