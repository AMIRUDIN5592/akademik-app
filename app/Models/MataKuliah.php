<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'sks',
    ];

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'nilais')
            ->withPivot('id', 'nilai')
            ->withTimestamps();
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}
