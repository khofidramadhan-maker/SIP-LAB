@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="text-muted small">Pengguna</div><h3 class="fw-bold mt-2 mb-0">{{ \App\Models\User::count() }}</h3></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="text-muted small">Laboratorium</div><h3 class="fw-bold mt-2 mb-0">{{ \App\Models\LabRoom::count() }}</h3></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="text-muted small">Aset</div><h3 class="fw-bold mt-2 mb-0">{{ \App\Models\Asset::count() }}</h3></div></div>
</div>
<div class="card p-4">
    <h4 class="fw-bold mb-3">Aktivitas Umum</h4>
    <p class="text-muted mb-0">Akses sistem admin digunakan untuk pengelolaan akun, aset, dan ringkasan data SIP-Lab.</p>
</div>
@endsection
