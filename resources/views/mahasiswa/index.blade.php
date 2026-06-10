@extends('layouts.app')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="fw-bold mb-1">Data Mahasiswa</h3>
            <p class="text-muted mb-0">
                Menampilkan data mahasiswa beserta jurusan, kartu mahasiswa, dan mata kuliah.
            </p>
            <p class="text-muted mb-0">
                <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm">
                    Tambah Data
                </a>
            </p>
        </div>
    </div>

    <div class="mb-3">
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary btn-sm">Semua</a>
        <a href="{{ route('mahasiswa.filterJurusan', 'TI') }}" class="btn btn-primary btn-sm">TI</a>
        <a href="{{ route('mahasiswa.filterJurusan', 'SI') }}" class="btn btn-success btn-sm">SI</a>
        <a href="{{ route('mahasiswa.filterJurusan', 'MI') }}" class="btn btn-warning btn-sm">MI</a>
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
                            <th>No Kartu</th>
                            <th>Mata Kuliah</th>
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
                                <td>{{ $mahasiswa->kartuMahasiswa->nomor_kartu ?? '-' }}</td>
                                <td>
                                    @foreach ($mahasiswa->mataKuliah as $mk)
                                        <span class="badge bg-info text-dark mb-1">
                                            {{ $mk->nama_mk }}: {{ $mk->pivot->nilai }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('mahasiswa.detail', $mahasiswa->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Data mahasiswa belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
