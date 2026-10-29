@extends('layouts.app')

@section('title', 'Persetujuan Kepala Lab - SIP-LAB')
@section('header-title', 'Persetujuan Resmi Kepala Laboratorium')
@section('header-sub', 'Evaluasi akhir dan pengesahan surat izin penggunaan laboratorium riset')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Pengajuan Lolos Verifikasi Staf Sarpras</h5>
            <p class="text-muted small mb-0">Total {{ $bookings->count() }} pengajuan siap disahkan</p>
        </div>
        <div>
            <a href="{{ route('lab-head.dashboard') }}" class="btn btn-sm btn-brand-outline">
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
                    <th>Jadwal & Waktu</th>
                    <th>Verifikasi Staf</th>
                    <th>Dokumen</th>
                    <th class="text-end">Aksi Putusan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $ruang = $booking->detailRuangan->first()?->ruangan;
                        $persetujuanStaf = $booking->persetujuan->where('tingkat', '1')->first();
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $booking->user->nama ?? $booking->user->name }}</div>
                            <small class="text-muted">NIM: {{ $booking->user->nim ?? $booking->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-muted border px-2 py-0 rounded-pill small mb-1">{{ $booking->kode_peminjaman }}</span>
                            <div class="fw-semibold text-dark">{{ Str::limit($booking->keperluan, 45) }}</div>
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
                            <div class="fw-semibold text-dark">{{ date('d M Y', strtotime($booking->tanggal_pinjam)) }}</div>
                            <small class="text-muted">{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }} WIB</small>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                <i class="bi bi-check2-circle me-1"></i> Lolos Verifikasi
                            </span>
                            @if($persetujuanStaf?->user)
                                <div class="text-muted small mt-1" style="font-size: 0.72rem;">Oleh: {{ $persetujuanStaf->user->nama ?? $persetujuanStaf->user->name }}</div>
                            @endif
                        </td>
                        <td>
                            @if($booking->dokumen_pendukung)
                                <a href="{{ asset('storage/' . $booking->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="font-size:0.75rem;">
                                    <i class="bi bi-file-pdf"></i> PDF
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('booking.show', $booking) }}" class="btn btn-sm btn-brand-outline" title="Lihat Rincian">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <form method="POST" action="{{ route('lab-head.decide', $booking) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success px-3" onclick="return confirm('Sahkan izin peminjaman laboratorium ini?')">
                                        <i class="bi bi-check-circle-fill"></i> Setujui
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#tolakModalKalab{{ $booking->id }}">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>
                            </div>

                            <!-- Modal Penolakan -->
                            <div class="modal fade text-start" id="tolakModalKalab{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                        <form method="POST" action="{{ route('lab-head.decide', $booking) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <div class="modal-header border-bottom">
                                                <h6 class="modal-title fw-bold">Alasan Penolakan Permohonan {{ $booking->kode_peminjaman }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-2">Tuliskan alasan penolakan izin resmi:</p>
                                                <textarea name="notes" class="form-control" rows="4" placeholder="Penjelasan penolakan izin..." required></textarea>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
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
                            <i class="bi bi-clipboard2-check fs-1 text-light d-block mb-2"></i>
                            Tidak ada pengajuan yang memerlukan persetujuan saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
