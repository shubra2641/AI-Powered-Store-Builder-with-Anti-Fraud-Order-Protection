<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register user routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" and "auth" middleware.
|
*/

Route::get('/dashboard', function () {
    return view('user.dashboard');
})->name('dashboard');
