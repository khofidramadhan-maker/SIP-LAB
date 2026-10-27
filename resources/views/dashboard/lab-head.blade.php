@extends('layouts.app')

@section('title', 'Dashboard Kepala Lab')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="text-muted small">Menunggu persetujuan</div><h3 class="fw-bold mt-2 mb-0">{{ $pending }}</h3></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="text-muted small">Disetujui</div><h3 class="fw-bold mt-2 mb-0">{{ $approved }}</h3></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="text-muted small">Insiden</div><h3 class="fw-bold mt-2 mb-0">{{ $incidents }}</h3></div></div>
</div>

<div class="card p-4">
    <h4 class="fw-bold mb-3">Persetujuan Tingkat II</h4>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Ruang</th>
                    <th>Tujuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Booking::where('status', 'Menunggu Persetujuan Kepala Lab')->latest()->get() as $booking)
                    <tr>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->labRoom?->name ?? '-' }}</td>
                        <td>{{ Str::limit($booking->purpose, 40) }}</td>
                        <td>
                            <form method="POST" action="{{ route('lab-head.decide', $booking) }}" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="btn btn-sm btn-success" type="submit">Setujui</button>
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Tolak</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
