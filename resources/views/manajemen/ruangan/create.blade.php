@extends('layouts.app')

@section('title', 'Tambah Ruangan - SIP-LAB')
@section('header-title', 'Tambah Ruangan Baru')
@section('header-sub', 'Isi formulir berikut untuk menambahkan ruangan laboratorium')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark mb-0">Form Tambah Ruangan</h5>
                <a href="{{ route('staff.ruangan.index') }}" class="btn btn-sm btn-brand-outline">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e)
                            <li style="font-size:.875rem;">{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('staff.ruangan.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="kode_ruangan">Kode Ruangan <span class="text-danger">*</span></label>
                        <input type="text" id="kode_ruangan" name="kode_ruangan" class="form-control @error('kode_ruangan') is-invalid @enderror"
                               value="{{ old('kode_ruangan') }}" placeholder="mis. LAB-01" required>
                        @error('kode_ruangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="nama_ruangan">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" id="nama_ruangan" name="nama_ruangan" class="form-control @error('nama_ruangan') is-invalid @enderror"
                               value="{{ old('nama_ruangan') }}" placeholder="mis. Lab Komputer 1" required>
                        @error('nama_ruangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="kapasitas">Kapasitas (orang) <span class="text-danger">*</span></label>
                        <input type="number" id="kapasitas" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror"
                               value="{{ old('kapasitas', 30) }}" min="1" required>
                        @error('kapasitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="lokasi">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" id="lokasi" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror"
                               value="{{ old('lokasi') }}" placeholder="mis. Gedung A Lt. 2" required>
                        @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="fasilitas">Fasilitas / Keterangan</label>
                        <textarea id="fasilitas" name="fasilitas" class="form-control @error('fasilitas') is-invalid @enderror"
                                  rows="3" placeholder="mis. AC, Proyektor, 30 PC">{{ old('fasilitas') }}</textarea>
                        @error('fasilitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="status">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="tersedia" {{ old('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terpakai" {{ old('status') === 'terpakai' ? 'selected' : '' }}>Terpakai</option>
                            <option value="perbaikan" {{ old('status') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-floppy me-1"></i> Simpan Ruangan
                    </button>
                    <a href="{{ route('staff.ruangan.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

