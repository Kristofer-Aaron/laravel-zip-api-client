<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountyController;
use App\Http\Controllers\ApiAuthController;

// Home page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// API Authentication routes (public)
Route::get('/login', [ApiAuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [ApiAuthController::class, 'showRegisterForm'])->name('register');
Route::get('/api-login', [ApiAuthController::class, 'showLoginForm'])->name('api.login.form');
Route::post('/api-login', [ApiAuthController::class, 'login'])->name('api.login');
Route::post('/api-register', [ApiAuthController::class, 'register'])->name('api.register');
Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout');
Route::post('/api-logout', [ApiAuthController::class, 'logout'])->name('api.logout');

// Dashboard (requires API token)
Route::middleware(['api.token'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Cities management
    Route::get('/cities', [CityController::class, 'webIndex'])->name('cities-view.index');
    Route::post('/cities', [CityController::class, 'webStore'])->name('cities-view.store');
    Route::put('/cities/{id}', [CityController::class, 'webUpdate'])->name('cities-view.update');
    Route::delete('/cities/{id}', [CityController::class, 'webDestroy'])->name('cities-view.destroy');
    Route::get('/cities/export', [CityController::class, 'export'])->name('cities-view.export');

    // Counties management
    Route::get('/counties', [CountyController::class, 'webIndex'])->name('counties-view.index');
    Route::post('/counties', [CountyController::class, 'webStore'])->name('counties-view.store');
    Route::put('/counties/{id}', [CountyController::class, 'webUpdate'])->name('counties-view.update');
    Route::delete('/counties/{id}', [CountyController::class, 'webDestroy'])->name('counties-view.destroy');
    Route::get('/counties/export', [CountyController::class, 'export'])->name('counties-view.export');
});
