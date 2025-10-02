<?php

use App\Http\Controllers\FacilityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::resource('facilities', FacilityController::class);
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
