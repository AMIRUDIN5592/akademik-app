<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\KartuMahasiswa;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswas = Mahasiswa::with([
            'jurusan',
            'kartuMahasiswa',
            'mataKuliah'
        ])->latest()->get();

        return view('mahasiswa.index', compact('mahasiswas'));
    }

    public function detail($id)
    {
        $mahasiswa = Mahasiswa::with([
            'jurusan',
            'kartuMahasiswa',
            'mataKuliah'
        ])->findOrFail($id);

        return view('mahasiswa.detail', compact('mahasiswa'));
    }

    public function filterJurusan($kode)
    {
        $mahasiswas = Mahasiswa::with('jurusan')
            ->whereHas('jurusan', function ($query) use ($kode) {
                $query->where('kode_jurusan', $kode);
            })
            ->get();

        return view('mahasiswa.index', compact('mahasiswas'));
    }

    //create mahasiswa
    public function create()
    {
        $jurusans = Jurusan::all();
        $title = 'Tambah Mahasiswa';

        return view('mahasiswa.create', compact('jurusans', 'title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'nim' => ['required', 'string', 'max:255', 'unique:mahasiswas,nim'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:mahasiswas,email'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
        ]);

        Mahasiswa::create($validated);

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function generateKartu($id)
    {
        $mahasiswa = Mahasiswa::with('kartuMahasiswa')->findOrFail($id);

        if (!$mahasiswa->kartuMahasiswa) {
            KartuMahasiswa::create([
                'mahasiswa_id' => $mahasiswa->id,
                'nomor_kartu' => 'KTM-' . $mahasiswa->nim,
                'tanggal_terbit' => now(),
                'tanggal_berlaku' => now()->addYears(4),
            ]);
        }

        return redirect()
            ->route('mahasiswa.detail', $mahasiswa->id)
            ->with('success', 'Kartu mahasiswa berhasil digenerate.');
    }


}
