@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Daftar Mahasiswa</h1>
                <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary mb-3">Tambah Mahasiswa</a>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            <div class="card-body">
                <!-- Content for the card body can be added here -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Umur</th>
                            <th>Jurusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($mahasiswa as $index => $mhs)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $mhs->nama }}</td>
                                <td>{{ $mhs->email }}</td>
                                <td>{{ $mhs->umur }}</td>
                                <td>{{ $mhs->jurusan }}</td>
                                <td>
                                    <a href="{{ route('mahasiswa.show', $mhs->id) }}" class="btn btn-sm btn-info">Show</a>
                                    <a href="{{ route('mahasiswa.edit', $mhs->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('mahasiswa.destroy', $mhs->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
