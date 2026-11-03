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
                    <h6 class="fw-bold mb-3">Pemeriksaan alat — per kode barang</h6>
                    <p class="text-muted small">Periksa tiap unit yang dulu diserahkan. Centang "Rusak" dan tulis deskripsinya hanya untuk unit yang memang rusak, supaya laporan kerusakan menyebut kode unit yang tepat.</p>
                    @foreach($booking->detailAlat as $detail)
                        @if($detail->detailAlatUnits->isNotEmpty())
                            <div class="fw-semibold mt-3 mb-2">
                                {{ $detail->alat?->nama_alat ?? 'Alat' }} ({{ $detail->detailAlatUnits->count() }} unit diserahkan)
                            </div>
                            @foreach($detail->detailAlatUnits as $row)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="fw-semibold mb-1">{{ $row->unitAlat?->kode_unit ?? '-' }}</div>
                                    <div class="text-muted small mb-3">Kondisi awal: {{ $row->kondisi_awal ?? '-' }}</div>
                                    <div class="mb-3">
                                        <label for="unit-condition-{{ $detail->id }}-{{ $row->unit_alat_id }}" class="form-label">Kondisi akhir</label>
                                        <input id="unit-condition-{{ $detail->id }}-{{ $row->unit_alat_id }}" type="text"
                                               name="unit_conditions[{{ $detail->id }}][{{ $row->unit_alat_id }}]"
                                               class="form-control @error('unit_conditions.'.$detail->id.'.'.$row->unit_alat_id) is-invalid @enderror"
                                               value="{{ old('unit_conditions.'.$detail->id.'.'.$row->unit_alat_id) }}"
                                               maxlength="100" required>
                                        @error('unit_conditions.'.$detail->id.'.'.$row->unit_alat_id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox"
                                               id="unit-damaged-{{ $detail->id }}-{{ $row->unit_alat_id }}"
                                               name="unit_damaged[{{ $detail->id }}][{{ $row->unit_alat_id }}]" value="1"
                                               {{ old('unit_damaged.'.$detail->id.'.'.$row->unit_alat_id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="unit-damaged-{{ $detail->id }}-{{ $row->unit_alat_id }}">Unit ini rusak</label>
                                    </div>
                                    <div>
                                        <label for="unit-damage-{{ $detail->id }}-{{ $row->unit_alat_id }}" class="form-label">Deskripsi kerusakan (wajib bila unit ini rusak)</label>
                                        <textarea id="unit-damage-{{ $detail->id }}-{{ $row->unit_alat_id }}"
                                                  name="unit_damage_descriptions[{{ $detail->id }}][{{ $row->unit_alat_id }}]"
                                                  class="form-control @error('unit_damage_descriptions.'.$detail->id.'.'.$row->unit_alat_id) is-invalid @enderror"
                                                  rows="2" maxlength="2000">{{ old('unit_damage_descriptions.'.$detail->id.'.'.$row->unit_alat_id) }}</textarea>
                                        @error('unit_damage_descriptions.'.$detail->id.'.'.$row->unit_alat_id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="fw-semibold mb-2">
                                    {{ $detail->alat?->nama_alat ?? 'Alat' }} (Kode Alat: {{ $detail->alat?->kode_alat ?? '-' }} · {{ $detail->jumlah }} unit)
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
                        @endif
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
