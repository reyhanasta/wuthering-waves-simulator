<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GachaController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/convene', [GachaController::class, 'showGachaPage'])->name('gacha.page');
Route::post('/gacha', [GachaController::class, 'performGacha'])->name('gacha.perform');


