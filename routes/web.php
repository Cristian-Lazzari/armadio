<?php

use App\Http\Controllers\GarmentPhotoController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

// ====== ARMADIO — aggiungi queste rotte al tuo routes/web.php ======

Route::view('/armadio', 'armadio');

Route::prefix('api')->group(function () {
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plans/auto', [PlanController::class, 'auto']);
    Route::put('/plans/auto', [PlanController::class, 'saveAuto']);
    Route::post('/plans', [PlanController::class, 'store']);
    Route::get('/plans/{plan}', [PlanController::class, 'show']);
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy']);
    Route::post('/garment-photo', [GarmentPhotoController::class, 'store']);
});
