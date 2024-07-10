<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Weapon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class GachaController extends Controller
{
    private $rarityProbabilities = [
        '1' => 1.5,
        '4' => 9.5,
        '5' => 89,
    ];

    private $cacheDuration = 60; // Cache duration in minutes

    public function showGachaPage()
    {
        return view('gacha.pull-page');
    }

    public function performGacha(Request $request)
    {
        $sessionId = Session::getId();
        $gachaResult = $this->getGachaResult();
        $totalPulls = Cache::get('totalPulls_count', 0);
        $pitty4 = Cache::get('pitty4_count', 0);
        $pitty5 = Cache::get('pitty5_count', 0);

        if ($gachaResult) {
            Cache::put('gacha_result_' . $sessionId, $gachaResult, $this->cacheDuration);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                    'totalPulls' => $totalPulls,
                    'pitty4' => $pitty4,
                    'pitty5' => $pitty5,
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gacha failed. Please try again.'
            ]);
        }
    }

    private function getGachaResult()
    {
        $rand = mt_rand(0, 10000) / 100;
        $totalPulls = Cache::get('totalPulls_count', 0) + 1;
        $fourstarPitty = Cache::get('pitty4_count', 0) + 1;
        $fivestarPitty = Cache::get('pitty5_count', 0) + 1;

        Cache::put('totalPulls_count', $totalPulls, $this->cacheDuration);
        Cache::put('pitty4_count', $fourstarPitty, $this->cacheDuration);
        Cache::put('pitty5_count', $fivestarPitty, $this->cacheDuration);

        if ($fourstarPitty >= 10) {
            Cache::forget('pitty4_count');
            return $this->getRandomWeaponByRarity(4);
        }

        if ($fivestarPitty >= 80) {
            Cache::forget('pitty5_count');
            return $this->getRandomWeaponByRarity(1);
        }

        $cumulativeProbability = 0;
        foreach ($this->rarityProbabilities as $rarity => $probability) {
            $cumulativeProbability += $probability;
            if ($rand <= $cumulativeProbability) {
                if ($rarity == 1) {
                    Cache::forget('pitty5_count');
                    Cache::forget('pitty4_count');
                } elseif ($rarity == 4) {
                    Cache::forget('pitty4_count');
                }
                return $this->getRandomWeaponByRarity($rarity);
            }
        }

        return null;
    }

    private function getRandomWeaponByRarity($rarity)
    {
        return Weapon::where('rarity', $rarity)->inRandomOrder()->first();
    }

    public function resetGacha()
    {
        Cache::flush();
        return redirect()->route('gacha.page');
    }
}
