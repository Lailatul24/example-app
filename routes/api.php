<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\LoanController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

Route::apiResource('facilities', FacilityController::class);
Route::apiResource('loans', LoanController::class);
