<?php

namespace App\Services;

use App\Models\Rarity;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected $cacheDuration;

    public function __construct($cacheDuration)
    {
        $this->cacheDuration = $cacheDuration;
    }

    public function getBaseDropRates()
    {
        return Cache::remember('baseDropRates', $this->cacheDuration * 60, function () {
            return Rarity::all();
        });
    }
}
