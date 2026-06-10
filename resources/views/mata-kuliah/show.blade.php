@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1">Detail Mata Kuliah</h3>
                    <p class="text-muted mb-0">{{ $mataKuliah->kode_mk }} - {{ $mataKuliah->nama_mk }}</p>
                </div>
                <a href="{{ route('mata-kuliah.edit', $mataKuliah->id) }}" class="btn btn-warning">Edit</a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-bold">Mahasiswa Peserta</div>
            <div class="card-body">
                <p><strong>SKS:</strong> {{ $mataKuliah->sks }}</p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mataKuliah->mahasiswa as $mahasiswa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mahasiswa->nim }}</td>
                                    <td>{{ $mahasiswa->nama }}</td>
                                    <td>{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
                                    <td>{{ $mahasiswa->pivot->nilai }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Belum ada mahasiswa yang mengambil mata kuliah ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection
