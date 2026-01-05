<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

use App\Http\Controllers\CityController;
use App\Http\Controllers\CountyController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Cities and Counties management

    // Cities

Route::middleware(['auth'])->group(function () {
    Route::get('/cities', [CityController::class, 'webIndex'])
        ->name('cities-view.index');

    Route::post('/cities', [CityController::class, 'webStore'])
        ->name('cities-view.store');

    Route::put('/cities/{id}', [CityController::class, 'webUpdate'])
        ->name('cities-view.update');

    Route::delete('/cities/{id}', [CityController::class, 'webDestroy'])
        ->name('cities-view.destroy');

    Route::get('/cities/export', [CityController::class, 'export'])
        ->name('cities-view.export');
});
    // Counties
    
Route::middleware(['auth'])->group(function () {
    Route::get('/counties', [CountyController::class, 'webIndex'])
        ->name('counties-view.index');

    Route::post('/counties', [CountyController::class, 'webStore'])
        ->name('counties-view.store');

    Route::put('/counties/{id}', [CountyController::class, 'webUpdate'])
        ->name('counties-view.update');

    Route::delete('/counties/{id}', [CountyController::class, 'webDestroy'])
        ->name('counties-view.destroy');

    Route::get('/counties/export', [CountyController::class, 'export'])
        ->name('counties-view.export');
});
});
