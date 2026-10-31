@extends('layouts.app')

@section('title', 'Form Serah Terima - SIP-LAB')
@section('header-title', 'Formulir Serah Terima')
@section('header-sub', 'Catat kondisi alat saat diserahkan kepada mahasiswa')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="card-custom p-4 p-md-5">
            <h5 class="fw-bold mb-1">{{ $booking->kode_peminjaman }}</h5>
            <p class="text-muted mb-4">
                Penerima: <strong>{{ $booking->user->nama ?? $booking->user->name }}</strong>
                · {{ $booking->user->nim ?? $booking->user->email }}
            </p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('staff.handover.store', $booking) }}">
                @csrf
                @if($booking->detailAlat->isNotEmpty())
                    <h6 class="fw-bold mb-3">Kondisi awal alat</h6>
                    @foreach($booking->detailAlat as $detail)
                        <div class="mb-3">
                            <label for="condition-{{ $detail->id }}" class="form-label">
                                {{ $detail->alat?->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} unit)
                            </label>
                            <input id="condition-{{ $detail->id }}" type="text"
                                   name="conditions[{{ $detail->id }}]"
                                   class="form-control @error('conditions.'.$detail->id) is-invalid @enderror"
                                   value="{{ old('conditions.'.$detail->id) }}"
                                   maxlength="100" required>
                            @error('conditions.'.$detail->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">Peminjaman ini hanya mencakup ruangan, tanpa alat tambahan.</p>
                @endif

                <div class="mb-4">
                    <label for="notes" class="form-label">Catatan serah terima (opsional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3" maxlength="2000">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('staff.handover.index') }}" class="btn btn-brand-outline">Batal</a>
                    <button type="submit" class="btn btn-brand-primary">Simpan Serah Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
