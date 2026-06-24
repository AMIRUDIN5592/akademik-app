@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>Nilai Saya</h3>
    <p class="text-muted">Nilai yang terhubung dengan akun mahasiswa yang sedang login.</p>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode MK</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nilai as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->mataKuliah->kode_mk ?? '-' }}</td>
                            <td>{{ $row->mataKuliah->nama_mk ?? '-' }}</td>
                            <td>{{ $row->mataKuliah->sks ?? '-' }}</td>
                            <td>{{ $row->nilai }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Belum ada data nilai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
