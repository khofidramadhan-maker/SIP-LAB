@extends('layouts.app')

@section('title', 'Verifikasi Staff')

@section('content')
<div class="card p-4">
    <h3 class="fw-bold mb-4">Antrean Verifikasi</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Judul</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->title }}</td>
                    <td>{{ $booking->start_date }} - {{ $booking->end_date }}</td>
                    <td><span class="badge-status badge-pending">{{ $booking->status }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('staff.verify', $booking) }}" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
