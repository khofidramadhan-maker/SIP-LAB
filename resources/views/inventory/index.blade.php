@extends('layouts.app')

@section('title', 'Katalog Ruang & Inventaris - SIP-LAB')
@section('header-title', 'Ruang Laboratorium & Inventaris')
@section('header-sub', 'Cari dan periksa ketersediaan fasilitas laboratorium beserta peralatan riset')

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <!-- Navigation Tabs -->
    <ul class="nav nav-pills gap-2" id="inventoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 fw-semibold rounded-pill" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms-pane" type="button" role="tab">
                <i class="bi bi-buildings me-2"></i> Ruang Laboratorium ({{ $rooms->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold rounded-pill" id="assets-tab" data-bs-toggle="tab" data-bs-target="#assets-pane" type="button" role="tab">
                <i class="bi bi-box-seam me-2"></i> Inventaris Peralatan ({{ $assets->count() }})
            </button>
        </li>
    </ul>

    @if(Auth::user()->role === 'mahasiswa')
        <a href="{{ route('booking.create') }}" class="btn btn-brand-primary">
            <i class="bi bi-calendar-plus"></i> Ajukan Peminjaman
        </a>
    @endif
</div>

<div class="tab-content" id="inventoryTabsContent">
    <!-- Tab 1: Ruang Laboratorium -->
    <div class="tab-pane fade show active" id="rooms-pane" role="tabpanel">
        <div class="row g-4">
            @forelse($rooms as $room)
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom h-100 d-flex flex-column justify-content-between p-4" style="border-radius: 18px;">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-bold">
                                    {{ $room->kode_ruangan }}
                                </span>
                                @if(strtolower($room->status) === 'tersedia')
                                    <span class="badge-status badge-success">Tersedia</span>
                                @else
                                    <span class="badge-status badge-warning">{{ ucfirst($room->status) }}</span>
                                @endif
                            </div>

                            <h5 class="fw-bold text-dark mb-2">{{ $room->nama_ruangan }}</h5>
                            <p class="text-muted small mb-4" style="line-height: 1.6; min-height: 48px;">
                                {{ $room->fasilitas ?? 'Laboratorium riset terpadu untuk pengujian dan praktikum akademik.' }}
                            </p>

                            <div class="d-flex flex-column gap-2 p-3 rounded-3 bg-light-subtle mb-4" style="border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted"><i class="bi bi-geo-alt text-primary me-2"></i>Lokasi:</span>
                                    <span class="fw-semibold text-dark">{{ $room->lokasi }}</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted"><i class="bi bi-people text-primary me-2"></i>Kapasitas:</span>
                                    <span class="fw-semibold text-dark">{{ $room->kapasitas }} Mahasiswa</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if(Auth::user()->role === 'mahasiswa')
                                <a href="{{ route('booking.create') }}?ruangan_id={{ $room->id }}" class="btn btn-brand-primary w-100 justify-content-center">
                                    <i class="bi bi-calendar2-check"></i> Pilih Ruangan Ini
                                </a>
                            @else
                                <span class="badge bg-light text-muted border w-100 py-2 d-block text-center">Status: {{ ucfirst($room->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-buildings fs-1 d-block mb-2 text-light"></i>
                    Belum ada ruang laboratorium terdaftar di sistem.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tab 2: Inventaris & Alat Riset -->
    <div class="tab-pane fade" id="assets-pane" role="tabpanel">
        <div class="card-custom p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Daftar Peralatan & Aset Ilmiah</h5>
                    <p class="text-muted small mb-0">Instrumen pendukung praktikum dan pengujian laboratorium</p>
                </div>
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="assetSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama atau kode alat..." style="border-radius: 0 10px 10px 0;" onkeyup="filterAssets()">
                </div>
            </div>

            <div class="table-custom-wrapper">
                <table class="table-custom" id="assetTable">
                    <thead>
                        <tr>
                            <th>Kode Inventaris</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Kondisi</th>
                            <th>Jumlah Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary font-monospace">{{ $asset->kode_inventaris }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $asset->nama_alat }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        {{ $asset->kategori }}
                                    </span>
                                </td>
                                <td>
                                    @if(strtolower($asset->kondisi) === 'baik')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">Baik</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill small">{{ $asset->kondisi }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $asset->jumlah_total }}</span> unit
                                </td>
                                <td>
                                    @if(strtolower($asset->status) === 'tersedia')
                                        <span class="badge-status badge-success">Siap Pakai</span>
                                    @else
                                        <span class="badge-status badge-warning">{{ ucfirst($asset->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada inventaris alat terdata.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function filterAssets() {
        const input = document.getElementById('assetSearchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('assetTable');
        const trs = table.getElementsByTagName('tr');

        for (let i = 1; i < trs.length; i++) {
            const td = trs[i].textContent.toLowerCase();
            trs[i].style.display = td.includes(filter) ? '' : 'none';
        }
    }
</script>
@endsection
