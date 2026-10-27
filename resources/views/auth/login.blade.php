@extends('layouts.app')

@section('title', 'Login SIP-Lab')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow-sm">
                <div class="text-center mb-4">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" style="width:52px;height:52px;">
                        <i class="bi bi-flask fs-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">SIP-Lab</h2>
                    <p class="text-muted mb-0">Sistem Informasi Peminjaman Laboratorium</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100 mb-3" type="submit">Masuk</button>
                    <div class="text-center">
                        <a href="{{ route('register') }}" class="text-decoration-none">Belum punya akun? Registrasi</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
