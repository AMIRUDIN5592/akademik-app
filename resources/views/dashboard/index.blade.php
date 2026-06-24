@extends('layouts.app')

@section('content')
@php
    use App\Helpers\RoleHelper;
@endphp

<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h3>Dashboard Akademik</h3>
    <p class="text-muted">Login sebagai: {{ RoleHelper::label(auth()->user()->role) }}</p>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Total Mahasiswa</h6><h2>{{ $totalMahasiswa }}</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Total Mata Kuliah</h6><h2>{{ $totalMataKuliah }}</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Total Nilai</h6><h2>{{ $totalNilai }}</h2>
            </div></div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Rata-rata Nilai</h6><h2>{{ $rataRata }}</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Nilai Tertinggi</h6><h2>{{ $tertinggi }}</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Nilai Terendah</h6><h2>{{ $terendah }}</h2>
            </div></div>
        </div>
    </div>

    <h5 class="mt-4">Nilai Terbaru</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Mahasiswa</th>
                    <th>Mata Kuliah</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($nilaiTerbaru as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $row->mataKuliah->nama_mk ?? '-' }}</td>
                        <td>{{ $row->nilai }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
