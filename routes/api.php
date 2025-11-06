<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\LoanController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

Route::apiResource('facilities', FacilityController::class);
Route::get('inventaris/{categoryName}', [FacilityController::class, 'byCategory']);

Route::apiResource('loans', LoanController::class);
Route::apiResource('categories', CategoryController::class);
