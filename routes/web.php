<?php

use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('facilities', FacilityController::class);
Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');
Route::get('/facilities/export', [FacilityController::class, 'export'])->name('facilities.export');


Route::resource('categories', CategoryController::class);
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::resource('borrowings', BorrowingController::class)->only(['index','store','update']);
Route::post('/loans/{id}/return', [LoanController::class, 'returnLoan']);
Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');


// Route::prefix('facilities')->group(function () {
//     Route::get('/', fn () => Inertia::render('Facilities/Index'))->name('facilities.index');
//     Route::get('/create', fn () => Inertia::render('Facilities/Create'))->name('facilities.create');
// });

