<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function getSummary(): array
    {
        return [
            'totalMahasiswa' => Mahasiswa::count(),
            'totalMataKuliah' => MataKuliah::count(),
            'totalNilai' => Nilai::count(),
            'rataRataNilai' => round((float) (Nilai::avg('nilai') ?? 0), 2),
            'nilaiTertinggi' => Nilai::max('nilai') ?? 0,
            'nilaiTerendah' => Nilai::min('nilai') ?? 0,
        ];
    }

    public function getLatestNilai(int $limit = 5): Collection
    {
        return Nilai::with(['mahasiswa', 'mataKuliah'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getDistribusiNilai(): array
    {
        return [
            'A' => Nilai::whereBetween('nilai', [85, 100])->count(),
            'B' => Nilai::whereBetween('nilai', [75, 84])->count(),
            'C' => Nilai::whereBetween('nilai', [65, 74])->count(),
            'D' => Nilai::whereBetween('nilai', [50, 64])->count(),
            'E' => Nilai::whereBetween('nilai', [0, 49])->count(),
        ];
    }

    public function getMahasiswaPerJurusan(): Collection
    {
        return Mahasiswa::query()
            ->select('jurusans.nama_jurusan')
            ->selectRaw('COUNT(mahasiswas.id) as total')
            ->join('jurusans', 'jurusans.id', '=', 'mahasiswas.jurusan_id')
            ->groupBy('jurusans.nama_jurusan')
            ->orderBy('jurusans.nama_jurusan')
            ->get();
    }
}
