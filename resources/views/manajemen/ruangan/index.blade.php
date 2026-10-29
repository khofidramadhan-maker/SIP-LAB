@extends('layouts.app')

@section('title', 'Manajemen Ruangan - SIP-LAB')
@section('header-title', 'Manajemen Ruangan Laboratorium')
@section('header-sub', 'Kelola data ruangan laboratorium yang terdaftar di sistem')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Ruangan Laboratorium</h5>
            <p class="text-muted small mb-0">Total {{ $ruangan->total() }} ruangan terdaftar</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.ruangan.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2" style="border-radius:10px;">
                <i class="bi bi-plus-lg"></i> Tambah Ruangan
            </a>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-brand-outline">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Kode & Ruangan</th>
                    <th>Lokasi</th>
                    <th>Kapasitas</th>
                    <th>Fasilitas</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangan as $r)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $r->nama_ruangan }}</div>
                            <small class="text-muted">{{ $r->kode_ruangan }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-muted small">
                                <i class="bi bi-geo-alt text-primary"></i>
                                <span>{{ $r->lokasi }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                <i class="bi bi-people me-1 text-muted"></i>{{ $r->kapasitas }} orang
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $r->fasilitas ? \Illuminate\Support\Str::limit($r->fasilitas, 40) : '-' }}</small>
                        </td>
                        <td>
                            @if(strtolower($r->status) === 'tersedia')
                                <span class="badge-status badge-success">Tersedia</span>
                            @elseif(strtolower($r->status) === 'terpakai')
                                <span class="badge-status badge-danger">Terpakai</span>
                            @else
                                <span class="badge-status badge-warning">{{ ucfirst($r->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('staff.ruangan.show', $r) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('staff.ruangan.edit', $r) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data ruangan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ruangan->hasPages())
        <div class="mt-3">
            {{ $ruangan->links() }}
        </div>
    @endif
</div>
@endsection

