<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::withCount('mahasiswa')->get();

        return view('mahasiswa.statistik', compact('jurusans'));
    }

    public function about(): View
    {
        $aplikasi = 'Akademik App';
        $versi = '1.0.0';
        $pembuat = 'Amirudin';
        $title = 'About';

        return view('about', compact(
            'aplikasi',
            'versi',
            'pembuat',
            'title'
        ));
    }
    
}
