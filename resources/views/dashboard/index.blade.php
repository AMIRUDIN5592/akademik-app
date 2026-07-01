@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Dashboard Akademik</h3>
            <p class="text-muted mb-0">Ringkasan data mahasiswa, mata kuliah, dan nilai.</p>
        </div>
        @if (in_array(auth()->user()->role, ['admin', 'dosen'], true))
            <a href="{{ route('laporan.nilai') }}" class="btn btn-primary">
                <i class="fas fa-chart-column me-1"></i> Buka Laporan Nilai
            </a>
        @endif
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            ['label' => 'Total Mahasiswa', 'value' => $totalMahasiswa, 'icon' => 'fa-user-graduate'],
            ['label' => 'Total Mata Kuliah', 'value' => $totalMataKuliah, 'icon' => 'fa-book'],
            ['label' => 'Total Nilai', 'value' => $totalNilai, 'icon' => 'fa-pen-to-square'],
        ] as $item)
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">{{ $item['label'] }}</small>
                            <h2 class="fw-bold mb-0">{{ $item['value'] }}</h2>
                        </div>
                        <i class="fas {{ $item['icon'] }} fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Rata-rata Nilai', 'value' => $rataRataNilai],
            ['label' => 'Nilai Tertinggi', 'value' => $nilaiTertinggi],
            ['label' => 'Nilai Terendah', 'value' => $nilaiTerendah],
        ] as $item)
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">{{ $item['label'] }}</small>
                        <h3 class="fw-bold mb-0">{{ $item['value'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Distribusi Nilai</h5>
                    <canvas id="chartDistribusiNilai" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Mahasiswa per Jurusan</h5>
                    <canvas id="chartMahasiswaJurusan" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Nilai Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>Mata Kuliah</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nilaiTerbaru as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->mahasiswa->nama ?? '-' }}</td>
                                <td>{{ $row->mataKuliah->nama_mk ?? '-' }}</td>
                                <td>{{ $row->nilai }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartDistribusiNilai'), {
    type: 'bar',
    data: {
        labels: @json($labelDistribusiNilai),
        datasets: [{
            label: 'Jumlah Mahasiswa',
            data: @json($dataDistribusiNilai),
            backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#6c757d', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});

new Chart(document.getElementById('chartMahasiswaJurusan'), {
    type: 'doughnut',
    data: {
        labels: @json($labelJurusan),
        datasets: [{
            label: 'Jumlah Mahasiswa',
            data: @json($dataJurusan),
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#0dcaf0']
        }]
    },
    options: { responsive: true }
});
</script>
@endpush
