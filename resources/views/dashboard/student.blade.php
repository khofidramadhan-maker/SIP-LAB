@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SIP-LAB')
@section('header-title', 'Dashboard Mahasiswa')
@section('header-sub', 'Selamat datang di portal layanan peminjaman fasilitas laboratorium riset')

@section('content')
<!-- Hero Welcome Banner -->
<div class="hero-banner mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-white-subtle text-white-75 px-3 py-1 rounded-pill mb-2 border border-white-subtle" style="font-size: 0.78rem;">
                <i class="bi bi-mortarboard me-1 text-info"></i> Mahasiswa Aktif &bull; NIM: {{ Auth::user()->nim ?? '-' }}
            </span>
            <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.02em;">Selamat Datang, {{ Auth::user()->nama ?? Auth::user()->name }}! 👋</h2>
            <p class="text-white-50 mb-0" style="max-width: 580px; font-size: 0.92rem; line-height: 1.5;">
                Periksa ketersediaan ruangan, pinjam peralatan riset terpadu, dan pantau proses verifikasi dokumen secara langsung.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('booking.create') }}" class="btn btn-light fw-bold px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; color: #0f172a;">
                <i class="bi bi-calendar-plus-fill text-primary"></i> Ajukan Peminjaman
            </a>
        </div>
    </div>
</div>

<!-- 4 Key Stat Metrics -->
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Pengajuan Saya</div>
                    <div class="stat-card-value">{{ Auth::user()->peminjaman()->count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-blue">
                    <i class="bi bi-folder2-open"></i>
                </div>
            </div>
            <div class="stat-card-footer text-primary">
                <i class="bi bi-clock-history"></i> {{ Auth::user()->peminjaman()->where('status', 'Disetujui')->count() }} telah disetujui
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Laboratorium</div>
                    <div class="stat-card-value">{{ \App\Models\Ruangan::count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-emerald">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
            <div class="stat-card-footer text-success">
                <i class="bi bi-check-circle"></i> {{ \App\Models\Ruangan::where('status', 'tersedia')->count() }} ruang tersedia
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Inventaris Alat</div>
                    <div class="stat-card-value">{{ \App\Models\Alat::where('status', 'tersedia')->count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-amber">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-tools"></i> Siap dipinjam
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Notifikasi</div>
                    <div class="stat-card-value">{{ Auth::user()->notifikasi()->count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-purple">
                    <i class="bi bi-bell"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <a href="{{ route('student.notifications') }}" class="text-decoration-none text-muted">Lihat semua notif &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Section: 2 Column Layout -->
<div class="row g-4">
    <!-- Left Column: Lab Availability -->
    <div class="col-lg-7">
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Ketersediaan Ruang Laboratorium</h5>
                    <p class="text-muted small mb-0">Status operasional ruang laboratorium riset</p>
                </div>
                <a href="{{ route('student.inventory') }}" class="btn btn-sm btn-brand-outline">
                    Semua Katalog <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-custom-wrapper">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Kode & Ruangan</th>
                            <th>Lokasi</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $room->nama_ruangan }}</div>
                                    <small class="text-muted">{{ $room->kode_ruangan }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-muted small">
                                        <i class="bi bi-geo-alt text-primary"></i>
                                        <span>{{ $room->lokasi }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        <i class="bi bi-people me-1 text-muted"></i>{{ $room->kapasitas }} orang
                                    </span>
                                </td>
                                <td>
                                    @if(strtolower($room->status) === 'tersedia')
                                        <span class="badge-status badge-success">Tersedia</span>
                                    @else
                                        <span class="badge-status badge-warning">{{ ucfirst($room->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data laboratorium.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Featured Assets Grid -->
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Peralatan Riset Siap Pakai</h5>
                    <p class="text-muted small mb-0">Instrumen dan perangkat keras pendukung pengujian</p>
                </div>
                <a href="{{ route('student.inventory') }}" class="btn btn-sm btn-brand-outline">Lihat Aset</a>
            </div>

            <div class="row g-3">
                @forelse($assets as $asset)
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light-subtle d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px; background: #eff6ff;">
                                    <i class="bi bi-tools fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small mb-0">{{ $asset->nama_alat }}</div>
                                    <small class="text-muted">{{ $asset->kode_inventaris }} &bull; {{ $asset->kategori }}</small>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">
                                {{ $asset->jumlah_total }} unit
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3 small">Tidak ada aset tersedia saat ini.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Bookings & Timeline -->
    <div class="col-lg-5">
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Pengajuan Terakhir</h5>
                    <p class="text-muted small mb-0">Status progres verifikasi permohonan Anda</p>
                </div>
                <a href="{{ route('student.history') }}" class="btn btn-sm btn-brand-outline">Riwayat</a>
            </div>

            @forelse($bookings as $booking)
                <div class="p-3 rounded-3 border mb-3 position-relative" style="background: #ffffff; border-color: #e2e8f0 !important; transition: all 0.15s ease;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-light text-muted border px-2 py-0 rounded-pill small mb-1" style="font-size: 0.68rem;">{{ $booking->kode_peminjaman }}</span>
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $booking->keperluan }}</div>
                        </div>
                        @if($booking->status === 'Disetujui')
                            <span class="badge-status badge-success">Disetujui</span>
                        @elseif($booking->status === 'Menunggu Verifikasi')
                            <span class="badge-status badge-pending">Verifikasi Staf</span>
                        @elseif($booking->status === 'Menunggu Persetujuan Kepala Lab')
                            <span class="badge-status badge-info">Persetujuan Kalab</span>
                        @elseif($booking->status === 'Perlu Revisi')
                            <span class="badge-status badge-warning">Perlu Revisi</span>
                        @else
                            <span class="badge-status badge-danger">{{ $booking->status }}</span>
                        @endif
                    </div>

                    <div class="text-muted small mb-2">
                        <i class="bi bi-buildings me-1 text-primary"></i> 
                        {{ $booking->detailRuangan->first()?->ruangan?->nama_ruangan ?? 'Penggunaan Mandiri' }}
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                        <div>
                            <i class="bi bi-calendar-event me-1"></i> {{ date('d M Y', strtotime($booking->tanggal_pinjam)) }}
                        </div>
                        <a href="{{ route('booking.show', $booking) }}" class="text-primary text-decoration-none fw-semibold">
                            Detail &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-light"></i>
                    Belum ada pengajuan peminjaman aktif.<br>
                    <a href="{{ route('booking.create') }}" class="btn btn-sm btn-brand-primary mt-3">Ajukan Sekarang</a>
                </div>
            @endforelse
        </div>

        <!-- Guidelines / Policy Info Card -->
        <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe;">
            <div class="d-flex gap-3">
                <div class="text-primary fs-3">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Tata Tertib Laboratorium</h6>
                    <p class="text-muted small mb-2" style="line-height: 1.5;">
                        Pastikan mematuhi SOP K3 (Jas Lab, Sepatu Tertutup) dan mengunggah dokumen pendukung resmi saat mengajukan izin peminjaman ruang atau alat laboratorium.
                    </p>
                    <a href="{{ route('student.inventory') }}" class="fw-bold text-primary small text-decoration-none">
                        Pelajari SOP Penggunaan &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
