@extends('layouts.app')

@section('title', 'Detail Peminjaman - SIP-LAB')
@section('header-title', 'Detail Pengajuan Peminjaman')
@section('header-sub', 'Periksa data pemohon, jadwal, fasilitas, dan dokumen pendukung pengajuan')

@section('content')
<style>
    .booking-proof-print {
        display: none;
    }

    @media print {
        @page {
            size: A4;
            margin: 14mm;
        }

        body * {
            visibility: hidden !important;
        }

        .booking-proof-print,
        .booking-proof-print * {
            visibility: visible !important;
        }

        .booking-proof-print {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            background: #fff !important;
            color: #111827 !important;
            font: 10pt/1.45 Arial, sans-serif !important;
        }

        .booking-proof-print * {
            box-sizing: border-box !important;
        }

        .booking-proof-print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #1e293b;
        }

        .booking-proof-brand {
            font-size: 19pt;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .booking-proof-title {
            margin: 2px 0 0;
            color: #475569;
            font-size: 10pt;
        }

        .booking-proof-code {
            text-align: right;
            font-size: 9pt;
            color: #475569;
        }

        .booking-proof-code strong {
            display: block;
            color: #111827;
            font-size: 11pt;
        }

        .booking-proof-status {
            display: inline-block;
            margin-top: 5px;
            padding: 3px 8px;
            border: 1px solid #94a3b8;
            border-radius: 12px;
            font-weight: 700;
        }

        .booking-proof-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px 18px;
        }

        .booking-proof-field {
            min-width: 0;
            padding: 7px 0;
            border-bottom: 1px solid #cbd5e1;
            break-inside: avoid;
        }

        .booking-proof-field-wide {
            grid-column: 1 / -1;
        }

        .booking-proof-label {
            display: block;
            margin-bottom: 3px;
            color: #64748b;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .booking-proof-value {
            overflow-wrap: anywhere;
            color: #111827;
        }

        .booking-proof-equipment {
            margin: 0;
            padding-left: 18px;
        }

        .booking-proof-equipment li {
            margin: 0 0 3px;
            break-inside: avoid;
        }

        .booking-proof-footer {
            margin-top: 18px;
            color: #64748b;
            font-size: 8pt;
            text-align: right;
        }
    }
</style>

@php
    $ruang = $booking->detailRuangan->first()?->ruangan;
    $latestApproval = $booking->persetujuan->sortByDesc('tanggal')->first();
@endphp

<section class="booking-proof-print" aria-label="Bukti peminjaman untuk dicetak">
    <header class="booking-proof-print-header">
        <div>
            <div class="booking-proof-brand">SIP-LAB</div>
            <div class="booking-proof-title">Bukti Pengajuan Peminjaman Laboratorium</div>
        </div>
        <div class="booking-proof-code">
            Kode Peminjaman
            <strong>{{ $booking->kode_peminjaman }}</strong>
            <span class="booking-proof-status">{{ $booking->status }}</span>
        </div>
    </header>

    <div class="booking-proof-grid">
        <div class="booking-proof-field">
            <span class="booking-proof-label">Pemohon</span>
            <div class="booking-proof-value">{{ $booking->user->nama ?? $booking->user->name }}</div>
        </div>
        <div class="booking-proof-field">
            <span class="booking-proof-label">NIM / NIP</span>
            <div class="booking-proof-value">{{ $booking->user->nim ?? '-' }}</div>
        </div>
        @if($booking->user->program_study)
            <div class="booking-proof-field booking-proof-field-wide">
                <span class="booking-proof-label">Program Studi</span>
                <div class="booking-proof-value">{{ $booking->user->program_study }}</div>
            </div>
        @endif
        <div class="booking-proof-field">
            <span class="booking-proof-label">Tanggal Pelaksanaan</span>
            <div class="booking-proof-value">{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->translatedFormat('d F Y') }}</div>
        </div>
        <div class="booking-proof-field">
            <span class="booking-proof-label">Waktu</span>
            <div class="booking-proof-value">{{ substr($booking->jam_mulai, 0, 5) }}–{{ substr($booking->jam_selesai, 0, 5) }} WIB</div>
        </div>
        <div class="booking-proof-field booking-proof-field-wide">
            <span class="booking-proof-label">Ruang Laboratorium</span>
            <div class="booking-proof-value">
                @if($ruang)
                    {{ $ruang->nama_ruangan }} ({{ $ruang->kode_ruangan }}) — {{ $ruang->lokasi }}
                @else
                    Tanpa peminjaman ruang
                @endif
            </div>
        </div>
        <div class="booking-proof-field booking-proof-field-wide">
            <span class="booking-proof-label">Keperluan</span>
            <div class="booking-proof-value">{{ $booking->keperluan }}</div>
        </div>
        <div class="booking-proof-field booking-proof-field-wide">
            <span class="booking-proof-label">Peralatan</span>
            @if($booking->detailAlat->isNotEmpty())
                <ul class="booking-proof-equipment">
                    @foreach($booking->detailAlat as $item)
                        <li>
                            {{ $item->alat?->nama_alat ?? 'Alat' }}
                            ({{ $item->alat?->kode_inventaris ?? '-' }}) — {{ $item->jumlah }} unit
                            @if($item->detailAlatUnits->isNotEmpty())
                                <span> | Kode barang: {{ $item->detailAlatUnits->map(fn ($row) => $row->unitAlat?->kode_unit)->filter()->join(', ') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="booking-proof-value">Tidak ada peralatan tambahan.</div>
            @endif
        </div>
        <div class="booking-proof-field">
            <span class="booking-proof-label">Dokumen Pendukung</span>
            <div class="booking-proof-value">{{ $booking->dokumen_pendukung ? 'Terlampir' : 'Tidak ada' }}</div>
        </div>
        @if($latestApproval)
            <div class="booking-proof-field">
                <span class="booking-proof-label">Keputusan Terakhir</span>
                <div class="booking-proof-value">
                    {{ ucfirst($latestApproval->keputusan) }}
                    — {{ $latestApproval->user?->nama ?? $latestApproval->user?->name ?? 'Petugas' }}
                </div>
            </div>
        @endif
    </div>

    <footer class="booking-proof-footer">
        Dicetak {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </footer>
</section>

<div class="row g-4 booking-detail-layout">
    <!-- Main Detail Info -->
    <div class="col-lg-8 booking-proof-column">
        <div class="card-custom p-4 p-md-5 mb-4 booking-proof-card">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-3 mb-4 border-bottom">
                <div>
                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small mb-2">
                        {{ $booking->kode_peminjaman }}
                    </span>
                    <h4 class="fw-bold text-dark mb-1">Bukti Peminjaman Laboratorium</h4>
                    <p class="text-muted small mb-0">
                        Pemohon: <strong class="text-dark">{{ $booking->user->nama ?? $booking->user->name }}</strong>
                        &bull; NIM: {{ $booking->user->nim ?? '-' }}
                        @if($booking->user->program_study)
                            &bull; Program Studi: {{ $booking->user->program_study }}
                        @endif
                    </p>
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
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Ruang Laboratorium</small>
                        <div class="fw-bold text-dark fs-6 mt-1">{{ $ruang?->nama_ruangan ?? 'Tanpa peminjaman ruang' }}</div>
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
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Keperluan Peminjaman</small>
                        <p class="text-dark small mt-2 mb-0" style="line-height: 1.6;">{{ $booking->keperluan }}</p>
                    </div>
                </div>

                <!-- Detail Alat -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light-subtle border" style="border-color: #e2e8f0 !important;">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Peralatan yang Dipinjam</small>
                        @if($booking->detailAlat->count() > 0)
                            <ul class="list-unstyled mb-0 mt-2">
                                @foreach($booking->detailAlat as $item)
                                    <li class="small mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-semibold text-dark">{{ $item->alat?->nama_alat }} <small class="text-muted">(Kode Alat: {{ $item->alat?->kode_alat ?? "-" }})</small></span>
                                            <span class="text-muted">{{ $item->jumlah }} unit</span>
                                        </div>
                                        @if($item->detailAlatUnits->isNotEmpty())
                                            <div class="text-muted mt-1">
                                                @foreach($item->detailAlatUnits as $row)
                                                    <div>{{ $row->unitAlat?->kode_unit ?? '-' }} — awal: {{ $row->kondisi_awal ?? '-' }}@if($row->kondisi_akhir) · akhir: {{ $row->kondisi_akhir }}@endif</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">Kondisi awal: {{ $item->kondisi_awal }}@if($item->kondisi_akhir) · akhir: {{ $item->kondisi_akhir }}@endif</div>
                                        @endif
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
                                <a href="{{ route('booking.document', $booking) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 booking-document-link">
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
                <div class="row g-3 mb-4 booking-no-print">
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
                                        @if($damage->unitAlat){{ $damage->unitAlat->kode_unit }} ({{ $damage->detailAlat?->alat?->nama_alat ?? 'Alat' }})@else{{ $damage->detailAlat?->alat?->nama_alat ?? 'Alat' }}@endif: {{ $damage->jenis_kondisi }}
                                        @if($damage->deskripsi) — {{ $damage->deskripsi }} @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Back & Print Buttons -->
            <div class="d-flex justify-content-between pt-3 border-top booking-no-print">
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
        <div class="card-custom p-4 mb-4 booking-approval-history">
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
