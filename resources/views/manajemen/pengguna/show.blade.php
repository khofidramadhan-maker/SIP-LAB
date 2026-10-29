@extends('layouts.app')

@section('title', 'Detail Pengguna - SIP-LAB')
@section('header-title', 'Detail Pengguna')
@section('header-sub', 'Informasi akun dan riwayat perubahan data pengguna')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">Informasi Akun</h5>
                <div class="d-flex gap-2">
                    @if(!in_array($user->role, ['kepala_lab']))
                        <a href="{{ route('staff.pengguna.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Ubah
                        </a>
                    @endif
                    <a href="{{ route('staff.pengguna.index') }}" class="btn btn-sm btn-brand-outline"><i class="bi bi-arrow-left"></i></a>
                </div>
            </div>

            <div class="mb-3 p-3 rounded-3 d-flex align-items-center gap-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width:48px;height:48px;background:#3b82f6;font-size:1.1rem;">
                    {{ strtoupper(substr($user->nama ?? $user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-bold text-dark">{{ $user->nama ?? $user->name }}</div>
                    <small class="text-muted">@{{ $user->username }}</small>
                </div>
            </div>

            @php $roleLabels = ['mahasiswa'=>'Mahasiswa','staff'=>'Staf Sarpras','kepala_lab'=>'Kepala Laboratorium']; @endphp

            <dl class="row g-0 small">
                <dt class="col-5 text-muted fw-semibold py-2 border-bottom">Email</dt>
                <dd class="col-7 py-2 border-bottom text-dark" style="word-break:break-all;">{{ $user->email }}</dd>

                <dt class="col-5 text-muted fw-semibold py-2 border-bottom">NIM / NIP</dt>
                <dd class="col-7 py-2 border-bottom text-dark">{{ $user->nim ?? '-' }}</dd>

                <dt class="col-5 text-muted fw-semibold py-2 border-bottom">Role</dt>
                <dd class="col-7 py-2 border-bottom">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2" style="font-size:.72rem;">
                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                    </span>
                </dd>

                <dt class="col-5 text-muted fw-semibold py-2 border-bottom">Status</dt>
                <dd class="col-7 py-2 border-bottom">
                    @if(($user->status_aktif ?? 'aktif') === 'aktif')
                        <span class="badge-status badge-success">Aktif</span>
                    @else
                        <span class="badge-status badge-warning">Nonaktif</span>
                    @endif
                </dd>

                <dt class="col-5 text-muted fw-semibold py-2 border-bottom">Total Pengajuan</dt>
                <dd class="col-7 py-2 border-bottom text-dark">{{ $user->peminjaman()->count() }} pengajuan</dd>

                <dt class="col-5 text-muted fw-semibold py-2">Terdaftar</dt>
                <dd class="col-7 py-2 text-dark">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card-custom p-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Perubahan
            </h5>

            @forelse($riwayat as $log)
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                             style="width:36px;height:36px;background:{{ $log->jenis_aksi === 'tambah' ? '#10b981' : '#3b82f6' }};">
                            <i class="bi bi-{{ $log->jenis_aksi === 'tambah' ? 'plus' : 'pencil' }}" style="font-size:.8rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-dark small">{{ $log->aktivitas }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            Oleh: <strong>{{ $log->user?->nama ?? $log->user?->name ?? 'Sistem' }}</strong>
                            &bull; {{ \Carbon\Carbon::parse($log->waktu)->format('d/m/Y H:i') }}
                        </div>
                        @if($log->data_sebelum && $log->jenis_aksi === 'ubah')
                            <details class="mt-1">
                                <summary class="text-muted small" style="cursor:pointer;">Lihat perubahan</summary>
                                <div class="mt-2 p-2 rounded-2" style="background:#f8fafc;font-size:.72rem;">
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
                    </div>
                </div>
            @empty
                <p class="text-muted small text-center py-3">Belum ada riwayat perubahan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

