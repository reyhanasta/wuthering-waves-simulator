<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Weapon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class GachaController extends Controller
{
    private $rarityProbabilities = [
        '1' => 0.5,
        '2' => 6.0,
        '3' => 93.5,
    ];

    private $cacheDuration = 120; // Cache duration in minutes
    public function showGachaPage()
    {
        $sessionId = Session::getId();
        $bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $cachedData = $this->getCacheData($sessionId);

        return view('gacha.pull-page',compact('cachedData','bgImg'));
    }
    public function performGacha()
    {
        $sessionId = Session::getId();
        $this->initializeCache($sessionId);
        $gachaResult = $this->getGachaResult($sessionId);
        Redis::incr('totalPulls_count_' . $sessionId);
        $cacheData = $this->getCacheData($sessionId);

        if ($gachaResult) {
            //masukan kode redis untuk update cache inventory
            $this->updateInventory($sessionId, $gachaResult);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                    'img' => asset('storage/' . $gachaResult->img),
                ],
                'totalPulls' => $cacheData['totalPulls'],
                'pitty4' => $cacheData['pitty4'],
                'pitty5' => $cacheData['pitty5'],
                
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gacha failed. Please try again.'
            ]);
        }
    }

    public function performTenGacha(Request $request)
    {
        $sessionId = Session::getId();
        $this->initializeCache($sessionId);

        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->getGachaResult($sessionId);
            if ($gachaResult) {
                $results[] = [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'img' => asset('storage/' . $gachaResult->img),
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                ];
                $this->updateInventory($sessionId, $gachaResult);
            }
        }
        Redis::incrby('totalPulls_count_' . $sessionId, 10);
        $cacheData = $this->getCacheData($sessionId);
        return response()->json([
            'success' => true,
            'data' => $results,
            'totalPulls' => $cacheData['totalPulls'],
            'pitty4' => $cacheData['pitty4'],
            'pitty5' => $cacheData['pitty5'],
        ]);
    }

    private function getGachaResult($sessionId)
    {
        $rand = mt_rand(0, 10000) / 100;
        $fourstarPitty = Redis::incr('pitty4_count_' . $sessionId);
        $fivestarPitty = Redis::incr('pitty5_count_' . $sessionId);

        $increasedDropRate = ($fivestarPitty >= 70) ? $this->rarityProbabilities[1] * 1.8 + (1 / 100) : $this->rarityProbabilities[1];

        if ($fourstarPitty >= 10 && $fivestarPitty < 80) {
            $this->resetPitty(2, $sessionId);
            return $this->getRandomWeaponByRarity(2);
        }

        if ($fivestarPitty == 80) {
            $this->resetPitty(1, $sessionId);
            return $this->getRandomWeaponByRarity(1);
        }

        $cumulativeProbability = 0;
        foreach ($this->rarityProbabilities as $rarity => $probability) {
            $cumulativeProbability += ($rarity == 1) ? $probability * $increasedDropRate : $probability;
            if ($rand <= $cumulativeProbability) {
                $this->resetPitty($rarity, $sessionId);
                return $this->getRandomWeaponByRarity($rarity);
            }
        }

        return null;
    }

    private function getRandomWeaponByRarity($rarity)
    {
        $weapons = Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        });

        return $weapons->random();
    }

    private function updateInventory($sessionId, $item)
    {
        Redis::lpush('inventory_' . $sessionId,json_encode($item));
    }

    private function initializeCache($sessionId)
    {
        if (!Redis::exists('totalPulls_count_' . $sessionId)) {
            Redis::setex('totalPulls_count_' . $sessionId, $this->cacheDuration * 60, 0);
        }
        if (!Redis::exists('pitty4_count_' . $sessionId)) {
            Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
        }
        if (!Redis::exists('pitty5_count_' . $sessionId)) {
            Redis::setex('pitty5_count_' . $sessionId, $this->cacheDuration * 60, 0);
        }
        if (!Redis::exists('inventory_' . $sessionId)) {
            Redis::setex('inventory_' . $sessionId, $this->cacheDuration * 60, 0);
        }
    }

    private function getCacheData($sessionId)
    {
        return [
            'inventory' => Redis::get('inventory_' . $sessionId) ?? 0,
            'totalPulls' => Redis::get('totalPulls_count_' . $sessionId) ?? 0,
            'pitty4' => Redis::get('pitty4_count_' . $sessionId) ?? 0,
            'pitty5' => Redis::get('pitty5_count_' . $sessionId) ?? 0
        ];
    }

    public function resetGacha()
    {
        $sessionId = Session::getId();
        foreach (['totalPulls_count_', 'pitty4_count_', 'pitty5_count_'] as $prefix) {
            Redis::del($prefix . $sessionId);
        }
        return redirect()->route('gacha.page');
    }

    public function resetPitty($rarity, $sessionId)
    {
        switch ($rarity) {
            case 1:
                Redis::setex('pitty5_count_' . $sessionId, $this->cacheDuration * 60, 0);
                Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
                break;
            case 2:
                Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
                break;
            default:
                break;
        }
    }
}
