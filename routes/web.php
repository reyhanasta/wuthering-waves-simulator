<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GachaController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/convene', [GachaController::class, 'showGachaPage'])->name('gacha.page');
Route::post('/perform-gacha', [GachaController::class, 'performGacha'])->name('gacha.perform');
Route::post('/perform-ten-gacha', [GachaController::class, 'performTenGacha'])->name('multiple-gacha.perform');
Route::get('/test-pity', [GachaController::class, 'pittyBuildUp']);
Route::get('/reset-gacha', [GachaController::class, 'resetGacha'])->name('gacha.reset');



