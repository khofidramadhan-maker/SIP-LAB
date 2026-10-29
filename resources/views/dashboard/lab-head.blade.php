@extends('layouts.app')

@section('title', 'Dashboard Kepala Lab - SIP-LAB')
@section('header-title', 'Dashboard Kepala Laboratorium')
@section('header-sub', 'Persetujuan resmi tingkat akhir, pengawasan fasilitas, dan audit laporan operasional')

@section('content')
<!-- Stat Cards for Kepala Lab -->
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Menunggu Persetujuan</div>
                    <div class="stat-card-value text-danger">{{ $pending }}</div>
                </div>
                <div class="stat-card-icon stat-icon-amber">
                    <i class="bi bi-clipboard2-check"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-hourglass-split"></i> Butuh keputusan pimpinan
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


@endsection
