<?php
namespace Database\Seeders;

use App\Models\KartuMahasiswa;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class KartuMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = Mahasiswa::all();

        foreach ($mahasiswas as $mahasiswa) {
            KartuMahasiswa::create([
                'mahasiswa_id' => $mahasiswa->id,
                'nomor_kartu' => 'KTM-' . $mahasiswa->nim,
                'tanggal_terbit' => now(),
                'tanggal_berlaku' => now()->addYears(4),
            ]);
        }
    }
}
