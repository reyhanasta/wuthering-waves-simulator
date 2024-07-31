<?php

namespace App\Services;

use App\Models\Weapon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class GachaService
{
    protected $baseDropRates;
    protected $sessionId;
    protected $cacheDuration;
    protected $fiveStarId;
    protected $fourStarId;

    public function __construct($sessionId, $baseDropRates, $cacheDuration)
    {
        $this->sessionId = Session::getId();
        $this->baseDropRates = $baseDropRates;
        $this->cacheDuration = $cacheDuration;
        $this->fiveStarId = $this->baseDropRates->firstWhere('level', 'SSR')->id;
        $this->fourStarId =$this->baseDropRates->firstWhere('level', 'SR')->id;
    }

    public function getGachaResult()
    {
        $rand = mt_rand(0, 10000) / 100;
        $fourStarPity = Redis::incr('pity4_count_' . $this->sessionId);
        $fiveStarPity = Redis::incr('pity5_count_' . $this->sessionId);

        $fiveStarDropRates = $this->baseDropRates->firstWhere('level', 'SSR')->drop_rates;
        // $fiveStarId = $this->baseDropRates->firstWhere('level', 'SSR')->id;
        // $fourStarId = $this->baseDropRates->firstWhere('level', 'SR')->id;

        $increasedDropRate = ($fiveStarPity >= 70) ? $fiveStarDropRates * 1.8 + (1 / 100) : $fiveStarDropRates;

        if ($fiveStarPity == 80) {
            $this->resetPity($this->fiveStarId);
            return $this->getRandomWeaponByRarity($this->fiveStarId);
        }

        if ($fourStarPity >= 10 && $fiveStarPity < 80) {
            $this->resetPity($this->fourStarId);
            return $this->getRandomWeaponByRarity($this->fourStarId);
        }

        $cumulativeProbability = 0;
        foreach ($this->baseDropRates as $rates) {
            $cumulativeProbability += ($rates->level == 'SSR') ? $increasedDropRate : $rates->drop_rates;
            if ($rand <= $cumulativeProbability) {
                $this->resetPity($rates->id);
                return $this->getRandomWeaponByRarity($rates->id);
            }
        }

        return null;
    }

    public function resetPity($rarityId)
    {
        if ($rarityId == $this->fiveStarId) {
            Redis::setex('pity5_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
            Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        } elseif ($rarityId == $this->fourStarId) {
            Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        }
    }

    private function getRandomWeaponByRarity($rarity)
    {
        $weapons = Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        });

        if ($weapons->isNotEmpty()) {
            return $weapons->random();
        }

        return null;
    }
}
