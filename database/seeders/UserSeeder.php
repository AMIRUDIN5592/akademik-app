<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kampus.test'],
            ['name' => 'Admin Akademik', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'dosen@kampus.test'],
            ['name' => 'Dosen Pengampu', 'password' => Hash::make('password'), 'role' => 'dosen']
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@kampus.test'],
            [
                'name' => 'Mahasiswa Demo',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'mahasiswa_id' => Mahasiswa::first()?->id,
            ]
        );
    }
}
