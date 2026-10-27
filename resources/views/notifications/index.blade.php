@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="card p-4">
    <h3 class="fw-bold mb-4">Notifikasi</h3>
    @forelse($notifications as $notification)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between">
                <strong>{{ $notification->title }}</strong>
                <small class="text-muted">{{ $notification->created_at->format('d M Y') }}</small>
            </div>
            <p class="mb-0 mt-2 text-muted">{{ $notification->message }}</p>
        </div>
    @empty
        <p class="text-muted mb-0">Tidak ada notifikasi.</p>
    @endforelse
</div>
@endsection
