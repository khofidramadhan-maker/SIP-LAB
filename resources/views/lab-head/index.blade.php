@extends('layouts.app')

@section('title', 'Persetujuan Kepala Lab')

@section('content')
<div class="card p-4">
    <h3 class="fw-bold mb-4">Persetujuan Kepala Lab</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Judul</th>
                    <th>Tujuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->title }}</td>
                    <td>{{ Str::limit($booking->purpose, 60) }}</td>
                    <td>
                        <form method="POST" action="{{ route('lab-head.decide', $booking) }}" class="d-flex gap-2">
                            @csrf
                            <button type="submit" name="status" value="approved" class="btn btn-sm btn-success">Setujui</button>
                            <button type="submit" name="status" value="rejected" class="btn btn-sm btn-outline-danger">Tolak</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
