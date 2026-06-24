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
        ];
    }

    public function getLatestNilai(int $limit = 5): Collection
    {
        return Nilai::with(['mahasiswa', 'mataKuliah'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getStatistikNilai(): array
    {
        return [
            'rataRata' => round((float) Nilai::avg('nilai'), 2),
            'tertinggi' => Nilai::max('nilai') ?? 0,
            'terendah' => Nilai::min('nilai') ?? 0,
        ];
    }
}
