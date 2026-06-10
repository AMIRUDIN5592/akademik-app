@extends('layouts.app')

@section('content')
    <div class="container py-4">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">Data Mahasiswa</div>
            <div class="card-body">
                <p><strong>NIM:</strong> {{ $mahasiswa->nim }}</p>
                <p><strong>Nama:</strong> {{ $mahasiswa->nama }}</p>
                <p><strong>Jurusan:</strong> {{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white fw-bold">Form Input Nilai</div>
            <div class="card-body">
                <form action="{{ route('nilai.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">

                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <select name="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror">
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach ($mataKuliahs as $mk)
                                <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                    {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                                </option>
                            @endforeach
                        </select>
                        @error('mata_kuliah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nilai</label>
                        <input type="number" name="nilai" value="{{ old('nilai') }}"
                            class="form-control @error('nilai') is-invalid @enderror">
                        @error('nilai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('mahasiswa.detail', $mahasiswa->id) }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-success">Simpan Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
