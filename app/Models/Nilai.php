<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'nilai',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function getGradeAttribute(): string
    {
        return match (true) {
            $this->nilai >= 85 => 'A',
            $this->nilai >= 75 => 'B',
            $this->nilai >= 65 => 'C',
            $this->nilai >= 50 => 'D',
            default => 'E',
        };
    }
}
