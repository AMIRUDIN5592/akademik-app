<?php

namespace App\Http\Controllers;

use App\Http\Requests\NilaiRequest;
use App\Services\NilaiService;

class NilaiController extends Controller
{
    public function index(NilaiService $nilaiService)
    {
        $mahasiswas = $nilaiService->getMahasiswasForIndex();

        return view('nilai.index', compact('mahasiswas'));
    }

    public function create($mahasiswa_id, NilaiService $nilaiService)
    {
        $mahasiswa = $nilaiService->getMahasiswaForCreate($mahasiswa_id);
        $mataKuliahs = $nilaiService->getMataKuliahsForForm();

        return view('nilai.create', compact('mahasiswa', 'mataKuliahs'));
    }

    public function store(NilaiRequest $request, NilaiService $nilaiService)
    {
        $validated = $request->validated();

        if ($nilaiService->hasDuplicate($validated['mahasiswa_id'], $validated['mata_kuliah_id'])) {
            return back()
                ->withInput()
                ->with('error', 'Nilai untuk mata kuliah tersebut sudah ada.');
        }

        $nilaiService->create($validated);

        return redirect()
            ->route('mahasiswa.detail', $validated['mahasiswa_id'])
            ->with('success', 'Nilai mahasiswa berhasil ditambahkan.');
    }

    public function edit($id, NilaiService $nilaiService)
    {
        $nilai = $nilaiService->findForEdit($id);
        $mataKuliahs = $nilaiService->getMataKuliahsForForm();

        return view('nilai.edit', compact('nilai', 'mataKuliahs'));
    }

    public function update(NilaiRequest $request, $id, NilaiService $nilaiService)
    {
        $nilai = $nilaiService->find($id);
        $validated = $request->validated();

        if ($nilaiService->hasDuplicate($nilai->mahasiswa_id, $validated['mata_kuliah_id'], $nilai->id)) {
            return back()
                ->withInput()
                ->with('error', 'Mahasiswa ini sudah memiliki nilai pada mata kuliah tersebut.');
        }

        $nilaiService->update($nilai, $validated);

        return redirect()
            ->route('mahasiswa.detail', $nilai->mahasiswa_id)
            ->with('success', 'Nilai mahasiswa berhasil diperbarui.');
    }

    public function destroy($id, NilaiService $nilaiService)
    {
        $nilai = $nilaiService->find($id);
        $mahasiswaId = $nilai->mahasiswa_id;

        $nilaiService->delete($nilai);

        return redirect()
            ->route('mahasiswa.detail', $mahasiswaId)
            ->with('success', 'Nilai mahasiswa berhasil dihapus.');
    }
}
