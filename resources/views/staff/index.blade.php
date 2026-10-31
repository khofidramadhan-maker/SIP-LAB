@extends('layouts.app')

@section('title', 'Antrean Verifikasi Staf - SIP-LAB')
@section('header-title', 'Antrean Verifikasi Permohonan')
@section('header-sub', 'Pemeriksaan administratif dan kelayakan jadwal peminjaman laboratorium')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Antrean Menunggu Verifikasi Sarpras</h5>
            <p class="text-muted small mb-0">Total {{ $bookings->count() }} pengajuan memerlukan tindak lanjut Anda</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('staff.handover.index') }}" class="btn btn-sm btn-brand-primary">
                <i class="bi bi-box-arrow-up-right"></i> Serah Terima
            </a>
            <a href="{{ route('staff.returns.index') }}" class="btn btn-sm btn-brand-primary">
                <i class="bi bi-box-arrow-in-down"></i> Pengembalian
            </a>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-brand-outline">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Pemohon</th>
                    <th>Keperluan & Ruang Lab</th>
                    <th>Peralatan (detail_alat)</th>
                    <th>Waktu Pelaksanaan</th>
                    <th>Status</th>
                    <th>Dokumen</th>
                    <th class="text-end">Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
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
                            <div class="fw-semibold text-dark">{{ Str::limit($booking->keperluan, 40) }}</div>
                            <small class="text-primary"><i class="bi bi-buildings me-1"></i>{{ $ruang?->nama_ruangan ?? 'Ruang Khusus' }}</small>
                        </td>
                        <td>
                            @if($booking->detailAlat->count() > 0)
                                <div class="small text-dark fw-semibold">{{ $booking->detailAlat->count() }} jenis alat</div>
                                <small class="text-muted">{{ $booking->detailAlat->pluck('alat.nama_alat')->filter()->join(', ') }}</small>
                            @else
                                <span class="text-muted small">Ruangan Saja</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ date('d/m/Y', strtotime($booking->tanggal_pinjam)) }}</div>
                            <small class="text-muted">{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }} WIB</small>
                        </td>
                        <td>
                            @if(in_array($booking->status, ['Menunggu Pemeriksaan Staf Sarpras', 'Menunggu Verifikasi'], true))
                                <span class="badge-status badge-pending">Menunggu Staf</span>
                            @elseif($booking->status === 'Perlu Revisi')
                                <span class="badge-status badge-warning">Perlu Revisi</span>
                            @else
                                <span class="badge-status badge-success">{{ $booking->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->dokumen_pendukung)
                                <a href="{{ asset('storage/' . $booking->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="font-size:0.75rem;">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('booking.show', $booking) }}" class="btn btn-sm btn-brand-outline" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <form method="POST" action="{{ route('staff.verify', $booking) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success px-3" onclick="return confirm('Setujui pengajuan peminjaman ini?')">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#staffRevisiModal{{ $booking->id }}">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </div>

                            <div class="modal fade text-start" id="staffRevisiModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                        <form method="POST" action="{{ route('staff.verify', $booking) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <div class="modal-header border-bottom">
                                                <h6 class="modal-title fw-bold">Tolak Pengajuan {{ $booking->kode_peminjaman }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-2">Berikan alasan penolakan kepada mahasiswa:</p>
                                                <textarea name="notes" class="form-control" rows="4" placeholder="Tuliskan alasan penolakan..." required></textarea>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 text-light d-block mb-2"></i>
                            Tidak ada pengajuan dalam antrean verifikasi saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
