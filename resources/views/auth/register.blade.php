@extends('layouts.app')

@section('title', 'Registrasi Mahasiswa - SIP-LAB')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-7">
            <div class="card-custom p-4 p-md-5 shadow-sm border" style="border-radius: 20px;">
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <div class="brand-logo-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem; border-radius: 14px;">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Pendaftaran Akun Mahasiswa</h3>
                    <p class="text-muted small mb-0">Lengkapi identitas akademik Anda untuk mengajukan izin peminjaman laboratorium</p>
                </div>

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="nama" class="form-control border-start-0 ps-0" placeholder="Contoh: Khofid Ramadhan" value="{{ old('nama') }}" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alamat Email Aktif</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="mahasiswa@kampus.ac.id" value="{{ old('email') }}" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                    <i class="bi bi-card-text"></i>
                                </span>
                                <input type="text" name="nim" class="form-control border-start-0 ps-0" placeholder="Contoh: 240101082" value="{{ old('nim') }}" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Program Studi / Jurusan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                    <i class="bi bi-mortarboard"></i>
                                </span>
                                <input type="text" name="program_study" class="form-control border-start-0 ps-0" placeholder="Teknik Informatika" value="{{ old('program_study') }}" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Minimal 8 karakter" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                    <i class="bi bi-shield-lock"></i>
                                </span>
                                <input type="password" name="password_confirmation" class="form-control border-start-0 ps-0" placeholder="Ulangi kata sandi" required style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-brand-primary w-100 py-2 justify-content-center mt-4 fw-bold" type="submit" style="border-radius: 12px; font-size: 0.95rem;">
                        <i class="bi bi-person-check"></i> Selesaikan Pendaftaran
                    </button>

                    <div class="text-center pt-3 border-top mt-4">
                        <span class="text-muted small">Sudah memiliki akun terdaftar?</span>
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold small ms-1">Masuk ke Portal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
