<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExternalApiController;


//Route::get('/', [ExternalApiController::class, 'index']);

Route::prefix('external-api')->group(function () {
    Route::get('/call-data', [ExternalApiController::class, 'index']);
});

