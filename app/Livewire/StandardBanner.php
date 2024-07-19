<?php

namespace App\Livewire;

use App\Models\Weapon;
use Livewire\Component;
use App\Services\CharacterService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class StandardBanner extends Component
{
    public $rarityProbabilities = [
        '1' => 0.5,
        '2' => 6.0,
        '3' => 93.5,
    ];

    public $characters = [];
    public $cacheDuration = 120; // Cache duration in minutes

    public $cachedData;
    public $gachaResults=[];

    public $sessionId;
    public $bgImg;
    public $weaponColor = 'cyan';

    public function mount(CharacterService $characterService)
    {
        $sessionId = Session::getId();
        $this->characters = $characterService->getData();
        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $this->cachedData = $this->getCacheData($sessionId);
    }



    public function singlePull()
    {
        $this->sessionId = Session::getId();
        $this->initializeCache($this->sessionId);
        $gachaResult = $this->getGachaResult($this->sessionId);
        Redis::incr('totalPulls_count_' . $this->sessionId);
        $this->cachedData = $this->getCacheData($this->sessionId);
        if ($gachaResult) {
            $this->bgImg = '';
            $this->gachaResults = [[
                'id' => $gachaResult->id,
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
            $gachaResult = $this->getGachaResult($this->sessionId);
            if ($gachaResult) {
                $results[] = [
                    'id' => $gachaResult->id,
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

    private function getGachaResult($sessionId)
    {
        // Logika untuk mendapatkan hasil gacha
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

    private function getRandomWeaponByRarity($rarity)
    {
        $weapons = Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        });

        return $weapons->random();
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
