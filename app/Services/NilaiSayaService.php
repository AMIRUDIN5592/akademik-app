<?php

namespace App\Services;

use App\Models\Nilai;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class NilaiSayaService
{
    public function getByMahasiswaId(?int $mahasiswaId): Collection|BaseCollection
    {
        if (! $mahasiswaId) {
            return collect();
        }

        return Nilai::with('mataKuliah')
            ->where('mahasiswa_id', $mahasiswaId)
            ->latest()
            ->get();
    }
}
