@extends('layouts.app')

@section('content')
    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1">Data Mata Kuliah</h3>
                    <p class="text-muted mb-0">Kelola data mata kuliah.</p>
                </div>
                <a href="{{ route('mata-kuliah.create') }}" class="btn btn-primary">
                    Tambah Mata Kuliah
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Jumlah Mahasiswa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mataKuliahs as $mk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mk->kode_mk }}</td>
                                    <td>{{ $mk->nama_mk }}</td>
                                    <td>{{ $mk->sks }}</td>
                                    <td>{{ $mk->mahasiswa_count }}</td>
                                    <td>
                                        <a href="{{ route('mata-kuliah.show', $mk->id) }}" class="btn btn-sm btn-outline-info">
                                            Detail
                                        </a>
                                        <a href="{{ route('mata-kuliah.edit', $mk->id) }}" class="btn btn-sm btn-outline-warning">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Data belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
