@extends('layouts.app')

@section('content')

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h3 class="fw-bold mb-1">Statistik Mahasiswa per Jurusan</h3>
        <p class="text-muted mb-0">
            Contoh penggunaan query relasi dengan withCount().
        </p>
    </div>
</div>

<div class="row g-4">
    @foreach ($jurusans as $jurusan)
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
	       <h5 class="fw-bold">{{ $jurusan->nama_jurusan }}</h5>
                    <p class="text-muted mb-2">{{ $jurusan->kode_jurusan }}</p>
                    <h1 class="fw-bold text-primary">{{ $jurusan->mahasiswa_count }}</h1>
                    <p class="mb-0">Jumlah Mahasiswa</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection
