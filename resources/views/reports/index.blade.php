
@extends('layouts.app')

@section('title', 'Laporan Operasional - SIP-LAB')
@section('header-title', 'Laporan Operasional')
@section('header-sub', 'Pantau data peminjaman, fasilitas, dan aktivitas laboratorium.')

@section('content')

<style>
    /* ===== REPORT PAGE ===== */
    .report-page {
        color: #263445;
    }

    /* ===== STATISTIC CARDS ===== */
    .report-page .stat-card {
        height: 100%;
        padding: 18px 20px;
        background: #fff;
        border: 1px solid #e9edf2;
        border-radius: 14px;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .report-page .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(25, 42, 70, .06);
    }

    .report-page .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .report-page .stat-card-label {
        color: #738092;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .report-page .stat-card-value {
        font-size: 26px;
        line-height: 1.1;
        font-weight: 700;
        color: #243247;
    }

    .report-page .stat-card-icon {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 20px;
        flex-shrink: 0;
    }

    .report-page .stat-icon-blue {
        color: #3478d4;
        background: #edf4ff;
    }

    .report-page .stat-icon-emerald {
        color: #15966b;
        background: #eaf8f2;
    }

    .report-page .stat-icon-purple {
        color: #8060c9;
        background: #f2edfc;
    }

    .report-page .stat-icon-amber {
        color: #c58a25;
        background: #fff6e7;
    }

    .report-page .stat-card-footer {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 10px;
        padding-top: 9px;
        border-top: 1px solid #f0f2f5;
        font-size: 12px;
    }

    /* ===== REPORT CONTENT ===== */
    .report-page .report-card {
        background: #fff;
        border: 1px solid #e9edf2;
        border-radius: 14px;
        padding: 22px 24px;
    }

    .report-page .report-heading {
        font-size: 17px;
        font-weight: 700;
        color: #263445;
        margin-bottom: 5px;
    }

    .report-page .report-description {
        color: #8490a0;
        font-size: 13px;
        margin-bottom: 0;
    }

    .report-page .report-toolbar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
        flex-wrap: wrap;
    }

    .report-page .search-wrapper {
        position: relative;
        width: 245px;
    }

    .report-page .search-wrapper i {
        position: absolute;
        top: 50%;
        left: 13px;
        transform: translateY(-50%);
        color: #8994a3;
        z-index: 2;
    }

    .report-page .search-wrapper input {
        height: 40px;
        padding-left: 36px;
        border: 1px solid #e1e6ed;
        border-radius: 9px;
        font-size: 13px;
        box-shadow: none;
    }

    .report-page .search-wrapper input:focus,
    .report-page .form-control:focus {
        border-color: #8ba9d5;
        box-shadow: 0 0 0 3px rgba(52, 120, 212, .08);
    }

    .report-page .btn {
        min-height: 40px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    /* ===== FILTER ===== */
    .report-page .filter-section {
        background: #f8fafc;
        border: 1px solid #edf0f4;
        border-radius: 11px;
        padding: 15px 17px;
        margin: 20px 0;
    }

    .report-page .filter-section .form-label {
        color: #526174;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .report-page .filter-section .form-control {
        height: 40px;
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        font-size: 13px;
        box-shadow: none;
    }

    /* ===== TABLE ===== */
    .report-page .table-custom-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #edf0f4;
        border-radius: 10px;
    }

    .report-page .table-custom {
        width: 100%;
        min-width: 1050px;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .report-page .table-custom thead th {
        padding: 13px 15px;
        background: #f8fafc;
        color: #687587;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .35px;
        white-space: nowrap;
        border-bottom: 1px solid #e9edf2;
    }

    .report-page .table-custom tbody td {
        padding: 14px 15px;
        color: #465467;
        font-size: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
    }

    .report-page .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    .report-page .table-custom tbody tr:hover {
        background: #fafbfd;
    }

    .report-page .booking-code {
        display: inline-block;
        padding: 5px 9px;
        margin-bottom: 7px;
        color: #526b8d;
        background: #f2f6fb;
        border: 1px solid #e5ebf3;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .report-page .primary-info {
        color: #2d3b4e;
        font-weight: 600;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .report-page .secondary-info {
        color: #8994a3;
        font-size: 11px;
        line-height: 1.5;
    }

    .report-page .time-badge {
        display: inline-block;
        padding: 6px 9px;
        color: #53647a;
        background: #f5f7fa;
        border: 1px solid #e8ecf1;
        border-radius: 6px;
        font-size: 11px;
        white-space: nowrap;
    }

    /* ===== STATUS ===== */
    .report-page .badge-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .report-page .badge-success {
        color: #16845c;
        background: #e9f7f0;
    }

    .report-page .badge-info {
        color: #2876bd;
        background: #eaf3fc;
    }

    .report-page .badge-pending {
        color: #a8751e;
        background: #fff5df;
    }

    .report-page .badge-warning {
        color: #b66c22;
        background: #fff1e5;
    }

    .report-page .badge-danger {
        color: #c44e55;
        background: #fceced;
    }

    /* ===== EMPTY STATE ===== */
    .report-page .empty-state {
        padding: 45px 20px !important;
        text-align: center;
    }

    .report-page .empty-state i {
        display: block;
        font-size: 36px;
        color: #cbd3dd;
        margin-bottom: 10px;
    }

    .report-page .empty-state-title {
        color: #536174;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .report-page .empty-state-description {
        color: #98a2af;
        font-size: 12px;
        margin-bottom: 0;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 767px) {
        .report-page .report-card {
            padding: 17px;
        }

        .report-page .stat-card {
            padding: 15px;
        }

        .report-page .stat-card-value {
            font-size: 23px;
        }

        .report-page .stat-card-icon {
            width: 38px;
            height: 38px;
            font-size: 17px;
        }

        .report-page .stat-card-footer {
            font-size: 11px;
            margin-top: 9px;
            padding-top: 8px;
        }

        .report-page .report-toolbar {
            justify-content: flex-start;
            width: 100%;
        }

        .report-page .search-wrapper {
            width: 100%;
        }

        .report-page .report-toolbar .btn {
            flex: 1;
        }

        .report-page .filter-actions {
            width: 100%;
        }

        .report-page .filter-actions .btn {
            flex: 1;
        }
    }

    /* ===== PRINT ===== */
    @media print {
        @page {
            size: landscape;
            margin: 12mm;
        }

        body {
            background: #fff !important;
        }

        .report-page .no-print,
        .sidebar,
        .navbar,
        header,
        nav {
            display: none !important;
        }

        .report-page .report-card,
        .report-page .stat-card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            break-inside: avoid;
        }

        .report-page .table-custom-wrapper {
            overflow: visible !important;
            border: none !important;
        }

        .report-page .table-custom {
            min-width: 100% !important;
        }

        .report-page .table-custom thead th,
        .report-page .table-custom tbody td {
            padding: 8px !important;
            font-size: 9px !important;
            color: #222 !important;
        }

        .report-page .table-custom thead th {
            background: #eee !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .report-page .badge-status {
            border: 1px solid #ccc;
            color: #222 !important;
            background: #fff !important;
        }

        .report-page .stat-card-value {
            color: #222 !important;
        }
    }
</style>

<div class="report-page">

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
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
                    <i class="bi bi-geo-alt"></i>
                    Fasilitas terdaftar
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
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
                    <i class="bi bi-tools"></i>
                    Peralatan laboratorium
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-card-label">Peminjaman Disetujui</div>
                        <div class="stat-card-value text-success">{{ $approvedCount }}</div>
                    </div>
                    <div class="stat-card-icon stat-icon-purple">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div class="stat-card-footer text-muted">
                    <i class="bi bi-check-circle"></i>
                    Berhasil disetujui
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div>
                        <div class="stat-card-label">Total Peminjaman</div>
                        <div class="stat-card-value text-amber">{{ $totalBookings }}</div>
                    </div>
                    <div class="stat-card-icon stat-icon-amber">
                        <i class="bi bi-journal-check"></i>
                    </div>
                </div>
                <div class="stat-card-footer text-muted">
                    <i class="bi bi-database"></i>
                    Seluruh data peminjaman
                </div>
            </div>
        </div>

    </div>

    {{-- REPORT CONTENT --}}
    <div class="report-card">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">

            <div>
                <h5 class="report-heading">Rekapitulasi Peminjaman</h5>
                <p class="report-description">
                    Ringkasan data peminjaman ruangan dan peralatan laboratorium.
                </p>
            </div>

            <div class="report-toolbar no-print">

                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input
                        type="text"
                        id="reportSearch"
                        class="form-control"
                        placeholder="Cari nama, NIM, atau kode..."
                        oninput="filterReport()"
                    >
                </div>

                <button
                    type="button"
                    class="btn btn-brand-outline"
                    onclick="exportTableToCSV('laporan_peminjaman_siplab.csv')"
                >
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    Ekspor CSV
                </button>

                <button
                    type="button"
                    class="btn btn-brand-primary"
                    onclick="window.print()"
                >
                    <i class="bi bi-printer"></i>
                    Cetak
                </button>

            </div>
        </div>

        {{-- FILTER DATE --}}
        <form
            method="GET"
            action="{{ route('lab-head.reports') }}"
            class="filter-section no-print"
        >
            <div class="row g-3 align-items-end">

                <div class="col-12 col-md-4">
                    <label for="date_from" class="form-label fw-semibold">
                        Tanggal Mulai
                    </label>
                    <input
                        id="date_from"
                        name="date_from"
                        type="date"
                        class="form-control"
                        value="{{ $filters['date_from'] ?? '' }}"
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="date_to" class="form-label fw-semibold">
                        Tanggal Akhir
                    </label>
                    <input
                        id="date_to"
                        name="date_to"
                        type="date"
                        class="form-control"
                        value="{{ $filters['date_to'] ?? '' }}"
                    >
                </div>

                <div class="col-12 col-md-4">
                    <div class="d-flex gap-2 filter-actions">
                        <button type="submit" class="btn btn-brand-primary">
                            <i class="bi bi-funnel"></i>
                            Terapkan
                        </button>

                        <a
                            href="{{ route('lab-head.reports') }}"
                            class="btn btn-brand-outline"
                        >
                            Reset
                        </a>
                    </div>
                </div>

            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-custom-wrapper">
            <table class="table-custom" id="reportTable">

                <thead>
                    <tr>
                        <th>Kode & Pemohon</th>
                        <th>Keperluan</th>
                        <th>Ruang Laboratorium</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Peralatan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($bookings as $booking)

                        @php
                            $ruang = $booking->detailRuangan->first()?->ruangan;
                            $user = $booking->user;
                        @endphp

                        <tr>
                            <td>
                                <span class="booking-code">
                                    {{ $booking->kode_peminjaman }}
                                </span>

                                <div class="primary-info">
                                    {{ $user->nama ?? ($user->name ?? '-') }}
                                </div>

                                <div class="secondary-info">
                                    NIM: {{ $user->nim ?? ($user->email ?? '-') }}
                                </div>
                            </td>

                            <td>
                                <div class="primary-info">
                                    {{ Str::limit($booking->keperluan, 45) }}
                                </div>
                                <div class="secondary-info">
                                    Diajukan:
                                    {{ $booking->created_at?->format('d/m/Y') ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="primary-info">
                                    {{ $ruang?->nama_ruangan ?? 'Ruang Khusus' }}
                                </div>
                                <div class="secondary-info">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $ruang?->lokasi ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="primary-info">
                                    {{ $booking->tanggal_pinjam
                                        ? \Carbon\Carbon::parse($booking->tanggal_pinjam)->format('d/m/Y')
                                        : '-' }}
                                </div>
                            </td>

                            <td>
                                <span class="time-badge">
                                    {{ $booking->jam_mulai ? substr($booking->jam_mulai, 0, 5) : '-' }}
                                    -
                                    {{ $booking->jam_selesai ? substr($booking->jam_selesai, 0, 5) : '-' }}
                                </span>
                            </td>

                            <td>
                                @if($booking->detailAlat->count() > 0)

                                    <div class="primary-info">
                                        {{ $booking->detailAlat->count() }} jenis alat
                                    </div>

                                    <div class="secondary-info">
                                        {{ $booking->detailAlat->pluck('alat.nama_alat')->filter()->join(', ') }}
                                    </div>

                                @else
                                    <span class="secondary-info">Tidak ada alat</span>
                                @endif
                            </td>

                            <td>
                                @if($booking->status === 'Disetujui')

                                    <span class="badge-status badge-success">
                                        Disetujui
                                    </span>

                                @elseif($booking->status === 'Sedang Dipinjam')

                                    <span class="badge-status badge-info">
                                        Sedang Dipinjam
                                    </span>

                                @elseif($booking->status === 'Selesai')

                                    <span class="badge-status badge-success">
                                        Selesai
                                    </span>

                                @elseif(in_array($booking->status, [
                                    'Menunggu Verifikasi',
                                    'Menunggu Pemeriksaan Staf Sarpras'
                                ], true))

                                    <span class="badge-status badge-pending">
                                        Menunggu Verifikasi
                                    </span>

                                @elseif($booking->status === 'Perlu Revisi')

                                    <span class="badge-status badge-warning">
                                        Perlu Revisi
                                    </span>

                                @else

                                    <span class="badge-status badge-danger">
                                        {{ $booking->status }}
                                    </span>

                                @endif
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="bi bi-clipboard-x"></i>

                                <div class="empty-state-title">
                                    Belum ada data peminjaman
                                </div>

                                <p class="empty-state-description">
                                    Data peminjaman akan ditampilkan di sini setelah tersedia.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                    <tr id="searchEmptyState" style="display: none;">
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-search"></i>

                            <div class="empty-state-title">
                                Data tidak ditemukan
                            </div>

                            <p class="empty-state-description">
                                Coba gunakan kata kunci pencarian yang berbeda.
                            </p>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
            <span class="secondary-info">
                Menampilkan data peminjaman sesuai periode yang dipilih.
            </span>
            <span class="secondary-info">
                SIP-LAB &middot; Laporan Operasional
            </span>
        </div>

    </div>

</div>

<script>
    // ===== SEARCH TABLE =====
    function filterReport() {
        const input = document.getElementById('reportSearch');
        const filter = input.value.toLowerCase().trim();
        const table = document.getElementById('reportTable');
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr:not(#searchEmptyState)');
        const emptyState = document.getElementById('searchEmptyState');

        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const match = text.includes(filter);

            row.style.display = match ? '' : 'none';

            if (match) {
                visibleCount++;
            }
        });

        emptyState.style.display =
            (filter !== '' && visibleCount === 0) ? '' : 'none';
    }

    // ===== EXPORT CSV =====
    function exportTableToCSV(filename) {
        const table = document.getElementById('reportTable');
        const rows = table.querySelectorAll(
            'thead tr, tbody tr:not(#searchEmptyState)'
        );

        const csv = [];

        rows.forEach(row => {
            if (row.querySelector('.empty-state')) {
                return;
            }

            const cols = row.querySelectorAll('th, td');
            const data = [];

            cols.forEach(col => {
                let value = col.innerText
                    .replace(/(\r\n|\n|\r)/gm, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();

                value = value.replace(/"/g, '""');
                data.push('"' + value + '"');
            });

            csv.push(data.join(','));
        });

        if (csv.length <= 1) {
            alert('Tidak ada data yang dapat diekspor.');
            return;
        }

        const csvContent = '\uFEFF' + csv.join('\r\n');
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });

        const url = URL.createObjectURL(blob);
        const downloadLink = document.createElement('a');

        downloadLink.href = url;
        downloadLink.download = filename;

        document.body.appendChild(downloadLink);
        downloadLink.click();

        document.body.removeChild(downloadLink);
        URL.revokeObjectURL(url);
    }
</script>

@endsection
