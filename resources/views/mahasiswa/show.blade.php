@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detail Mahasiswa</h4>
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-light btn-sm">
                        Kembali
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle">
                        <tr>
                            <th width="200" class="bg-light">Nama</th>
                            <td>{{ $mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Email</th>
                            <td>{{ $mahasiswa->email }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Umur</th>
                            <td>{{ $mahasiswa->umur }} tahun</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Jurusan</th>
                            <td>{{ $mahasiswa->jurusan }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tanggal Dibuat</th>
                            <td>{{ $mahasiswa->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Terakhir Diupdate</th>
                            <td>{{ $mahasiswa->updated_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                    <div class="mt-3">
                        <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-warning">
                            Edit Data
                        </a>
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
