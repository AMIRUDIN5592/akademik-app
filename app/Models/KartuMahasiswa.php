<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuMahasiswa extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'nomor_kartu',
        'tanggal_terbit',
        'tanggal_berlaku',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
