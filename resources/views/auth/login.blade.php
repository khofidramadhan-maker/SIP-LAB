@extends('layouts.app')

@section('title', 'Masuk Akun - SIP-LAB')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card-custom p-4 p-md-5 shadow-sm border" style="border-radius: 20px;">
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <div class="brand-logo-icon mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.6rem; border-radius: 16px;">
                        <i class="bi bi-flask"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">SIP-LAB Portal</h3>
                    <p class="text-muted small mb-0">Sistem Informasi Peminjaman Laboratorium Riset</p>
                </div>

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" id="loginEmail" class="form-control border-start-0 ps-0" placeholder="nama@siplab.test" value="{{ old('email') }}" required autofocus style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="mb-1">
                            <label class="form-label mb-0">Kata Sandi</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" id="loginPassword" class="form-control border-start-0 ps-0" placeholder="••••••••" required style="border-radius: 0 12px 12px 0;" value="password">
                        </div>
                    </div>

                    <button class="btn btn-brand-primary w-100 py-2 justify-content-center mb-3 fw-bold" type="submit" style="border-radius: 12px; font-size: 0.95rem;">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                    </button>

                    <div class="text-center pt-2 border-top">
                        <span class="text-muted small">Belum memiliki akun mahasiswa?</span>
                        <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold small ms-1">Daftar Akun Baru</a>
                    </div>
                </form>

                <!-- Quick Demo Switcher -->
                <div class="mt-4 pt-3 border-top">
                    <div class="text-muted small fw-semibold text-center mb-2" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        Pilih Akun Cepat (Demo Mode):
                    </div>
                    <div class="d-grid gap-2 d-flex flex-wrap justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" onclick="fillDemo('mahasiswa@siplab.test')">
                            <i class="bi bi-person me-1"></i> Mahasiswa
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" onclick="fillDemo('staff@siplab.test')">
                            <i class="bi bi-shield-check me-1"></i> Staf Sarpras
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" onclick="fillDemo('kepala@siplab.test')">
                            <i class="bi bi-award me-1"></i> Kepala Lab
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillDemo(email) {
        document.getElementById('loginEmail').value = email;
        document.getElementById('loginPassword').value = 'password';
    }
</script>
@endsection
