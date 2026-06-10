<?php
namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JurusanSeeder::class,
            MahasiswaSeeder::class,
            MataKuliahSeeder::class,
            KartuMahasiswaSeeder::class,
            NilaiSeeder::class,
        ]);
    }
}
