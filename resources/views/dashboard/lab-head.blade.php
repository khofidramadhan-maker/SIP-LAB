@extends('layouts.app')

@section('title', 'Dashboard Kepala Lab - SIP-LAB')
@section('header-title', 'Dashboard Kepala Laboratorium')
@section('header-sub', 'Pengawasan fasilitas laboratorium dan laporan operasional')

@section('content')
<!-- Stat Cards for Kepala Lab -->
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Keputusan Peminjaman</div>
                    <div class="stat-card-value text-primary">{{ $decided }}</div>
                </div>
                <div class="stat-card-icon stat-icon-amber">
                    <i class="bi bi-clipboard2-check"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-check2-circle"></i> Disetujui atau ditolak Staf Sarpras
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Telah Disetujui</div>
                    <div class="stat-card-value text-success">{{ $approved }}</div>
                </div>
                <div class="stat-card-icon stat-icon-emerald">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            </div>
            <div class="stat-card-footer text-success">
                <i class="bi bi-shield-check"></i> Izin peminjaman disahkan
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Ruang Laboratorium</div>
                    <div class="stat-card-value text-primary">{{ $rooms }}</div>
                </div>
                <div class="stat-card-icon stat-icon-blue">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-door-open"></i> Fasilitas terdaftar
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Aset Peralatan</div>
                    <div class="stat-card-value text-purple">{{ $assets }}</div>
                </div>
                <div class="stat-card-icon stat-icon-purple">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-tools"></i> Total alat terdata
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-lg-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-label">Total Peminjaman</div>
            <div class="stat-card-value">{{ $totalBookings }}</div>
            <div class="stat-card-footer text-muted">Seluruh transaksi</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-label">Kerusakan Tercatat</div>
            <div class="stat-card-value text-danger">{{ $incidents }}</div>
            <div class="stat-card-footer text-muted">Berdasarkan laporan pengembalian</div>
        </div>
    </div>
</div>

<!-- Laporan Operasional Quick Access Banner -->
<div class="card-custom p-4 mb-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-card-icon stat-icon-blue" style="width: 50px; height: 50px; font-size: 1.4rem;">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-1">Modul Laporan & Audit Laboratorium</h5>
                <p class="text-muted small mb-0">Total <strong>{{ $totalBookings }}</strong> transaksi peminjaman tercatat dalam sistem. Unduh atau cetak laporan untuk akreditasi & audit.</p>
            </div>
        </div>
        <a href="{{ route('lab-head.reports') }}" class="btn btn-brand-primary">
            <i class="bi bi-table"></i> Buka Laporan Lengkap & Ekspor CSV
        </a>
    </div>
</div>

<div class="card-custom p-4 mb-4">
    <h5 class="fw-bold text-dark mb-1">Aktivitas Peminjaman Terbaru</h5>
    <p class="text-muted small mb-3">Perubahan aktivitas yang tercatat di sistem</p>
    <div class="list-group list-group-flush">
        @forelse($recentActivity as $activity)
            <div class="list-group-item px-0 d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <div class="fw-semibold text-dark">{{ $activity->aktivitas }}</div>
                    <small class="text-muted">{{ $activity->user?->nama ?? $activity->user?->name ?? 'Sistem' }}</small>
                </div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($activity->waktu)->format('d/m/Y H:i') }}</small>
            </div>
        @empty
            <p class="text-muted small mb-0">Belum ada aktivitas yang tercatat.</p>
        @endforelse
    </div>
</div>

@endsection
