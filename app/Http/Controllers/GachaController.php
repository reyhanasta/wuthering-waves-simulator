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
            '1' => 1,
            '4' => 15,
            '5' => 84,
        ];

        // Generate random float between 0 and 100
        $rand = mt_rand(0, 10000) / 100;
        
        // Ambil pull count dari cache (jika ada)
        $pullCount = Cache::get('gacha_pull_count', 0);
        $fourstarPitty = Cache::get('pitty_1', 0);
         // Tambah 1 ke pull count
         $pullCount++;
         $fourstarPitty++;
         Cache::put('gacha_pull_count', $pullCount, 60);
         Cache::put('pitty_1', $fourstarPitty, 60);
        
         // Periksa pity counter
         if ($fourstarPitty % 10 === 0) {
            // Reset Pitty 4 star dan berikan item 4 star
            Cache::forget('pitty_1');
            $weapon =  Weapon::where('rarity', 4)->inRandomOrder()->first();
            return $weapon;

        }elseif($pullCount % 80 === 0){
            // Reset Pitty 4 star dan berikan item 4 star
            Cache::forget('gacha_pull_count');
            $weapon =  Weapon::where('rarity', 1)->inRandomOrder()->first();
            return $weapon;

        }

        // Tentukan rarity berdasarkan probabilitas
        $cumulativeProbability = 0;
        foreach ($rarityProbabilities as $rarity => $probability) {
            $cumulativeProbability += $probability;
            if ($rand <= $cumulativeProbability) {
                //Reset Pitty Jika Hit 4 Star
                if($rarity == 1){
                    Cache::forget('gacha_pull_count');
                    Cache::forget('pitty_1');
                }elseif($rarity == 4){
                    Cache::forget('pitty_1');
                }
                // Ambil satu item dengan rarity ini dari database secara acak
                $weapon =  Weapon::where('rarity', $rarity)->inRandomOrder()->first();
                return $weapon;  
            }
        }
        // Fallback jika terjadi kesalahan
        return null;
    }

    public function pittyBuildUp(){
        $pitty_4 = 0;

        $gachaResult = $this->getGachaResult()->rarity;
        if($gachaResult != 5) {

        }
    }
}
