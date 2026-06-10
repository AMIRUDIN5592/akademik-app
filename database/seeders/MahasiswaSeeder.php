<?php
namespace Database\Seeders;
use App\Models\Jurusan;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $ti = Jurusan::where('kode_jurusan', 'TI')->first();
        $si = Jurusan::where('kode_jurusan', 'SI')->first();
        $mi = Jurusan::where('kode_jurusan', 'MI')->first();

        Mahasiswa::create([
            'jurusan_id' => $ti->id,
            'nim' => '2026001',
            'nama' => 'Andi Saputra',
            'email' => 'andi@example.com',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jakarta',
        ]);

        Mahasiswa::create([
            'jurusan_id' => $si->id,
            'nim' => '2026002',
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'jenis_kelamin' => 'L',
            'alamat' => 'Tangerang',
        ]);

        Mahasiswa::create([
            'jurusan_id' => $mi->id,
            'nim' => '2026003',
            'nama' => 'Citra Lestari',
            'email' => 'citra@example.com',
            'jenis_kelamin' => 'P',
            'alamat' => 'Bekasi',
        ]);
    }
}
