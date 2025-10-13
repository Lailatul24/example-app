<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

Route::apiResource('facilities', FacilityController::class);
