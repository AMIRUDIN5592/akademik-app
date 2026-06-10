<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_jurusan' => 'Teknik Informatika', 'kode_jurusan' => 'TI'],
            ['nama_jurusan' => 'Sistem Informasi', 'kode_jurusan' => 'SI'],
            ['nama_jurusan' => 'Manajemen Informatika', 'kode_jurusan' => 'MI'],
        ];

        foreach ($data as $item) {
            Jurusan::create($item);
        }
    }
}
