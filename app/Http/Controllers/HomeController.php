<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        $data = [
            'title' => 'Home Page',
            'nama' => 'Amirudin',
            'matkul' => 'Pemrograman Web Lanjut',
            'kampus' => 'STMIK Antar Bangsa',
            'deskripsi' => 'Belajar dasar Controller, Route, dan Blade di Laravel.',
            'daftarMateri' => [
                'Pengenalan Laravel',
                'Routing',
                'Controller',
                'Blade Template'
            ]
        ];

        return view('home', $data);
    }
    public function about()
    {
        $data = [
            'title' => 'About',
            'aplikasi' => 'Aplikasi Belajar Laravel untuk Pemrograman Web Lanjut',
            'versi' => '1.0',
            'pembuat' => 'Mahasiswa Pemrograman Web Lanjut'
        ];

        return view('about', $data);
    }
}