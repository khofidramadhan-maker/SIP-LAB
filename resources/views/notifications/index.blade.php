@extends('layouts.app')

@section('title', 'Pemberitahuan - SIP-LAB')
@section('header-title', 'Pusat Notifikasi')
@section('header-sub', 'Pantau perubahan status pengajuan, instruksi revisi, dan informasi jadwal laboratorium')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-card-icon stat-icon-purple" style="width: 44px; height: 44px; font-size: 1.25rem;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Semua Pemberitahuan</h5>
                        <small class="text-muted">Total {{ $notifications->count() }} pesan notifikasi sistem</small>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($notifications as $notification)
                    <div class="p-4 rounded-4 border bg-white d-flex align-items-start gap-3" style="border-color: #e2e8f0 !important; transition: all 0.15s ease;">
                        <div class="stat-card-icon stat-icon-blue flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                            <i class="bi bi-chat-left-dots-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                <span class="badge {{ $notification->sudah_dibaca ? 'bg-light text-muted border' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-2 py-1 rounded-pill small">
                                    {{ $notification->sudah_dibaca ? 'Dibaca' : 'Pemberitahuan Baru' }}
                                </span>
                                <span class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('d M Y, H:i') }})
                                </span>
                            </div>
                            <p class="text-dark small mb-2" style="line-height: 1.6; font-size: 0.95rem;">
                                {{ $notification->pesan }}
                            </p>
                            @if($notification->peminjaman_id)
                                <a href="{{ route('booking.show', $notification->peminjaman_id) }}" class="btn btn-sm btn-brand-outline py-1 px-3" style="font-size: 0.78rem;">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Lihat Rincian Pengajuan
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-3 text-light"></i>
                        <h6 class="fw-bold text-dark">Belum Ada Notifikasi</h6>
                        <p class="text-muted small mb-0">Pemberitahuan terkait status verifikasi dan persetujuan akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
