@extends('layouts.app')

@section('title', 'Dashboard Staf Sarpras - SIP-LAB')
@section('header-title', 'Dashboard Staf Laboratorium')
@section('header-sub', 'Verifikasi berkas persyaratan permohonan peminjaman laboratorium riset')

@section('content')
<!-- 4 Key Stat Metrics for Staff -->
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Menunggu Verifikasi</div>
                    <div class="stat-card-value text-warning">{{ $pending }}</div>
                </div>
                <div class="stat-card-icon stat-icon-amber">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-exclamation-circle"></i> Butuh tindak lanjut
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
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
            <div class="stat-card-footer text-success">
                <i class="bi bi-arrow-up-right"></i> Diteruskan / Selesai
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Ruang Lab Aktif</div>
                    <div class="stat-card-value">{{ $rooms }}</div>
                </div>
                <div class="stat-card-icon stat-icon-blue">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-door-open"></i> Siap operasional
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Jadwal Hari Ini</div>
                    <div class="stat-card-value text-primary">{{ $todayBookings }}</div>
                </div>
                <div class="stat-card-icon stat-icon-purple">
                    <i class="bi bi-calendar2-day"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-calendar-event"></i> Kegiatan aktif hari ini
            </div>
        </div>
    </div>
</div>

<!-- Antrean Verifikasi Cepat -->
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Antrean Verifikasi Tingkat I</h5>
            <p class="text-muted small mb-0">Periksa kelengkapan jadwal, kapasitas ruangan, dan dokumen pendukung mahasiswa</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('staff.handover.index') }}" class="btn btn-brand-outline">
                <i class="bi bi-box-arrow-up-right"></i> Serah Terima
            </a>
            <a href="{{ route('staff.returns.index') }}" class="btn btn-brand-outline">
                <i class="bi bi-box-arrow-in-down"></i> Pengembalian
            </a>
            <a href="{{ route('staff.verification') }}" class="btn btn-brand-primary">
                <i class="bi bi-check2-square"></i> Buka Antrean Lengkap ({{ $pending }})
            </a>
        </div>
    </div>

    @php
        $queueBookings = \App\Models\Peminjaman::with(['user', 'detailRuangan.ruangan', 'detailAlat.alat'])
            ->whereIn('status', ['Menunggu Verifikasi', 'Menunggu Pemeriksaan Staf Sarpras'])
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Pemohon</th>
                    <th>Keperluan & Ruang Lab</th>
                    <th>Jadwal Pelaksanaan</th>
                    <th>Status</th>
                    <th>Dokumen</th>
                    <th class="text-end">Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($queueBookings as $booking)
                    @php
                        $ruang = $booking->detailRuangan->first()?->ruangan;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $booking->user->nama ?? $booking->user->name }}</div>
                            <small class="text-muted">NIM: {{ $booking->user->nim ?? $booking->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-muted border px-2 py-0 rounded-pill small mb-1">{{ $booking->kode_peminjaman }}</span>
                            <div class="fw-semibold text-dark">{{ Str::limit($booking->keperluan, 45) }}</div>
                            <small class="text-muted"><i class="bi bi-buildings text-primary me-1"></i>{{ $ruang?->nama_ruangan ?? 'Ruang Khusus' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ date('d M Y', strtotime($booking->tanggal_pinjam)) }}</div>
                            <small class="text-muted">{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }} WIB</small>
                        </td>
                        <td>
                            @if(in_array($booking->status, ['Menunggu Verifikasi', 'Menunggu Pemeriksaan Staf Sarpras'], true))
                                <span class="badge-status badge-pending">Menunggu Staf</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->dokumen_pendukung)
                                <a href="{{ asset('storage/' . $booking->dokumen_pendukung) }}" target="_blank" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none px-2 py-1 rounded-pill">
                                    <i class="bi bi-file-pdf"></i> Lihat PDF
                                </a>
                            @else
                                <span class="text-muted small">Tidak ada</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('booking.show', $booking) }}" class="btn btn-sm btn-brand-outline" title="Lihat Rincian">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Form Approve -->
                                <form method="POST" action="{{ route('staff.verify', $booking) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success px-3" onclick="return confirm('Verifikasi dan teruskan pengajuan ini ke Kepala Laboratorium?')">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>

                                <!-- Button Trigger Modal Reject/Revisi -->
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $booking->id }}">
                                    <i class="bi bi-pencil-square"></i> Revisi
                                </button>
                            </div>

                            <!-- Modal Catatan Revisi -->
                            <div class="modal fade text-start" id="revisiModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                        <form method="POST" action="{{ route('staff.verify', $booking) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <div class="modal-header border-bottom">
                                                <h6 class="modal-title fw-bold">Instruksi Revisi {{ $booking->kode_peminjaman }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-3">Tuliskan instruksi atau alasan perbaikan kepada mahasiswa:</p>
                                                <textarea name="notes" class="form-control" rows="4" placeholder="Contoh: Jadwal bertabrakan dengan jadwal praktikum. Silakan ubah jam atau pilih ruangan laboratorium lain..." required></textarea>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-danger">Kirim Instruksi Revisi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-check-all fs-1 text-success d-block mb-2"></i>
                            Tidak ada pengajuan peminjaman yang menunggu verifikasi saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card-custom p-4 mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Aktivitas Operasional Terbaru</h5>
            <p class="text-muted small mb-0">Pembaruan aktivitas yang tercatat di sistem</p>
        </div>
        <a href="{{ route('staff.riwayat') }}" class="btn btn-sm btn-brand-outline">Riwayat Perubahan Data</a>
    </div>
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
