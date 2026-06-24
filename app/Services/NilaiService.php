<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use Illuminate\Database\Eloquent\Collection;

class NilaiService
{
    public function getMahasiswasForIndex(): Collection
    {
        return Mahasiswa::with(['jurusan', 'mataKuliah'])
            ->orderBy('nama')
            ->get();
    }

    public function getMahasiswaForCreate(int|string $mahasiswaId): Mahasiswa
    {
        return Mahasiswa::with('jurusan')->findOrFail($mahasiswaId);
    }

    public function getMataKuliahsForForm(): Collection
    {
        return MataKuliah::orderBy('nama_mk')->get();
    }

    public function findForEdit(int|string $nilaiId): Nilai
    {
        return Nilai::with(['mahasiswa', 'mataKuliah'])->findOrFail($nilaiId);
    }

    public function find(int|string $nilaiId): Nilai
    {
        return Nilai::findOrFail($nilaiId);
    }

    public function hasDuplicate(
        int $mahasiswaId,
        int $mataKuliahId,
        ?int $ignoredNilaiId = null
    ): bool {
        return Nilai::where('mahasiswa_id', $mahasiswaId)
            ->where('mata_kuliah_id', $mataKuliahId)
            ->when($ignoredNilaiId, fn ($query) => $query->where('id', '!=', $ignoredNilaiId))
            ->exists();
    }

    public function create(array $data): Nilai
    {
        return Nilai::create([
            'mahasiswa_id' => $data['mahasiswa_id'],
            'mata_kuliah_id' => $data['mata_kuliah_id'],
            'nilai' => $data['nilai'],
        ]);
    }

    public function update(Nilai $nilai, array $data): bool
    {
        return $nilai->update([
            'mata_kuliah_id' => $data['mata_kuliah_id'],
            'nilai' => $data['nilai'],
        ]);
    }

    public function delete(Nilai $nilai): ?bool
    {
        return $nilai->delete();
    }
}
