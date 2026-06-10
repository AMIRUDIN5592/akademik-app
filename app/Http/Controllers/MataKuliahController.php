<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $mataKuliahs = MataKuliah::withCount('mahasiswa')
            ->orderBy('nama_mk')
            ->get();

        return view('mata-kuliah.index', compact('mataKuliahs'));
    }

    public function create()
    {
        return view('mata-kuliah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => ['required', 'max:20', 'unique:mata_kuliahs,kode_mk'],
            'nama_mk' => ['required', 'max:100'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        MataKuliah::create($validated);

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil ditambahkan.');
    }

    public function show($id)
    {
        $mataKuliah = MataKuliah::with('mahasiswa.jurusan')->findOrFail($id);

        return view('mata-kuliah.show', compact('mataKuliah'));
    }

    public function edit($id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);

        return view('mata-kuliah.edit', compact('mataKuliah'));
    }

    public function update(Request $request, $id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);

        $validated = $request->validate([
            'kode_mk' => ['required', 'max:20', 'unique:mata_kuliahs,kode_mk,' . $mataKuliah->id],
            'nama_mk' => ['required', 'max:100'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        $mataKuliah->update($validated);

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil diperbarui.');
    }
}
