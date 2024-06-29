<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExternalApiController;
use App\Http\Controllers\CronController;


Route::get('/', [ExternalApiController::class, 'index']);

Route::prefix('external-api')->group(function () {
    Route::get('/call-data', [ExternalApiController::class, 'index']);
    Route::get('/call-data1', [ExternalApiController::class, 'index1']);
    Route::get('/call-data2', [ExternalApiController::class, 'index2']);
});
Route::prefix('cron')->group(function () {
    Route::get('/call-data', [CronController::class, 'index']);
});
