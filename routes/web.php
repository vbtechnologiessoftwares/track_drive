<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExternalApiController;

<<<<<<< HEAD
Route::get('/', [ExternalApiController::class, 'index2']);

/*Route::get('/', [ExternalApiController::class, 'index']);
*/
Route::prefix('external-api')->group(function () {
    Route::get('/call-data', [ExternalApiController::class, 'index']);
    Route::get('/call-data1', [ExternalApiController::class, 'index1']);
});
=======

Route::get('/', [ExternalApiController::class, 'index']);

Route::prefix('external-api')->group(function () {
    Route::get('/call-data', [ExternalApiController::class, 'index']);
    Route::get('/call-data1', [ExternalApiController::class, 'index1']);
    Route::get('/call-data2', [ExternalApiController::class, 'index2']);
});

>>>>>>> 5e635e8120008715825bd6d90110e3a978331149
