<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = [
        'jurusan_id',
        'nim',
        'nama',
        'email',
        'jenis_kelamin',
        'alamat',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function kartuMahasiswa() 
   {
        return $this->hasOne(KartuMahasiswa::class);
    }

    public function mataKuliah()
    {
        return $this->belongsToMany(MataKuliah::class, 'nilais')
            ->withPivot('id', 'nilai')
            ->withTimestamps();
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}
