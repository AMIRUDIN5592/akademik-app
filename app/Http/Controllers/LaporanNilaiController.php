<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Services\LaporanNilaiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanNilaiController extends Controller
{
    public function index(Request $request, LaporanNilaiService $service): View
    {
        $nilaiMaxRules = ['nullable', 'numeric', 'min:0', 'max:100'];

        if ($request->filled('nilai_min')) {
            $nilaiMaxRules[] = 'gte:nilai_min';
        }

        $filters = $request->validate([
            'mahasiswa_id' => ['nullable', 'exists:mahasiswas,id'],
            'mata_kuliah_id' => ['nullable', 'exists:mata_kuliahs,id'],
            'nilai_min' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_max' => $nilaiMaxRules,
        ]);

        return view('laporan.nilai', [
            'nilai' => $service->getFiltered($filters),
            'summary' => $service->getSummary($filters),
            'mahasiswas' => Mahasiswa::orderBy('nama')->get(),
            'mataKuliahs' => MataKuliah::orderBy('nama_mk')->get(),
            'filters' => $filters,
        ]);
    }
}
