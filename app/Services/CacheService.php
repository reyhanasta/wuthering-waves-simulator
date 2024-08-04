<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class CacheService{ 

    public function getCacheData($sessionId)
    {
        // Using pipeline to fetch all data in one go
        $results = Redis::pipeline(function ($pipe) use ($sessionId) {
            $pipe->get('totalPulls_count_' . $sessionId);
            $pipe->get('pity4_count_' . $sessionId);
            $pipe->get('pity5_count_' . $sessionId);
            $pipe->hgetall('inventory_' . $sessionId);
        });

        // Map the results to corresponding keys
        return [
            'totalPulls' => $results[0] ?? 0,
            'pity4' => $results[1] ?? 0,
            'pity5' => $results[2] ?? 0,
            'inventory' => $results[3] ?? []
        ];
    }

}
