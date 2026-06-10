@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h3 class="fw-bold mb-1">Tambah Mata Kuliah</h3>
                <p class="text-muted mb-0">Tambahkan data mata kuliah baru.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-bold">Form Mata Kuliah</div>
            <div class="card-body">
                <form action="{{ route('mata-kuliah.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Kode Mata Kuliah</label>
                        <input type="text" name="kode_mk" value="{{ old('kode_mk') }}"
                            class="form-control @error('kode_mk') is-invalid @enderror">
                        @error('kode_mk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Mata Kuliah</label>
                        <input type="text" name="nama_mk" value="{{ old('nama_mk') }}"
                            class="form-control @error('nama_mk') is-invalid @enderror">
                        @error('nama_mk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SKS</label>
                        <input type="number" name="sks" value="{{ old('sks') }}"
                            class="form-control @error('sks') is-invalid @enderror">
                        @error('sks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
