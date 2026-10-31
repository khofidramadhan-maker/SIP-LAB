@extends('layouts.app')

@section('title', 'Profil Saya - SIP-LAB')
@section('header-title', 'Profil Saya')
@section('header-sub', 'Kelola informasi akun Anda dengan aman')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="card-custom p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="user-avatar-circle" style="width: 52px; height: 52px; font-size: 1.1rem;">
                    {{ strtoupper(substr($user->nama ?: $user->name, 0, 1)) }}
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">{{ $user->nama ?: $user->name }}</h5>
                    <span class="user-role-badge">{{ str_replace('_', ' ', $user->role) }}</span>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <h6 class="fw-bold mb-3">Informasi Akun</h6>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input id="nama" name="nama" type="text"
                           class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $user->nama ?: $user->name) }}" maxlength="100" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if($user->role === 'mahasiswa')
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input id="nim" name="nim" type="text"
                               class="form-control @error('nim') is-invalid @enderror"
                               value="{{ old('nim', $user->nim) }}" maxlength="20">
                        @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" maxlength="100" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <h6 class="fw-bold mb-1">Ubah Kata Sandi</h6>
                <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengganti kata sandi.</p>

                <div class="mb-3">
                    <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                    <input id="current_password" name="current_password" type="password"
                           class="form-control @error('current_password') is-invalid @enderror"
                           autocomplete="current-password">
                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Kata Sandi Baru</label>
                        <input id="password" name="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               class="form-control" autocomplete="new-password">
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <span class="text-muted small align-self-center">Peran akun tidak dapat diubah dari halaman profil.</span>
                    <button type="submit" class="btn btn-brand-primary">
                        <i class="bi bi-check2-circle"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
