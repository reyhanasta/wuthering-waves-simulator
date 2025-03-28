<?php

namespace App\Services\Gacha;

use App\Models\Weapon;
use Illuminate\Support\Facades\Cache;

class StandardBannerService
{
    protected $cacheDuration = 3600; // 1 jam

    public function getCache($key)
    {
        return Cache::get($key);
    }

    public function singlePull($key)
    {
        return $this->pull($key, 1);
    }

    public function tenPull($key)
    {
        return $this->pull($key, 10);
    }

    public function resetCache($key)
    {
        Cache::forget($key);
    }

    protected function pull($key, $count)
    {
        $cache = $this->getCache($key) ?? [
            'results' => [],
            'total_pulls' => 0,
            'five_star_count' => 0,
            'four_star_count' => 0,
            'three_star_count' => 0,
        ];

        $results = [];
        for ($i = 0; $i < $count; $i++) {
            $rarity = $this->determineRarity($cache['total_pulls']);
            $weapon = Weapon::where('rarity_id', $rarity)->inRandomOrder()->first();

            if ($weapon) {
                $results[] = $weapon;

                $cache['results'][] = $weapon;
                $cache['total_pulls']++;

                if ($rarity == 1) $cache['five_star_count']++;
                if ($rarity == 2) $cache['four_star_count']++;
                if ($rarity == 3) $cache['three_star_count']++;
            }
        }

        $bgImg = $this->getBackgroundImage($results);

        $cache['bg_img'] = $bgImg;

        Cache::put($key, $cache, $this->cacheDuration);

        return [
            'results' => $cache['results'],
            'bg_img' => $bgImg,
            'total_pulls' => $cache['total_pulls'],
            'five_star_count' => $cache['five_star_count'],
            'four_star_count' => $cache['four_star_count'],
            'three_star_count' => $cache['three_star_count'],
        ];
    }

    protected function determineRarity($totalPulls)
    {
        $rand = mt_rand(1, 1000) / 10; // 0.1 - 100%

        if ($totalPulls >= 90 || $rand <= 1.5) {
            return 1; // ★5
        } elseif ($rand <= 9.5) {
            return 2; // ★4
        } else {
            return 3; // ★3
        }
    }

    protected function getBackgroundImage($results)
    {
        $hasFiveStar = collect($results)->contains(fn ($item) => $item->rarity_id === 1);
        $hasFourStar = collect($results)->contains(fn ($item) => $item->rarity_id === 2);

        if ($hasFiveStar) {
            return asset('images/bg-5star.png');
        } elseif ($hasFourStar) {
            return asset('images/bg-4star.png');
        } else {
            return asset('images/bg-3star.png');
        }
    }
}
