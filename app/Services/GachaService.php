<?php

namespace App\Services;


use App\Models\Weapon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;


class GachaService
{
    public function getGachaResult($baseDropRates, $cacheDuration = 120, $sessionId)
    {
        $rand = mt_rand(0, 10000) / 100;

        // Using pipeline to increment both pity counters in a single connection
        list($fourStarPity, $fiveStarPity) =
            Redis::multi()
            ->incr('pity4_count_' . $sessionId)
            ->incr('pity5_count_' . $sessionId)
            ->exec();

        $fiveStar = $baseDropRates->firstWhere('level', 'SSR');
        $fourStar = $baseDropRates->firstWhere('level', 'SR');
        $fiveStarDropRates = $fiveStar->drop_rates;
        $increasedDropRate = ($fiveStarPity >= 70) ? $fiveStarDropRates * 1.8 + 0.01 : $fiveStarDropRates;

        if ($fiveStarPity == 80) {
            return $this->awardWeaponAndResetPity($fiveStar->id, $fiveStar->id, $fourStar->id, $cacheDuration, $sessionId);
        }

        if ($fourStarPity >= 10 && $fiveStarPity < 80) {
            return $this->awardWeaponAndResetPity($fourStar->id, $fiveStar->id, $fourStar->id, $cacheDuration, $sessionId);
        }

        $cumulativeProbability = 0;
        foreach ($baseDropRates as $rates) {
            $dropRate = ($rates->level == 'SSR') ? $increasedDropRate : $rates->drop_rates;
            $cumulativeProbability += $dropRate;
            if ($rand <= $cumulativeProbability) {
                return $this->awardWeaponAndResetPity($rates->id, $fiveStar->id, $fourStar->id, $cacheDuration, $sessionId);
            }
        }

        return null;
    }

    private function awardWeaponAndResetPity($rarityId, $fiveStarId, $fourStarId, $cacheDuration, $sessionId)
    {
        $this->resetPity($rarityId, $fiveStarId, $fourStarId, $cacheDuration, $sessionId);
        return $this->getRandomWeaponByRarity($rarityId, $cacheDuration);
    }

    private function resetPity($rarityId, $fiveStarId, $fourStarId, $cacheDuration, $sessionId)
    {
        $pipeline = Redis::pipeline();
        if ($rarityId == $fiveStarId) {
            $pipeline->setex('pity5_count_' . $sessionId, $cacheDuration * 60, 0);
        }
        if ($rarityId == $fiveStarId || $rarityId == $fourStarId) {
            $pipeline->setex('pity4_count_' . $sessionId, $cacheDuration * 60, 0);
        }
        $pipeline->exec();
    }

    public function getRandomWeaponByRarity($rarity, $cacheDuration)
    {
        return Cache::remember("weapons_rarity_{$rarity}", $cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        })->random();
    }
}
