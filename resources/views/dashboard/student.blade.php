@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SIP-LAB')
@section('header-title', 'Dashboard Mahasiswa')
@section('header-sub', 'Selamat datang di portal layanan peminjaman fasilitas laboratorium riset')

@section('content')
<!-- Hero Welcome Banner -->
<div class="hero-banner mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-white-subtle text-white-75 px-3 py-1 rounded-pill mb-2 border border-white-subtle" style="font-size: 0.78rem;">
                <i class="bi bi-mortarboard me-1 text-info"></i> Mahasiswa Aktif &bull; NIM: {{ Auth::user()->nim ?? '-' }}
            </span>
            <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.02em;">Selamat Datang, {{ Auth::user()->nama ?? Auth::user()->name }}! 👋</h2>
            <p class="text-white-50 mb-0" style="max-width: 580px; font-size: 0.92rem; line-height: 1.5;">
                Periksa ketersediaan ruangan, pinjam peralatan riset terpadu, dan pantau proses verifikasi dokumen secara langsung.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('booking.create') }}" class="btn btn-light fw-bold px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; color: #0f172a;">
                <i class="bi bi-calendar-plus-fill text-primary"></i> Ajukan Peminjaman
            </a>
        </div>
    </div>
</div>

<!-- 4 Key Stat Metrics -->
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Pengajuan Saya</div>
                    <div class="stat-card-value">{{ Auth::user()->peminjaman()->count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-blue">
                    <i class="bi bi-folder2-open"></i>
                </div>
            </div>
            <div class="stat-card-footer text-primary">
                <i class="bi bi-clock-history"></i> {{ Auth::user()->peminjaman()->whereIn('status', ['Disetujui', 'Sedang Dipinjam', 'Selesai'])->count() }} disetujui / selesai
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Laboratorium</div>
                    <div class="stat-card-value">{{ \App\Models\Ruangan::count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-emerald">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
            <div class="stat-card-footer text-success">
                <i class="bi bi-check-circle"></i> {{ \App\Models\Ruangan::where('status', 'tersedia')->count() }} ruang tersedia
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Inventaris Alat</div>
                    <div class="stat-card-value">{{ \App\Models\Alat::where('status', 'tersedia')->count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-amber">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <i class="bi bi-tools"></i> Siap dipinjam
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-card-label">Notifikasi</div>
                    <div class="stat-card-value">{{ Auth::user()->notifikasi()->count() }}</div>
                </div>
                <div class="stat-card-icon stat-icon-purple">
                    <i class="bi bi-bell"></i>
                </div>
            </div>
            <div class="stat-card-footer text-muted">
                <a href="{{ route('student.notifications') }}" class="text-decoration-none text-muted">Lihat semua notif &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-3 p-md-4 mb-4">
    <h5 class="fw-bold text-dark mb-3">Ringkasan Status Pengajuan</h5>
    <div class="row g-3">
        @foreach([
            'Menunggu Pemeriksaan Staf Sarpras' => ['Menunggu Staf', 'text-warning'],
            'Disetujui' => ['Disetujui', 'text-success'],
            'Sedang Dipinjam' => ['Sedang Dipinjam', 'text-info'],
            'Selesai' => ['Selesai', 'text-success'],
            'Ditolak' => ['Ditolak', 'text-danger'],
        ] as $status => [$label, $color])
            <div class="col-6 col-md-4 col-xl-2">
                <div class="p-3 h-100 rounded-3 border bg-white">
                    <div class="small text-muted">{{ $label }}</div>
                    <div class="fs-4 fw-bold {{ $color }}">{{ $bookingStatusCounts[$status] ?? 0 }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Main Section: 2 Column Layout -->
<div class="row g-4">
    <!-- Left Column: Lab Availability -->
    <div class="col-lg-7">
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Ketersediaan Ruang Laboratorium</h5>
                    <p class="text-muted small mb-0" id="katalog-sub-label">Status operasional ruang laboratorium riset</p>
                </div>
                <a href="{{ route('student.inventory') }}" class="btn btn-sm btn-brand-outline">
                    Semua Katalog <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-custom-wrapper">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Kode & Ruangan</th>
                            <th>Lokasi</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr data-room-nama="{{ $room->nama_ruangan }}">
                                <td>
                                    <div class="fw-bold text-dark">{{ $room->nama_ruangan }}</div>
                                    <small class="text-muted">{{ $room->kode_ruangan }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-muted small">
                                        <i class="bi bi-geo-alt text-primary"></i>
                                        <span>{{ $room->lokasi }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        <i class="bi bi-people me-1 text-muted"></i>{{ $room->kapasitas }} orang
                                    </span>
                                </td>
                                <td>
                                    <span class="room-status-badge" data-default="{{ strtolower($room->status) }}">
                                        @if(strtolower($room->status) === 'tersedia')
                                            <span class="badge-status badge-success">Tersedia</span>
                                        @else
                                            <span class="badge-status badge-warning">{{ ucfirst($room->status) }}</span>
                                        @endif
                                    </span>
                                </td>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data laboratorium.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Featured Assets Grid -->
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Peralatan Riset Siap Pakai</h5>
                    <p class="text-muted small mb-0">Instrumen dan perangkat keras pendukung pengujian</p>
                </div>
                <a href="{{ route('student.inventory') }}" class="btn btn-sm btn-brand-outline">Lihat Aset</a>
            </div>

            <div class="row g-3">
                @forelse($assets as $asset)
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light-subtle d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px; background: #eff6ff;">
                                    <i class="bi bi-tools fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small mb-0">{{ $asset->nama_alat }}</div>
                                    <small class="text-muted">{{ $asset->kode_inventaris }} &bull; {{ $asset->kategori }}</small>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">
                                {{ $asset->jumlah_total }} unit
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3 small">Tidak ada aset tersedia saat ini.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Calendar + Recent Bookings & Timeline -->
    <div class="col-lg-5">

        {{-- ── KALENDER PEMAKAIAN LAB ── --}}
        <div class="card-custom p-4 mb-4" id="kalender-section">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-calendar3 text-primary me-1"></i> Kalender Pemakaian Lab
                    </h6>
                    <small class="text-muted">Jadwal peminjaman disetujui</small>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-sm btn-brand-outline px-2 py-1" id="btn-prev-month">
                        <i class="bi bi-chevron-left" style="font-size:.7rem;"></i>
                    </button>
                    <span class="fw-semibold text-dark" id="cal-label"
                          style="min-width:120px; text-align:center; font-size:.8rem;"></span>
                    <button class="btn btn-sm btn-brand-outline px-2 py-1" id="btn-next-month">
                        <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
                    </button>
                </div>
            </div>

            {{-- Day headers --}}
            <div class="row g-0 text-center mb-1" style="border-bottom:1px solid #e2e8f0; padding-bottom:4px;">
                @foreach(['Mi','Se','Se','Ra','Ka','Ju','Sa'] as $d)
                    <div class="col" style="font-size:.65rem; font-weight:700; color:#94a3b8;">{{ $d }}</div>
                @endforeach
            </div>

            {{-- Grid (JS renders) --}}
            <div id="cal-grid" class="mb-2"></div>

            {{-- Loading --}}
            <div id="cal-loading" class="text-center py-2" style="display:none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>

            {{-- Legend --}}
            <div class="d-flex gap-3 mb-3" style="font-size:.7rem; color:#94a3b8;">
                <span>
                    <span style="display:inline-block;width:7px;height:7px;border-radius:50%;
                                 background:#3b82f6;margin-right:3px;"></span>Ada jadwal
                </span>
                <span>
                    <span style="display:inline-block;width:7px;height:7px;border-radius:50%;
                                 background:#dbeafe;border:1.5px solid #3b82f6;margin-right:3px;"></span>Hari ini
                </span>
            </div>

            {{-- Detail / Placeholder --}}
            <div id="cal-placeholder" class="text-center text-muted py-2" style="font-size:.78rem;">
                <i class="bi bi-hand-index-thumb me-1 text-primary opacity-50"></i>
                Pilih tanggal untuk melihat jadwal
            </div>

            <div id="cal-detail" style="display:none;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-primary"
                         style="width:28px;height:28px;background:#eff6ff;flex-shrink:0;font-size:.8rem;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size:.8rem;" id="cal-detail-title"></div>
                        <div class="text-muted" style="font-size:.68rem;" id="cal-detail-sub"></div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size:.75rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Ruangan</th>
                                <th>Waktu</th>
                                <th>Durasi</th>
                            </tr>
                        </thead>
                        <tbody id="cal-detail-body"></tbody>
                    </table>
                </div>
            </div>

            <div id="cal-empty" style="display:none;"
                 class="text-center text-muted py-2" style="font-size:.78rem;">
                <i class="bi bi-calendar-x me-1"></i>
                Tidak ada jadwal pada tanggal ini.
            </div>
        </div>
        {{-- ── END KALENDER ── --}}
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Pengajuan Terakhir</h5>
                    <p class="text-muted small mb-0">Status progres verifikasi permohonan Anda</p>
                </div>
                <a href="{{ route('student.history') }}" class="btn btn-sm btn-brand-outline">Riwayat</a>
            </div>

            @forelse($bookings as $booking)
                <div class="p-3 rounded-3 border mb-3 position-relative" style="background: #ffffff; border-color: #e2e8f0 !important; transition: all 0.15s ease;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-light text-muted border px-2 py-0 rounded-pill small mb-1" style="font-size: 0.68rem;">{{ $booking->kode_peminjaman }}</span>
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $booking->keperluan }}</div>
                        </div>
                        @if($booking->status === 'Disetujui')
                            <span class="badge-status badge-success">Disetujui</span>
                        @elseif($booking->status === 'Sedang Dipinjam')
                            <span class="badge-status badge-info">Sedang Dipinjam</span>
                        @elseif($booking->status === 'Selesai')
                            <span class="badge-status badge-success">Selesai</span>
                        @elseif(in_array($booking->status, ['Menunggu Verifikasi', 'Menunggu Pemeriksaan Staf Sarpras'], true))
                            <span class="badge-status badge-pending">Verifikasi Staf</span>
                        @elseif($booking->status === 'Perlu Revisi')
                            <span class="badge-status badge-warning">Perlu Revisi</span>
                        @else
                            <span class="badge-status badge-danger">{{ $booking->status }}</span>
                        @endif
                    </div>

                    <div class="text-muted small mb-2">
                        <i class="bi bi-buildings me-1 text-primary"></i> 
                        {{ $booking->detailRuangan->first()?->ruangan?->nama_ruangan ?? 'Penggunaan Mandiri' }}
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                        <div>
                            <i class="bi bi-calendar-event me-1"></i> {{ date('d M Y', strtotime($booking->tanggal_pinjam)) }}
                        </div>
                        <a href="{{ route('booking.show', $booking) }}" class="text-primary text-decoration-none fw-semibold">
                            Detail &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-light"></i>
                    Belum ada pengajuan peminjaman aktif.<br>
                    <a href="{{ route('booking.create') }}" class="btn btn-sm btn-brand-primary mt-3">Ajukan Sekarang</a>
                </div>
            @endforelse
        </div>

        <!-- Guidelines / Policy Info Card -->
        <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe;">
            <div class="d-flex gap-3">
                <div class="text-primary fs-3">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Tata Tertib Laboratorium</h6>
                    <p class="text-muted small mb-2" style="line-height: 1.5;">
                        Pastikan mematuhi SOP K3 (Jas Lab, Sepatu Tertutup) dan mengunggah dokumen pendukung resmi saat mengajukan izin peminjaman ruang atau alat laboratorium.
                    </p>
                    <a href="{{ route('student.inventory') }}" class="fw-bold text-primary small text-decoration-none">
                        Pelajari SOP Penggunaan &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Config ──────────────────────────────────────────────
    const ENDPOINT = "{{ route('student.calendar.data') }}";
    const MONTHS   = ['Januari','Februari','Maret','April','Mei','Juni',
                      'Juli','Agustus','September','Oktober','November','Desember'];

    let currentYear  = new Date().getFullYear();
    let currentMonth = new Date().getMonth() + 1; // 1-based
    let calendarData = {}; // keyed by "YYYY-MM-DD"
    let selectedDate  = null;

    // ── DOM refs ────────────────────────────────────────────
    const label       = document.getElementById('cal-label');
    const grid        = document.getElementById('cal-grid');
    const loading     = document.getElementById('cal-loading');
    const detail      = document.getElementById('cal-detail');
    const detailTitle = document.getElementById('cal-detail-title');
    const detailSub   = document.getElementById('cal-detail-sub');
    const detailBody  = document.getElementById('cal-detail-body');
    const empty       = document.getElementById('cal-empty');
    const placeholder = document.getElementById('cal-placeholder');

    document.getElementById('btn-prev-month').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 1) { currentMonth = 12; currentYear--; }
        fetchAndRender();
    });

    document.getElementById('btn-next-month').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 12) { currentMonth = 1; currentYear++; }
        fetchAndRender();
    });

    // ── Fetch ────────────────────────────────────────────────
    function fetchAndRender() {
        loading.style.display = '';
        grid.innerHTML        = '';
        hideDetail();

        fetch(`${ENDPOINT}?year=${currentYear}&month=${currentMonth}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(json => {
            calendarData = json.calendar || {};
            renderGrid(json.year, json.month);
        })
        .catch(() => {
            grid.innerHTML = '<p class="text-danger small text-center py-3">Gagal memuat data kalender.</p>';
        })
        .finally(() => { loading.style.display = 'none'; });
    }

    // ── Render calendar grid ─────────────────────────────────
    function renderGrid(year, month) {
        label.textContent = `${MONTHS[month - 1]} ${year}`;

        const firstDay  = new Date(year, month - 1, 1).getDay(); // 0=Sun
        const daysInMon = new Date(year, month, 0).getDate();
        const today     = new Date();
        const todayStr  = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;

        let html = '<div class="row g-1">';

        // Leading empty cells
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="col"></div>';
        }

        for (let d = 1; d <= daysInMon; d++) {
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const hasSchedule = calendarData[dateStr] && calendarData[dateStr].length > 0;
            const isToday     = dateStr === todayStr;
            const isSelected  = dateStr === selectedDate;

            let dayClass  = 'cal-day';
            if (isToday)    dayClass += ' cal-day-today';
            if (isSelected) dayClass += ' cal-day-selected';

            html += `<div class="col d-flex justify-content-center mb-1">
                <button class="${dayClass}" data-date="${dateStr}"
                    style="width:36px;height:36px;border-radius:50%;border:none;cursor:pointer;
                           font-size:.82rem;font-weight:600;position:relative;
                           background:${isSelected ? '#3b82f6' : (isToday ? '#eff6ff' : 'transparent')};
                           color:${isSelected ? '#fff' : (isToday ? '#3b82f6' : '#334155')};
                           transition:background .15s;">
                    ${d}
                    ${hasSchedule ? `<span style="position:absolute;bottom:3px;left:50%;transform:translateX(-50%);
                        width:5px;height:5px;border-radius:50%;
                        background:${isSelected ? '#fff' : '#3b82f6'};"></span>` : ''}
                </button>
            </div>`;

            // Break row every 7 days
            if ((firstDay + d) % 7 === 0) {
                html += '</div><div class="row g-1">';
            }
        }

        html += '</div>';
        grid.innerHTML = html;

        // Attach click listeners
        grid.querySelectorAll('.cal-day').forEach(btn => {
            btn.addEventListener('click', function () {
                selectedDate = this.dataset.date;
                renderGrid(year, month);   // re-render to reflect selection
                showDetail(selectedDate);
            });
        });
    }

    // ── Catalog status sync ──────────────────────────────────
    function syncCatalogStatus(occupiedSet) {
        document.querySelectorAll('tr[data-room-nama]').forEach(function(row) {
            var badge = row.querySelector('.room-status-badge');
            if (!badge) return;
            var nama = row.dataset.roomNama.trim().toLowerCase();
            var def  = (badge.dataset.default || 'tersedia').trim().toLowerCase();
            if (occupiedSet && occupiedSet.has(nama)) {
                badge.innerHTML = '<span class="badge-status badge-danger">Terpakai</span>';
                row.style.background = '#fff5f5';
            } else {
                if (def === 'tersedia') {
                    badge.innerHTML = '<span class="badge-status badge-success">Tersedia</span>';
                } else {
                    badge.innerHTML = '<span class="badge-status badge-warning">' + def.charAt(0).toUpperCase() + def.slice(1) + '</span>';
                }
                row.style.background = '';
            }
        });
    }

    // ── Detail panel ─────────────────────────────────────────
    function showDetail(dateStr) {
        var schedules = calendarData[dateStr];
        var d     = new Date(dateStr + 'T00:00:00');
        var days  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        var dateLabel = days[d.getDay()] + ', ' + d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
        detailTitle.textContent = dateLabel;
        var totalJadwal = schedules ? schedules.length : 0;
        if (detailSub) detailSub.textContent = totalJadwal + ' jadwal pemakaian';
        if (placeholder) placeholder.style.display = 'none';

        // Sync catalog badges
        var occupied = new Set(schedules ? schedules.map(function(s){ return s.ruangan.trim().toLowerCase(); }) : []);
        syncCatalogStatus(occupied);

        // Update catalog subtitle
        var subLabel = document.getElementById('katalog-sub-label');
        if (subLabel) {
            if (schedules && schedules.length > 0) {
                subLabel.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-calendar-event me-1"></i>Tanggal: ' + dateLabel + '</span>';
            } else {
                subLabel.textContent = 'Status operasional ruang laboratorium riset';
            }
        }

        if (!schedules || schedules.length === 0) {
            detail.style.display = 'none';
            empty.style.display  = '';
            return;
        }
        empty.style.display = 'none';
        var rows = '';
        schedules.forEach(function(s) {
            rows += '<tr>' +
                '<td><div class="fw-semibold text-dark" style="font-size:.82rem;">' + s.ruangan + '</div>' +
                '<small class="text-muted">' + s.kode_ruangan + '</small></td>' +
                '<td class="text-nowrap" style="font-size:.82rem;">' + s.jam_mulai + ' \u2013 ' + s.jam_selesai + '</td>' +
                '<td class="text-nowrap" style="font-size:.82rem;">' + s.durasi + '</td>' +
                '<td><span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill" style="font-size:.72rem;">Terpakai</span></td>' +
                '</tr>';
        });
        detailBody.innerHTML  = rows;
        detail.style.display  = '';
    }

    function hideDetail() {
        detail.style.display = 'none';
        empty.style.display  = 'none';
        if (placeholder) placeholder.style.display = '';
        selectedDate = null;
        syncCatalogStatus(null);
        var subLabel = document.getElementById('katalog-sub-label');
        if (subLabel) subLabel.textContent = 'Status operasional ruang laboratorium riset';
    }

    // ── Init ─────────────────────────────────────────────────
    fetchAndRender();
})();
</script>
@endpush
