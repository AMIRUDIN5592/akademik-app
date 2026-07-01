<?php

namespace App\Services;

use App\Models\Nilai;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class LaporanNilaiService
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['mahasiswa.jurusan', 'mataKuliah'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function getSummary(array $filters): array
    {
        $query = $this->filteredQuery($filters);

        return [
            'totalData' => (clone $query)->count(),
            'rataRata' => round((float) ((clone $query)->avg('nilai') ?? 0), 2),
            'nilaiTertinggi' => (clone $query)->max('nilai') ?? 0,
            'nilaiTerendah' => (clone $query)->min('nilai') ?? 0,
        ];
    }

    private function filteredQuery(array $filters): Builder
    {
        return Nilai::query()
            ->when(
                filled($filters['mahasiswa_id'] ?? null),
                fn (Builder $query) => $query->where('mahasiswa_id', $filters['mahasiswa_id'])
            )
            ->when(
                filled($filters['mata_kuliah_id'] ?? null),
                fn (Builder $query) => $query->where('mata_kuliah_id', $filters['mata_kuliah_id'])
            )
            ->when(
                filled($filters['nilai_min'] ?? null),
                fn (Builder $query) => $query->where('nilai', '>=', $filters['nilai_min'])
            )
            ->when(
                filled($filters['nilai_max'] ?? null),
                fn (Builder $query) => $query->where('nilai', '<=', $filters['nilai_max'])
            );
    }
}
