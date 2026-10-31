@extends('layouts.app')

@section('title', 'Form Pengembalian - SIP-LAB')
@section('header-title', 'Formulir Pengembalian')
@section('header-sub', 'Catat kondisi akhir alat yang dikembalikan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="card-custom p-4 p-md-5">
            <h5 class="fw-bold mb-1">{{ $booking->kode_peminjaman }}</h5>
            <p class="text-muted mb-4">
                Peminjam: <strong>{{ $booking->user->nama ?? $booking->user->name }}</strong>
                · {{ $booking->user->nim ?? $booking->user->email }}
            </p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('staff.returns.store', $booking) }}">
                @csrf
                @if($booking->detailAlat->isNotEmpty())
                    <h6 class="fw-bold mb-3">Pemeriksaan alat</h6>
                    @foreach($booking->detailAlat as $detail)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="fw-semibold mb-2">
                                {{ $detail->alat?->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} unit)
                            </div>
                            <div class="mb-3">
                                <label for="condition-{{ $detail->id }}" class="form-label">Kondisi akhir</label>
                                <input id="condition-{{ $detail->id }}" type="text"
                                       name="conditions[{{ $detail->id }}]"
                                       class="form-control @error('conditions.'.$detail->id) is-invalid @enderror"
                                       value="{{ old('conditions.'.$detail->id) }}"
                                       maxlength="100" required>
                                @error('conditions.'.$detail->id)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label for="damage-{{ $detail->id }}" class="form-label">Deskripsi kerusakan (isi bila ada)</label>
                                <textarea id="damage-{{ $detail->id }}"
                                          name="damage_descriptions[{{ $detail->id }}]"
                                          class="form-control" rows="2" maxlength="2000">{{ old('damage_descriptions.'.$detail->id) }}</textarea>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">Peminjaman ini hanya mencakup ruangan, tanpa alat tambahan.</p>
                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('staff.returns.index') }}" class="btn btn-brand-outline">Batal</a>
                    <button type="submit" class="btn btn-brand-primary">Simpan Pengembalian</button>
                </div>
            </form>
            <p class="text-muted small mt-3 mb-0">
                Tidak ada aturan nominal sanksi yang didefinisikan dalam sistem; keterlambatan dicatat tanpa membuat sanksi otomatis.
            </p>
        </div>
    </div>
</div>
@endsection
