<?php

namespace App\Helpers;

class RoleHelper
{
    public static function label(?string $role): string
    {
        return match ($role) {
            'admin' => 'Administrator',
            'dosen' => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
            default => 'Tidak diketahui',
        };
    }
}
