@extends('layouts.app')

@section('title', 'Manajemen Pengguna - SIP-LAB')
@section('header-title', 'Manajemen Pengguna')
@section('header-sub', 'Kelola akun pengguna sistem peminjaman laboratorium')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Pengguna Sistem</h5>
            <p class="text-muted small mb-0">Total {{ $users->total() }} akun terdaftar</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.pengguna.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2" style="border-radius:10px;">
                <i class="bi bi-person-plus"></i> Tambah Pengguna
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
                    <th>Nama & Username</th>
                    <th>Email</th>
                    <th>NIM/NIP</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $user->nama ?? $user->name }}</div>
                            <small class="text-muted">@{{ $user->username }}</small>
                        </td>
                        <td><small class="text-muted">{{ $user->email }}</small></td>
                        <td><small class="text-muted">{{ $user->nim ?? '-' }}</small></td>
                        <td>
                            @php $roleLabels = ['mahasiswa'=>'Mahasiswa','staff'=>'Staf Sarpras','kepala_lab'=>'Kepala Lab']; @endphp
                            <span class="badge bg-light border text-dark rounded-pill px-2" style="font-size:.72rem;">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if(($user->status_aktif ?? 'aktif') === 'aktif')
                                <span class="badge-status badge-success">Aktif</span>
                            @else
                                <span class="badge-status badge-warning">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('staff.pengguna.show', $user) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(!in_array($user->role, ['kepala_lab']))
                                    <a href="{{ route('staff.pengguna.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-3">{{ $users->links() }}</div>
    @endif
</div>
@endsection

