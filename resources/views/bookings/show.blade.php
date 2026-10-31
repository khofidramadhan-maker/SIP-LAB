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
                    @elseif(in_array($booking->status, ['Menunggu Verifikasi', 'Menunggu Pemeriksaan Staf Sarpras'], true))
                        <span class="badge-status badge-pending fs-6 px-3 py-2">Menunggu Pemeriksaan Staf Sarpras</span>
                    @elseif($booking->status === 'Sedang Dipinjam')
                        <span class="badge-status badge-info fs-6 px-3 py-2">Sedang Dipinjam</span>
                    @elseif($booking->status === 'Selesai')
                        <span class="badge-status badge-success fs-6 px-3 py-2">Selesai</span>
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
                                        <span class="text-muted">
                                            {{ $item->jumlah }} unit (kondisi awal: {{ $item->kondisi_awal }}
                                            @if($item->kondisi_akhir) · akhir: {{ $item->kondisi_akhir }} @endif)
                                        </span>
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

            @if($booking->serahTerima || $booking->pengembalian)
                <div class="row g-3 mb-4">
                    @if($booking->serahTerima)
                        <div class="col-12">
                            <div class="p-3 rounded-3 bg-light-subtle border">
                                <h6 class="fw-bold mb-2">Informasi Serah Terima</h6>
                                <div class="small text-muted">
                                    Diserahkan oleh {{ $booking->serahTerima->petugas->nama ?? $booking->serahTerima->petugas->name ?? '-' }}
                                    pada {{ \Carbon\Carbon::parse($booking->serahTerima->waktu_serah)->format('d/m/Y H:i') }}.
                                    Penerima: {{ $booking->user->nama ?? $booking->user->name }}.
                                </div>
                                @if($booking->serahTerima->catatan)
                                    <div class="small mt-1">{{ $booking->serahTerima->catatan }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($booking->pengembalian)
                        <div class="col-12">
                            <div class="p-3 rounded-3 bg-light-subtle border">
                                <h6 class="fw-bold mb-2">Informasi Pengembalian</h6>
                                <div class="small text-muted">
                                    Diterima oleh {{ $booking->pengembalian->petugas->nama ?? $booking->pengembalian->petugas->name ?? '-' }}
                                    pada {{ \Carbon\Carbon::parse($booking->pengembalian->waktu_kembali)->format('d/m/Y H:i') }}.
                                    Terlambat {{ $booking->pengembalian->hari_terlambat }} hari.
                                </div>
                                @foreach($booking->pengembalian->kerusakanAlat as $damage)
                                    <div class="small text-danger mt-2">
                                        {{ $damage->detailAlat?->alat?->nama_alat ?? 'Alat' }}: {{ $damage->jenis_kondisi }}
                                        @if($damage->deskripsi) — {{ $damage->deskripsi }} @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

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

    <div class="col-lg-4">
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-clipboard2-check-fill text-primary"></i> Keputusan Persetujuan
            </h6>

            @forelse($booking->persetujuan->sortBy('tanggal') as $approval)
                <div class="border-bottom pb-3 mb-3">
                    <div class="fw-bold text-dark small">
                        @if((string) $approval->tingkat === '2')
                            Riwayat keputusan Kepala Lab (historis)
                        @else
                            Keputusan Staf Sarpras
                        @endif
                    </div>
                    <span class="badge {{ $approval->keputusan === 'disetujui' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($approval->keputusan) }}
                    </span>
                    @if($approval->catatan)
                        <small class="text-muted d-block mt-1">{{ $approval->catatan }}</small>
                    @endif
                    <small class="text-muted d-block">{{ date('d M Y, H:i', strtotime($approval->tanggal)) }}</small>
                    <small class="text-muted d-block">
                        Oleh {{ $approval->user?->nama ?? $approval->user?->name ?? 'Pengguna' }}
                    </small>
                </div>
            @empty
                @if(in_array($booking->status, ['Menunggu Verifikasi', 'Menunggu Pemeriksaan Staf Sarpras'], true))
                    <p class="text-warning mb-0">Menunggu keputusan Staf Sarpras.</p>
                @else
                    <p class="text-muted mb-0">Belum ada riwayat keputusan persetujuan.</p>
                @endif
            @endforelse
        </div>
    </div>
</div>
@endsection
