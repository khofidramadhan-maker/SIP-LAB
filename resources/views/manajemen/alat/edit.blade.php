@extends('layouts.app')

@section('title', 'Ubah Alat - SIP-LAB')
@section('header-title', 'Ubah Data Alat')
@section('header-sub', 'Perbarui informasi alat laboratorium')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark mb-0">Edit: {{ $alat->nama_alat }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('staff.alat.show', $alat) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    <a href="{{ route('staff.alat.index') }}" class="btn btn-sm btn-brand-outline">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
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

            <form action="{{ route('staff.alat.update', $alat) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kode Inventaris <span class="text-danger">*</span></label>
                        <input type="text" name="kode_inventaris" class="form-control @error('kode_inventaris') is-invalid @enderror"
                               value="{{ old('kode_inventaris', $alat->kode_inventaris) }}" required>
                        @error('kode_inventaris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nama Alat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_alat" class="form-control @error('nama_alat') is-invalid @enderror"
                               value="{{ old('nama_alat', $alat->nama_alat) }}" required>
                        @error('nama_alat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="kategori" class="form-control @error('kategori') is-invalid @enderror"
                               value="{{ old('kategori', $alat->kategori) }}" required>
                        @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Total <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_total" class="form-control @error('jumlah_total') is-invalid @enderror"
                               value="{{ old('jumlah_total', $alat->jumlah_total) }}" min="0" required>
                        @error('jumlah_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kondisi <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-select @error('kondisi') is-invalid @enderror" required>
                            <option value="baik" {{ old('kondisi', $alat->kondisi) === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ old('kondisi', $alat->kondisi) === 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak berat" {{ old('kondisi', $alat->kondisi) === 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('kondisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="tersedia" {{ old('status', $alat->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="dipinjam" {{ old('status', $alat->status) === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="perbaikan" {{ old('status', $alat->status) === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('staff.alat.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

