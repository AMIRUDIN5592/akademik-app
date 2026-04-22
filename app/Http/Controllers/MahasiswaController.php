<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class MahasiswaController extends Controller
{
    public function form()
    {
        return view('mahasiswa.form');
    }

    public function proses(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email'],
            'umur' => ['required', 'integer', 'min:17'],
            'username' => ['required', 'string', 'min:4', 'max:20', 'regex:/^[A-Za-z0-9_]+$/'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'role' => ['required', Rule::in(['admin', 'staff', 'mahasiswa'])],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'in:PHP,Laravel,JavaScript,MySQL'],
            // 'skills.*' => ['string', Rule::in(['PHP', 'Laravel', 'JavaScript', 'MySQL'])],
            'cv' => ['required', File::types(['pdf', 'doc', 'docx'])->max('2mb')],
            'foto' => ['nullable', File::image()->max('2mb')],
        ]);

        $data = $validated;

        if ($request->hasFile('cv')) {
            $data['cv'] = $request->file('cv')->getClientOriginalName();
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->getClientOriginalName();
        }

        unset($data['password']);

        return view('mahasiswa.hasil', compact('data'));
    }
}
