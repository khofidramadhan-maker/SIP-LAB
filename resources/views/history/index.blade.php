@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="card p-4">
    <h3 class="fw-bold mb-4">Riwayat Peminjaman</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Ruang</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->title }}</td>
                    <td>{{ $booking->labRoom?->name ?? '-' }}</td>
                    <td>{{ $booking->start_date }} s/d {{ $booking->end_date }}</td>
                    <td><span class="badge-status badge-info">{{ $booking->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
