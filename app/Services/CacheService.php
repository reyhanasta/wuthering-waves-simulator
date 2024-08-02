<?php

namespace App\Services;

use App\Models\Rarity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService{ 

    public function getBaseDropRates($cacheDuration)
    {
        return Cache::remember('baseDropRates',$cacheDuration * 60, function () {
            return Rarity::all();
        });
    }

    public function getCacheData($sessionId)
    {
        return [
            'totalPulls' => Redis::get('totalPulls_count_' . $sessionId) ?? 0,
            'pity4' => Redis::get('pity4_count_' . $sessionId) ?? 0,
            'pity5' => Redis::get('pity5_count_' . $sessionId) ?? 0,
            'inventory' => Redis::get('inventory_' . $sessionId) ?? []
        ];
    }

}
