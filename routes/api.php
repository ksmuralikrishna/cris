<?php

use App\Http\Controllers\Api\RegistrationApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public endpoint (no auth needed for duplicate check via web)
    Route::get('/check-duplicate', [RegistrationApiController::class, 'checkDuplicate']);

    // Protected endpoints requiring tablet token
    Route::middleware('auth.tablet')->group(function () {
        Route::post('/register', [RegistrationApiController::class, 'store']);
        Route::post('/heartbeat', [RegistrationApiController::class, 'heartbeat']);
    });
});
