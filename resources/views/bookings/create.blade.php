
@extends('layouts.app')

@section('title', 'Ajukan Peminjaman - SIP-LAB')
@section('header-title', 'Formulir Pengajuan Peminjaman')
@section('header-sub', 'Lengkapi detail kegiatan, jadwal penggunaan, serta berkas dokumen pendukung')

@section('content')

<div class="booking-page">

    <div class="row g-4 align-items-start">

        {{-- FORM PENGAJUAN --}}
        <div class="col-12 col-xl-8">
            <div class="card-custom booking-form-card">

                <div class="booking-heading booking-main-heading">
                    <div class="booking-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <h5>Permohonan Peminjaman</h5>
                        <p>Isi informasi kegiatan, jadwal, dan peralatan yang dibutuhkan.</p>
                    </div>
                </div>

                {{-- Pesan validasi Laravel --}}
                @if($errors->any())
                    <div class="alert alert-danger mt-4" role="alert">
                        <strong>Pengajuan belum dapat diproses.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="bookingForm"
                      method="POST"
                      action="{{ route('booking.store') }}"
                      enctype="multipart/form-data">

                    @csrf

                    {{-- BAGIAN 1 --}}
                    <section class="booking-section">
                        <div class="booking-section-heading">
                            <span class="booking-step">01</span>
                            <div>
                                <h6>Informasi Kegiatan</h6>
                                <p>Jelaskan keperluan dan lokasi penggunaan.</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="keperluan" class="form-label">
                                    Keperluan / Judul Kegiatan
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text" id="keperluan"
                                       name="keperluan"
                                       class="form-control @error('keperluan') is-invalid @enderror"
                                       placeholder="Contoh: Pengujian model untuk tugas akhir"
                                       value="{{ old('keperluan') }}"
                                       required>

                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="ruangan_id" class="form-label">
                                    Ruang Laboratorium
                                    <span class="text-danger">*</span>
                                </label>

                                <select id="ruangan_id" name="ruangan_id"
                                        class="form-select @error('ruangan_id') is-invalid @enderror"
                                        required>
                                    <option value="">Pilih ruang laboratorium</option>

                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ old('ruangan_id', request('ruangan_id')) == $room->id ? 'selected' : '' }}>
                                            {{ $room->nama_ruangan }}
                                            ({{ $room->kode_ruangan }})
                                            — Kapasitas {{ $room->kapasitas }} orang
                                            — {{ $room->lokasi }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('ruangan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if($rooms->isEmpty())
                                    <small class="form-help text-danger">
                                        Belum ada ruang laboratorium yang terdaftar.
                                    </small>
                                @else
                                    <small class="form-help">
                                        Pilih ruangan sesuai kebutuhan kegiatan.
                                    </small>
                                @endif
                            </div>
                        </div>
                    </section>

                    {{-- BAGIAN 2 --}}
                    <section class="booking-section">
                        <div class="booking-section-heading">
                            <span class="booking-step">02</span>
                            <div>
                                <h6>Jadwal Peminjaman</h6>
                                <p>Tentukan tanggal serta durasi penggunaan.</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="tanggal_pinjam" class="form-label">
                                    Tanggal Pelaksanaan
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="date" id="tanggal_pinjam"
                                       name="tanggal_pinjam"
                                       class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                       value="{{ old('tanggal_pinjam', request('date', date('Y-m-d'))) }}"
                                       required>

                                @error('tanggal_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 col-md-4">
                                <label for="jam_mulai" class="form-label">
                                    Jam Mulai <span class="text-danger">*</span>
                                </label>

                                <input type="time" id="jam_mulai"
                                       name="jam_mulai"
                                       class="form-control @error('jam_mulai') is-invalid @enderror"
                                       value="{{ old('jam_mulai', request('start', '08:00')) }}"
                                       required>

                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 col-md-4">
                                <label for="jam_selesai" class="form-label">
                                    Jam Selesai <span class="text-danger">*</span>
                                </label>

                                <input type="time" id="jam_selesai"
                                       name="jam_selesai"
                                       class="form-control @error('jam_selesai') is-invalid @enderror"
                                       value="{{ old('jam_selesai', request('end', '16:00')) }}"
                                       required>

                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="bookingTimeError"
                             class="booking-validation-message"
                             role="alert"
                             aria-live="polite"
                             hidden>
                            <i class="bi bi-exclamation-circle"></i>
                            <span></span>
                        </div>

                        <div class="booking-info-note">
                            <i class="bi bi-info-circle"></i>
                            <span>
                                Jam selesai harus lebih akhir daripada jam mulai.
                                Pastikan jadwal sesuai dengan ketersediaan ruangan.
                            </span>
                        </div>
                    </section>

                    {{-- BAGIAN 3 --}}
                    <section class="booking-section">
                        <div class="booking-section-heading">
                            <span class="booking-step">03</span>
                            <div>
                                <h6>Peralatan dan Dokumen</h6>
                                <p>Tambahkan peralatan dan dokumen jika diperlukan.</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label for="alat_id" class="form-label">
                                    Peralatan Tambahan
                                    <span class="optional-label">(Opsional)</span>
                                </label>

                                <select id="alat_id" name="alat_id"
                                        class="form-select @error('alat_id') is-invalid @enderror">
                                    <option value="">Tidak meminjam alat khusus</option>

                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}"
                                            {{ old('alat_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->nama_alat }}
                                            ({{ $asset->kode_inventaris }})
                                            — {{ $asset->jumlah_total }} unit
                                        </option>
                                    @endforeach
                                </select>

                                @error('alat_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="jumlah_alat" class="form-label">
                                    Jumlah Alat
                                </label>

                                <input type="number" id="jumlah_alat"
                                       name="jumlah_alat"
                                       class="form-control @error('jumlah_alat') is-invalid @enderror"
                                       min="1"
                                       value="{{ old('jumlah_alat', 1) }}">

                                @error('jumlah_alat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="document" class="form-label">
                                    Dokumen Pendukung
                                    <span class="optional-label">(Opsional)</span>
                                </label>

                                <label for="document" class="booking-upload-area">
                                    <span class="booking-upload-icon">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </span>

                                    <span class="booking-upload-text">
                                        <strong>Pilih dokumen PDF</strong>
                                        <small>
                                            Surat tugas atau rekomendasi dosen pembimbing.
                                        </small>
                                    </span>

                                    <span class="booking-upload-button">Pilih File</span>
                                </label>

                                <input type="file" id="document" name="document"
                                       class="booking-file-input"
                                       accept=".pdf,application/pdf"
                                       aria-describedby="documentHelp">

                                <small id="documentHelp" class="form-help">
                                    Format PDF. Ukuran file mengikuti ketentuan server.
                                </small>

                                <div id="documentFileName"
                                     class="booking-file-name"
                                     aria-live="polite"></div>

                                @error('document')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    {{-- AKSI --}}
                    <div class="booking-form-actions">
                        <a href="{{ route('student.dashboard') }}"
                           class="btn btn-booking-cancel">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>

                        <button type="submit"
                                id="submitBooking"
                                class="btn btn-brand-primary booking-submit">
                            <i class="bi bi-send"></i> Kirim Pengajuan
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- SIDEBAR INFORMASI --}}
        <aside class="col-12 col-xl-4">
            <div class="card-custom booking-sidebar">
                <div class="booking-heading mb-3">
                    <div class="booking-icon booking-icon-amber">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div>
                        <h5>Informasi Peminjaman</h5>
                        <p>Hal yang perlu diperhatikan.</p>
                    </div>
                </div>

                <div class="booking-guideline">
                    <span class="guideline-number">01</span>
                    <div>
                        <strong>Pilih fasilitas sesuai kebutuhan</strong>
                        <p>Pengajuan dapat mencakup ruang laboratorium dan peralatan.</p>
                    </div>
                </div>

                <div class="booking-guideline">
                    <span class="guideline-number">02</span>
                    <div>
                        <strong>Periksa jadwal penggunaan</strong>
                        <p>Pastikan tanggal dan waktu sesuai dengan kebutuhan kegiatan.</p>
                    </div>
                </div>

                <div class="booking-guideline">
                    <span class="guideline-number">03</span>
                    <div>
                        <strong>Lengkapi dokumen pendukung</strong>
                        <p>Siapkan dokumen sesuai ketentuan laboratorium.</p>
                    </div>
                </div>

                <div class="booking-sidebar-footer">
                    <i class="bi bi-shield-check"></i>
                    <span>
                        Gunakan fasilitas sesuai prosedur operasional dan arahan petugas.
                    </span>
                </div>
            </div>
        </aside>

    </div>
</div>

<style>
    .booking-page {
        color: #172033;
    }

    .booking-filter-card,
    .booking-form-card,
    .booking-sidebar {
        padding: 24px;
        border: 1px solid #e5eaf2;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 2px 8px rgb(15 23 42 / 3%);
    }

    .booking-heading {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .booking-heading h5 {
        margin: 0 0 5px;
        font-size: 15px;
        font-weight: 700;
    }

    .booking-heading p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    .booking-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 12px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 20px;
    }

    .booking-icon-amber {
        background: #fffbeb;
        color: #b45309;
    }

    .booking-main-heading {
        padding-bottom: 22px;
        border-bottom: 1px solid #e8edf4;
    }

    .booking-section {
        padding: 25px 0;
        border-bottom: 1px solid #e8edf4;
    }

    .booking-section-heading {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 22px;
    }

    .booking-step {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
    }

    .booking-section-heading h6 {
        margin: 2px 0 5px;
        font-size: 14px;
        font-weight: 700;
    }

    .booking-section-heading p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    .booking-page .form-label {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
    }

    .booking-page .form-control,
    .booking-page .form-select {
        min-height: 44px;
        border: 1px solid #dce3ed;
        border-radius: 9px;
        color: #263449;
        font-size: 13px;
        box-shadow: none;
    }

    .booking-page .form-control:focus,
    .booking-page .form-select:focus {
        border-color: #6b9df8;
        box-shadow: 0 0 0 3px rgb(37 99 235 / 9%);
    }

    .booking-page .form-control.is-invalid,
    .booking-page .form-select.is-invalid {
        border-color: #dc3545;
    }

    .form-help {
        display: block;
        margin-top: 8px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    .optional-label {
        color: #94a3b8;
        font-size: 11px;
        font-weight: 400;
    }

    .booking-info-note {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-top: 16px;
        padding: 13px;
        border: 1px solid #dbeafe;
        border-radius: 9px;
        background: #f8fbff;
        color: #475569;
        font-size: 11px;
        line-height: 1.7;
    }

    .booking-info-note i {
        color: #2563eb;
    }

    .booking-validation-message {
        display: flex;
        gap: 9px;
        margin-top: 14px;
        padding: 12px 14px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 12px;
        line-height: 1.6;
    }

    .booking-validation-message[hidden] {
        display: none;
    }

    .booking-upload-area {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 16px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        cursor: pointer;
    }

    .booking-upload-area:hover {
        border-color: #93b4f8;
        background: #f3f7ff;
    }

    .booking-upload-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        border-radius: 9px;
        background: #fff;
        color: #dc2626;
        font-size: 22px;
    }

    .booking-upload-text {
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 5px;
        min-width: 0;
    }

    .booking-upload-text strong {
        font-size: 12px;
    }

    .booking-upload-text small {
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    .booking-upload-button {
        padding: 7px 10px;
        border: 1px solid #dbeafe;
        border-radius: 7px;
        background: #fff;
        color: #2563eb;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .booking-file-input {
        display: block;
        width: 100%;
        height: 1px;
        opacity: 0;
    }

    .booking-file-input:focus-visible {
        outline: 2px solid #2563eb;
    }

    .booking-file-name {
        display: none;
        overflow-wrap: anywhere;
        margin-top: 8px;
        color: #475569;
        font-size: 12px;
    }

    .booking-file-name.is-visible {
        display: block;
    }

    .booking-form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 24px;
    }

    .btn-booking-cancel,
    .btn-filter,
    .btn-filter-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 14px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-booking-cancel {
        border: 1px solid #dce3ed;
        background: #fff;
        color: #475569;
    }

    .btn-filter {
        border: 1px solid #1769f5;
        background: #1769f5;
        color: #fff;
    }

    .btn-filter:hover {
        background: #1556cb;
        color: #fff;
    }

    .btn-filter-reset {
        width: 42px;
        padding: 0;
        border: 1px solid #dce3ed;
        background: #fff;
        color: #64748b;
    }

    .booking-submit {
        min-height: 44px;
        padding: 11px 20px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
    }

    .booking-sidebar {
        position: sticky;
        top: 20px;
    }

    .booking-guideline {
        display: flex;
        gap: 12px;
        padding: 17px 0;
        border-bottom: 1px solid #edf1f6;
    }

    .guideline-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 10px;
        font-weight: 700;
    }

    .booking-guideline strong {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
    }

    .booking-guideline p {
        margin: 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.8;
    }

    .booking-sidebar-footer {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        padding: 13px;
        border: 1px solid #dbeafe;
        border-radius: 9px;
        background: #f8fbff;
        color: #475569;
        font-size: 11px;
        line-height: 1.8;
    }

    .booking-sidebar-footer i {
        color: #2563eb;
        font-size: 16px;
    }

    @media (max-width: 1199.98px) {
        .booking-sidebar {
            position: static;
        }
    }

    @media (max-width: 767.98px) {
        .booking-filter-card,
        .booking-form-card,
        .booking-sidebar {
            padding: 18px;
        }

        .booking-upload-area {
            flex-wrap: wrap;
        }

        .booking-upload-text {
            flex-basis: calc(100% - 60px);
        }

        .booking-upload-button {
            margin-left: 55px;
        }

        .booking-form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .booking-form-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bookingForm = document.getElementById('bookingForm');
    const filterForm = document.getElementById('availabilityFilterForm');

    const bookingDate = document.getElementById('tanggal_pinjam');
    const bookingStart = document.getElementById('jam_mulai');
    const bookingEnd = document.getElementById('jam_selesai');

    const filterDate = document.getElementById('filter_date');
    const filterStart = document.getElementById('filter_start');
    const filterEnd = document.getElementById('filter_end');

    const timeError = document.getElementById('bookingTimeError');
    const fileInput = document.getElementById('document');
    const fileName = document.getElementById('documentFileName');

    // Gunakan tanggal lokal, bukan UTC.
    function getLocalDate() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function validateDate(input) {
        if (!input) return true;

        input.min = getLocalDate();

        const invalid = input.value !== '' &&
            input.value < input.min;

        input.setCustomValidity(
            invalid ? 'Tanggal tidak boleh sebelum hari ini.' : ''
        );

        input.classList.toggle('is-invalid', invalid);

        return !invalid;
    }

    function validateTime(start, end, showMessage = true) {
        if (!start || !end) return true;

        const invalid = start.value !== '' &&
            end.value !== '' &&
            end.value <= start.value;

        end.setCustomValidity(
            invalid
                ? 'Jam selesai harus lebih akhir daripada jam mulai.'
                : ''
        );

        end.classList.toggle('is-invalid', invalid);

        if (timeError) {
            timeError.hidden = !(invalid && showMessage);

            if (invalid && showMessage) {
                timeError.querySelector('span').textContent =
                    'Jam selesai harus lebih akhir daripada jam mulai. Periksa kembali jadwal peminjaman.';
            }
        }

        return !invalid;
    }

    function setupDate(input) {
        if (!input) return;

        input.min = getLocalDate();

        input.addEventListener('input', function () {
            validateDate(input);
        });

        input.addEventListener('change', function () {
            validateDate(input);
        });
    }

    function setupTime(start, end, showMessage = true) {
        if (!start || !end) return;

        const check = function () {
            validateTime(start, end, showMessage);
        };

        start.addEventListener('input', check);
        start.addEventListener('change', check);
        end.addEventListener('input', check);
        end.addEventListener('change', check);
    }

    function setupForm(form, date, start, end, showMessage = true) {
        if (!form) return;

        setupDate(date);
        setupTime(start, end, showMessage);

        form.addEventListener('submit', function (event) {
            const dateValid = validateDate(date);
            const timeValid = validateTime(start, end, showMessage);

            if (!dateValid || !timeValid || !form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        });
    }

    setupForm(
        bookingForm,
        bookingDate,
        bookingStart,
        bookingEnd,
        true
    );

    setupForm(
        filterForm,
        filterDate,
        filterStart,
        filterEnd,
        false
    );

    // Nama file yang dipilih.
    if (fileInput && fileName) {
        fileInput.addEventListener('change', function () {
            const file = this.files && this.files.length
                ? this.files[0]
                : null;

            fileName.textContent = file
                ? 'File dipilih: ' + file.name
                : '';

            fileName.classList.toggle('is-visible', Boolean(file));
        });
    }
});
</script>

@endsection
