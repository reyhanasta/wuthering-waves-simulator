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

        if ($gachaResult) {
            // Simpan hasil gacha di cache selama 60 menit
            Cache::put('gacha_result_' . $sessionId, $gachaResult, 60);

            // Kembalikan hasil gacha sebagai respons JSON
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity
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
        // Tentukan rarity level dan probabilitasnya
        $rarityProbabilities = [
            '1' => 1.5,
            '4' => 9.5,
            '5' => 89,
        ];

        // Generate random float between 0 and 100
        $rand = mt_rand(0, 10000) / 100;

        // Tentukan rarity berdasarkan probabilitas
        $cumulativeProbability = 0;
        foreach ($rarityProbabilities as $rarity => $probability) {
            $cumulativeProbability += $probability;
            if ($rand <= $cumulativeProbability) {
                // Ambil satu item dengan rarity ini dari database secara acak
                return Weapon::where('rarity', $rarity)->inRandomOrder()->first();
            }
        }
        // Fallback jika terjadi kesalahan
        return null;
    }
}
