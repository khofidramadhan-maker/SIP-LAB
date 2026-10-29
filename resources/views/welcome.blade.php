@extends('layouts.app')

@section('title', 'SIP-LAB - Sistem Informasi Peminjaman Laboratorium')

@section('content')
<div class="container-fluid px-lg-5 py-4">
    <!-- Hero Section -->
    <div class="hero-banner mb-5 p-5 position-relative overflow-hidden" style="border-radius: 24px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-7 py-3">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill" style="background: rgba(37, 99, 235, 0.25); border: 1px solid rgba(96, 165, 250, 0.3); color: #93c5fd; font-size: 0.82rem; font-weight: 600;">
                    <i class="bi bi-shield-check"></i>
                    <span>Sistem Terintegrasi Laboratorium Riset & Akademik</span>
                </div>
                <h1 class="display-4 fw-bold text-white mb-3" style="letter-spacing: -0.03em; line-height: 1.15;">
                    Peminjaman Laboratorium & Inventaris Alat Riset
                </h1>
                <p class="lead text-white-50 mb-4" style="max-width: 580px; font-size: 1.05rem; line-height: 1.6;">
                    Portal terpadu untuk mahasiswa, staf sarpras, dan kepala laboratorium dalam mengelola jadwal penggunaan ruang laboratorium, peminjaman peralatan ilmiah, verifikasi dokumen, serta persetujuan resmi secara real-time.
                </p>
                
                <div class="d-flex flex-wrap gap-3">
                    @auth
                        @if(Auth::user()->role === 'mahasiswa')
                            <a href="{{ route('student.dashboard') }}" class="btn btn-primary px-4 py-3 fw-bold rounded-3 shadow-sm d-inline-flex align-items-center gap-2" style="background:#2563eb; border:none;">
                                <i class="bi bi-grid-fill"></i> Buka Dashboard Mahasiswa
                            </a>
                        @elseif(Auth::user()->role === 'staff')
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-primary px-4 py-3 fw-bold rounded-3 shadow-sm d-inline-flex align-items-center gap-2" style="background:#2563eb; border:none;">
                                <i class="bi bi-speedometer2"></i> Dashboard Verifikasi Staf
                            </a>
                        @else
                            <a href="/kepala-lab/dashboard" class="btn btn-primary px-4 py-3 fw-bold rounded-3 shadow-sm d-inline-flex align-items-center gap-2" style="background:#2563eb; border:none;">
                                <i class="bi bi-clipboard2-check"></i> Dashboard Persetujuan
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn px-4 py-3 fw-bold rounded-3 shadow-sm d-inline-flex align-items-center gap-2 text-white" style="background:#2563eb; border:none;">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk ke Akun
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light px-4 py-3 fw-semibold rounded-3 d-inline-flex align-items-center gap-2">
                            <i class="bi bi-person-plus"></i> Registrasi Mahasiswa
                        </a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block text-center position-relative">
                <div class="p-4 mx-auto" style="max-width: 360px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 24px; backdrop-filter: blur(16px); box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
                    <div class="d-flex align-items-center justify-content-center text-white mb-3 mx-auto rounded-circle" style="width: 76px; height: 76px; background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);">
                        <i class="bi bi-cpu fs-1"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-1">SIP-LAB Intelligent</h5>
                    <p class="text-white-50 small mb-4">Monitoring jadwal & inventaris instrumen riset terpadu.</p>
                    
                    <div class="d-flex flex-column gap-2 text-start">
                        <div class="p-2 px-3 rounded-3 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255,255,255,0.08);">
                            <span class="text-white-50 small"><i class="bi bi-buildings me-2 text-info"></i>Lab AI & IoT</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Tersedia</span>
                        </div>
                        <div class="p-2 px-3 rounded-3 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255,255,255,0.08);">
                            <span class="text-white-50 small"><i class="bi bi-laptop me-2 text-warning"></i>Laptop Riset & GPU</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Siap Pakai</span>
                        </div>
                        <div class="p-2 px-3 rounded-3 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255,255,255,0.08);">
                            <span class="text-white-50 small"><i class="bi bi-check2-all me-2 text-success"></i>Verifikasi Staf & Kalab</span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat Counters -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-card-label">Ruang Laboratorium</div>
                        <div class="stat-card-value">{{ \App\Models\Ruangan::count() }}</div>
                    </div>
                    <div class="stat-card-icon stat-icon-blue">
                        <i class="bi bi-door-open"></i>
                    </div>
                </div>
                <div class="stat-card-footer text-success">
                    <i class="bi bi-check-circle"></i> Seluruh ruang siap pakai
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-card-label">Total Aset & Alat</div>
                        <div class="stat-card-value">{{ \App\Models\Alat::count() }}</div>
                    </div>
                    <div class="stat-card-icon stat-icon-emerald">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
                <div class="stat-card-footer text-primary">
                    <i class="bi bi-layers"></i> Terdata di inventaris
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-card-label">Peminjaman Selesai</div>
                        <div class="stat-card-value">{{ \App\Models\Peminjaman::where('status', 'Disetujui')->count() }}</div>
                    </div>
                    <div class="stat-card-icon stat-icon-purple">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                </div>
                <div class="stat-card-footer text-muted">
                    <i class="bi bi-graph-up"></i> Tingkat persetujuan tinggi
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-card-label">Total Pengguna</div>
                        <div class="stat-card-value">{{ \App\Models\User::count() }}</div>
                    </div>
                    <div class="stat-card-icon stat-icon-amber">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="stat-card-footer text-muted">
                    <i class="bi bi-person-badge"></i> Mahasiswa & Staf
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Pillars -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card-custom p-4 h-100">
                <div class="stat-card-icon stat-icon-blue mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="bi bi-calendar-range"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Penjadwalan Terstruktur</h5>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    Hindari bentrokan jadwal praktikum dan riset. Sistem memastikan ketersediaan ruangan dan inventaris alat dapat diperiksa secara transparan sebelum diajukan.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom p-4 h-100">
                <div class="stat-card-icon stat-icon-emerald mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Verifikasi 2 Tingkat</h5>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    Alur peminjaman resmi melalui verifikasi berkas oleh Staf Sarpras Laboratorium dan persetujuan final bertanda tangan digital dari Kepala Laboratorium.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom p-4 h-100">
                <div class="stat-card-icon stat-icon-amber mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Pelacakan Status & Aset</h5>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    Pantau tahapan pengajuan secara langsung mulai dari status Menunggu, Revisi, hingga Disetujui lengkap dengan catatan evaluator.
                </p>
            </div>
        </div>
    </div>

    <!-- Lab Availability Showcase Preview -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h4 class="fw-bold text-dark mb-1">Daftar Ruang Laboratorium Utama</h4>
                <p class="text-muted small mb-0">Ruang riset dan praktikum yang siap digunakan untuk kegiatan akademik.</p>
            </div>
            @auth
                <a href="{{ route('student.inventory') }}" class="btn btn-brand-primary">Lihat Seluruh Katalog <i class="bi bi-arrow-right"></i></a>
            @else
                <a href="{{ route('login') }}" class="btn btn-brand-primary">Login untuk Pinjam <i class="bi bi-arrow-right"></i></a>
            @endauth
        </div>

        <div class="row g-4">
            @forelse(\App\Models\Ruangan::take(3)->get() as $room)
                <div class="col-md-4">
                    <div class="border rounded-4 p-3 bg-light-subtle h-100 d-flex flex-column justify-content-between" style="border-color: #e2e8f0 !important;">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1 rounded-pill small">{{ $room->kode_ruangan }}</span>
                                <span class="badge-status {{ $room->status === 'tersedia' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $room->nama_ruangan }}</h6>
                            <p class="text-muted small mb-3">{{ Str::limit($room->fasilitas, 85) }}</p>
                        </div>
                        
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center small text-muted">
                            <div><i class="bi bi-geo-alt text-primary me-1"></i>{{ $room->lokasi }}</div>
                            <div><i class="bi bi-people text-secondary me-1"></i>{{ $room->kapasitas }} Kursi</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">Belum ada ruang laboratorium yang terdata.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
