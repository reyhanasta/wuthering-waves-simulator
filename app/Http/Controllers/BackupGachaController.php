<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Weapon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $results = Cache::get('gacha_results_' . $sessionId, []);

        return view('gacha.pull-page', ['gachaResults' => $results]);
    }

    public function performGacha()
    {
        $sessionId = Session::getId();
        $this->initializeCache($sessionId);

        $gachaResult = $this->getGachaResult($sessionId);
        $cacheData = $this->getCacheData($sessionId);

        if ($gachaResult) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                    'img' => asset('storage/'.$gachaResult->img),
                    'totalPulls' => $cacheData['totalPulls'],
                    'pitty4' => $cacheData['pitty4'],
                    'pitty5' => $cacheData['pitty5'],
                ]
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
        
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->getGachaResult($sessionId);
            if ($gachaResult) {
                $results[] = [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'img' => asset('storage/'.$gachaResult->img),
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                ];
            }
        }

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

        Cache::increment('totalPulls_count_' . $sessionId);
        $fourstarPitty = Cache::increment('pitty4_count_' . $sessionId);
        $fivestarPitty = Cache::increment('pitty5_count_' . $sessionId);

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
        $weapons = Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        });

        return $weapons->random();
    }

    private function initializeCache($sessionId)
    {
        if (!Cache::has('totalPulls_count_' . $sessionId)) {
            Cache::put('totalPulls_count_' . $sessionId, 0, $this->cacheDuration);
        }
        if (!Cache::has('pitty4_count_' . $sessionId)) {
            Cache::put('pitty4_count_' . $sessionId, 0, $this->cacheDuration);
        }
        if (!Cache::has('pitty5_count_' . $sessionId)) {
            Cache::put('pitty5_count_' . $sessionId, 0, $this->cacheDuration);
        }
    }

    private function getCacheData($sessionId)
    {
        return [
            'totalPulls' => Cache::get('totalPulls_count_' . $sessionId, 0),
            'pitty4' => Cache::get('pitty4_count_' . $sessionId, 0),
            'pitty5' => Cache::get('pitty5_count_' . $sessionId, 0),
        ];
    }

    public function resetGacha()
    {
        $sessionId = Session::getId();
        Cache::forget('totalPulls_count_' . $sessionId);
        Cache::forget('pitty4_count_' . $sessionId);
        Cache::forget('pitty5_count_' . $sessionId);
        return redirect()->route('gacha.page');
    }

    public function resetPitty($rarity, $sessionId)
    {
        switch ($rarity) {
            case 1:
                Cache::forget('pitty5_count_' . $sessionId);
                Cache::forget('pitty4_count_' . $sessionId);
                break;
            case 2:
                Cache::forget('pitty4_count_' . $sessionId);
                break;
            default:
                break;
        }
    }
}
