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
        '1' => 1.5,
        '2' => 9.5,
        '3' => 89,
    ];

    private $cacheDuration = 120; // Cache duration in minutes

    public function showGachaPage()
    {
        return view('gacha.pull-page');
    }

    public function performGacha(Request $request)
    {
        $sessionId = Session::getId();
        $gachaResult = $this->getGachaResult($sessionId);
        $totalPulls = Cache::get('totalPulls_count_' . $sessionId, 0);
        $pitty4 = Cache::get('pitty4_count_' . $sessionId, 0);
        $pitty5 = Cache::get('pitty5_count_' . $sessionId, 0);

        if ($gachaResult) {
            Cache::put('gacha_result_' . $sessionId, $gachaResult, $this->cacheDuration);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                    'img' => asset('storage/'.$gachaResult->img),
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

    public function performTenGacha(Request $request)
    {
        $sessionId = Session::getId();
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

        $totalPulls = Cache::get('totalPulls_count_' . $sessionId, 0);
        $pitty4 = Cache::get('pitty4_count_' . $sessionId, 0);
        $pitty5 = Cache::get('pitty5_count_' . $sessionId, 0);

        return response()->json([
            'success' => true,
            'data' => $results,
            'totalPulls' => $totalPulls,
            'pitty4' => $pitty4,
            'pitty5' => $pitty5,
        ]);
    }

    private function getGachaResult($sessionId)
    {
        $rand = mt_rand(0, 10000) / 100;

        // Ensure the cache keys are set before incrementing
        if (!Cache::has('totalPulls_count_' . $sessionId)) {
            Cache::put('totalPulls_count_' . $sessionId, 0, $this->cacheDuration);
        }
        if (!Cache::has('pitty4_count_' . $sessionId)) {
            Cache::put('pitty4_count_' . $sessionId, 0, $this->cacheDuration);
        }
        if (!Cache::has('pitty5_count_' . $sessionId)) {
            Cache::put('pitty5_count_' . $sessionId, 0, $this->cacheDuration);
        }

        Cache::increment('totalPulls_count_' . $sessionId);
        $fourstarPitty = Cache::increment('pitty4_count_' . $sessionId);
        $fivestarPitty = Cache::increment('pitty5_count_' . $sessionId);

        // Peningkatan drop rate hanya untuk rarity 1 jika totalPulls_count >= 70
        $increasedDropRate = ($fivestarPitty >= 70) ? $this->rarityProbabilities[1] * 1.8 + (1 / 100) : $this->rarityProbabilities[1];

        //Pitty menyentuh kelipatan 10's
        if ($fourstarPitty >= 10 && $fivestarPitty < 80) {
            Cache::forget('pitty4_count_' . $sessionId);
            return $this->getRandomWeaponByRarity(2);
        }
        //Hard pity (80)
        if ($fivestarPitty == 80) {
            Cache::forget('pitty5_count_' . $sessionId);
            return $this->getRandomWeaponByRarity(1);
        }
        //Melakukan perhitungan gacha
        $cumulativeProbability = 0;
        foreach ($this->rarityProbabilities as $rarity => $probability) {
            $cumulativeProbability += ($rarity == 1) ? $probability * $increasedDropRate : $probability;
            if ($rand <= $cumulativeProbability) {
                if ($rarity == 1) {
                    Cache::forget('pitty5_count_' . $sessionId);
                    Cache::forget('pitty4_count_' . $sessionId);
                } elseif ($rarity == 2) {
                    Cache::forget('pitty4_count_' . $sessionId);
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
        $sessionId = Session::getId();
        Cache::forget('totalPulls_count_' . $sessionId);
        Cache::forget('pitty4_count_' . $sessionId);
        Cache::forget('pitty5_count_' . $sessionId);
        return redirect()->route('gacha.page');
    }
}
