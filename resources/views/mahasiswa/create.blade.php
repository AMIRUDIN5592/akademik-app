@extends('layouts.app')
@section('content')
   
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Tambah Mahasiswa
                        @if($errors->any())
                            <div class="alert alert-danger mt-2">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">

                        <form action="{{ route('mahasiswa.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="umur" class="form-label">Umur</label>
                                <input type="number" class="form-control" id="umur" name="umur" value="{{ old('umur') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="jurusan" class="form-label">Jurusan</label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan" value="{{ old('jurusan') }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    
@endsection
