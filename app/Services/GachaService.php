<?php

namespace App\Services;

use App\Models\Rarity;
use App\Models\Weapon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class GachaService {

    protected $fiveStarDropRates;
    protected $fourStarDropRates;
    protected $fiveStarId;
    protected $fourStarId;
    protected $sessionId;
    protected $baseDropRates;

    public function __construct()
    {
        $this->sessionId = Session::getId();
    }


    public function getGachaResult($baseDropRates,$cacheDuration=120)
    {

        $rand = mt_rand(0, 10000) / 100;
        $fourStarPity = Redis::incr('pity4_count_' . $this->sessionId);
        $fiveStarPity = Redis::incr('pity5_count_' . $this->sessionId);

        $fiveStarDropRates = $baseDropRates->firstWhere('level', 'SSR')->drop_rates;
        $fiveStarId = $baseDropRates->firstWhere('level', 'SSR')->id;
        $fourStarId = $baseDropRates->firstWhere('level', 'SR')->id;
        $increasedDropRate = ($fiveStarPity >= 70) ? $fiveStarDropRates * 1.8 + (1 / 100) : $fiveStarDropRates;

        if ($fiveStarPity == 80) {
            $this->resetPity($rarity=$fiveStarId,$fiveStarId,$fourStarId,$cacheDuration);
            return $this->getRandomWeaponByRarity($fiveStarId,$cacheDuration);
        }

        if ($fourStarPity >= 10 && $fiveStarPity < 80) {
            $this->resetPity($rarity=$fourStarId,$fiveStarId,$fourStarId,$cacheDuration);
            return $this->getRandomWeaponByRarity($fourStarId,$cacheDuration);
        }

        $cumulativeProbability = 0;
        foreach ($baseDropRates as $rates) {
            $cumulativeProbability += ($rates->level == 'SSR') ? $increasedDropRate : $rates->drop_rates;
            if ($rand <= $cumulativeProbability) {
                $this->resetPity($rates->id,$fiveStarId,$fourStarId,$cacheDuration);
                return $this->getRandomWeaponByRarity($rates->id,$cacheDuration);
            }
        }

        return null;
    }

    //RESET PITTY MASIH ERROR
    public function resetPity($rarityId,$fiveStarId,$fourStarId,$cacheDuration)
    {

        if ($rarityId == $fiveStarId) {
            Redis::setex('pity5_count_' . $this->sessionId, $cacheDuration * 60, 0);
            Redis::setex('pity4_count_' . $this->sessionId, $cacheDuration * 60, 0);
        } elseif ($rarityId == $fourStarId) {
            Redis::setex('pity4_count_' . $this->sessionId, $cacheDuration * 60, 0);
        }
    }

    public function getRandomWeaponByRarity($rarity,$cacheDuration)
    {
        return Cache::remember("weapons_rarity_{$rarity}", $cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        })->random();
    }
}
