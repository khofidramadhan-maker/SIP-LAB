
@extends('layouts.app')

@section('title', 'Katalog Ruang & Inventaris - SIP-LAB')
@section('header-title', 'Ruang Laboratorium & Inventaris')
@section('header-sub', 'Cari dan periksa ketersediaan fasilitas laboratorium beserta peralatan riset')

@section('content')

<div class="inventory-page">

    {{-- Toolbar --}}
    <div class="inventory-toolbar">
        <ul class="nav nav-pills inventory-tabs"
            id="inventoryTabs"
            role="tablist">

            <li class="nav-item" role="presentation">
                <button
                    class="nav-link active"
                    id="rooms-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#rooms-pane"
                    type="button"
                    role="tab"
                    aria-controls="rooms-pane"
                    aria-selected="true">

                    <i class="bi bi-buildings"></i>
                    <span>Ruang Laboratorium</span>
                    <span class="inventory-count">{{ $rooms->count() }}</span>
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="assets-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#assets-pane"
                    type="button"
                    role="tab"
                    aria-controls="assets-pane"
                    aria-selected="false">

                    <i class="bi bi-box-seam"></i>
                    <span>Inventaris Peralatan</span>
                    <span class="inventory-count">{{ $assets->count() }}</span>
                </button>
            </li>

        </ul>

        @if(Auth::user()->role === 'mahasiswa')
            <a href="{{ route('booking.create') }}"
               class="btn btn-brand-primary inventory-action">
                <i class="bi bi-calendar-plus"></i>
                <span>Ajukan Peminjaman</span>
            </a>
        @endif
    </div>

    {{-- Tab Content --}}
    <div class="tab-content" id="inventoryTabsContent">

        {{-- Tab Ruang Laboratorium --}}
        <div
            class="tab-pane fade show active"
            id="rooms-pane"
            role="tabpanel"
            aria-labelledby="rooms-tab"
            tabindex="0">

            @forelse($rooms as $room)
                @if($loop->first)
                    <div class="row g-4">
                @endif

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card-custom room-card">

                        {{-- Informasi Utama --}}
                        <div class="room-card-content">

                            <div class="room-card-top">
                                <span class="room-code">
                                    {{ $room->kode_ruangan }}
                                </span>

                                @if(strtolower($room->status) === 'tersedia')
                                    <span class="badge-status badge-success">
                                        <span class="status-dot"></span>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="badge-status badge-warning">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                @endif
                            </div>

                            <h5 class="room-name">
                                {{ $room->nama_ruangan }}
                            </h5>

                            <p class="room-description">
                                {{ $room->fasilitas ?? 'Laboratorium riset terpadu untuk pengujian dan praktikum akademik.' }}
                            </p>

                            {{-- Detail Ruangan --}}
                            <div class="room-details">

                                <div class="room-detail-item">
                                    <div class="room-detail-label">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>Lokasi</span>
                                    </div>

                                    <span class="room-detail-value">
                                        {{ $room->lokasi }}
                                    </span>
                                </div>

                                <div class="room-detail-item">
                                    <div class="room-detail-label">
                                        <i class="bi bi-people"></i>
                                        <span>Kapasitas</span>
                                    </div>

                                    <span class="room-detail-value">
                                        {{ $room->kapasitas }} mahasiswa
                                    </span>
                                </div>

                            </div>
                        </div>

                        {{-- Aksi Ruangan --}}
                        <div class="room-card-footer">
                            @if(Auth::user()->role === 'mahasiswa')
                                <a
                                    href="{{ route('booking.create') }}?ruangan_id={{ $room->id }}"
                                    class="btn btn-room-action">

                                    <i class="bi bi-calendar2-check"></i>
                                    <span>Pilih Ruangan Ini</span>
                                </a>
                            @else
                                <span class="room-admin-status">
                                    Status: {{ ucfirst($room->status) }}
                                </span>
                            @endif
                        </div>

                    </div>
                </div>

                @if($loop->last)
                    </div>
                @endif

            @empty
                <div class="inventory-empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-buildings"></i>
                    </div>

                    <h5>Belum Ada Ruang Laboratorium</h5>

                    <p>
                        Data ruang laboratorium belum tersedia di sistem.
                        Silakan periksa kembali nanti.
                    </p>
                </div>
            @endforelse

        </div>

        {{-- Tab Inventaris Peralatan --}}
        <div
            class="tab-pane fade"
            id="assets-pane"
            role="tabpanel"
            aria-labelledby="assets-tab"
            tabindex="0">

            <div class="card-custom inventory-table-card">

                {{-- Header Tabel --}}
                <div class="inventory-table-header">
                    <div>
                        <h5 class="inventory-section-title">
                            Daftar Peralatan & Aset Ilmiah
                        </h5>

                        <p class="inventory-section-description">
                            Instrumen pendukung praktikum dan pengujian laboratorium
                        </p>
                    </div>

                    <div class="asset-search">
                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="assetSearchInput"
                            placeholder="Cari nama atau kode alat..."
                            aria-label="Cari nama atau kode inventaris"
                            autocomplete="off"
                            oninput="filterAssets()">
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="table-custom-wrapper inventory-table-wrapper">
                    <table class="table-custom inventory-table" id="assetTable">

                        <thead>
                            <tr>
                                <th scope="col">Kode Inventaris</th>
                                <th scope="col">Nama Alat</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Kondisi</th>
                                <th scope="col">Jumlah Total</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($assets as $asset)
                                <tr>
                                    <td>
                                        <span class="asset-code">
                                            {{ $asset->kode_inventaris }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="asset-name">
                                            {{ $asset->nama_alat }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="asset-category">
                                            {{ $asset->kategori }}
                                        </span>
                                    </td>

                                    <td>
                                        @if(strtolower($asset->kondisi) === 'baik')
                                            <span class="asset-condition condition-good">
                                                <span class="status-dot"></span>
                                                Baik
                                            </span>
                                        @else
                                            <span class="asset-condition condition-warning">
                                                {{ ucfirst($asset->kondisi) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="asset-quantity">
                                            {{ $asset->jumlah_total }}
                                        </span>
                                        <span class="asset-unit">unit</span>
                                    </td>

                                    <td>
                                        @if(strtolower($asset->status) === 'tersedia')
                                            <span class="badge-status badge-success">
                                                <span class="status-dot"></span>
                                                Siap Pakai
                                            </span>
                                        @else
                                            <span class="badge-status badge-warning">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr class="asset-empty-row">
                                    <td colspan="6">
                                        <div class="asset-empty-content">
                                            <i class="bi bi-box-seam"></i>
                                            <span>Belum ada inventaris peralatan terdata.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="inventory-table-footer">
                    <span>
                        Total {{ $assets->count() }} jenis inventaris
                    </span>
                    <span class="inventory-footer-note">
                        Data inventaris laboratorium
                    </span>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- CSS Halaman Inventaris --}}
<style>
    .inventory-page {
        width: 100%;
        color: #172033;
    }

    /* Toolbar dan navigasi */
    .inventory-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
    }

    .inventory-tabs {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        border: 0;
    }

    .inventory-tabs .nav-link {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        min-height: 42px;
        padding: 10px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        color: #526078;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color .2s, color .2s, border-color .2s;
    }

    .inventory-tabs .nav-link > i {
        font-size: 16px;
    }

    .inventory-tabs .nav-link:hover:not(.active) {
        background: #f1f6ff;
        color: #2563eb;
        border-color: #c9dcff;
    }

    .inventory-tabs .nav-link.active {
        background: #1769f5;
        color: #fff;
        border-color: #1769f5;
        box-shadow: 0 3px 8px rgb(23 105 245 / 12%);
    }

    .inventory-count {
        font-size: 12px;
        font-weight: 700;
    }

    .inventory-action {
        min-height: 42px;
        white-space: nowrap;
    }

    /* Kartu ruangan */
    .room-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 340px;
        padding: 22px;
        border: 1px solid #e6ebf2;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 2px 8px rgb(15 23 42 / 3%);
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }

    .room-card:hover {
        border-color: #c7d9fb;
        box-shadow: 0 8px 22px rgb(15 23 42 / 6%);
        transform: translateY(-2px);
    }

    .room-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .room-code {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border: 1px solid #dbeafe;
        border-radius: 7px;
        background: #eff6ff;
        color: #1d4ed8;
        font-family: monospace;
        font-size: 12px;
        font-weight: 700;
    }

    .room-name {
        margin: 0 0 10px;
        color: #172033;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .room-description {
        display: -webkit-box;
        min-height: 44px;
        margin: 0 0 22px;
        overflow: hidden;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
        overflow-wrap: anywhere;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
    }

    .room-details {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 15px;
        border: 1px solid #e8edf4;
        border-radius: 10px;
        background: #f8fafc;
    }

    .room-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        font-size: 12px;
    }

    .room-detail-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        flex-shrink: 0;
        color: #64748b;
    }

    .room-detail-label i {
        color: #2563eb;
        font-size: 14px;
    }

    .room-detail-value {
        color: #263449;
        font-weight: 600;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .room-card-footer {
        margin-top: 22px;
    }

    .btn-room-action {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        width: 100%;
        min-height: 42px;
        padding: 10px 14px;
        border: 1px solid #1769f5;
        border-radius: 9px;
        background: #1769f5;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: background-color .2s, border-color .2s;
    }

    .btn-room-action:hover {
        border-color: #1556cb;
        background: #1556cb;
        color: #fff;
    }

    .room-admin-status {
        display: block;
        padding: 11px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #f8fafc;
        color: #64748b;
        font-size: 13px;
        text-align: center;
    }

    /* Status */
    .status-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        flex-shrink: 0;
        border-radius: 50%;
        background: currentColor;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .badge-warning {
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #a16207;
    }

    /* Empty state ruangan */
    .inventory-empty-state {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 320px;
        padding: 40px 24px;
        border: 1px dashed #d5deeb;
        border-radius: 14px;
        background: #fff;
        text-align: center;
    }

    .empty-state-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 68px;
        height: 68px;
        margin-bottom: 20px;
        border-radius: 16px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 28px;
    }

    .inventory-empty-state h5 {
        margin-bottom: 9px;
        color: #172033;
        font-size: 16px;
        font-weight: 700;
    }

    .inventory-empty-state p {
        max-width: 390px;
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }

    /* Panel tabel */
    .inventory-table-card {
        overflow: hidden;
        padding: 24px;
        border: 1px solid #e6ebf2;
        border-radius: 14px;
        background: #fff;
    }

    .inventory-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .inventory-section-title {
        margin: 0 0 7px;
        color: #172033;
        font-size: 17px;
        font-weight: 700;
    }

    .inventory-section-description {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    /* Pencarian inventaris */
    .asset-search {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        max-width: 320px;
        min-height: 42px;
        padding: 0 13px;
        border: 1px solid #dce3ed;
        border-radius: 9px;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
    }

    .asset-search:focus-within {
        border-color: #6b9df8;
        box-shadow: 0 0 0 3px rgb(37 99 235 / 9%);
    }

    .asset-search > i {
        flex-shrink: 0;
        color: #94a3b8;
        font-size: 15px;
    }

    .asset-search input {
        width: 100%;
        min-width: 0;
        padding: 10px 0;
        border: 0;
        outline: 0;
        background: transparent;
        color: #172033;
        font-size: 13px;
    }

    .asset-search input::placeholder {
        color: #94a3b8;
    }

    /* Tabel inventaris */
    .inventory-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e6ebf2;
        border-radius: 10px;
    }

    .inventory-table {
        width: 100%;
        min-width: 760px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .inventory-table thead th {
        padding: 15px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .35px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .inventory-table tbody td {
        padding: 16px;
        border-bottom: 1px solid #edf1f6;
        background: #fff;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .inventory-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .inventory-table tbody tr:not(.asset-empty-row):hover td {
        background: #f8fbff;
    }

    .asset-code {
        color: #2563eb;
        font-family: monospace;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .asset-name {
        color: #263449;
        font-weight: 600;
    }

    .asset-category {
        display: inline-block;
        padding: 5px 9px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
    }

    .asset-condition {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        border: 1px solid transparent;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .condition-good {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .condition-warning {
        border-color: #fde68a;
        background: #fffbeb;
        color: #a16207;
    }

    .asset-quantity {
        color: #263449;
        font-weight: 700;
    }

    .asset-unit {
        margin-left: 3px;
        color: #64748b;
        font-size: 12px;
    }

    .asset-empty-row td {
        padding: 0 !important;
    }

    .asset-empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 170px;
        color: #64748b;
        text-align: center;
    }

    .asset-empty-content i {
        color: #94a3b8;
        font-size: 30px;
    }

    .asset-empty-content span {
        font-size: 13px;
    }

    .inventory-table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding-top: 18px;
        color: #64748b;
        font-size: 12px;
    }

    .inventory-footer-note {
        color: #94a3b8;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .inventory-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .inventory-tabs {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
        }

        .inventory-tabs .nav-link {
            justify-content: flex-start;
            width: 100%;
            white-space: normal;
        }

        .inventory-action {
            justify-content: center;
            width: 100%;
        }

        .room-card {
            min-height: 0;
        }

        .inventory-table-card {
            padding: 16px;
        }

        .inventory-table-header {
            align-items: stretch;
            flex-direction: column;
        }

        .asset-search {
            max-width: none;
        }

        .inventory-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .room-card,
        .inventory-tabs .nav-link,
        .btn-room-action,
        .asset-search {
            transition: none;
        }

        .room-card:hover {
            transform: none;
        }
    }
</style>

{{-- Filter Pencarian Inventaris --}}
<script>
    function filterAssets() {
        const input = document.getElementById('assetSearchInput');
        const table = document.getElementById('assetTable');

        if (!input || !table) {
            return;
        }

        const filter = input.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr:not(.asset-empty-row)');

        rows.forEach(function (row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }
</script>

@endsection
