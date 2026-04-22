@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Form Input Mahasiswa</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('mahasiswa.proses') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama') }}">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" id="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>

                <div class="mb-3">
                    <label for="umur" class="form-label">Umur</label>
                    <input type="number" id="umur" name="umur" class="form-control" value="{{ old('umur') }}">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}">
                    <div class="form-text">Gunakan huruf, angka, atau underscore. Minimal 4 karakter.</div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <div class="form-text">Minimal 8 karakter, huruf besar/kecil, angka, dan simbol.</div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">Pilih Role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="mahasiswa" {{ old('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Skills</label>
                    @foreach (['PHP', 'Laravel', 'JavaScript', 'MySQL'] as $skill)
                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="skill_{{ $loop->index }}"
                                name="skills[]"
                                value="{{ $skill }}"
                                class="form-check-input"
                                {{ in_array($skill, old('skills', [])) ? 'checked' : '' }}
                            >
                            <label for="skill_{{ $loop->index }}" class="form-check-label">{{ $skill }}</label>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label for="cv" class="form-label">CV</label>
                    <input type="file" id="cv" name="cv" class="form-control">
                    <div class="form-text">Format: PDF, DOC, atau DOCX. Maksimal 2 MB.</div>
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" id="foto" name="foto" class="form-control">
                    <div class="form-text">Format gambar. Maksimal 2 MB.</div>
                </div>

                <button type="submit" class="btn btn-primary">Proses</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </form>
        </div>
    </div>
</div>
@endsection
