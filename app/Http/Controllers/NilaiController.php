<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function create($mahasiswa_id)
    {
        $mahasiswa = Mahasiswa::with('jurusan')->findOrFail($mahasiswa_id);
        $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();

        return view('nilai.create', compact('mahasiswa', 'mataKuliahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswas,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliahs,id'],
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $sudahAda = Nilai::where('mahasiswa_id', $validated['mahasiswa_id'])
            ->where('mata_kuliah_id', $validated['mata_kuliah_id'])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withInput()
                ->with('error', 'Nilai untuk mata kuliah tersebut sudah ada.');
        }

        Nilai::create($validated);

        return redirect()
            ->route('mahasiswa.detail', $validated['mahasiswa_id'])
            ->with('success', 'Nilai mahasiswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $nilai = Nilai::with(['mahasiswa', 'mataKuliah'])->findOrFail($id);
        $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();

        return view('nilai.edit', compact('nilai', 'mataKuliahs'));
    }

    public function update(Request $request, $id)
    {
        $nilai = Nilai::findOrFail($id);

        $validated = $request->validate([
            'mata_kuliah_id' => ['required', 'exists:mata_kuliahs,id'],
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $sudahAda = Nilai::where('mahasiswa_id', $nilai->mahasiswa_id)
            ->where('mata_kuliah_id', $validated['mata_kuliah_id'])
            ->where('id', '!=', $nilai->id)
            ->exists();

        if ($sudahAda) {
            return back()
                ->withInput()
                ->with('error', 'Mahasiswa ini sudah memiliki nilai pada mata kuliah tersebut.');
        }

        $nilai->update($validated);

        return redirect()
            ->route('mahasiswa.detail', $nilai->mahasiswa_id)
            ->with('success', 'Nilai mahasiswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $nilai = Nilai::findOrFail($id);
        $mahasiswaId = $nilai->mahasiswa_id;

        $nilai->delete();

        return redirect()
            ->route('mahasiswa.detail', $mahasiswaId)
            ->with('success', 'Nilai mahasiswa berhasil dihapus.');
    }
}
