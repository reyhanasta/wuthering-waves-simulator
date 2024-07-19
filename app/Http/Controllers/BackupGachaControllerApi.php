<?php

namespace App\Livewire;

use App\Models\Weapon;
use Livewire\Component;
use App\Services\CharacterService;
use App\Services\WeaponService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class StandardBanner extends Component
{
    public $rarityProbabilities = [
        '5' => 0.5,
        '4' => 6.0,
        '3' => 93.5,
    ];

    public $sessionId;
    public $cachedData;
    public $cacheDuration = 120; // Cache duration in minutes
    public $characters = [];

    public $weaponService;
    public $randomWeapon;

    public $gachaResults=[];

    public $bgImg;
    public $weaponColor = 'cyan';

    public function mount(CharacterService $characterService)
    {
        $sessionId = Session::getId();
        $this->characters = $characterService->getData();
        
        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $this->cachedData = $this->getCacheData($sessionId);
    }



    public function singlePull(WeaponService $weaponService)
    {
        $this->sessionId = Session::getId();
        $this->weaponService = $weaponService;
        $this->initializeCache($this->sessionId);
        $gachaResult = $this->getGachaResult($this->sessionId,$this->weaponService);
        Redis::incr('totalPulls_count_' . $this->sessionId);
        $this->cachedData = $this->getCacheData($this->sessionId);
        if ($gachaResult) {
            $this->bgImg = '';
            $this->gachaResults = [[
               
                'name' => $gachaResult->name,
                'img' => Storage::url($gachaResult->img),
                'type' => $gachaResult->type,
                'rarity' => $gachaResult->rarity,
            ]];
         } else {
            $this->gachaResults = ['errors'];
        }
    }

    public function tenPulls(){
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->getGachaResult($this->sessionId,$this->weaponService);
            if ($gachaResult) {
                $results[] = [
                    'name' => $gachaResult->name,
                    'img' => Storage::url($gachaResult->img),
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                ];
            }
        }
        Redis::incrby('totalPulls_count_' . $this->sessionId, 10);
        $this->cachedData = $this->getCacheData($this->sessionId);
        $this->bgImg = '';
        $this->gachaResults = $results;

    }

    public function getGachaResult($sessionId,$weaponService)
    {
        // Logika untuk mendapatkan hasil gacha
        $rand = mt_rand(0, 10000) / 100;
      
        $fourstarPitty = Redis::incr('pitty4_count_' . $sessionId);
        $fivestarPitty = Redis::incr('pitty5_count_' . $sessionId);
       

        $increasedDropRate = ($fivestarPitty >= 70) ? $this->rarityProbabilities[5] * 1.8 + (1 / 100) : $this->rarityProbabilities[5];

        if ($fourstarPitty >= 10 && $fivestarPitty < 80) {
            $this->resetPitty(5, $sessionId);
            return $this->getRandomWeaponByRarity(2);
        }

        if ($fivestarPitty == 80) {
            $this->resetPitty(4, $sessionId);
            return $this->getRandomWeaponByRarity(1);
        }

        $cumulativeProbability = 0;
        foreach ($this->rarityProbabilities as $rarity => $probability) {
            $cumulativeProbability += ($rarity == 5) ? $probability * $increasedDropRate : $probability;
            if ($rand <= $cumulativeProbability) {
                $this->resetPitty($rarity, $sessionId);
                return $this->getRandomWeaponByRarity($rarity);
            }
        }

        return null;
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
    }

    private function getCacheData($sessionId)
    {

        return [

            'totalPulls' => Redis::get('totalPulls_count_' . $sessionId) ?? 0,
            'pitty4' => Redis::get('pitty4_count_' . $sessionId) ?? 0,
            'pitty5' => Redis::get('pitty5_count_' . $sessionId) ?? 0
        ];
    }

    // private function getRandomWeaponByRarity($rarity)
    // {
    //     $weapons = Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
    //         return Weapon::where('rarity', $rarity)->get();
    //     });

    //     return $weapons->random();
    // }

    private function getRandomWeaponByRarity($rarity)
{
    $cacheKey = "weapons_rarity_{$rarity}";

    return Cache::remember($cacheKey, $this->cacheDuration, function () use ($rarity) {
        $filteredWeapons = collect();

        $types = $this->weaponService->getAllTypes();
        foreach ($types as $type) {
            $randomType = rand(0,count($type)-1);
          
            $weaponsByType = $this->weaponService->getWeaponsByType($type[$randomType]);
            $totalWeaponByType = count($weaponsByType['weapons']);

            if ($totalWeaponByType > 0) {
                $randomIndex = rand(0, $totalWeaponByType - 1);
                $weaponDetail = $this->weaponService->getWeaponDetail($type[$randomType], $weaponsByType['weapons'][$randomIndex]);
               dd( $weaponDetail);
                if ($weaponDetail['rarity'] == $rarity) {
                    $filteredWeapons->push($weaponDetail);
                }
            }
        }
      
        return $filteredWeapons;
    });
}


    public function resetPitty($rarity, $sessionId)
    {
        switch ($rarity) {
            case 5:
                Redis::setex('pitty5_count_' . $sessionId, $this->cacheDuration * 60, 0);
                Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
                break;
            case 4:
                Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
                break;
            default:
                break;
        }
    }
    public function weaponColor($rarity)
    {
        if($rarity == 1){
            $this->weaponColor = '#ffe0a9';
        }elseif($rarity == 2){
            $this->weaponColor ='#df96e6';
        }else {
            $this->weaponColor = 'cyan';
        }
    }

    public function render()
    {
        return view('livewire.standard-banner');
    }
}
