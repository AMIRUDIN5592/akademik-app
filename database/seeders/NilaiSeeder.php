<?php
namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = Mahasiswa::all();
        $mataKuliahs = MataKuliah::all();

        foreach ($mahasiswas as $mahasiswa) {
            foreach ($mataKuliahs as $mataKuliah) {
                Nilai::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'mata_kuliah_id' => $mataKuliah->id,
                    'nilai' => rand(70, 100),
                ]);
 	}
        }
    }
}
