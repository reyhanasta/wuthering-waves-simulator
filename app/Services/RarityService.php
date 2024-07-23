<?php

namespace App\Services;

use App\Models\Rarity;
use Illuminate\Support\Facades\Http;

class RarityService
{
    public function standardBanner($rarity){
        $rarityList = Rarity::all();
        $rules = [
            'SSR' => 0.8,
            'SR' => 6,
            'R' => 93.2
        ];

        foreach($rarityList as $item){
            if($item == $rarity){
                return $rules[$rarity];
            }
        }
    }
}
