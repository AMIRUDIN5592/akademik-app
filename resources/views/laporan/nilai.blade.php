@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Laporan Nilai</h3>
            <p class="text-muted mb-0">Filter dan rekap data nilai mahasiswa.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.nilai') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="mahasiswa_id">Mahasiswa</label>
                        <select id="mahasiswa_id" name="mahasiswa_id" class="form-select">
                            <option value="">Semua Mahasiswa</option>
                            @foreach ($mahasiswas as $mahasiswa)
                                <option value="{{ $mahasiswa->id }}" @selected(($filters['mahasiswa_id'] ?? '') == $mahasiswa->id)>
                                    {{ $mahasiswa->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="mata_kuliah_id">Mata Kuliah</label>
                        <select id="mata_kuliah_id" name="mata_kuliah_id" class="form-select">
                            <option value="">Semua Mata Kuliah</option>
                            @foreach ($mataKuliahs as $mataKuliah)
                                <option value="{{ $mataKuliah->id }}" @selected(($filters['mata_kuliah_id'] ?? '') == $mataKuliah->id)>
                                    {{ $mataKuliah->nama_mk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="nilai_min">Nilai Min</label>
                        <input
                            id="nilai_min"
                            type="number"
                            name="nilai_min"
                            class="form-control @error('nilai_min') is-invalid @enderror"
                            value="{{ $filters['nilai_min'] ?? '' }}"
                            min="0"
                            max="100"
                        >
                        @error('nilai_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="nilai_max">Nilai Max</label>
                        <input
                            id="nilai_max"
                            type="number"
                            name="nilai_max"
                            class="form-control @error('nilai_max') is-invalid @enderror"
                            value="{{ $filters['nilai_max'] ?? '' }}"
                            min="0"
                            max="100"
                        >
                        @error('nilai_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
                <a href="{{ route('laporan.nilai') }}" class="btn btn-outline-danger btn-sm mt-3">
                    Reset Filter
                </a>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Total Data', 'value' => $summary['totalData']],
            ['label' => 'Rata-rata', 'value' => $summary['rataRata']],
            ['label' => 'Nilai Tertinggi', 'value' => $summary['nilaiTertinggi']],
            ['label' => 'Nilai Terendah', 'value' => $summary['nilaiTerendah']],
        ] as $item)
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">{{ $item['label'] }}</small>
                        <h4 class="fw-bold mb-0">{{ $item['value'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Mahasiswa</th>
                            <th>Jurusan</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Nilai</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nilai as $row)
                            @php
                                $badgeClass = match ($row->grade) {
                                    'A' => 'bg-success',
                                    'B' => 'bg-primary',
                                    'C' => 'bg-warning text-dark',
                                    'D' => 'bg-secondary',
                                    default => 'bg-danger',
                                };
                            @endphp
                            <tr>
                                <td>{{ $nilai->firstItem() + $loop->index }}</td>
                                <td>{{ $row->mahasiswa->nim ?? '-' }}</td>
                                <td>{{ $row->mahasiswa->nama ?? '-' }}</td>
                                <td>{{ $row->mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>{{ $row->mataKuliah->nama_mk ?? '-' }}</td>
                                <td>{{ $row->mataKuliah->sks ?? '-' }}</td>
                                <td>{{ $row->nilai }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ $row->grade }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $nilai->links() }}
        </div>
    </div>
</div>
@endsection
