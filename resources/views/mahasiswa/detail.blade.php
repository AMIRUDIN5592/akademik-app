@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="fw-bold mb-1">Detail Mahasiswa</h3>
            <p class="text-muted mb-0">Informasi lengkap mahasiswa dan nilai mata kuliah.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    Biodata Mahasiswa
                </div>
                <div class="card-body">
                    <p><strong>NIM:</strong> {{ $mahasiswa->nim }}</p>
                    <p><strong>Nama:</strong> {{ $mahasiswa->nama }}</p>
                    <p><strong>Email:</strong> {{ $mahasiswa->email }}</p>
                    <p><strong>Jenis Kelamin:</strong> {{ $mahasiswa->jenis_kelamin }}</p>
                    <p><strong>Alamat:</strong> {{ $mahasiswa->alamat }}</p>
                    <p><strong>Jurusan:</strong> {{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</p>
                    <p><strong>No Kartu:</strong> {{ $mahasiswa->kartuMahasiswa->nomor_kartu ?? '-' }}</p>

                    @if (!$mahasiswa->kartuMahasiswa)
                        <form action="{{ route('mahasiswa.generate-kartu', $mahasiswa->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                Generate Kartu
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                    <span>Nilai Mata Kuliah</span>
                    <a href="{{ route('nilai.create', $mahasiswa->id) }}" class="btn btn-light btn-sm">
                        Input Nilai
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mahasiswa->mataKuliah as $mk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mk->kode_mk }}</td>
                                    <td>{{ $mk->nama_mk }}</td>
                                    <td>{{ $mk->sks }}</td>
                                    <td>{{ $mk->pivot->nilai }}</td>
                                    <td>
                                        <a href="{{ route('nilai.edit', $mk->pivot->id) }}" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('nilai.destroy', $mk->pivot->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus nilai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada nilai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
