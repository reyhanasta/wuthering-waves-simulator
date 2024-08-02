<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GachaController;
use App\Http\Livewire\StandardBanner;
use App\Livewire\StandardBanner as LivewireStandardBanner;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/standard-banner',LivewireStandardBanner::class)->name('standard-banner');



