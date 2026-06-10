<?php
namespace Database\Seeders;

use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode_mk' => 'MK001', 'nama_mk' => 'Pemrograman Web Lanjut', 'sks' => 3],
            ['kode_mk' => 'MK002', 'nama_mk' => 'Basis Data', 'sks' => 3],
            ['kode_mk' => 'MK003', 'nama_mk' => 'Analisis Sistem', 'sks' => 2],
        ];

        foreach ($data as $item) {
            MataKuliah::create($item);
        }
    }
}
