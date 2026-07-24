<?php

use App\Http\Controllers\GarmentPhotoController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

// ====== ARMADIO ======

Route::view('/', 'home')->name('home');
Route::view('/categorie', 'categorie')->name('categorie');
Route::view('/storico', 'storico')->name('storico');
Route::view('/bagaglio', 'bagaglio')->name('bagaglio');
Route::view('/armadio', 'armadio')->name('armadio');   // pagina vuota, a disposizione

Route::prefix('api')->group(function () {
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plans/auto', [PlanController::class, 'auto']);
    Route::put('/plans/auto', [PlanController::class, 'saveAuto']);
    Route::post('/plans', [PlanController::class, 'store']);
    Route::get('/plans/{plan}', [PlanController::class, 'show']);
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy']);
    Route::post('/garment-photo', [GarmentPhotoController::class, 'store']);
});
