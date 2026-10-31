@extends('layouts.app')

@section('title', 'Riwayat Perubahan Data - SIP-LAB')
@section('header-title', 'Riwayat Perubahan Data')
@section('header-sub', 'Log aktivitas perubahan data ruangan, pengguna, dan alat laboratorium')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Log Aktivitas Perubahan Data</h5>
            <p class="text-muted small mb-0">Tercatat {{ $logs->total() }} aktivitas</p>
        </div>
        <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-brand-outline">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('staff.riwayat') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-auto">
            <label class="form-label mb-0 fw-semibold small text-muted text-nowrap">Filter jenis data:</label>
            <select name="jenis_data" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="ruangan" {{ $jenisData === 'ruangan' ? 'selected' : '' }}>Ruangan</option>
                <option value="pengguna" {{ $jenisData === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                <option value="alat" {{ $jenisData === 'alat' ? 'selected' : '' }}>Alat</option>
            </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="date_from" class="form-label mb-1 fw-semibold small text-muted">Dari tanggal</label>
                <input id="date_from" type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3">
                <label for="date_to" class="form-label mb-1 fw-semibold small text-muted">Sampai tanggal</label>
                <input id="date_to" type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-brand-primary">Terapkan</button>
                <a href="{{ route('staff.riwayat') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="table-custom-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Staf</th>
                    <th>Jenis Data</th>
                    <th>Identitas</th>
                    <th>Aksi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">
                            <div class="fw-semibold text-dark" style="font-size:.82rem;">
                                {{ \Carbon\Carbon::parse($log->waktu)->format('d/m/Y') }}
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($log->waktu)->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:.82rem;">
                                {{ $log->user?->nama ?? $log->user?->name ?? 'Sistem' }}
                            </div>
                            <small class="text-muted">{{ $log->user?->username ?? '-' }}</small>
                        </td>
                        <td>
                            @php
                                $jdColor = ['ruangan'=>'primary','pengguna'=>'success','alat'=>'warning'];
                                $color = $jdColor[$log->jenis_data] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle rounded-pill px-2" style="font-size:.72rem;">
                                {{ ucfirst($log->jenis_data) }}
                            </span>
                        </td>
                        <td>
                            <code style="font-size:.78rem;">{{ $log->kode_identitas }}</code>
                        </td>
                        <td>
                            @if($log->jenis_aksi === 'tambah')
                                <span class="badge-status badge-success">Tambah</span>
                            @else
                                <span class="badge-status badge-pending">Ubah</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:.78rem;" class="text-muted">{{ $log->aktivitas }}</span>
                            @if($log->data_sebelum && $log->jenis_aksi === 'ubah')
                                <details class="mt-1">
                                    <summary class="text-muted" style="cursor:pointer;font-size:.72rem;">Lihat perubahan</summary>
                                    <div class="mt-1 p-2 rounded-2" style="background:#f8fafc;font-size:.70rem;">
                                        @foreach($log->data_sesudah ?? [] as $k => $v)
                                            @if(isset($log->data_sebelum[$k]) && $log->data_sebelum[$k] != $v)
                                                <div class="mb-1">
                                                    <span class="text-muted">{{ $k }}:</span>
                                                    <span class="text-danger text-decoration-line-through">{{ $log->data_sebelum[$k] }}</span>
                                                    <i class="bi bi-arrow-right mx-1"></i>
                                                    <span class="text-success">{{ $v }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat perubahan data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="mt-3">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
