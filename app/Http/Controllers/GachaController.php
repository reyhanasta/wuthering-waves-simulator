<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Weapon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class GachaController extends Controller
{
    public function showGachaPage()
    {
        return view('gacha.pull-page');
    }

    public function performGacha(Request $request)
    {
        $sessionId = Session::getId();
        $gachaResult = $this->getGachaResult();

        Cache::put('gacha_result_' . $sessionId, $gachaResult, 60);

        return response()->json([$gachaResult]);
    }

    private function getGachaResult()
    {
        // Tentukan rarity level dan probabilitasnya
        $rarityProbabilities = [
            '5' => 1.5,
            '4' => 9.5,
            '1' => 89,
        ];

        // Hitung total bobot probabilitas (total dari semua probabilitas rarity)
        $totalWeight = array_sum($rarityProbabilities);

        // Generate random float between 0 to 100 (representing percentage)
        $rand = mt_rand(1, 100);

        // Tentukan rarity berdasarkan probabilitas
        $cumulativeProbability = 0;
        dd($weapon);
        foreach ($rarityProbabilities as $rarity => $probability) {
            $cumulativeProbability += $probability / $totalWeight; // normalisasi probabilitas
            if ($rand <= $cumulativeProbability) {
                // Ambil satu item dengan rarity ini dari database secara acak
                $weapon = Weapon::where('rarity', $rarity)->inRandomOrder()->first();

                return $weapon;
            }
        }

        // Fallback jika terjadi kesalahan
        return null;
    }
}
