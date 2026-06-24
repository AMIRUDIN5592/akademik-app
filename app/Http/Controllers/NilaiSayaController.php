<?php

namespace App\Http\Controllers;

use App\Services\NilaiSayaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NilaiSayaController extends Controller
{
    public function index(Request $request, NilaiSayaService $nilaiSayaService): View
    {
        $nilai = $nilaiSayaService->getByMahasiswaId($request->user()->mahasiswa_id);

        return view('nilai-saya.index', compact('nilai'));
    }
}
