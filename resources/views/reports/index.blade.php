@extends('layouts.app')

@section('title', 'Laporan Operasional - SIP-LAB')
@section('header-title', 'Laporan & Audit Operasional Laboratorium')
@section('header-sub', 'Rekapitulasi jadwal peminjaman, utilisasi ruangan, dan statistik persetujuan fasilitas')

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Ruang Lab</div>
                    <div class="stat-card-value text-primary">{{ $rooms }}</div>
                </div>
                <div class="stat-card-icon stat-icon-blue">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-geo-alt"></i> Fasilitas terdaftar
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Inventaris Aset</div>
                    <div class="stat-card-value text-emerald">{{ $assets }}</div>
                </div>
                <div class="stat-card-icon stat-icon-emerald">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-tools"></i> Peralatan riset
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Peminjaman Disetujui</div>
                    <div class="stat-card-value text-success">
                        {{ \App\Models\Peminjaman::where('status', 'Disetujui')->count() }}
                    </div>
                </div>
                <div class="stat-card-icon stat-icon-purple">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
            <div class="stat-card-footer text-success">
                <i class="bi bi-arrow-up-right"></i> Berhasil disahkan
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Total Log Peminjaman</div>
                    <div class="stat-card-value text-amber">
                        {{ \App\Models\Peminjaman::count() }}
                    </div>
                </div>
                <div class="stat-card-icon stat-icon-amber">
                    <i class="bi bi-journal-check"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-database"></i> Seluruh rekaman sistem
            </div>
        </div>
    </div>
</div>

<!-- Laporan Table Card -->
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Rekapitulasi Data Peminjaman Fasilitas</h5>
            <p class="text-muted small mb-0">Laporan operasional resmi untuk kebutuhan evaluasi jurusan & audit akademik</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <div class="input-group" style="max-width: 260px;">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="reportSearch" class="form-control border-start-0 ps-0" placeholder="Cari pemohon / kode..." style="border-radius: 0 10px 10px 0;" onkeyup="filterReport()">
            </div>

            <button type="button" class="btn btn-brand-outline" onclick="exportTableToCSV('laporan_peminjaman_siplab.csv')">
                <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV
            </button>

            <button type="button" class="btn btn-brand-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <div class="table-custom-wrapper">
        <table class="table-custom" id="reportTable">
            <thead>
                <tr>
                    <th>Kode & Mahasiswa Pemohon</th>
                    <th>Keperluan</th>
                    <th>Ruang Laboratorium</th>
                    <th>Jadwal Pelaksanaan</th>
                    <th>Waktu (WIB)</th>
                    <th>Peralatan</th>
                    <th>Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $ruang = $booking->detailRuangan->first()?->ruangan;
                    @endphp
                    <tr>
                        <td>
                            <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small mb-1">
                                {{ $booking->kode_peminjaman }}
                            </span>
                            <div class="fw-bold text-dark">{{ $booking->user->nama ?? $booking->user->name }}</div>
                            <small class="text-muted">NIM: {{ $booking->user->nim ?? $booking->user->email }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ Str::limit($booking->keperluan, 45) }}</div>
                            <small class="text-muted">Diajukan: {{ $booking->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $ruang?->nama_ruangan ?? 'Ruang Khusus' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $ruang?->lokasi ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ date('d/m/Y', strtotime($booking->tanggal_pinjam)) }}</div>
                            <small class="text-muted">1 Hari</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small">
                                {{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}
                            </span>
                        </td>
                        <td>
                            @if($booking->detailAlat->count() > 0)
                                <div class="text-dark small fw-semibold">{{ $booking->detailAlat->count() }} unit alat</div>
                                <small class="text-muted">{{ $booking->detailAlat->pluck('alat.nama_alat')->filter()->join(', ') }}</small>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 text-light d-block mb-2"></i>
                            Belum ada rekaman data peminjaman di sistem.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function filterReport() {
        const input = document.getElementById('reportSearch');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('reportTable');
        const trs = table.getElementsByTagName('tr');

        for (let i = 1; i < trs.length; i++) {
            const td = trs[i].textContent.toLowerCase();
            trs[i].style.display = td.includes(filter) ? '' : 'none';
        }
    }

    function exportTableToCSV(filename) {
        let csv = [];
        const rows = document.querySelectorAll("#reportTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) {
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/(\s\s+)/g, " ");
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }
        const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        const downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
@endsection
