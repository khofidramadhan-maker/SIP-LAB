@extends('layouts.app')

@section('title', 'Ajukan Peminjaman - SIP-LAB')
@section('header-title', 'Formulir Pengajuan Peminjaman')
@section('header-sub', 'Lengkapi detail kegiatan, jadwal penggunaan, serta berkas dokumen pendukung')

@section('content')
<div class="row g-4">
    <!-- Form Area -->
    <div class="col-lg-8">
        <div class="card-custom p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                <div class="stat-card-icon stat-icon-blue" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Permohonan Izin Laboratorium & Alat</h5>
                    <small class="text-muted">Pastikan data yang diisikan sesuai dengan proposal atau surat tugas Anda</small>
                </div>
            </div>

            <form method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Section 1: Informasi Dasar -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-circle" style="width: 22px; height: 22px; font-size: 0.72rem; display: flex; align-items: center; justify-content: center;">1</span>
                        Informasi Kegiatan & Ruang Laboratorium
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Keperluan / Judul Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="keperluan" class="form-control" placeholder="Contoh: Pengujian Model Deep Learning untuk Skripsi Tugas Akhir" value="{{ old('keperluan') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Pilihan Ruang Laboratorium <span class="text-danger">*</span></label>
                            <select name="ruangan_id" class="form-select" required>
                                <option value="">-- Pilih Ruang Laboratorium --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ (old('ruangan_id', request('ruangan_id')) == $room->id) ? 'selected' : '' }}>
                                        {{ $room->nama_ruangan }} ({{ $room->kode_ruangan }}) &bull; Kapasitas: {{ $room->kapasitas }} org &bull; {{ $room->lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Waktu Pelaksanaan -->
                <div class="mb-4 pt-3 border-top">
                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-circle" style="width: 22px; height: 22px; font-size: 0.72rem; display: flex; align-items: center; justify-content: center;">2</span>
                        Jadwal & Waktu Peminjaman
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pinjam" class="form-control" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', '08:00') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', '16:00') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Peminjaman Alat Pendukung (Opsional) -->
                <div class="mb-4 pt-3 border-top">
                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-circle" style="width: 22px; height: 22px; font-size: 0.72rem; display: flex; align-items: center; justify-content: center;">3</span>
                        Peralatan Tambahan & Dokumen Pendukung
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Pilih Alat (Opsional)</label>
                            <select name="alat_id" class="form-select">
                                <option value="">-- Tidak Meminjam Alat Khusus --</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ old('alat_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->nama_alat }} ({{ $asset->kode_inventaris }}) &bull; Tersedia: {{ $asset->jumlah_total }} unit
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah Alat</label>
                            <input type="number" name="jumlah_alat" class="form-control" min="1" value="{{ old('jumlah_alat', 1) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Dokumen Pendukung / Surat Rekomendasi (Format PDF)</label>
                            <input type="file" name="document" class="form-control" accept="application/pdf">
                            <small class="text-muted d-block mt-1">Unggah berkas rekomendasi atau surat tugas bertanda tangan dosen pembimbing.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-brand-outline">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-brand-primary px-4 py-2">
                        <i class="bi bi-send-fill"></i> Kirimkan Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Guidelines / Helpful Notes -->
    <div class="col-lg-4">
        <div class="card-custom p-4 mb-4" style="background: #ffffff;">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-primary"></i> Alur Persetujuan Bertingkat
            </h6>
            
            <div class="position-relative ps-4 mb-3" style="border-left: 2px solid #e2e8f0;">
                <div class="mb-3 position-relative">
                    <span class="badge bg-primary rounded-circle position-absolute" style="left: -25px; top: 0; width: 18px; height: 18px; font-size: 0.65rem; display: flex; align-items: center; justify-content: center;">1</span>
                    <div class="fw-bold text-dark small">Pengajuan Mahasiswa</div>
                    <small class="text-muted">Formulir & dokumen pendukung dikirimkan.</small>
                </div>
                <div class="mb-3 position-relative">
                    <span class="badge bg-secondary rounded-circle position-absolute" style="left: -25px; top: 0; width: 18px; height: 18px; font-size: 0.65rem; display: flex; align-items: center; justify-content: center;">2</span>
                    <div class="fw-bold text-dark small">Pemeriksaan Staf Sarpras</div>
                    <small class="text-muted">Verifikasi tingkat I kelayakan ruang & alat.</small>
                </div>
                <div class="position-relative">
                    <span class="badge bg-secondary rounded-circle position-absolute" style="left: -25px; top: 0; width: 18px; height: 18px; font-size: 0.65rem; display: flex; align-items: center; justify-content: center;">3</span>
                    <div class="fw-bold text-dark small">Pengesahan Kepala Lab</div>
                    <small class="text-muted">Keputusan final tingkat II izin penggunaan.</small>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe;">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-lightbulb text-warning me-1"></i> Informasi SOP</h6>
            <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                <li>Peminjaman dapat mencakup <strong>Ruang Lab</strong> dan <strong>Alat</strong> sekaligus.</li>
                <li>Gunakan instrumen sesuai petunjuk operasional di bawah pengawasan petugas lab.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
