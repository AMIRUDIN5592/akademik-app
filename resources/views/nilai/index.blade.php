@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="fw-bold mb-1">Kelola Nilai Mahasiswa</h3>
            <p class="text-muted mb-0">Pilih mahasiswa untuk melihat dan mengelola nilainya.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Jumlah Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $mahasiswa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mahasiswa->nim }}</td>
                                <td>{{ $mahasiswa->nama }}</td>
                                <td>{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>{{ $mahasiswa->mataKuliah->count() }}</td>
                                <td>
                                    <a href="{{ route('nilai.create', $mahasiswa) }}" class="btn btn-primary btn-sm">
                                        Input Nilai
                                    </a>
                                    @if (auth()->user()->role === 'admin')
                                        <a href="{{ route('mahasiswa.detail', $mahasiswa) }}" class="btn btn-outline-secondary btn-sm">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Data mahasiswa belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
