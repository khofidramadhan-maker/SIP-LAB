@extends('layouts.app')

@section('title', 'Detail Peminjaman - SIP-LAB')
@section('header-title', 'Detail Pengajuan Peminjaman')
@section('header-sub', 'Informasi lengkap jadwal penggunaan fasilitas, relasi alat, dan riwayat persetujuan')

@section('content')
<div class="row g-4">
    <!-- Main Detail Info -->
    <div class="col-lg-8">
        <div class="card-custom p-4 p-md-5 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-3 mb-4 border-bottom">
                <div>
                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small mb-2">
                        {{ $booking->kode_peminjaman }}
                    </span>
                    <h4 class="fw-bold text-dark mb-1">{{ $booking->keperluan }}</h4>
                    <p class="text-muted small mb-0">Pemohon: <strong class="text-dark">{{ $booking->user->nama ?? $booking->user->name }}</strong> (NIM: {{ $booking->user->nim ?? '-' }})</p>
                </div>

                <div>
                    @if($booking->status === 'Disetujui')
                        <span class="badge-status badge-success fs-6 px-3 py-2">Disetujui</span>
                    @elseif($booking->status === 'Menunggu Verifikasi')
                        <span class="badge-status badge-pending fs-6 px-3 py-2">Menunggu Verifikasi Staf</span>
                    @elseif($booking->status === 'Menunggu Persetujuan Kepala Lab')
                        <span class="badge-status badge-info fs-6 px-3 py-2">Persetujuan Kepala Lab</span>
                    @elseif($booking->status === 'Perlu Revisi')
                        <span class="badge-status badge-warning fs-6 px-3 py-2">Perlu Revisi</span>
                    @else
                        <span class="badge-status badge-danger fs-6 px-3 py-2">{{ $booking->status }}</span>
                    @endif
                </div>
            </div>

            <!-- Detail Sections -->
            <div class="row g-4 mb-4">
                <!-- Ruang Lab -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light-subtle border" style="border-color: #e2e8f0 !important;">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Ruang Laboratorium (detail_ruangan)</small>
                        @php $ruang = $booking->detailRuangan->first()?->ruangan; @endphp
                        <div class="fw-bold text-dark fs-6 mt-1">{{ $ruang?->nama_ruangan ?? 'Ruang Tidak Terdata' }}</div>
                        <div class="text-muted small">
                            <i class="bi bi-geo-alt text-primary me-1"></i>{{ $ruang?->lokasi ?? '-' }} &bull; Kode: {{ $ruang?->kode_ruangan ?? '-' }}
                        </div>
                    </div>
                </div>

                <!-- Jadwal -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light-subtle border" style="border-color: #e2e8f0 !important;">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Jadwal Pelaksanaan</small>
                        <div class="fw-bold text-dark fs-6 mt-1">
                            {{ date('d F Y', strtotime($booking->tanggal_pinjam)) }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-clock text-primary me-1"></i>{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }} WIB
                        </div>
                    </div>
                </div>

                <!-- Keperluan -->
                <div class="col-12">
                    <div class="p-3 rounded-3 bg-light-subtle border" style="border-color: #e2e8f0 !important;">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Keperluan Lengkap</small>
                        <p class="text-dark small mt-2 mb-0" style="line-height: 1.6;">{{ $booking->keperluan }}</p>
                    </div>
                </div>

                <!-- Detail Alat (detail_alat) -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light-subtle border" style="border-color: #e2e8f0 !important;">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Peralatan Dipinjam (detail_alat)</small>
                        @if($booking->detailAlat->count() > 0)
                            <ul class="list-unstyled mb-0 mt-2">
                                @foreach($booking->detailAlat as $item)
                                    <li class="small d-flex justify-content-between mb-1">
                                        <span class="fw-semibold text-dark">{{ $item->alat?->nama_alat }}</span>
                                        <span class="text-muted">{{ $item->jumlah }} unit (kondisi: {{ $item->kondisi_awal }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted small mt-1">Tidak meminjam peralatan khusus.</div>
                        @endif
                    </div>
                </div>

                <!-- Dokumen Pendukung -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light-subtle border" style="border-color: #e2e8f0 !important;">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Dokumen Pendukung</small>
                        @if($booking->dokumen_pendukung)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $booking->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen Pendukung
                                </a>
                            </div>
                        @else
                            <div class="text-muted small mt-1">Tidak ada lampiran dokumen PDF.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Back & Print Buttons -->
            <div class="d-flex justify-content-between pt-3 border-top">
                @if(Auth::user()->role === 'mahasiswa')
                    <a href="{{ route('student.history') }}" class="btn btn-brand-outline">
                        <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                    </a>
                @elseif(Auth::user()->role === 'staff')
                    <a href="{{ route('staff.verification') }}" class="btn btn-brand-outline">
                        <i class="bi bi-arrow-left"></i> Kembali ke Antrean
                    </a>
                @else
                    <a href="{{ route('lab-head.dashboard') }}" class="btn btn-brand-outline">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                    </a>
                @endif

                <button type="button" class="btn btn-brand-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak Bukti Peminjaman
                </button>
            </div>
        </div>
    </div>

    <!-- Stepper / Persetujuan Timeline Sidebar -->
    <div class="col-lg-4">
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-primary"></i> Riwayat Persetujuan (persetujuan)
            </h6>

            @php
                $p1 = $booking->persetujuan->where('tingkat', '1')->first();
                $p2 = $booking->persetujuan->where('tingkat', '2')->first();
            @endphp

            <div class="timeline position-relative ps-4" style="border-left: 2px solid #e2e8f0;">
                <!-- Step 1: Diajukan -->
                <div class="mb-4 position-relative">
                    <span class="badge bg-success rounded-circle position-absolute" style="left: -25px; top: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-check text-white" style="font-size: 0.75rem;"></i>
                    </span>
                    <div class="fw-bold text-dark small">Pengajuan Terkirim</div>
                    <small class="text-muted d-block">{{ $booking->created_at->format('d M Y, H:i') }} WIB</small>
                    <small class="text-success">Data tersimpan di sistem SIP-LAB</small>
                </div>

                <!-- Step 2: Persetujuan Tingkat 1 (Staf) -->
                <div class="mb-4 position-relative">
                    @if($p1 && $p1->keputusan === 'disetujui')
                        <span class="badge bg-success rounded-circle position-absolute" style="left: -25px; top: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-check text-white" style="font-size: 0.75rem;"></i>
                        </span>
                        <div class="fw-bold text-dark small">Verifikasi Tingkat I (Staf)</div>
                        <small class="text-success d-block">Disetujui: {{ $p1->catatan ?? 'Lolos verifikasi' }}</small>
                        <small class="text-muted">{{ date('d M Y, H:i', strtotime($p1->tanggal)) }}</small>
                    @elseif($booking->status === 'Menunggu Verifikasi')
                        <span class="badge bg-warning rounded-circle position-absolute" style="left: -25px; top: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-clock text-dark" style="font-size: 0.65rem;"></i>
                        </span>
                        <div class="fw-bold text-dark small">Verifikasi Tingkat I (Staf)</div>
                        <small class="text-warning">Dalam antrean verifikasi staf</small>
                    @else
                        <span class="badge bg-secondary rounded-circle position-absolute" style="left: -25px; top: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-dot text-white"></i>
                        </span>
                        <div class="fw-bold text-muted small">Verifikasi Tingkat I (Staf)</div>
                        @if($p1)
                            <small class="text-danger d-block">Catatan: {{ $p1->catatan }}</small>
                        @endif
                    @endif
                </div>

                <!-- Step 3: Persetujuan Tingkat 2 (Kepala Lab) -->
                <div class="position-relative">
                    @if($p2 && $p2->keputusan === 'disetujui')
                        <span class="badge bg-success rounded-circle position-absolute" style="left: -25px; top: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-check text-white" style="font-size: 0.75rem;"></i>
                        </span>
                        <div class="fw-bold text-dark small">Persetujuan Tingkat II (Kepala Lab)</div>
                        <small class="text-success d-block">Disahkan resmi: {{ $p2->catatan ?? 'Izin diterbitkan' }}</small>
                        <small class="text-muted">{{ date('d M Y, H:i', strtotime($p2->tanggal)) }}</small>
                    @elseif($booking->status === 'Menunggu Persetujuan Kepala Lab')
                        <span class="badge bg-info rounded-circle position-absolute" style="left: -25px; top: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-hourglass-split text-white" style="font-size: 0.65rem;"></i>
                        </span>
                        <div class="fw-bold text-dark small">Persetujuan Tingkat II (Kepala Lab)</div>
                        <small class="text-info">Menunggu keputusan pimpinan</small>
                    @else
                        <span class="badge bg-secondary rounded-circle position-absolute" style="left: -25px; top: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-dot text-white"></i>
                        </span>
                        <div class="fw-bold text-muted small">Persetujuan Tingkat II (Kepala Lab)</div>
                        @if($p2)
                            <small class="text-danger d-block">Catatan: {{ $p2->catatan }}</small>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
