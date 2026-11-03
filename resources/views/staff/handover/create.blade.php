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

            <div class="p-3 rounded-3 bg-light-subtle border mb-4">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Dokumen Pendukung</small>
                @if($booking->dokumen_pendukung)
                    <div class="mt-2">
                        <a href="{{ route('booking.document', $booking) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen Pendukung (PDF)
                        </a>
                    </div>
                @else
                    <div class="text-muted small mt-1">Tidak ada lampiran dokumen PDF.</div>
                @endif
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('staff.handover.store', $booking) }}">
                @csrf
                @if($booking->detailAlat->isNotEmpty())
                    <h6 class="fw-bold mb-3">Kondisi awal alat — per kode barang</h6>
                    <p class="text-muted small">Centang tepat sebanyak unit yang dipinjam, lalu tulis kondisi awal tiap unit yang dicentang. Kode yang tercantum di sini adalah unit fisik yang benar-benar diserahkan, bukan kode jenis alat.</p>
                    @foreach($booking->detailAlat as $detail)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="fw-semibold mb-1">
                                {{ $detail->alat?->nama_alat ?? 'Alat' }} — pilih {{ $detail->jumlah }} unit
                                <small class="text-muted">(kode jenis: {{ $detail->alat?->kode_inventaris ?? '-' }})</small>
                            </div>
                            @error('units.'.$detail->id)
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                            @forelse($detail->alat?->availableUnits ?? [] as $unit)
                                <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center mb-2">
                                    <div class="form-check" style="min-width: 160px;">
                                        <input class="form-check-input" type="checkbox"
                                               id="unit-{{ $detail->id }}-{{ $unit->id }}"
                                               name="units[{{ $detail->id }}][]" value="{{ $unit->id }}"
                                               {{ in_array($unit->id, old('units.'.$detail->id, [])) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="unit-{{ $detail->id }}-{{ $unit->id }}">
                                            {{ $unit->kode_unit }}
                                        </label>
                                    </div>
                                    <input type="text"
                                           name="unit_conditions[{{ $detail->id }}][{{ $unit->id }}]"
                                           class="form-control form-control-sm @error('unit_conditions.'.$detail->id.'.'.$unit->id) is-invalid @enderror"
                                           value="{{ old('unit_conditions.'.$detail->id.'.'.$unit->id) }}"
                                           maxlength="100" placeholder="Kondisi awal unit ini (mis. baik)">
                                    @error('unit_conditions.'.$detail->id.'.'.$unit->id)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @empty
                                <div class="text-danger small">Tidak ada unit tersedia untuk alat ini. Jalankan migrasi unit dulu, atau tambah unit di Manajemen Alat.</div>
                            @endforelse
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
