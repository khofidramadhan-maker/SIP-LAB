@extends('layouts.app')

@section('title', 'Manajemen Alat - SIP-LAB')
@section('header-title', 'Manajemen Alat Laboratorium')
@section('header-sub', 'Kelola inventaris peralatan laboratorium')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Alat Laboratorium</h5>
            <p class="text-muted small mb-0">Total {{ $alat->total() }} alat terdaftar</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.alat.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2" style="border-radius:10px;">
                <i class="bi bi-plus-lg"></i> Tambah Alat
            </a>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-brand-outline">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Kode & Nama Alat</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alat as $a)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $a->nama_alat }}</div>
                            <small class="text-muted">{{ $a->kode_inventaris }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light border text-dark rounded-pill px-2" style="font-size:.72rem;">
                                {{ $a->kategori }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $a->jumlah_total }}</span>
                            <span class="text-muted small"> unit</span>
                        </td>
                        <td>
                            @if($a->kondisi === 'baik')
                                <span class="badge-status badge-success">Baik</span>
                            @elseif($a->kondisi === 'rusak ringan')
                                <span class="badge-status badge-warning">Rusak Ringan</span>
                            @else
                                <span class="badge-status badge-danger">Rusak Berat</span>
                            @endif
                        </td>
                        <td>
                            @if($a->status === 'tersedia')
                                <span class="badge-status badge-success">Tersedia</span>
                            @elseif($a->status === 'dipinjam')
                                <span class="badge-status badge-pending">Dipinjam</span>
                            @else
                                <span class="badge-status badge-warning">{{ ucfirst($a->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('staff.alat.show', $a) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('staff.alat.edit', $a) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data alat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($alat->hasPages())
        <div class="mt-3">{{ $alat->links() }}</div>
    @endif
</div>
@endsection

