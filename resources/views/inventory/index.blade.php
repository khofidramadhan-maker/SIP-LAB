@extends('layouts.app')

@section('title', 'Inventaris')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Daftar Laboratorium & Inventaris</h3>
        <span class="badge-status badge-info">{{ $assets->count() }} aset</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Ruang</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                <tr>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category?->name ?? '-' }}</td>
                    <td>{{ $asset->labRoom?->name ?? '-' }}</td>
                    <td>{{ $asset->condition }}</td>
                    <td><span class="badge-status {{ $asset->status === 'available' ? 'badge-success' : 'badge-warning' }}">{{ $asset->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
