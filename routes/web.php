<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MahasiswaController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/hello', function () {
    return view('hello');
});

Route::get('/mahasiswa/form', [MahasiswaController::class, 'form'])
    ->name('mahasiswa.form');
Route::post('/mahasiswa/proses', [MahasiswaController::class, 'proses'])
    ->name('mahasiswa.proses');

