@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="card p-4">
    <h3 class="fw-bold mb-4">Formulir Pengajuan Peminjaman</h3>
    <form method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Judul Peminjaman</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Laboratorium</label>
                <select name="lab_room_id" class="form-select">
                    <option value="">Pilih ruang</option>
                    @foreach(\App\Models\LabRoom::all() as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Tujuan Penggunaan</label>
                <textarea name="purpose" class="form-control" rows="4" required></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Dosen Pembimbing</label>
                <input type="text" name="supervisor_name" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">NIP Dosen Pembimbing</label>
                <input type="text" name="supervisor_nip" class="form-control">
            </div>
            <div class="col-md-12">
                <label class="form-label">Dokumen Rekomendasi (PDF)</label>
                <input type="file" name="document" class="form-control" accept="application/pdf">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Kirim Pengajuan</button>
    </form>
</div>
@endsection
