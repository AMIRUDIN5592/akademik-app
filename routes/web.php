<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\NilaiController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/mahasiswa', [MahasiswaController::class, 'index'])
    ->name('mahasiswa.index');

//create mahasiswa
Route::get('/mahasiswa/create', [MahasiswaController::class, 'create'])
    ->name('mahasiswa.create');

Route::post('/mahasiswa', [MahasiswaController::class, 'store'])
    ->name('mahasiswa.store');

Route::get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'detail'])
    ->name('mahasiswa.detail');

Route::post('/mahasiswa/{id}/generate-kartu', [MahasiswaController::class, 'generateKartu'])
    ->name('mahasiswa.generate-kartu');

Route::get('/mahasiswa/jurusan/{kode}', [MahasiswaController::class, 'filterJurusan'])
    ->name('mahasiswa.filterJurusan');

Route::get('/statistik-jurusan', [MahasiswaController::class, 'statistik'])
    ->name('mahasiswa.statistik');

Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])
    ->name('mata-kuliah.index');

Route::get('/mata-kuliah/create', [MataKuliahController::class, 'create'])
    ->name('mata-kuliah.create');

Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])
    ->name('mata-kuliah.store');

Route::get('/mata-kuliah/{id}', [MataKuliahController::class, 'show'])
    ->name('mata-kuliah.show');

Route::get('/mata-kuliah/{id}/edit', [MataKuliahController::class, 'edit'])
    ->name('mata-kuliah.edit');

Route::put('/mata-kuliah/{id}', [MataKuliahController::class, 'update'])
    ->name('mata-kuliah.update');

Route::get('/nilai/create/{mahasiswa_id}', [NilaiController::class, 'create'])
    ->name('nilai.create');

Route::post('/nilai', [NilaiController::class, 'store'])
    ->name('nilai.store');

Route::get('/nilai/{id}/edit', [NilaiController::class, 'edit'])
    ->name('nilai.edit');

Route::put('/nilai/{id}', [NilaiController::class, 'update'])
    ->name('nilai.update');

Route::delete('/nilai/{id}', [NilaiController::class, 'destroy'])
    ->name('nilai.destroy');
