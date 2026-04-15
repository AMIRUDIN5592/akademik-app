<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/about',[HomeController::class, 'about']);

Route::get('/hello', function () {
    return view('hello');
});
