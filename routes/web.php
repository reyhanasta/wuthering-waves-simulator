<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Gacha\StandardBanner as LivewireStandardBanner;
use App\Livewire\HomePage as LivewireHomePage;

Route::get('/',  LivewireHomePage::class)->name('homepage');

Route::get('/standard-banner',LivewireStandardBanner::class)->name('standard-banner');
